<?php

namespace App\Filament\Pages;

use App\Models\Project;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ProjectGantt extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Gantt časovnica';

    protected static ?string $title = 'Projektna časovnica';

    protected string $view = 'filament.pages.project-gantt';

    public function getGanttData(): array
    {
        $projects = Project::with(['user', 'customer'])
            ->orderBy('starts_at')
            ->get();

        $tasks = [];
        $links = [];

        foreach ($projects as $index => $project) {
            $tasks[] = [
                'id' => $project->id,
                'text' => $project->name,
                'start_date' => $project->starts_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                'duration' => $project->starts_at && $project->ends_at
                    ? $project->starts_at->diffInDays($project->ends_at)
                    : 30,
                'progress' => $this->calculateProgress($project),
                'open' => true,
                'customer' => $project->customer?->name ?? '',
                'user' => $project->user?->name ?? '',
                'phase' => $project->current_phase->getLabel(),
                'status' => $project->status->getLabel(),
            ];
        }

        return [
            'data' => $tasks,
            'links' => $links,
        ];
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
