<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('customer_id')
                    ->relationship('customer', 'name'),
                Select::make('quote_id')
                    ->relationship('quote', 'id'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(ProjectStatus::class)
                    ->required(),
                DatePicker::make('starts_at'),
                DatePicker::make('ends_at'),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
            ]);
    }
}
