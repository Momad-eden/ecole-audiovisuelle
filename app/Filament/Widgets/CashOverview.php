<?php

namespace App\Filament\Widgets;

use App\Enums\CashDirection;
use App\Enums\UserRole;
use App\Models\CashTransaction;
use App\Models\Place;
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
        $user = auth()->user();
        $month = CashTransaction::visibleTo($user)->whereDate('occurred_on', '>=', now()->startOfMonth()->toDateString());
        $in = (int) (clone $month)->where('direction', CashDirection::IN)->sum('amount');
        $out = (int) (clone $month)->where('direction', CashDirection::OUT)->sum('amount');

        // Une caisse par campus : le personnel d'un campus voit la sienne, la direction voit chacune.
        $campuses = $user?->place_id ? Place::whereKey($user->place_id)->get() : Place::campuses()->orderBy('position')->get();
        $balances = $campuses->isEmpty()
            ? [Stat::make('Solde de caisse', Money::fcfa($total = app(CashRegister::class)->balance()))->color($total < 0 ? 'danger' : 'success')]
            : $campuses->map(function (Place $place) {
                $balance = app(CashRegister::class)->balance($place);

                return Stat::make('Caisse '.($place->city ?? $place->name), Money::fcfa($balance))
                    ->description('Solde, toutes opérations confondues')
                    ->color($balance < 0 ? 'danger' : 'success');
            })->all();

        return [
            ...$balances,
            Stat::make('Encaissé ce mois-ci', Money::fcfa($in))->color('success'),
            Stat::make('Dépensé ce mois-ci', Money::fcfa($out))->color('danger'),
        ];
    }
}
