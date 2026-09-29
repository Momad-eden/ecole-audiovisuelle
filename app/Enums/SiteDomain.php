<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/** Domaine du site auquel une page appartient (les trois activités + le tronc commun). */
enum SiteDomain: string implements HasLabel
{
    case GENERAL = 'general';
    case MAISON = 'maison';
    case EMSI = 'emsi';
    case STUDIO = 'studio';

    public function label(): string
    {
        return match ($this) {
            self::GENERAL => 'Général',
            self::MAISON => 'Maison Habib Faye',
            self::EMSI => 'EMSI',
            self::STUDIO => 'Impact Live Studio',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }

    public function color(): string
    {
        return match ($this) {
            self::GENERAL => '#ff7a1a',
            self::MAISON => '#e0a84a',
            self::EMSI => '#ff7a1a',
            self::STUDIO => '#ff3b30',
        };
    }
}
