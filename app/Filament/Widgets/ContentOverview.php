<?php

namespace App\Filament\Widgets;

use App\Enums\ContactMessageStatus;
use App\Enums\PublicationStatus;
use App\Enums\UserRole;
use App\Models\Artwork;
use App\Models\ContactMessage;
use App\Models\Page;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(UserRole::DIRECTEUR, UserRole::COMMUNICATION) ?? false;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Œuvres publiées', Artwork::published()->count())
                ->description(Artwork::where('status', PublicationStatus::DRAFT)->count().' en brouillon'),
            Stat::make('Pages avec modifications non publiées', Page::all()->filter->hasUnpublishedChanges()->count())->color('warning'),
            Stat::make('Messages non traités', ContactMessage::where('status', ContactMessageStatus::NEW)->count())->color('info'),
        ];
    }
}
