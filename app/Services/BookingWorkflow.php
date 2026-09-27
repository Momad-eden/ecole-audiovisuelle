<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Exceptions\BusinessRuleException;
use App\Models\BookingRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Traitement d'une demande : devis envoyé → confirmée → réalisée (ou annulée), avec historique. */
class BookingWorkflow
{
    public function transition(BookingRequest $request, BookingStatus $to, ?User $user, ?string $comment = null): void
    {
        if (! $request->status->canTransitionTo($to)) {
            throw new BusinessRuleException("Une demande « {$request->status->getLabel()} » ne peut pas passer à « {$to->getLabel()} ».");
        }

        DB::transaction(function () use ($request, $to, $user, $comment) {
            $from = $request->status;
            $request->update(['status' => $to]);
            $request->logs()->create(['user_id' => $user?->id, 'from_status' => $from->value, 'to_status' => $to->value, 'comment' => $comment]);
        });
    }

    public function addNote(BookingRequest $request, User $user, string $comment): void
    {
        $request->logs()->create(['user_id' => $user->id, 'to_status' => $request->status->value, 'comment' => $comment]);
    }
}
