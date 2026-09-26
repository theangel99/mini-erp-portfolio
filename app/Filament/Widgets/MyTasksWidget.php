<?php

namespace App\Filament\Widgets;

use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class MyTasksWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->whereNotIn('status', [TaskStatus::Completed, TaskStatus::Cancelled])
                    ->orderBy('due_at')
                    ->limit(10)
            )
            ->heading('Moje naloge')
            ->columns([
                TextColumn::make('title')
                    ->label('Naslov')
                    ->searchable()
                    ->description(fn ($record) => $record->description),
                TextColumn::make('project.name')
                    ->label('Projekt')
                    ->searchable(),
                TextColumn::make('assignedTo.name')
                    ->label('Dodeljeno')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('priority')
                    ->label('Prioriteta')
                    ->badge(),
                TextColumn::make('due_at')
                    ->label('Rok')
                    ->dateTime()
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Poglej')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.tasks.edit', $record)),
            ]);
    }
}
