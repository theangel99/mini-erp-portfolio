<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MilestoneStatus: string implements HasColor, HasIcon, HasLabel
{
    case Upcoming = 'upcoming';
    case InProgress = 'in_progress';
    case Waiting = 'waiting';
    case Done = 'done';
    case Skipped = 'skipped';

    public function getLabel(): string
    {
        return match ($this) {
            self::Upcoming => 'Prihaja',
            self::InProgress => 'V teku',
            self::Waiting => 'Čakamo',
            self::Done => 'Zaključeno',
            self::Skipped => 'Preskočeno',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Upcoming => 'gray',
            self::InProgress => 'primary',
            self::Waiting => 'warning',
            self::Done => 'success',
            self::Skipped => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Upcoming => 'heroicon-o-clock',
            self::InProgress => 'heroicon-o-arrow-path',
            self::Waiting => 'heroicon-o-pause-circle',
            self::Done => 'heroicon-o-check-circle',
            self::Skipped => 'heroicon-o-forward',
        };
    }
}
