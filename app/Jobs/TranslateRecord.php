<?php

namespace App\Jobs;

use App\Enums\TranslationStatus;
use App\Models\Setting;
use App\Services\FrontendRevalidator;
use App\Services\Translation\DeepLGlossary;
use App\Services\Translation\Exceptions\QuotaExceeded;
use App\Services\Translation\Exceptions\TranslationFailed;
use App\Services\Translation\TranslationQuota;
use App\Services\Translation\Translator;
use App\Support\Translation\BlockTexts;
use App\Support\Translation\TranslationLeaves;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Traduit en anglais les champs d'une fiche dont le français a changé (spec R2 §3).
 *
 * Tentatives : `$tries = 0` (pas de limite de passages) et `$maxExceptions = 3`. Seules les erreurs
 * passagères (réseau, 429, 5xx) lèvent une exception : trois au plus, espacées de 1, 5 puis 15 minutes,
 * puis `failed()` marque les champs en échec ; une lecture du quota en panne passagère suit le même
 * chemin. Le quota atteint remet la tâche au lendemain par `release()`, qui ne compte pas comme une
 * exception : l'attente peut durer jusqu'au renouvellement du quota sans épuiser les essais. Une erreur définitive (réponse invalide, requête refusée) échoue tout de suite.
 *
 * Unicité par fiche jusqu'au début du traitement : une modification enregistrée pendant qu'une tâche
 * tourne remet bien une nouvelle tâche en file (sinon ce français-là resterait sans traduction).
 */
class TranslateRecord implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Queueable;

    /** Attente quand le quota gratuit est atteint (secondes). */
    public const QUOTA_RETRY_DELAY = 86400;

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
            $prepared[$field] = ['hash' => $record->sourceHash($field), 'leaves' => []];
            if ($record->hasStructuredTranslation($field)) {
                // Champ structuré : seuls les textes sans traduction à jour partent (spec R2 §3.3).
                $french = $record->frenchLeaves($field);
                $states = TranslationLeaves::states($record, $field, $record->translation($field, 'en'));
                $toSend = array_intersect_key($french, array_flip(TranslationLeaves::pending($record, $field, $states)));
                $prepared[$field]['leaves'] = array_map(fn (string $text) => $record::leafHash($text), $toSend);
            } else {
                $toSend = ['' => (string) $record->frenchValue($field)];
            }
            foreach ($toSend as $key => $text) {
                $texts["{$field}::{$key}"] = $text;
                if ($this->isHtml($field, (string) $key, $text)) {
                    $htmlKeys[] = "{$field}::{$key}";
                }
            }
        }

        if ($texts !== []) {
            try {
                if (! $quota->hasRoomFor(array_sum(array_map('mb_strlen', $texts)))) {
                    $this->releaseUntilQuotaRenews();

                    return;
                }
                $this->syncPendingGlossary();
                $translated = $translator->translate($texts, $htmlKeys);
            } catch (QuotaExceeded) {
                $this->releaseUntilQuotaRenews();

                return;
            } catch (TranslationFailed $e) {
                // Inutile de réessayer : champs en échec tout de suite, tâche en échec (journalisée).
                $this->markFieldsFailed();
                $this->fail($e);

                return;
            }
            // TranslationTemporarilyUnavailable (traduction ou lecture du quota) remonte :
            // nouvel essai après attente (backoff), trois au plus (maxExceptions).
        } else {
            $translated = [];
        }

        foreach ($prepared as $field => ['hash' => $hash, 'leaves' => $leafHashes]) {
            $mine = [];
            foreach ($translated as $key => $text) {
                if (str_starts_with($key, "{$field}::")) {
                    $mine[substr($key, strlen($field) + 2)] = $text;
                }
            }
            $record->hasStructuredTranslation($field)
                ? $this->storeLeaves($record, $field, $mine, $leafHashes, $hash)
                : $this->store($record, $field, $mine[''] ?? (string) $record->frenchValue($field), $hash);
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
            if ($record->hasStructuredTranslation($field)) {
                $this->markLeavesFailed($record, $field);

                continue;
            }
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

    /**
     * DeepL renouvelle le quota à la date anniversaire du compte (pas forcément le 1er du mois) :
     * la tâche revient voir chaque jour.
     */
    private function releaseUntilQuotaRenews(): void
    {
        $this->release(self::QUOTA_RETRY_DELAY);
    }

    /**
     * Lexique enregistré alors qu'aucune clé n'était configurée : envoyé à DeepL une fois, avant la
     * première traduction. En cas d'échec, on traduit sans glossaire (nouvel essai à la tâche suivante).
     */
    private function syncPendingGlossary(): void
    {
        $setting = Setting::current();
        if ($setting->deepl_glossary_id || empty($setting->translation_glossary)) {
            return;
        }

        try {
            app(DeepLGlossary::class)->sync($setting->translation_glossary);
        } catch (Throwable $e) {
            Log::warning('Envoi du lexique à DeepL impossible : '.$e->getMessage());
        }
    }

    private function isHtml(string $field, string $key, string $text): bool
    {
        return $field === 'blocks' ? BlockTexts::isHtml($key, $text) : BlockTexts::isHtml($field, $text);
    }

    /**
     * Relit la ligne sous verrou juste avant d'écrire : une relecture enregistrée pendant l'appel à DeepL
     * n'est jamais perdue (gardée telle quelle si elle porte sur le français actuel, sinon mise dans
     * previous_value).
     */
    private function store(Model $record, string $field, string $value, string $hash): void
    {
        DB::transaction(function () use ($record, $field, $value, $hash) {
            $row = $record->translations()->where('field', $field)->where('locale', 'en')->lockForUpdate()->first();
            $attributes = ['value' => $value, 'source_hash' => $hash, 'status' => TranslationStatus::AUTO, 'translated_at' => now()];

            if (! $row) {
                $record->translations()->create(['field' => $field, 'locale' => 'en', ...$attributes]);

                return;
            }

            if ($row->status === TranslationStatus::REVIEWED) {
                if ($row->source_hash === $hash) {
                    return;
                }
                $attributes['previous_value'] = $row->value;
            }
            $row->update($attributes);
        });
    }

    /**
     * Champ structuré : fusionne les textes traduits dans l'état relu sous verrou. Un texte relu entre-temps
     * sur le même français est gardé ; un texte relu d'un ancien français passe dans previous_value[clé].
     *
     * @param  array<string, string>  $translated
     * @param  array<string, string>  $leafHashes  empreinte du français envoyé, par clé
     */
    private function storeLeaves(Model $record, string $field, array $translated, array $leafHashes, string $hash): void
    {
        DB::transaction(function () use ($record, $field, $translated, $leafHashes, $hash) {
            $row = $record->translations()->where('field', $field)->where('locale', 'en')->lockForUpdate()->first();
            $states = TranslationLeaves::states($record, $field, $row);
            $english = TranslationLeaves::english($record, $field, $row);
            $previous = TranslationLeaves::previous($row);

            foreach ($translated as $key => $text) {
                $key = (string) $key;
                $state = $states[$key] ?? null;
                if ($state && $state['s'] === TranslationStatus::REVIEWED->value) {
                    if ($state['h'] === $leafHashes[$key]) {
                        continue;
                    }
                    if (isset($english[$key])) {
                        $previous[$key] = $english[$key];
                    }
                }
                $english[$key] = $text;
                $states[$key] = ['h' => $leafHashes[$key], 's' => TranslationStatus::AUTO->value];
            }

            TranslationLeaves::persist($record, $field, $row, $english, $states, $previous, ['source_hash' => $hash, 'translated_at' => now()]);
        });
    }

    /** Champ structuré en échec : seuls les textes à traduire passent en « Échec » (un texte relu va dans previous_value). */
    private function markLeavesFailed(Model $record, string $field): void
    {
        DB::transaction(function () use ($record, $field) {
            $row = $record->translations()->where('field', $field)->where('locale', 'en')->lockForUpdate()->first();
            $states = TranslationLeaves::states($record, $field, $row);
            $english = TranslationLeaves::english($record, $field, $row);
            $previous = TranslationLeaves::previous($row);

            foreach (TranslationLeaves::pending($record, $field, $states) as $key) {
                $state = $states[$key] ?? null;
                if ($state && $state['s'] === TranslationStatus::REVIEWED->value && isset($english[$key])) {
                    $previous[$key] = $english[$key];
                }
                $states[$key] = ['h' => $state['h'] ?? null, 's' => TranslationStatus::FAILED->value];
            }

            TranslationLeaves::persist($record, $field, $row, $english, $states, $previous);
        });
    }
}
