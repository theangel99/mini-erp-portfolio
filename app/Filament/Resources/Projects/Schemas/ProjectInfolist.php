<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Schema;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Napredek projekta')
                    ->schema([
                        ViewEntry::make('phases')
                            ->view('filament.infolists.project-phase-stepper')
                            ->columnSpanFull(),
                    ]),

                Section::make('Podatki o projektu')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Ime projekta'),
                        TextEntry::make('customer.name')
                            ->label('Naročnik'),
                        TextEntry::make('status')
                            ->badge()
                            ->label('Status'),
                        TextEntry::make('current_phase')
                            ->badge()
                            ->label('Trenutna faza'),
                        TextEntry::make('starts_at')
                            ->label('Začetek')
                            ->date('d.m.Y'),
                        TextEntry::make('ends_at')
                            ->label('Rok')
                            ->date('d.m.Y'),
                        TextEntry::make('user.name')
                            ->label('Odgovorna oseba'),
                        TextEntry::make('description')
                            ->label('Opis')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
