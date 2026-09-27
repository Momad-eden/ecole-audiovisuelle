<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\Enrollment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SchoolOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(UserRole::DIRECTEUR, UserRole::GESTIONNAIRE, UserRole::SECRETAIRE) ?? false;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Candidatures à traiter', Application::whereIn('status', ApplicationStatus::open())->count())
                ->description(Application::where('submitted_at', '>=', now()->subDays(7))->count().' reçues ces 7 derniers jours')
                ->color('warning'),
            Stat::make('Entretiens à venir', Application::where('status', ApplicationStatus::INTERVIEW_SCHEDULED)->where('interview_at', '>=', now())->count()),
            Stat::make('Étudiants inscrits', Enrollment::where('status', EnrollmentStatus::ENROLLED)->count())->color('success'),
        ];
    }
}
