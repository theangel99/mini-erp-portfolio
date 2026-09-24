<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Enums\CustomerType;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Osnovni podatki')
                    ->schema([
                        Select::make('type')
                            ->label('Tip stranke')
                            ->options(CustomerType::class)
                            ->default(CustomerType::Legal)
                            ->required()
                            ->live(),

                        TextInput::make('name')
                            ->label('Naziv')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('vat_id')
                            ->label('Davčna številka')
                            ->maxLength(20)
                            ->visible(fn ($get) => $get('type') === CustomerType::Legal->value),

                        Checkbox::make('is_vat_payer')
                            ->label('Zavezanec za DDV')
                            ->default(true)
                            ->visible(fn ($get) => $get('type') === CustomerType::Legal->value),
                    ])
                    ->columns(2),

                Section::make('Kontaktni podatki')
                    ->schema([
                        TextInput::make('email')
                            ->label('E-pošta')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->maxLength(50),
                    ])
                    ->columns(2),

                Section::make('Naslov')
                    ->schema([
                        TextInput::make('address')
                            ->label('Naslov')
                            ->maxLength(255),

                        TextInput::make('postal_code')
                            ->label('Poštna številka')
                            ->maxLength(20),

                        TextInput::make('city')
                            ->label('Kraj')
                            ->maxLength(100),

                        TextInput::make('country')
                            ->label('Država')
                            ->default('SI')
                            ->maxLength(2),
                    ])
                    ->columns(2),

                Section::make('Dodatno')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Opombe')
                            ->rows(3),
                    ])
                    ->collapsible(),
            ]);
    }
}
