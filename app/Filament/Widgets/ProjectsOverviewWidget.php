<?php

namespace App\Filament\Widgets;

use App\Enums\PublishingPhase;
use App\Models\Project;
use Filament\Widgets\Widget;

class ProjectsOverviewWidget extends Widget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.projects-overview-timeline';

    public function getProjects(): array
    {
        $projects = Project::query()
            ->with(['customer', 'user'])
            ->whereNot('current_phase', PublishingPhase::Completed)
            ->orderBy('starts_at')
            ->get();

        $data = [];
        foreach ($projects as $project) {
            $data[] = [
                'id' => $project->id,
                'text' => $project->name,
                'start_date' => $project->starts_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                'duration' => $project->starts_at && $project->ends_at
                    ? $project->starts_at->diffInDays($project->ends_at)
                    : 30,
                'progress' => $this->calculateProgress($project),
                'customer' => $project->customer?->name ?? '',
                'user' => $project->user?->name ?? '',
                'phase' => $project->current_phase->getLabel(),
                'phase_value' => $project->current_phase->value,
                'status' => $project->status->getLabel(),
            ];
        }

        return ['data' => $data, 'links' => []];
    }

    protected function calculateProgress($project): float
    {
        if (!$project->starts_at || !$project->ends_at) {
            return 0;
        }

        $total = $project->starts_at->diffInDays($project->ends_at);
        $elapsed = $project->starts_at->diffInDays(now());

        if ($total <= 0) {
            return 0;
        }

        return min(100, max(0, ($elapsed / $total) * 100)) / 100;
    }
}
