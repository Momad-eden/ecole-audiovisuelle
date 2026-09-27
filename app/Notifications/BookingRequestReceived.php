<?php

namespace App\Notifications;

use App\Models\BookingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Nouvelle demande : alerte pour l'équipe et accusé de réception pour le client. */
class BookingRequestReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public BookingRequest $request, public bool $forTeam = false) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $r = $this->request;

        if ($this->forTeam) {
            return (new MailMessage)
                ->subject("Nouvelle demande {$r->reference} — {$r->type->getLabel()}")
                ->line("{$r->name}".($r->organization ? " ({$r->organization})" : '')." — {$r->phone}")
                ->line($r->message ?: 'Aucun message.')
                ->action('Ouvrir la demande', url('/admin/booking-requests'));
        }

        return (new MailMessage)
            ->subject("Votre demande {$r->reference} a bien été reçue")
            ->greeting("Bonjour {$r->name},")
            ->line("Nous avons bien reçu votre demande ({$r->type->getLabel()}). Notre équipe vous recontacte rapidement avec une proposition.")
            ->line("Référence à rappeler dans nos échanges : {$r->reference}.");
    }

    public static function team(BookingRequest $request): self
    {
        return new self($request, forTeam: true);
    }
}
