<?php

namespace App\Filament\Resources\BookingRequests\Pages;

use App\Enums\BookingStatus;
use App\Exceptions\BusinessRuleException;
use App\Filament\Resources\BookingRequests\BookingRequestResource;
use App\Models\BookingRequest;
use App\Services\BookingWorkflow;
use App\Support\Money;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewBookingRequest extends ViewRecord
{
    protected static string $resource = BookingRequestResource::class;

    public function getTitle(): string
    {
        return "Demande {$this->record->reference} — {$this->record->name}";
    }

    private function run(callable $callback, string $success): void
    {
        try {
            $callback(app(BookingWorkflow::class));
            Notification::make()->title($success)->success()->send();
            $this->record->refresh()->load(['logs' => fn ($q) => $q->latest('id')->with('user')]);
        } catch (BusinessRuleException $e) {
            Notification::make()->title($e->getMessage())->danger()->send();
        }
    }

    private function can(BookingStatus $to): bool
    {
        return $this->record->status->canTransitionTo($to);
    }

    protected function getHeaderActions(): array
    {
        /** @var BookingRequest $record */
        $record = $this->record;

        return [
            Action::make('quote')
                ->label('Devis envoyé')
                ->icon('heroicon-o-document-currency-dollar')
                ->color('info')
                ->visible(fn () => $this->can(BookingStatus::QUOTED))
                ->schema([
                    TextInput::make('amount')->label('Montant du devis (FCFA)')->numeric()->minValue(0)->step(1000)->required(),
                    Textarea::make('comment')->label('Commentaire (facultatif)')->rows(2),
                ])
                ->action(fn (array $data) => $this->run(function (BookingWorkflow $workflow) use ($record, $data) {
                    $record->update(['quoted_amount' => (int) $data['amount']]);
                    $workflow->transition($record, BookingStatus::QUOTED, auth()->user(), trim('Devis de '.Money::fcfa($data['amount']).'. '.($data['comment'] ?? '')));
                }, 'Devis enregistré.')),
            Action::make('confirm')
                ->label('Confirmer')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->can(BookingStatus::CONFIRMED))
                ->requiresConfirmation()
                ->action(fn () => $this->run(fn (BookingWorkflow $w) => $w->transition($record, BookingStatus::CONFIRMED, auth()->user()), 'Demande confirmée.')),
            Action::make('done')
                ->label('Marquer réalisée')
                ->icon('heroicon-o-flag')
                ->color('success')
                ->visible(fn () => $this->can(BookingStatus::DONE))
                ->requiresConfirmation()
                ->action(fn () => $this->run(fn (BookingWorkflow $w) => $w->transition($record, BookingStatus::DONE, auth()->user()), 'Prestation réalisée.')),
            ActionGroup::make([
                Action::make('note')
                    ->label('Ajouter une note')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->schema([Textarea::make('comment')->label('Note (appel, relance…)')->required()->rows(3)])
                    ->action(fn (array $data) => $this->run(fn (BookingWorkflow $w) => $w->addNote($record, auth()->user(), $data['comment']), 'Note ajoutée.')),
                EditAction::make()->label('Corriger la demande'),
                Action::make('cancel')
                    ->label('Annuler la demande')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn () => $this->can(BookingStatus::CANCELLED))
                    ->schema([Textarea::make('comment')->label('Motif')->required()->rows(2)])
                    ->action(fn (array $data) => $this->run(fn (BookingWorkflow $w) => $w->transition($record, BookingStatus::CANCELLED, auth()->user(), $data['comment']), 'Demande annulée.')),
            ]),
        ];
    }
}
