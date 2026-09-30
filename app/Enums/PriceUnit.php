<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** Unité d'un prix « à partir de » (affichée après le montant : « / jour »). */
enum PriceUnit: string implements HasLabel
{
    case HOUR = 'hour';
    case SESSION = 'session';
    case TRACK = 'track';
    case DAY = 'day';
    case EVENT = 'event';

    public function getLabel(): string
    {
        return match ($this) {
            self::HOUR => 'par heure',
            self::SESSION => 'par session',
            self::TRACK => 'par titre',
            self::DAY => 'par jour',
            self::EVENT => 'par événement',
        };
    }

    /** Libellé dans la langue de l'API publique (`en` : anglais) ; en français, identique à getLabel() (admin). */
    public function labelFor(string $locale): string
    {
        if ($locale !== 'en') {
            return $this->getLabel();
        }

        return match ($this) {
            self::HOUR => 'per hour',
            self::SESSION => 'per session',
            self::TRACK => 'per track',
            self::DAY => 'per day',
            self::EVENT => 'per event',
        };
    }

    public function suffix(): string
    {
        return match ($this) {
            self::HOUR => 'heure',
            self::SESSION => 'session',
            self::TRACK => 'titre',
            self::DAY => 'jour',
            self::EVENT => 'événement',
        };
    }
}
