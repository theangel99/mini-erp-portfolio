<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Naziv')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('vat_id')
                    ->label('Davčna št.')
                    ->searchable()
                    ->toggleable(),

                IconColumn::make('is_vat_payer')
                    ->label('DDV zavezanec')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('city')
                    ->label('Kraj')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-pošta')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Telefon')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Ustvarjeno')
                    ->dateTime('d. m. Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
