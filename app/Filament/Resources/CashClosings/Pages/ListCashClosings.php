<?php

namespace App\Filament\Resources\CashClosings\Pages;

use App\Exceptions\BusinessRuleException;
use App\Filament\Resources\CashClosings\CashClosingResource;
use App\Models\CashClosing;
use App\Services\CashRegister;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;

class ListCashClosings extends ListRecords
{
    protected static string $resource = CashClosingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('close')->label('Clôturer une période')->icon('heroicon-o-lock-closed')
                ->visible(fn () => auth()->user()->can('create', CashClosing::class))
                ->modalDescription('Après clôture, plus aucune opération ne pourra être enregistrée ou annulée à une date de la période.')
                ->schema([
                    DatePicker::make('period_end')->label('Clôturer jusqu\'au (inclus)')->default(now()->subMonthNoOverflow()->endOfMonth())->maxDate(now())->required(),
                    TextInput::make('counted_cash')->label('Espèces comptées dans la caisse (facultatif)')->integer()->suffix('FCFA'),
                    Textarea::make('notes')->label('Observations')->rows(2),
                ])
                ->action(function (array $data) {
                    try {
                        $closing = app(CashRegister::class)->close(Carbon::parse($data['period_end']), isset($data['counted_cash']) ? (int) $data['counted_cash'] : null, auth()->user(), $data['notes'] ?? null);
                        Notification::make()->title('Période clôturée au '.$closing->period_end->format('d/m/Y').'.')->success()->send();
                    } catch (BusinessRuleException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
        ];
    }
}
