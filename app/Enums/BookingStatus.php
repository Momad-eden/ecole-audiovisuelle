<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Suivi d'une demande de devis ou de réservation. */
enum BookingStatus: string implements HasColor, HasLabel
{
    case NEW = 'new';
    case QUOTED = 'quoted';
    case CONFIRMED = 'confirmed';
    case DONE = 'done';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::NEW => 'Nouvelle',
            self::QUOTED => 'Devis envoyé',
            self::CONFIRMED => 'Confirmée',
            self::DONE => 'Réalisée',
            self::CANCELLED => 'Annulée',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NEW => 'warning',
            self::QUOTED => 'info',
            self::CONFIRMED => 'primary',
            self::DONE => 'success',
            self::CANCELLED => 'gray',
        };
    }

    /** @return array<int, self> */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::NEW => [self::QUOTED, self::CONFIRMED, self::CANCELLED],
            self::QUOTED => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED => [self::DONE, self::CANCELLED],
            self::DONE, self::CANCELLED => [],
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status, $this->allowedTransitions(), true);
    }

    /** @return array<int, self> Demandes encore à traiter. */
    public static function open(): array
    {
        return [self::NEW, self::QUOTED, self::CONFIRMED];
    }
}
