<?php

namespace App\Jobs;

use App\Enums\TranslationStatus;
use App\Services\FrontendRevalidator;
use App\Services\Translation\Exceptions\QuotaExceeded;
use App\Services\Translation\Exceptions\TranslationFailed;
use App\Services\Translation\TranslationQuota;
use App\Services\Translation\Translator;
use App\Support\Translation\BlockTexts;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Traduit en anglais les champs d'une fiche dont le français a changé (spec R2 §3).
 *
 * Tentatives : `$tries = 0` (pas de limite de passages) et `$maxExceptions = 3`. Seules les erreurs
 * passagères (réseau, 429, 5xx) lèvent une exception : trois au plus, espacées de 1, 5 puis 15 minutes,
 * puis `failed()` marque les champs en échec. Le quota atteint remet la tâche au 1er du mois suivant
 * par `release()`, qui ne compte pas comme une exception : l'attente peut durer plusieurs mois sans
 * épuiser les essais. Une erreur définitive (réponse invalide, requête refusée) échoue tout de suite.
 *
 * Unicité par fiche jusqu'au début du traitement : une modification enregistrée pendant qu'une tâche
 * tourne remet bien une nouvelle tâche en file (sinon ce français-là resterait sans traduction).
 */
class TranslateRecord implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Queueable;

    public int $tries = 0;

    public int $maxExceptions = 3;

    public int $uniqueFor = 3600;

    public bool $deleteWhenMissingModels = true;

    public function __construct(public Model $record) {}

    public function uniqueId(): string
    {
        return $this->record->getMorphClass().':'.$this->record->getKey();
    }

    /** @return array<int, int> */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function handle(Translator $translator, TranslationQuota $quota): void
    {
        if (! $translator->isAvailable()) {
            return;
        }

        $record = $this->record->load('translations');
        $fields = $record->outdatedFields('en');
        if ($fields === []) {
            return;
        }

        $texts = [];
        $htmlKeys = [];
        $prepared = [];
        foreach ($fields as $field) {
            $source = $record->frenchValue($field);
            $prepared[$field] = ['source' => $source, 'hash' => $record->sourceHash($field)];
            foreach ($this->textsOf($field, $source) as $key => $text) {
                $texts["{$field}::{$key}"] = $text;
                if ($this->isHtml($field, (string) $key, $text)) {
                    $htmlKeys[] = "{$field}::{$key}";
                }
            }
        }

        if ($texts !== []) {
            if (! $quota->canSend(array_sum(array_map('mb_strlen', $texts)))) {
                $this->releaseUntilNextMonth();

                return;
            }

            try {
                $translated = $translator->translate($texts, $htmlKeys);
            } catch (QuotaExceeded) {
                $this->releaseUntilNextMonth();

                return;
            } catch (TranslationFailed $e) {
                // Inutile de réessayer : champs en échec tout de suite, tâche en échec (journalisée).
                $this->markFieldsFailed();
                $this->fail($e);

                return;
            }
            // TranslationTemporarilyUnavailable remonte : nouvel essai après attente (backoff).
        } else {
            $translated = [];
        }

        foreach ($prepared as $field => ['source' => $source, 'hash' => $hash]) {
            $mine = [];
            foreach ($translated as $key => $text) {
                if (str_starts_with($key, "{$field}::")) {
                    $mine[substr($key, strlen($field) + 2)] = $text;
                }
            }
            $this->store($record, $field, $this->valueOf($field, $source, $mine), $hash);
        }

        app(FrontendRevalidator::class)->queue(['content']);
    }

    /** Échec définitif : les champs à traduire passent en « Échec » (le site garde le français). */
    public function failed(?Throwable $e): void
    {
        Log::warning("Traduction impossible ({$this->uniqueId()}) : ".$e?->getMessage());
        $this->markFieldsFailed();
    }

    /** Idempotent : une valeur relue va dans previous_value avant de passer en « Échec ». */
    private function markFieldsFailed(): void
    {
        $record = $this->record->fresh();
        if (! $record) {
            return;
        }

        foreach ($record->load('translations')->outdatedFields('en') as $field) {
            $row = $record->translation($field, 'en');
            if ($row) {
                $row->update([
                    'status' => TranslationStatus::FAILED,
                    'previous_value' => $row->status === TranslationStatus::REVIEWED ? $row->value : $row->previous_value,
                ]);
            } else {
                $record->translations()->create(['field' => $field, 'locale' => 'en', 'status' => TranslationStatus::FAILED]);
            }
        }
    }

    private function releaseUntilNextMonth(): void
    {
        $this->release(Carbon::now('Africa/Dakar')->startOfMonth()->addMonthNoOverflow()->setTime(0, 5));
    }

    /**
     * Textes à envoyer pour un champ, par clé locale.
     *
     * @return array<string|int, string>
     */
    private function textsOf(string $field, mixed $source): array
    {
        if (! is_array($source)) {
            return ['' => (string) $source];
        }

        if ($field === 'blocks') {
            return BlockTexts::keyed($source);
        }

        $keys = $field === 'seo' ? ['title', 'description'] : array_keys($source);
        $out = [];
        foreach ($keys as $key) {
            if (is_string($source[$key] ?? null) && trim($source[$key]) !== '') {
                $out[$key] = $source[$key];
            }
        }

        return $out;
    }

    private function isHtml(string $field, string $key, string $text): bool
    {
        return $field === 'blocks' ? BlockTexts::isHtml($key, $text) : BlockTexts::isHtml($field, $text);
    }

    /** Valeur anglaise à ranger : texte, ou JSON (carte des blocs, tableau, SEO complet). */
    private function valueOf(string $field, mixed $source, array $translated): string
    {
        if (! is_array($source)) {
            return $translated[''] ?? (string) $source;
        }

        if ($field === 'blocks') {
            return json_encode($translated, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        foreach ($translated as $key => $text) {
            $source[$key] = $text;
        }

        return json_encode($source, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function store(Model $record, string $field, string $value, string $hash): void
    {
        $row = $record->translation($field, 'en');
        $attributes = ['value' => $value, 'source_hash' => $hash, 'status' => TranslationStatus::AUTO, 'translated_at' => now()];

        if (! $row) {
            $record->translations()->create(['field' => $field, 'locale' => 'en', ...$attributes]);

            return;
        }

        if ($row->status === TranslationStatus::REVIEWED) {
            $attributes['previous_value'] = $row->value;
        }
        $row->update($attributes);
    }
}
