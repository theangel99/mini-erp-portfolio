<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum WaitingOn: string implements HasColor, HasIcon, HasLabel
{
    case Customer = 'customer';
    case Us = 'us';
    case ThirdParty = 'third_party';

    public function getLabel(): string
    {
        return match ($this) {
            self::Customer => 'Stranka',
            self::Us => 'Mi',
            self::ThirdParty => 'Tretja oseba',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Customer => 'warning',
            self::Us => 'danger',
            self::ThirdParty => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Customer => 'heroicon-o-user',
            self::Us => 'heroicon-o-building-office',
            self::ThirdParty => 'heroicon-o-users',
        };
    }
}
