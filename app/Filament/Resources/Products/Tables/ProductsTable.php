<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('Šifra')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Naziv')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('unit')
                    ->label('Enota')
                    ->badge()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Cena')
                    ->money('EUR', locale: 'sl')
                    ->sortable(),

                TextColumn::make('vat_rate')
                    ->label('DDV')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktivno')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ustvarjeno')
                    ->dateTime('d. m. Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('is_active')
                    ->label('Samo aktivni')
                    ->query(fn ($query) => $query->where('is_active', true))
                    ->default(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }
}
