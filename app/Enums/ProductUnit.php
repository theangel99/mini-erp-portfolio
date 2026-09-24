<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProductUnit: string implements HasLabel
{
    case Piece = 'kos';
    case Hour = 'ura';
    case Day = 'dan';
    case SquareMeter = 'm2';
    case Kilogram = 'kg';
    case Lumpsum = 'pavsal';

    public function getLabel(): string
    {
        return match ($this) {
            self::Piece => 'kos',
            self::Hour => 'ura',
            self::Day => 'dan',
            self::SquareMeter => 'm²',
            self::Kilogram => 'kg',
            self::Lumpsum => 'pavšal',
        };
    }
}
