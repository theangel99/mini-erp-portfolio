<?php

namespace App\Filament\Resources\Deadlines\Schemas;

use App\Enums\DeadlinePriority;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DeadlineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                DateTimePicker::make('due_at')
                    ->required(),
                DateTimePicker::make('completed_at'),
                Select::make('priority')
                    ->options(DeadlinePriority::class)
                    ->required(),
                Select::make('project_id')
                    ->relationship('project', 'name'),
                Select::make('milestone_id')
                    ->relationship('milestone', 'title'),
            ]);
    }
}
