<?php

namespace App\Filament\Pages;

use App\Enums\MilestoneStatus;
use App\Models\Deadline;
use App\Models\Milestone;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class Casovnica extends Page
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Časovnica';

    protected static ?string $title = 'Časovnica';

    protected string $view = 'filament.pages.casovnica';

    protected static ?int $navigationSort = 1;

    public ?int $selectedProjectId = null;

    public ?Project $selectedProject = null;

    public function mount(): void
    {
        // Get project from URL query parameter
        $this->selectedProjectId = request()->query('project');

        if ($this->selectedProjectId) {
            $this->selectedProject = Project::with(['milestones' => function ($query) {
                $query->orderBy('sort');
            }])->find($this->selectedProjectId);
        }

        // If no project selected, select first in_progress project
        if (! $this->selectedProject) {
            $this->selectedProject = Project::with(['milestones' => function ($query) {
                $query->orderBy('sort');
            }])
                ->where('status', 'in_progress')
                ->orderBy('starts_at', 'desc')
                ->first();

            if ($this->selectedProject) {
                $this->selectedProjectId = $this->selectedProject->id;
            }
        }
    }

    public function updatedSelectedProjectId($value): void
    {
        if ($value) {
            $this->redirect(route('filament.admin.pages.casovnica', ['project' => $value]));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('completeCurrentMilestone')
                ->label('Zaključi korak')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->selectedProject?->currentMilestone() !== null)
                ->requiresConfirmation()
                ->modalHeading('Zaključi trenutni korak')
                ->modalDescription(fn () => 'Ali si prepričan, da želiš zaključiti korak "'.$this->selectedProject?->currentMilestone()?->title.'"?')
                ->form([
                    DateTimePicker::make('completed_at')
                        ->label('Datum zaključka')
                        ->default(now())
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data) {
                    $milestone = $this->selectedProject->currentMilestone();
                    if ($milestone) {
                        $milestone->update([
                            'status' => MilestoneStatus::Done,
                            'completed_at' => $data['completed_at'],
                            'waiting_on' => null,
                            'waiting_note' => null,
                            'waiting_since' => null,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Korak zaključen')
                            ->body('Korak "'.$milestone->title.'" je bil uspešno zaključen.')
                            ->send();

                        $this->redirect(route('filament.admin.pages.casovnica', ['project' => $this->selectedProjectId]));
                    }
                }),

            Action::make('setWaiting')
                ->label('Čakamo na...')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->visible(fn () => $this->selectedProject?->currentMilestone()?->status === MilestoneStatus::InProgress)
                ->form([
                    Select::make('waiting_on')
                        ->label('Čakamo na')
                        ->options([
                            'customer' => 'Stranko',
                            'supplier' => 'Dobavitelja',
                            'internal' => 'Interni proces',
                            'external' => 'Zunanji partner',
                            'other' => 'Drugo',
                        ])
                        ->required(),
                    Textarea::make('waiting_note')
                        ->label('Opomba')
                        ->placeholder('Kaj točno čakamo...'),
                ])
                ->action(function (array $data) {
                    $milestone = $this->selectedProject->currentMilestone();
                    if ($milestone) {
                        $milestone->update([
                            'status' => MilestoneStatus::Waiting,
                            'waiting_on' => $data['waiting_on'],
                            'waiting_note' => $data['waiting_note'] ?? null,
                            'waiting_since' => now(),
                        ]);

                        Notification::make()
                            ->warning()
                            ->title('Status posodobljen')
                            ->body('Projekt čaka na: '.\App\Enums\WaitingOn::from($data['waiting_on'])->getLabel())
                            ->send();

                        $this->redirect(route('filament.admin.pages.casovnica', ['project' => $this->selectedProjectId]));
                    }
                }),

            Action::make('continueWork')
                ->label('Nadaljuj')
                ->icon('heroicon-o-play')
                ->iconPosition(IconPosition::After)
                ->color('primary')
                ->visible(fn () => $this->selectedProject?->currentMilestone()?->status === MilestoneStatus::Waiting)
                ->requiresConfirmation()
                ->modalHeading('Nadaljuj z delom')
                ->modalDescription('Ali lahko nadaljujemo z delom na tem koraku?')
                ->action(function () {
                    $milestone = $this->selectedProject->currentMilestone();
                    if ($milestone) {
                        $milestone->update([
                            'status' => MilestoneStatus::InProgress,
                            'waiting_on' => null,
                            'waiting_note' => null,
                            'waiting_since' => null,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Delo nadaljujemo')
                            ->body('Delo na koraku "'.$milestone->title.'" se nadaljuje.')
                            ->send();

                        $this->redirect(route('filament.admin.pages.casovnica', ['project' => $this->selectedProjectId]));
                    }
                }),
        ];
    }

    public function getProjectsForSelect(): array
    {
        return Project::query()
            ->whereIn('status', ['planning', 'in_progress', 'on_hold'])
            ->orderBy('starts_at', 'desc')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getDeadlinesGrouped(): array
    {
        $user = auth()->user();
        $now = now();

        return [
            'overdue' => Deadline::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->where('due_at', '<', $now)
                ->orderBy('due_at')
                ->get(),

            'today' => Deadline::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->whereDate('due_at', $now->toDateString())
                ->orderBy('due_at')
                ->get(),

            'tomorrow' => Deadline::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->whereDate('due_at', $now->copy()->addDay()->toDateString())
                ->orderBy('due_at')
                ->get(),

            'this_week' => Deadline::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->whereBetween('due_at', [
                    $now->copy()->addDays(2)->startOfDay(),
                    $now->copy()->endOfWeek(),
                ])
                ->orderBy('due_at')
                ->get(),

            'next_month' => Deadline::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->where('due_at', '>', $now->copy()->endOfWeek())
                ->where('due_at', '<=', $now->copy()->addMonth())
                ->orderBy('due_at')
                ->get(),

            'completed' => Deadline::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    public function getDeadlineStats(): array
    {
        $user = auth()->user();
        $now = now();

        return [
            'tomorrow' => Deadline::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->whereBetween('due_at', [
                    $now->copy()->addDay()->startOfDay(),
                    $now->copy()->addDay()->endOfDay(),
                ])
                ->count(),

            'this_week' => Deadline::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->whereBetween('due_at', [
                    $now->copy()->addDays(2)->startOfDay(),
                    $now->copy()->endOfWeek(),
                ])
                ->count(),

            'next_month' => Deadline::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->where('due_at', '>', $now->copy()->endOfWeek())
                ->where('due_at', '<=', $now->copy()->addMonth())
                ->count(),

            'overdue' => Deadline::where('user_id', $user->id)
                ->whereNull('completed_at')
                ->where('due_at', '<', $now)
                ->count(),

            'completed' => Deadline::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->count(),
        ];
    }
}
