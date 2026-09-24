<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum InvoiceStatus: string implements HasLabel, HasColor, HasIcon
{
    case Issued = 'issued';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Issued => 'Izdan',
            self::PartiallyPaid => 'Delno plačan',
            self::Paid => 'Plačan',
            self::Overdue => 'Zapadel',
            self::Cancelled => 'Storniran',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Issued => 'info',
            self::PartiallyPaid => 'warning',
            self::Paid => 'success',
            self::Overdue => 'danger',
            self::Cancelled => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Issued => 'heroicon-o-document',
            self::PartiallyPaid => 'heroicon-o-clock',
            self::Paid => 'heroicon-o-check-circle',
            self::Overdue => 'heroicon-o-exclamation-circle',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }
}
