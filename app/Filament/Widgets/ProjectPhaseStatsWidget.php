<?php

namespace App\Filament\Widgets;

use App\Enums\PublishingPhase;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectPhaseStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Urejanje', Project::where('current_phase', PublishingPhase::Editing)->count())
                ->description('Projekti v urejanju')
                ->descriptionIcon('heroicon-o-pencil')
                ->color('info'),

            Stat::make('Pregled glavnega urednika', Project::where('current_phase', PublishingPhase::ChiefEditorReview)->count())
                ->description('Čakajo na pregled')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color('warning'),

            Stat::make('Multimedijska obdelava', Project::where('current_phase', PublishingPhase::Multimedia)->count())
                ->description('V multimedijski obdelavi')
                ->descriptionIcon('heroicon-o-photo')
                ->color('primary'),

            Stat::make('Tisk', Project::where('current_phase', PublishingPhase::Print)->count())
                ->description('V tisku')
                ->descriptionIcon('heroicon-o-printer')
                ->color('purple'),

            Stat::make('Potrditev direktorja', Project::where('current_phase', PublishingPhase::DirectorApproval)->count())
                ->description('Čakajo na potrditev')
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('orange'),

            Stat::make('Prodaja', Project::where('current_phase', PublishingPhase::Sales)->count())
                ->description('V prodaji')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Zaključeno', Project::where('current_phase', PublishingPhase::Completed)->count())
                ->description('Zaključeni projekti')
                ->descriptionIcon('heroicon-o-check-badge')
                ->color('success'),
        ];
    }
}
