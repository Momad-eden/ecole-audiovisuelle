<?php

namespace App\Support\Translation;

use App\Enums\TranslationStatus;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Model;

/**
 * État par texte d'un champ structuré (blocs, listes, SEO ; spec R2 §3.3), partagé par la tâche de
 * traduction et l'admin.
 *
 *  - `value` garde son format (carte clé → anglais pour les blocs, liste complète, SEO complet) : l'API ne change pas ;
 *  - `leaves` = `{clé: {h: empreinte du texte français traduit, s: auto|reviewed|failed}}` ; une clé absente
 *    n'a pas de texte anglais ;
 *  - `previous_value` = `{clé: ancienne version relue}` ;
 *  - statut de la ligne = agrégat (échec si un texte a échoué, sinon automatique si un texte l'est, sinon relue).
 *
 * Un texte est « à traduire » s'il n'a pas d'état, si son empreinte diffère du français actuel ou s'il a échoué.
 */
final class TranslationLeaves
{
    /** @return array<string, array{h: ?string, s: string}> */
    public static function states(Model $record, string $field, ?Translation $row): array
    {
        if (! $row) {
            return [];
        }

        if (is_array($row->leaves)) {
            $states = [];
            foreach ($row->leaves as $key => $state) {
                if (is_array($state) && is_string($state['s'] ?? null)) {
                    $states[(string) $key] = ['h' => is_string($state['h'] ?? null) ? $state['h'] : null, 's' => $state['s']];
                }
            }

            return $states;
        }

        // Ligne antérieure à l'état par texte : chaque texte anglais présent hérite du statut de la ligne.
        $decoded = self::decode($row->value);
        $current = $row->source_hash === $record->sourceHash($field);
        $states = [];
        foreach ($record->frenchLeaves($field) as $key => $text) {
            if (is_string($decoded[$key] ?? null)) {
                $states[$key] = ['h' => $current ? $record::leafHash($text) : null, 's' => $row->status->value];
            }
        }

        return $states;
    }

    /** @return array<string, string> textes anglais par clé (seulement les clés qui ont un état) */
    public static function english(Model $record, string $field, ?Translation $row): array
    {
        $decoded = self::decode($row?->value);
        $english = [];
        foreach (array_keys(self::states($record, $field, $row)) as $key) {
            if (is_string($decoded[$key] ?? null)) {
                $english[$key] = $decoded[$key];
            }
        }

        return $english;
    }

    /** @return array<string, string> */
    public static function previous(?Translation $row): array
    {
        $previous = [];
        foreach (self::decode($row?->previous_value) as $key => $text) {
            if (is_string($text)) {
                $previous[(string) $key] = $text;
            }
        }

        return $previous;
    }

    /** @return array<int, string> clés dont le français n'a pas de traduction à jour */
    public static function pending(Model $record, string $field, array $states, array $english = []): array
    {
        $known = self::byHash($english, $states);
        $pending = [];
        foreach ($record->frenchLeaves($field) as $key => $text) {
            $state = $states[$key] ?? null;
            $hash = $record::leafHash($text);
            if (($state['h'] ?? null) !== $hash && isset($known[$hash])) {
                continue; // texte déplacé (même français, autre clé) : sa traduction est reprise, rien à envoyer
            }
            if (! $state || $state['h'] !== $hash || $state['s'] === TranslationStatus::FAILED->value) {
                $pending[] = $key;
            }
        }

        return $pending;
    }

    /**
     * Anglais existant indexé par l'empreinte du français traduit : retrouve un texte déplacé (bloc inséré ou
     * réordonné, élément de liste déplacé). Une version relue l'emporte sur une version automatique.
     *
     * @param  array<string, string>  $english
     * @param  array<string, array{h: ?string, s: string}>  $states
     * @return array<string, array{text: string, s: string}>
     */
    public static function byHash(array $english, array $states): array
    {
        $out = [];
        foreach ($states as $key => $state) {
            if ($state['h'] === null || $state['s'] === TranslationStatus::FAILED->value || ! isset($english[$key])) {
                continue;
            }
            if (! isset($out[$state['h']]) || $state['s'] === TranslationStatus::REVIEWED->value) {
                $out[$state['h']] = ['text' => $english[$key], 's' => $state['s']];
            }
        }

        return $out;
    }

    /**
     * Anglais à montrer, par clé : seulement quand il traduit le français actuel à cet endroit (empreinte égale),
     * ou quand le même français a déjà une traduction sous une autre clé (texte déplacé). Jamais d'anglais
     * périmé ni sous le mauvais bloc : les autres textes restent en français.
     *
     * @return array<string, string>
     */
    public static function current(Model $record, string $field, ?Translation $row): array
    {
        $states = self::states($record, $field, $row);
        $english = self::english($record, $field, $row);
        $known = self::byHash($english, $states);
        $out = [];
        foreach ($record->frenchLeaves($field) as $key => $text) {
            $hash = $record::leafHash($text);
            if (($states[$key]['h'] ?? null) === $hash && ($states[$key]['s'] ?? null) !== TranslationStatus::FAILED->value && isset($english[$key])) {
                $out[$key] = $english[$key];
            } elseif (isset($known[$hash])) {
                $out[$key] = $known[$hash]['text'];
            }
        }

        return $out;
    }

    /** Clés à relire (sans traduction relue sur le français actuel). @return array{0: int, 1: int} [à relire, total] */
    public static function toReview(Model $record, string $field, ?Translation $row): array
    {
        $states = self::states($record, $field, $row);
        $french = $record->frenchLeaves($field);
        $count = 0;
        foreach ($french as $key => $text) {
            $state = $states[$key] ?? null;
            if (! $state || $state['s'] !== TranslationStatus::REVIEWED->value || $state['h'] !== $record::leafHash($text)) {
                $count++;
            }
        }

        return [$count, count($french)];
    }

    public static function aggregate(array $states): TranslationStatus
    {
        $values = array_column($states, 's');

        return match (true) {
            in_array(TranslationStatus::FAILED->value, $values, true) => TranslationStatus::FAILED,
            in_array(TranslationStatus::AUTO->value, $values, true) => TranslationStatus::AUTO,
            default => TranslationStatus::REVIEWED,
        };
    }

    /**
     * Enregistre l'état d'un champ (textes disparus du français retirés). Plus aucun état → ligne supprimée
     * (le site revient au français).
     *
     * @param  array<string, string>  $english
     * @param  array<string, array{h: ?string, s: string}>  $states
     * @param  array<string, string>  $previous
     */
    public static function persist(Model $record, string $field, ?Translation $row, array $english, array $states, array $previous, array $attributes = []): void
    {
        $keys = array_flip(array_keys($record->frenchLeaves($field)));
        $states = array_intersect_key($states, $keys);
        $english = array_intersect_key($english, $states);
        $previous = array_intersect_key($previous, $keys);

        if ($states === []) {
            $row?->delete();

            return;
        }

        $values = [
            'value' => self::encode($record, $field, $english),
            'leaves' => $states,
            'status' => self::aggregate($states),
            'previous_value' => $previous === [] ? null : json_encode($previous, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ...$attributes,
        ];

        $row ? $row->update($values) : $record->translations()->create(['field' => $field, 'locale' => 'en', ...$values]);
    }

    /** Valeur anglaise au format lu par l'API ; null si aucun texte anglais. */
    public static function encode(Model $record, string $field, array $english): ?string
    {
        if ($english === []) {
            return null;
        }

        if ($field === 'blocks') {
            $value = array_intersect_key(array_replace(array_fill_keys(array_keys($record->frenchLeaves($field)), null), $english), $english);
        } else {
            $value = $record->frenchValue($field);
            $value = is_array($value) ? $value : [];
            foreach ($english as $key => $text) {
                $value[$key] = $text;
            }
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private static function decode(?string $json): array
    {
        if ($json === null) {
            return [];
        }
        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }
}
