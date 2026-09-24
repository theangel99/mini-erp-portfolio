<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CustomerType: string implements HasLabel, HasColor, HasIcon
{
    case Legal = 'legal';
    case Person = 'person';

    public function getLabel(): string
    {
        return match ($this) {
            self::Legal => 'Pravna oseba',
            self::Person => 'Fizična oseba',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Legal => 'info',
            self::Person => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Legal => 'heroicon-o-building-office',
            self::Person => 'heroicon-o-user',
        };
    }
}
