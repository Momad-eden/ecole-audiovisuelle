<?php

namespace App\Support;

/**
 * Anciennes adresses du site EMSI → adresses du site des trois domaines. Mêmes règles que les
 * redirections 301 de frontend/next.config.ts, pour réécrire les liens enregistrés en base
 * (blocs des pages, menus, redirections de l'admin) sans passer par une redirection.
 *
 * Seules les adresses internes (« /… ») sont concernées. La requête et l'ancre sont gardées quand la
 * page cible est la même page déplacée (/studio#reserver → /maison-habib-faye/studio#reserver) ;
 * elles sont abandonnées quand plusieurs anciennes pages mènent à une seule (/events#devis → /maison-habib-faye).
 */
final class LegacyPaths
{
    /** Anciennes salles du « musée » devenues des univers. */
    private const ROOMS = ['salle-du-son' => 'son', 'salle-de-la-lumiere' => 'scene', 'salle-de-limage' => 'image', 'salle-du-visuel' => 'design'];

    public static function rewrite(string $url): string
    {
        if (! preg_match('~^(/[^?#]*)(.*)$~s', $url, $parts)) {
            return $url;
        }
        [, $path, $suffix] = $parts;
        $path = $path === '/' ? $path : rtrim($path, '/');
        $segments = $path === '/' ? [] : explode('/', ltrim($path, '/'));
        $rest = implode('/', array_slice($segments, 1));
        $tail = $rest === '' ? '' : '/'.$rest;

        $target = match ($segments[0] ?? null) {
            'univers' => $rest === '' ? '/emsi' : '/emsi/univers'.$tail,
            'formations', 'realisations', 'professionnels' => '/emsi/'.$segments[0].$tail,
            'studio' => $rest === '' ? '/maison-habib-faye/studio' : null,
            'ecole' => $rest === '' ? '/emsi' : null,
            'espace-habib-faye' => $rest === '' ? '/maison-habib-faye' : null,
            'agenda' => '/maison-habib-faye/agenda'.$tail,
            'events' => ['/maison-habib-faye'],
            'expositions' => ['/emsi/realisations'],
            'demande' => ['/maison-habib-faye/studio#reserver'],   // ancienne demande de devis d'Impact Live Events
            'musee' => match (true) {
                $rest === '' => ['/emsi/realisations'],
                ($segments[1] ?? null) === 'oeuvres' && isset($segments[2]) => '/emsi/realisations/'.$segments[2],
                default => '/emsi/univers/'.(self::ROOMS[$segments[1]] ?? $segments[1]),
            },
            default => null,
        };

        return match (true) {
            $target === null => $url,
            is_array($target) => $target[0],   // plusieurs anciennes pages réunies : ni requête ni ancre
            default => $target.$suffix,
        };
    }

    /** Réécrit les liens (href) d'un texte enrichi. */
    public static function rewriteHtml(string $html): string
    {
        return preg_replace_callback(
            '~(href\s*=\s*)(["\'])(/[^"\']*)\2~i',
            fn (array $m) => $m[1].$m[2].self::rewrite($m[3]).$m[2],
            $html,
        );
    }
}
