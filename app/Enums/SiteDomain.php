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

    /** Libellé dans la langue demandée (API publique) ; noms propres identiques dans les deux langues. */
    public function labelFor(string $locale): string
    {
        if ($locale !== 'en') {
            return $this->label();
        }

        return match ($this) {
            self::GENERAL => 'General',
            self::MAISON => 'Maison Habib Faye',
            self::EMSI => 'EMSI',
            self::STUDIO => 'Impact Live Studio',
        };
    }

    /** Domaine déduit de l'adresse d'une page (maison-habib-faye/studio…, maison-habib-faye…, emsi…), sinon null. */
    public static function forPath(?string $path): ?self
    {
        $path = trim((string) $path, '/');
        $under = fn (string $prefix) => $path === $prefix || str_starts_with($path, $prefix.'/');

        return match (true) {
            $under('maison-habib-faye/studio') => self::STUDIO,
            $under('maison-habib-faye') => self::MAISON,
            $under('emsi') => self::EMSI,
            default => null,
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
