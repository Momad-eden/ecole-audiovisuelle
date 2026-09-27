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
