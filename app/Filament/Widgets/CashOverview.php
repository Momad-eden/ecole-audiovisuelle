<?php

namespace App\Filament\Widgets;

use App\Enums\CashDirection;
use App\Enums\UserRole;
use App\Models\CashTransaction;
use App\Services\CashRegister;
use App\Support\Money;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CashOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(UserRole::DIRECTEUR, UserRole::GESTIONNAIRE) ?? false;
    }

    protected function getStats(): array
    {
        $month = CashTransaction::whereBetween('occurred_on', [now()->startOfMonth()->toDateString(), now()->toDateString()]);
        $in = (int) (clone $month)->where('direction', CashDirection::IN)->sum('amount');
        $out = (int) (clone $month)->where('direction', CashDirection::OUT)->sum('amount');
        $balance = app(CashRegister::class)->balance();

        return [
            Stat::make('Solde de caisse', Money::fcfa($balance))
                ->description('Toutes opérations confondues')
                ->color($balance < 0 ? 'danger' : 'success'),
            Stat::make('Encaissé ce mois-ci', Money::fcfa($in))->color('success'),
            Stat::make('Dépensé ce mois-ci', Money::fcfa($out))->color('danger'),
        ];
    }
}
