<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PublishingPhase: string implements HasColor, HasIcon, HasLabel
{
    case Editing = 'editing';
    case ChiefEditorReview = 'chief_editor_review';
    case Multimedia = 'multimedia';
    case Print = 'print';
    case DirectorApproval = 'director_approval';
    case Sales = 'sales';
    case Completed = 'completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Editing => 'Urejanje',
            self::ChiefEditorReview => 'Pregled glavnega urednika',
            self::Multimedia => 'Multimedijska obdelava',
            self::Print => 'Tisk',
            self::DirectorApproval => 'Potrditev direktorja',
            self::Sales => 'Prodaja',
            self::Completed => 'Zaključeno',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Editing => 'info',
            self::ChiefEditorReview => 'warning',
            self::Multimedia => 'primary',
            self::Print => 'purple',
            self::DirectorApproval => 'orange',
            self::Sales => 'success',
            self::Completed => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Editing => 'heroicon-o-pencil',
            self::ChiefEditorReview => 'heroicon-o-clipboard-document-check',
            self::Multimedia => 'heroicon-o-photo',
            self::Print => 'heroicon-o-printer',
            self::DirectorApproval => 'heroicon-o-shield-check',
            self::Sales => 'heroicon-o-currency-dollar',
            self::Completed => 'heroicon-o-check-badge',
        };
    }

    public function getNextPhase(): ?self
    {
        return match ($this) {
            self::Editing => self::ChiefEditorReview,
            self::ChiefEditorReview => self::Multimedia,
            self::Multimedia => self::Print,
            self::Print => self::DirectorApproval,
            self::DirectorApproval => self::Sales,
            self::Sales => self::Completed,
            self::Completed => null,
        };
    }
}
