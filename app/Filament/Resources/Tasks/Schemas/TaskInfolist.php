<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\Task;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TaskInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('project.name')
                    ->label('Project'),
                TextEntry::make('title'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('assigned_to')
                    ->numeric(),
                TextEntry::make('assigned_by')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('priority')
                    ->badge(),
                TextEntry::make('due_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Task $record): bool => $record->trashed()),
            ]);
    }
}
