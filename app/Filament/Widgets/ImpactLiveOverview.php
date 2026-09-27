<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Filament\Resources\BookingRequests\BookingRequestResource;
use App\Models\AgendaEvent;
use App\Models\BookingRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** Impact Live en un coup d'œil : demandes à traiter et prochains événements. */
class ImpactLiveOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Impact Live';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(UserRole::DIRECTEUR, UserRole::COMMERCIAL) ?? false;
    }

    protected function getStats(): array
    {
        $next = AgendaEvent::published()->upcoming()->orderBy('starts_at')->first();

        return [
            Stat::make('Nouvelles demandes', BookingRequest::where('status', BookingStatus::NEW)->count())
                ->description('À rappeler rapidement')->color('warning')->url(BookingRequestResource::getUrl('index')),
            Stat::make('Devis en attente de réponse', BookingRequest::where('status', BookingStatus::QUOTED)->count())->color('info'),
            Stat::make('Prestations confirmées', BookingRequest::where('status', BookingStatus::CONFIRMED)->count())->color('success'),
            Stat::make('Prochain événement', $next?->starts_at?->format('d/m') ?? '—')->description($next?->title ?? 'Aucun événement à venir'),
        ];
    }
}
