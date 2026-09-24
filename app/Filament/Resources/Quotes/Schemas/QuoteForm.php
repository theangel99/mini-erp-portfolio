<?php

namespace App\Filament\Resources\Quotes\Schemas;

use App\Enums\QuoteStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Osnovni podatki')
                    ->schema([
                        Select::make('customer_id')
                            ->label('Stranka')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->required(),

                        DatePicker::make('issued_at')
                            ->label('Datum izdaje')
                            ->default(now())
                            ->required()
                            ->native(false),

                        DatePicker::make('valid_until')
                            ->label('Veljavna do')
                            ->default(now()->addDays(30))
                            ->required()
                            ->native(false),

                        Select::make('status')
                            ->label('Status')
                            ->options(QuoteStatus::class)
                            ->default(QuoteStatus::Draft)
                            ->required(),

                        Hidden::make('user_id')
                            ->default(auth()->id()),

                        Hidden::make('subtotal')
                            ->default(0),

                        Hidden::make('discount_total')
                            ->default(0),

                        Hidden::make('vat_total')
                            ->default(0),

                        Hidden::make('total')
                            ->default(0),
                    ])
                    ->columns(2),

                Section::make('Dodatno')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Opombe')
                            ->rows(3),

                        Textarea::make('terms')
                            ->label('Pogoji')
                            ->rows(3),
                    ])
                    ->collapsible(),
            ]);
    }
}
