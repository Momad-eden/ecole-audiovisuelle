<?php

namespace App\Support;

/**
 * « Maison Habib Faye » devient « Centre culturel Habib Faye » (adresses /centre-culturel/…), et le site
 * ne parle plus d'« héritage de Habib Faye » : le lieu porte son nom, sans plus. Règles appliquées une fois
 * au contenu enregistré (migration du 01/10/2026). La clé interne du domaine (« maison ») ne change pas.
 */
final class CentreCulturelRename
{
    /** Remplacements exacts, dans l'ordre (les phrases longues d'abord). */
    public const TEXT = [
        'Un centre culturel à Saint-Louis : concerts, résidences et transmission, dans la maison de Habib Faye.' => 'Un centre culturel à Saint-Louis : concerts, résidences, ateliers et transmission.',
        'Concerts, résidences et transmission, dans la maison de Habib Faye.' => 'Concerts, résidences et transmission, à Saint-Louis.',
        'Le lieu, son histoire et l\'héritage de Habib Faye' => 'Le lieu, son projet et ses activités',
        'trois lieux réunis autour de l\'héritage de Habib Faye.' => 'trois lieux, un même projet.',
        ', réunis dans l\'héritage de Habib Faye.' => ', réunis.',
        'Maison de la culture Habib Faye' => 'Centre culturel Habib Faye',
        'Maison Habib Faye' => 'Centre culturel Habib Faye',
        'Découvrir la Maison' => 'Découvrir le Centre culturel',
        'de la Maison' => 'du Centre culturel',
        'à la Maison' => 'au Centre culturel',
        'la Maison' => 'le Centre culturel',
        'La Maison' => 'Le Centre culturel',
        'Nos trois maisons' => 'Nos trois lieux',
        'trois maisons' => 'trois lieux',
    ];

    public static function text(string $value): string
    {
        $value = strtr($value, self::TEXT);

        return match (true) {
            str_starts_with($value, '/maison-habib-faye') => LegacyPaths::rewrite($value),
            str_contains($value, 'maison-habib-faye') && str_contains($value, 'href') => LegacyPaths::rewriteHtml($value),
            default => $value,
        };
    }

    /** Toutes les chaînes d'une valeur (blocs, SEO…), clés comprises intactes. */
    public static function deep(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => self::deep($item), $value);
        }

        return is_string($value) ? self::text($value) : $value;
    }

    /** Adresse d'une page : maison-habib-faye/… devient centre-culturel/…. */
    public static function slug(string $slug): string
    {
        return preg_replace('~^maison-habib-faye(?=/|$)~', 'centre-culturel', $slug);
    }
}
