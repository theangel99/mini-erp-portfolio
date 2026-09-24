<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Enums\QuoteStatus;
use App\Filament\Resources\Quotes\QuoteResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send')
                ->label('Pošlji')
                ->icon('heroicon-o-paper-airplane')
                ->color(Color::Blue)
                ->requiresConfirmation()
                ->modalHeading('Pošlji ponudbo stranki')
                ->modalDescription('Ali ste prepričani, da želite poslati ponudbo stranki? Status se bo spremenil v "Poslana".')
                ->action(function () {
                    $this->record->update([
                        'status' => QuoteStatus::Sent,
                        'sent_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Ponudba poslana')
                        ->body('Ponudba je bila označena kot poslana.')
                        ->send();
                })
                ->visible(fn () => $this->record->status === QuoteStatus::Draft),

            Action::make('accept')
                ->label('Sprejeta')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => QuoteStatus::Accepted,
                        'accepted_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Ponudba sprejeta')
                        ->body('Ponudba je bila označena kot sprejeta.')
                        ->send();
                })
                ->visible(fn () => in_array($this->record->status, [QuoteStatus::Sent])),

            Action::make('reject')
                ->label('Zavrnjena')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => QuoteStatus::Rejected,
                    ]);

                    Notification::make()
                        ->warning()
                        ->title('Ponudba zavrnjena')
                        ->body('Ponudba je bila označena kot zavrnjena.')
                        ->send();
                })
                ->visible(fn () => in_array($this->record->status, [QuoteStatus::Sent])),

            Action::make('duplicate')
                ->label('Podvoji')
                ->icon('heroicon-o-document-duplicate')
                ->color(Color::Gray)
                ->action(function () {
                    $newQuote = $this->record->replicate([
                        'number',
                        'sent_at',
                        'accepted_at',
                    ]);
                    $newQuote->status = QuoteStatus::Draft;
                    $newQuote->issued_at = now();
                    $newQuote->valid_until = now()->addDays(30);
                    $newQuote->save();

                    // Duplicate items
                    foreach ($this->record->items as $item) {
                        $newItem = $item->replicate();
                        $newItem->quote_id = $newQuote->id;
                        $newItem->save();
                    }

                    Notification::make()
                        ->success()
                        ->title('Ponudba podvojena')
                        ->body('Nova ponudba je bila ustvarjena.')
                        ->send();

                    return redirect()->route('filament.admin.resources.quotes.edit', $newQuote);
                }),

            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Format decimals for display
        $data['items'] = collect($data['items'] ?? [])->map(function ($item) {
            return [
                ...$item,
                'quantity' => number_format((float) ($item['quantity'] ?? 0), 3, '.', ''),
                'unit_price' => number_format((float) ($item['unit_price'] ?? 0), 2, '.', ''),
                'discount_percent' => number_format((float) ($item['discount_percent'] ?? 0), 2, '.', ''),
            ];
        })->toArray();

        return $data;
    }
}
