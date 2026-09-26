<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'assigned_to',
        'assigned_by',
        'status',
        'priority',
        'due_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'priority' => TaskPriority::class,
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function complete(): void
    {
        $this->update([
            'status' => TaskStatus::Completed,
            'completed_at' => now(),
        ]);
    }

    public function isOverdue(): bool
    {
        return $this->due_at &&
            $this->due_at->isPast() &&
            $this->status !== TaskStatus::Completed;
    }

    public function isCompleted(): bool
    {
        return $this->status === TaskStatus::Completed;
    }
}
