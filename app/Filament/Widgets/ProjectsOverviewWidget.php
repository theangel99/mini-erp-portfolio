<?php

namespace App\Filament\Widgets;

use App\Enums\PublishingPhase;
use App\Models\Project;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ProjectsOverviewWidget extends TableWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()
                    ->with(['customer', 'user'])
                    ->whereNot('current_phase', PublishingPhase::Completed)
                    ->orderBy('starts_at')
            )
            ->heading('Aktivni projekti')
            ->columns([
                TextColumn::make('name')
                    ->label('Projekt')
                    ->searchable()
                    ->weight('semibold')
                    ->size('sm'),
                TextColumn::make('customer.name')
                    ->label('Naročnik')
                    ->searchable()
                    ->size('sm'),
                ViewColumn::make('current_phase')
                    ->label('Napredek')
                    ->view('filament.widgets.project-phase-progress'),
                TextColumn::make('ends_at')
                    ->label('Rok')
                    ->date('d.m.Y')
                    ->sortable()
                    ->size('sm'),
            ])
            ->recordUrl(fn ($record) => route('filament.admin.resources.projects.view', $record));
    }
}
