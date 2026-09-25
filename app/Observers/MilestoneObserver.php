<?php

namespace App\Observers;

use App\Enums\MilestoneStatus;
use App\Models\Milestone;

class MilestoneObserver
{
    /**
     * Handle the Milestone "saved" event.
     * Ensure only one milestone per project is in_progress or waiting at a time.
     */
    public function saved(Milestone $milestone): void
    {
        // Only enforce rule if milestone is in_progress or waiting
        if (! in_array($milestone->status, [MilestoneStatus::InProgress, MilestoneStatus::Waiting])) {
            return;
        }

        // Find all other milestones on the same project with in_progress or waiting status
        Milestone::where('project_id', $milestone->project_id)
            ->where('id', '!=', $milestone->id)
            ->whereIn('status', [MilestoneStatus::InProgress, MilestoneStatus::Waiting])
            ->update([
                'status' => MilestoneStatus::Upcoming,
                'waiting_on' => null,
                'waiting_note' => null,
                'waiting_since' => null,
            ]);
    }
}
