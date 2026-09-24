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

                Section::make('Postavke')
                    ->description('Postavke ponudbe - seštevki se izračunajo pri shranjevanju')
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Produkt')
                                    ->relationship('product', 'name', fn ($query) => $query->where('is_active', true))
                                    ->searchable()
                                    ->preload()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if (!$state) {
                                            return;
                                        }

                                        $product = \App\Models\Product::find($state);
                                        if ($product) {
                                            $set('description', $product->description ?? $product->name);
                                            $set('unit', $product->unit->value);
                                            $set('unit_price', (float) $product->price);
                                            $set('vat_rate', $product->vat_rate->value);
                                        }
                                    })
                                    ->live(onBlur: true),

                                \Filament\Forms\Components\TextInput::make('description')
                                    ->label('Opis')
                                    ->required()
                                    ->maxLength(255),

                                Select::make('unit')
                                    ->label('Enota')
                                    ->options(\App\Enums\ProductUnit::class)
                                    ->default(\App\Enums\ProductUnit::Piece)
                                    ->required(),

                                \Filament\Forms\Components\TextInput::make('quantity')
                                    ->label('Količina')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),

                                \Filament\Forms\Components\TextInput::make('unit_price')
                                    ->label('Cena/enoto')
                                    ->numeric()
                                    ->prefix('€')
                                    ->default(0)
                                    ->required(),

                                \Filament\Forms\Components\TextInput::make('discount_percent')
                                    ->label('Popust %')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('%'),

                                Select::make('vat_rate')
                                    ->label('DDV')
                                    ->options(\App\Enums\VatRate::class)
                                    ->default(\App\Enums\VatRate::Rate22)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->reorderable('sort')
                            ->defaultItems(1)
                            ->addActionLabel('Dodaj postavko')
                            ->collapsible(),
                    ]),

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
