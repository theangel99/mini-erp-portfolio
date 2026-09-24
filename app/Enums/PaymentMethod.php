<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasIcon;

enum PaymentMethod: string implements HasLabel, HasIcon
{
    case BankTransfer = 'nakazilo';
    case Cash = 'gotovina';
    case Card = 'kartica';

    public function getLabel(): string
    {
        return match ($this) {
            self::BankTransfer => 'Bančno nakazilo',
            self::Cash => 'Gotovina',
            self::Card => 'Kartica',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::BankTransfer => 'heroicon-o-building-library',
            self::Cash => 'heroicon-o-banknotes',
            self::Card => 'heroicon-o-credit-card',
        };
    }
}
