<?php

namespace App\Models\Concerns;

use App\Enums\TranslationStatus;
use App\Jobs\TranslateRecord;
use App\Models\Setting;
use App\Models\Translation;
use App\Services\Translation\Translator;
use App\Support\Translation\BlockTexts;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

/**
 * Champs traduits d'une fiche (anglais) : le français reste dans les colonnes d'origine.
 * Le modèle déclare `protected array $translatable`.
 */
trait HasTranslations
{
    public static function bootHasTranslations(): void
    {
        // Traduction automatique en file, une fois l'enregistrement validé (spec R2 §3.1).
        static::saved(function ($model) {
            if (! $model->shouldQueueTranslation()) {
                return;
            }
            DB::afterCommit(fn () => $model->queueTranslation());
        });

        static::deleted(function ($model) {
            // Suppression logique : les traductions restent pour une restauration éventuelle.
            if (! method_exists($model, 'isForceDeleting') || $model->isForceDeleting()) {
                $model->translations()->delete();
            }
        });
    }

    /** Vrai si l'enregistrement qui vient d'avoir lieu touche un champ traduit (ou crée la fiche). */
    public function shouldQueueTranslation(): bool
    {
        return $this->wasRecentlyCreated || $this->wasChanged($this->translatableFields());
    }

    /** Met en file la traduction anglaise si elle est activée, possible et utile. */
    public function queueTranslation(): void
    {
        if (! app(Translator::class)->isAvailable() || ! Setting::current()->auto_translate) {
            return;
        }

        if ($this->load('translations')->outdatedFields('en') !== []) {
            TranslateRecord::dispatch($this);
        }
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
    public function frenchValue(string $field): mixed
    {
        return $this->getAttribute($field);
    }

    protected function isStructuredField(string $field): bool
    {
        $cast = $this->getCasts()[$field] ?? null;

        return in_array($cast, ['array', 'json', 'object', 'collection'], true);
    }

    /** Champ traduit texte par texte (blocs, listes, SEO) : état par clé dans `translations.leaves`. */
    public function hasStructuredTranslation(string $field): bool
    {
        return $this->isStructuredField($field);
    }

    /**
     * Textes français d'un champ structuré, par clé : clé stable des blocs, rang d'une liste, `title` /
     * `description` du SEO. Textes vides (y compris HTML vide) exclus.
     *
     * @return array<string, string>
     */
    public function frenchLeaves(string $field): array
    {
        $value = $this->frenchValue($field);
        if (! is_array($value)) {
            return [];
        }

        $texts = match (true) {
            $field === 'blocks' => BlockTexts::keyed($value),
            $field === 'seo' => array_intersect_key($value, ['title' => true, 'description' => true]),
            default => $value,
        };

        $out = [];
        foreach ($texts as $key => $text) {
            if (is_string($text) && ! self::isBlankText($text)) {
                $out[(string) $key] = $text;
            }
        }

        return $out;
    }

    /** Empreinte d'un texte (même normalisation que sourceHash). */
    public static function leafHash(string $text): string
    {
        return hash('sha256', trim($text));
    }

    /** Vide, espaces insécables compris, une fois les balises retirées (« <p></p> », « <p>&nbsp;</p> »). */
    public static function isBlankText(?string $text): bool
    {
        $plain = html_entity_decode(strip_tags((string) $text), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(str_replace("\u{00A0}", ' ', $plain)) === '';
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
        if ($this->isStructuredField($field)) {
            return $this->frenchLeaves($field) !== [];
        }

        $value = $this->frenchValue($field);

        return ! is_array($value) && ! self::isBlankText(is_scalar($value) ? (string) $value : null);
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
