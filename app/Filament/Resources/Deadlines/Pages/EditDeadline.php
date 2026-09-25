<?php

namespace App\Filament\Resources\Deadlines\Pages;

use App\Filament\Resources\Deadlines\DeadlineResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeadline extends EditRecord
{
    protected static string $resource = DeadlineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
