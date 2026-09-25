<?php

namespace App\Models;

use App\Enums\DeadlinePriority;
use Database\Factories\DeadlineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deadline extends Model
{
    /** @use HasFactory<DeadlineFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'due_at',
        'completed_at',
        'priority',
        'project_id',
        'milestone_id',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'priority' => DeadlinePriority::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isOverdue(): bool
    {
        if ($this->isCompleted()) {
            return false;
        }

        return $this->due_at->isPast();
    }

    public function wasCompletedOnTime(): bool
    {
        if (! $this->isCompleted()) {
            return false;
        }

        return $this->completed_at->lessThanOrEqualTo($this->due_at);
    }

    public function daysLate(): int
    {
        if (! $this->isCompleted()) {
            return 0;
        }

        if ($this->wasCompletedOnTime()) {
            return 0;
        }

        return abs($this->due_at->diffInDays($this->completed_at));
    }
}
