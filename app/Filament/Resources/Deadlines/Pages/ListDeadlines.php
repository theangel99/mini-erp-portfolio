<?php

namespace App\Filament\Resources\Deadlines\Pages;

use App\Filament\Resources\Deadlines\DeadlineResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeadlines extends ListRecords
{
    protected static string $resource = DeadlineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
