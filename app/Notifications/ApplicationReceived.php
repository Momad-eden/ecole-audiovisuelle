<?php

namespace App\Notifications;

use App\Models\Application;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Accusé de réception envoyé au candidat. */
class ApplicationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $settings = Setting::current();

        return (new MailMessage)
            ->subject("Votre candidature {$this->application->reference} a bien été reçue")
            ->greeting("Bonjour {$this->application->first_name},")
            ->line('Nous avons bien reçu votre candidature. Notre équipe va l\'étudier et reviendra vers vous pour la suite (entretien ou demande de pièces complémentaires).')
            ->line("Numéro de dossier : **{$this->application->reference}** — à rappeler dans vos échanges avec l'école.")
            ->when($settings->phone, fn (MailMessage $m) => $m->line("Une question ? Contactez-nous au {$settings->phone}."))
            ->salutation("L'équipe de {$settings->school_name}");
    }
}
