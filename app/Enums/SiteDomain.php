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
            self::MAISON => 'Centre culturel Habib Faye',
            self::EMSI => 'EMSI',
            self::STUDIO => 'Impact Live Studio',
        };
    }

    /** Libellé dans la langue demandée (API publique) ; noms propres identiques dans les deux langues, sauf le Centre culturel. */
    public function labelFor(string $locale): string
    {
        if ($locale !== 'en') {
            return $this->label();
        }

        return match ($this) {
            self::GENERAL => 'General',
            self::MAISON => 'Habib Faye Cultural Centre',
            self::EMSI => 'EMSI',
            self::STUDIO => 'Impact Live Studio',
        };
    }

    /** Domaine déduit de l'adresse d'une page (centre-culturel/studio…, centre-culturel…, emsi…), sinon null. */
    public static function forPath(?string $path): ?self
    {
        $path = trim((string) $path, '/');
        $under = fn (string $prefix) => $path === $prefix || str_starts_with($path, $prefix.'/');

        return match (true) {
            $under('centre-culturel/studio') => self::STUDIO,
            $under('centre-culturel') => self::MAISON,
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
