<?php

namespace App\Filament\Resources\Quotes\Tables;

use App\Enums\QuoteStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Številka')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Stranka')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('issued_at')
                    ->label('Datum izdaje')
                    ->date('d. m. Y')
                    ->sortable(),

                TextColumn::make('valid_until')
                    ->label('Veljavna do')
                    ->date('d. m. Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Skupaj')
                    ->money('EUR', locale: 'sl')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ustvarjeno')
                    ->dateTime('d. m. Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(QuoteStatus::class)
                    ->multiple(),

                SelectFilter::make('customer')
                    ->label('Stranka')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('issued_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('issued_from')
                            ->label('Izdano od'),
                        \Filament\Forms\Components\DatePicker::make('issued_until')
                            ->label('Izdano do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['issued_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('issued_at', '>=', $date),
                            )
                            ->when(
                                $data['issued_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('issued_at', '<=', $date),
                            );
                    }),

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
