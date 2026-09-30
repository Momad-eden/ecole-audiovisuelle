<?php

namespace App\Models\Concerns;

use App\Enums\TranslationStatus;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Champs traduits d'une fiche (anglais) : le français reste dans les colonnes d'origine.
 * Le modèle déclare `protected array $translatable`.
 */
trait HasTranslations
{
    public static function bootHasTranslations(): void
    {
        static::deleted(function ($model) {
            // Suppression logique : les traductions restent pour une restauration éventuelle.
            if (! method_exists($model, 'isForceDeleting') || $model->isForceDeleting()) {
                $model->translations()->delete();
            }
        });
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function translatableFields(): array
    {
        return $this->translatable ?? [];
    }

    public function translation(string $field, string $locale = 'en'): ?Translation
    {
        return $this->translations->first(fn (Translation $t) => $t->field === $field && $t->locale === $locale);
    }

    /** Valeur française d'un champ (pour Page, `blocks` = blocs publiés). */
    protected function frenchValue(string $field): mixed
    {
        return $this->getAttribute($field);
    }

    protected function isStructuredField(string $field): bool
    {
        $cast = $this->getCasts()[$field] ?? null;

        return in_array($cast, ['array', 'json', 'object', 'collection'], true);
    }

    public function translated(string $field, string $locale): mixed
    {
        $french = $this->frenchValue($field);
        if ($locale === 'fr') {
            return $french;
        }

        $row = $this->translation($field, $locale);
        if (! $row || $row->value === null) {
            return $french;
        }

        if (! $this->isStructuredField($field)) {
            return $row->value;
        }

        // JSON invalide ou qui n'est pas un tableau : repli sur le français.
        try {
            $decoded = json_decode($row->value, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return $french;
        }

        return is_array($decoded) ? $decoded : $french;
    }

    public function sourceHash(string $field): string
    {
        $value = $this->frenchValue($field);

        if (is_array($value)) {
            $value = json_encode(self::sortKeys($value), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return hash('sha256', trim((string) ($value ?? '')));
    }

    /** Trie récursivement les clés des objets (MySQL json réordonne les clés) ; les listes gardent leur ordre. */
    private static function sortKeys(array $value): array
    {
        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = self::sortKeys($item);
            }
        }
        if (! array_is_list($value)) {
            ksort($value);
        }

        return $value;
    }

    protected function hasFrenchContent(string $field): bool
    {
        $value = $this->frenchValue($field);

        if (is_array($value)) {
            return $value !== [];
        }

        return trim((string) ($value ?? '')) !== '';
    }

    /** Champs au français non vide sans traduction à jour (absente, empreinte différente ou échec). */
    public function outdatedFields(string $locale = 'en'): array
    {
        $outdated = [];

        foreach ($this->translatableFields() as $field) {
            if (! $this->hasFrenchContent($field)) {
                continue;
            }

            $row = $this->translation($field, $locale);
            if (! $row || $row->source_hash !== $this->sourceHash($field) || $row->status === TranslationStatus::FAILED) {
                $outdated[] = $field;
            }
        }

        return $outdated;
    }
}
