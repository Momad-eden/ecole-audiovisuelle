<?php

namespace App\Filament\Resources\CashTransactions\Pages;

use App\Enums\UserRole;
use App\Exceptions\BusinessRuleException;
use App\Filament\Resources\CashTransactions\CashTransactionResource;
use App\Models\CashTransaction;
use App\Services\CashRegister;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCashTransaction extends ViewRecord
{
    protected static string $resource = CashTransactionResource::class;

    public function getTitle(): string
    {
        return "Opération {$this->record->number}";
    }

    protected function getHeaderActions(): array
    {
        /** @var CashTransaction $record */
        $record = $this->record;

        return [
            Action::make('receipt')->label('Imprimer le reçu')->icon('heroicon-o-printer')
                ->url(route('admin.cash.receipt', $record), true),
            Action::make('cancel')->label('Annuler cette opération')->icon('heroicon-o-x-circle')->color('danger')
                ->visible(fn () => ! $record->isCancelled() && $record->reverses_id === null
                    && auth()->user()->hasRole(UserRole::DIRECTEUR, UserRole::GESTIONNAIRE))
                ->modalDescription('Une contre-écriture de même montant sera créée à la date du jour. L\'opération d\'origine reste visible, marquée « annulée ».')
                ->schema([Textarea::make('reason')->label('Motif de l\'annulation')->required()->rows(3)])
                ->action(function (array $data) use ($record) {
                    try {
                        $reversal = app(CashRegister::class)->cancel($record, auth()->user(), $data['reason']);
                        Notification::make()->title("Opération annulée par la contre-écriture {$reversal->number}.")->success()->send();
                        $this->record->refresh();
                    } catch (BusinessRuleException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
        ];
    }
}
