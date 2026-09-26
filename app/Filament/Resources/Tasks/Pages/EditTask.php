<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Enums\TaskStatus;
use App\Filament\Resources\Tasks\TaskResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('complete')
                ->label('Označi kot zaključeno')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => ! $this->record->isCompleted())
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->complete();

                    Notification::make()
                        ->title('Naloga zaključena')
                        ->success()
                        ->send();
                }),
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
