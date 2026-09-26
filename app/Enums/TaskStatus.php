<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TaskStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Blocked = 'blocked';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Na čakanju',
            self::InProgress => 'V delu',
            self::Blocked => 'Blokirano',
            self::Completed => 'Zaključeno',
            self::Cancelled => 'Preklicano',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::InProgress => 'info',
            self::Blocked => 'warning',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::InProgress => 'heroicon-o-arrow-path',
            self::Blocked => 'heroicon-o-exclamation-triangle',
            self::Completed => 'heroicon-o-check-circle',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }
}
