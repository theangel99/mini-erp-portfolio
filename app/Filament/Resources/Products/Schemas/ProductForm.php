<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductUnit;
use App\Enums\VatRate;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Osnovni podatki')
                    ->schema([
                        TextInput::make('sku')
                            ->label('Šifra')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        TextInput::make('name')
                            ->label('Naziv')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Opis')
                            ->rows(3),
                    ])
                    ->columns(2),

                Section::make('Cena in DDV')
                    ->schema([
                        Select::make('unit')
                            ->label('Enota')
                            ->options(ProductUnit::class)
                            ->default(ProductUnit::Piece)
                            ->required(),

                        TextInput::make('price')
                            ->label('Cena')
                            ->numeric()
                            ->prefix('€')
                            ->required()
                            ->default(0),

                        Select::make('vat_rate')
                            ->label('DDV stopnja')
                            ->options(VatRate::class)
                            ->default(VatRate::Rate22)
                            ->required(),

                        Checkbox::make('is_active')
                            ->label('Aktivno')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
