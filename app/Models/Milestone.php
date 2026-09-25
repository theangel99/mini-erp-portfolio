<?php

namespace App\Models;

use App\Enums\MilestoneStatus;
use App\Enums\WaitingOn;
use Database\Factories\MilestoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
    /** @use HasFactory<MilestoneFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'sort',
        'title',
        'description',
        'planned_at',
        'completed_at',
        'status',
        'waiting_on',
        'waiting_note',
        'waiting_since',
    ];

    protected $casts = [
        'planned_at' => 'date',
        'completed_at' => 'datetime',
        'waiting_since' => 'datetime',
        'status' => MilestoneStatus::class,
        'waiting_on' => WaitingOn::class,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function deadlines(): HasMany
    {
        return $this->hasMany(Deadline::class);
    }

    public function isOverdue(): bool
    {
        if ($this->status === MilestoneStatus::Done || $this->status === MilestoneStatus::Skipped) {
            return false;
        }

        return $this->planned_at->isPast();
    }

    public function daysOverdue(): int
    {
        if (! $this->isOverdue()) {
            return 0;
        }

        return abs($this->planned_at->diffInDays(now()));
    }

    public function waitingDays(): int
    {
        if (! $this->waiting_since) {
            return 0;
        }

        return abs($this->waiting_since->diffInDays(now()));
    }
}
