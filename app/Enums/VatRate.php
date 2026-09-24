<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum VatRate: string implements HasLabel
{
    case Rate22 = '22';
    case Rate9_5 = '9.5';
    case Rate5 = '5';
    case Rate0 = '0';

    public function getLabel(): string
    {
        return match ($this) {
            self::Rate22 => '22 %',
            self::Rate9_5 => '9,5 %',
            self::Rate5 => '5 %',
            self::Rate0 => '0 %',
        };
    }

    public function getValue(): float
    {
        return (float) $this->value;
    }

    public function getDecimal(): float
    {
        return $this->getValue() / 100;
    }
}
