<?php

namespace App\Filament\Resources\CashClosings;

use App\Filament\Resources\CashClosings\Pages\ListCashClosings;
use App\Filament\Support\FrenchLabels;
use App\Models\CashClosing;
use App\Support\Money;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CashClosingResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = CashClosing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;

    protected static string|\UnitEnum|null $navigationGroup = 'Caisse';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'clôture';

    protected static ?string $pluralModelLabel = 'clôtures de caisse';

    public static function table(Table $table): Table
    {
        $money = fn ($state) => $state === null ? '—' : Money::fcfa($state);

        return $table
            ->defaultSort('period_end', 'desc')
            ->columns([
                TextColumn::make('period_start')->label('Du')->date('d/m/Y'),
                TextColumn::make('period_end')->label('Au')->date('d/m/Y'),
                TextColumn::make('opening_balance')->label('Solde d\'ouverture')->formatStateUsing($money),
                TextColumn::make('total_in')->label('Entrées')->formatStateUsing($money)->color('success'),
                TextColumn::make('total_out')->label('Sorties')->formatStateUsing($money)->color('danger'),
                TextColumn::make('closing_balance')->label('Solde de clôture')->formatStateUsing($money)->weight('bold'),
                TextColumn::make('counted_cash')->label('Espèces comptées')->formatStateUsing($money),
                TextColumn::make('difference')->label('Écart')->state(fn (CashClosing $r) => $r->difference())
                    ->formatStateUsing($money)->color(fn ($state) => $state ? 'danger' : null),
                TextColumn::make('closer.name')->label('Clôturée par'),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListCashClosings::route('/')];
    }
}
