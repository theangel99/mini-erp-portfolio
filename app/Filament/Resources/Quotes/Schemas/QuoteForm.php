<?php

namespace App\Filament\Resources\Quotes\Schemas;

use App\Enums\ProductUnit;
use App\Enums\QuoteStatus;
use App\Enums\VatRate;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Product;
use App\Services\DocumentTotalsCalculator;
use Filament\Schemas\Components\DatePicker;
use Filament\Schemas\Components\Hidden;
use Filament\Schemas\Components\Placeholder;
use Filament\Schemas\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Get;
use Filament\Schemas\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

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
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('contact_id', null);
                            }),

                        Select::make('contact_id')
                            ->label('Kontaktna oseba')
                            ->options(function (Get $get) {
                                $customerId = $get('customer_id');
                                if (! $customerId) {
                                    return [];
                                }

                                return Contact::where('customer_id', $customerId)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload(),

                        DatePicker::make('issued_at')
                            ->label('Datum izdaje')
                            ->default(now())
                            ->required()
                            ->native(false),

                        DatePicker::make('valid_until')
                            ->label('Veljavna do')
                            ->default(now()->addDays(30))
                            ->required()
                            ->native(false)
                            ->after('issued_at'),

                        Select::make('status')
                            ->label('Status')
                            ->options(QuoteStatus::class)
                            ->default(QuoteStatus::Draft)
                            ->required()
                            ->disabled(fn (?string $operation) => $operation === 'create'),

                        Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])
                    ->columns(2),

                Section::make('Postavke')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Produkt')
                                    ->options(Product::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (?int $state, Set $set) {
                                        if (! $state) {
                                            return;
                                        }

                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('description', $product->description ?? $product->name);
                                            $set('unit', $product->unit->value);
                                            $set('unit_price', number_format((float) $product->price, 2, '.', ''));
                                            $set('vat_rate', $product->vat_rate->value);
                                        }
                                    }),

                                Textarea::make('description')
                                    ->label('Opis')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Select::make('unit')
                                    ->label('Enota')
                                    ->options(ProductUnit::class)
                                    ->required(),

                                TextInput::make('quantity')
                                    ->label('Količina')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::updateLineTotals($set, $get)),

                                TextInput::make('unit_price')
                                    ->label('Cena/enoto')
                                    ->numeric()
                                    ->prefix('€')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::updateLineTotals($set, $get)),

                                TextInput::make('discount_percent')
                                    ->label('Popust %')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('%')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::updateLineTotals($set, $get)),

                                Select::make('vat_rate')
                                    ->label('DDV stopnja')
                                    ->options(VatRate::class)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::updateLineTotals($set, $get)),

                                Placeholder::make('line_net')
                                    ->label('Neto')
                                    ->content(fn (Get $get) => '€ '.number_format((float) ($get('line_net') ?? 0), 2, ',', '.')),

                                Placeholder::make('line_vat')
                                    ->label('DDV')
                                    ->content(fn (Get $get) => '€ '.number_format((float) ($get('line_vat') ?? 0), 2, ',', '.')),

                                Placeholder::make('line_total')
                                    ->label('Skupaj')
                                    ->content(fn (Get $get) => '€ '.number_format((float) ($get('line_total') ?? 0), 2, ',', '.')),

                                // Hidden fields to store calculated values
                                Hidden::make('line_net'),
                                Hidden::make('line_vat'),
                                Hidden::make('line_total'),
                            ])
                            ->columns(3)
                            ->reorderable('sort')
                            ->defaultItems(1)
                            ->addActionLabel('Dodaj postavko')
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::updateDocumentTotals($set, $get)),
                    ]),

                Section::make('Seštevki')
                    ->schema([
                        Placeholder::make('subtotal_display')
                            ->label('Neto vsota')
                            ->content(fn (Get $get) => '€ '.number_format((float) ($get('subtotal') ?? 0), 2, ',', '.')),

                        Placeholder::make('discount_total_display')
                            ->label('Popust skupaj')
                            ->content(fn (Get $get) => '€ '.number_format((float) ($get('discount_total') ?? 0), 2, ',', '.')),

                        Placeholder::make('vat_total_display')
                            ->label('DDV skupaj')
                            ->content(fn (Get $get) => '€ '.number_format((float) ($get('vat_total') ?? 0), 2, ',', '.')),

                        Placeholder::make('total_display')
                            ->label('Skupaj z DDV')
                            ->content(fn (Get $get) => '€ '.number_format((float) ($get('total') ?? 0), 2, ',', '.'))
                            ->extraAttributes(['class' => 'text-xl font-bold']),

                        // Hidden fields to store totals
                        Hidden::make('subtotal'),
                        Hidden::make('discount_total'),
                        Hidden::make('vat_total'),
                        Hidden::make('total'),
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

    protected static function updateLineTotals(Set $set, Get $get): void
    {
        $calculated = DocumentTotalsCalculator::calculateLineItem([
            'quantity' => $get('quantity') ?? 0,
            'unit_price' => $get('unit_price') ?? 0,
            'discount_percent' => $get('discount_percent') ?? 0,
            'vat_rate' => $get('vat_rate') ?? '0',
        ]);

        $set('line_net', $calculated['line_net']);
        $set('line_vat', $calculated['line_vat']);
        $set('line_total', $calculated['line_total']);
    }

    protected static function updateDocumentTotals(Set $set, Get $get): void
    {
        $items = $get('../../items') ?? [];

        if (empty($items)) {
            $set('../../subtotal', '0.00');
            $set('../../discount_total', '0.00');
            $set('../../vat_total', '0.00');
            $set('../../total', '0.00');

            return;
        }

        $totals = DocumentTotalsCalculator::calculateDocumentTotals($items);

        $set('../../subtotal', $totals['subtotal']);
        $set('../../discount_total', $totals['discount_total']);
        $set('../../vat_total', $totals['vat_total']);
        $set('../../total', $totals['total']);
    }
}
