<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Enums\MilestoneStatus;
use App\Enums\WaitingOn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    protected static ?string $title = 'Mejniki';

    protected static ?string $modelLabel = 'mejnik';

    protected static ?string $pluralModelLabel = 'mejniki';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Naslov')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Opis')
                    ->rows(3),

                Select::make('status')
                    ->label('Status')
                    ->options(MilestoneStatus::class)
                    ->default(MilestoneStatus::Upcoming)
                    ->required()
                    ->live(),

                DateTimePicker::make('planned_at')
                    ->label('Načrtovani datum')
                    ->required()
                    ->native(false),

                DateTimePicker::make('completed_at')
                    ->label('Datum zaključka')
                    ->visible(fn ($get) => $get('status') === MilestoneStatus::Done->value)
                    ->native(false),

                Select::make('waiting_on')
                    ->label('Čakamo na')
                    ->options(WaitingOn::class)
                    ->visible(fn ($get) => $get('status') === MilestoneStatus::Waiting->value),

                Textarea::make('waiting_note')
                    ->label('Opomba o čakanju')
                    ->visible(fn ($get) => $get('status') === MilestoneStatus::Waiting->value)
                    ->rows(2),

                DateTimePicker::make('waiting_since')
                    ->label('Čakamo od')
                    ->visible(fn ($get) => $get('status') === MilestoneStatus::Waiting->value)
                    ->native(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('sort')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Naslov')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('planned_at')
                    ->label('Načrtovano')
                    ->dateTime('d. m. Y')
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->label('Zaključeno')
                    ->dateTime('d. m. Y')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('waiting_on')
                    ->label('Čakamo na')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state ? WaitingOn::from($state)->getLabel() : null)
                    ->placeholder('—'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
