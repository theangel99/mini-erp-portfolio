<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->relationship('project', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('assigned_to')
                    ->relationship('assignedTo', 'name')
                    ->required()
                    ->searchable(),
                Select::make('assigned_by')
                    ->relationship('assignedBy', 'name')
                    ->required()
                    ->searchable()
                    ->default(fn () => auth()->id()),
                Select::make('status')
                    ->options(TaskStatus::class)
                    ->default('pending')
                    ->required(),
                Select::make('priority')
                    ->options(TaskPriority::class)
                    ->default('medium')
                    ->required(),
                DateTimePicker::make('due_at'),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
