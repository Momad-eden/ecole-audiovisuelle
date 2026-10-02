<?php

namespace App\Support\Translation;

use Illuminate\Database\Eloquent\Model;

/**
 * Lecture des champs traduits dans la langue de la requête (API publique, spec R2 §4).
 * En français, aucune traduction n'est chargée ni lue : les réponses restent identiques à R1.
 */
final class Localized
{
    public static function locale(): string
    {
        return app()->getLocale();
    }

    public static function isFrench(): bool
    {
        return self::locale() === 'fr';
    }

    /** Valeur du champ dans la langue courante, repli sur le français ; null si pas de fiche. */
    public static function value(?Model $model, string $field): mixed
    {
        if ($model === null) {
            return null;
        }

        return method_exists($model, 'translated') ? $model->translated($field, self::locale()) : $model->getAttribute($field);
    }

    /**
     * Relations `translations` à précharger (évite le N+1) hors français : '' pour la fiche elle-même,
     * sinon le chemin de la relation (`room`, `cohort.program`…).
     *
     * @return array<int, string>
     */
    public static function eager(string ...$relations): array
    {
        if (self::isFrench()) {
            return [];
        }

        return array_map(fn (string $relation) => $relation === '' ? 'translations' : "{$relation}.translations", $relations ?: ['']);
    }
}
