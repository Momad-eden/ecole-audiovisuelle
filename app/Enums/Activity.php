<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** Les activités de l'ensemble fondé par Boubacar Tall. */
enum Activity: string implements HasLabel
{
    case SCHOOL = 'school';
    case STUDIO = 'studio';
    case EVENTS = 'events';
    case SPACE = 'space';

    public function getLabel(): string
    {
        return match ($this) {
            self::SCHOOL => 'EMSI',
            self::STUDIO => 'Impact Live Studio',
            self::EVENTS => 'Impact Live Events',
            self::SPACE => 'Espace Habib Faye',
        };
    }

    /** Libellé dans la langue de l'API publique (`en` : anglais) ; en français, identique à getLabel() (admin). */
    public function labelFor(string $locale): string
    {
        if ($locale !== 'en') {
            return $this->getLabel();
        }

        return match ($this) {
            self::SCHOOL => 'EMSI',
            self::STUDIO => 'Impact Live Studio',
            self::EVENTS => 'Impact Live Events',
            self::SPACE => 'Espace Habib Faye',
        };
    }
}
