<?php

namespace App\Services\Translation;

use App\Services\Translation\Exceptions\TranslationFailed;
use App\Services\Translation\Exceptions\TranslationTemporarilyUnavailable;
use Illuminate\Support\Facades\Cache;

/** Consommation DeepL : on ne dépasse jamais 95 % de la limite mensuelle. */
class TranslationQuota
{
    public const CACHE_KEY = 'deepl:usage';

    public function __construct(private DeepLClient $client) {}

    /** @return array{used:int, limit:int} */
    public function usage(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(10), function () {
            $response = $this->client->send('GET', '/v2/usage');
            $used = $response->json('character_count');
            $limit = $response->json('character_limit');
            if (! is_numeric($used) || ! is_numeric($limit)) {
                throw new TranslationFailed('Réponse DeepL invalide (usage).');
            }

            return ['used' => (int) $used, 'limit' => (int) $limit];
        });
    }

    /** Ajoute au compteur en cache les caractères qui viennent d'être envoyés. */
    public function record(int $characters): void
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached)) {
            $cached['used'] += $characters;
            Cache::put(self::CACHE_KEY, $cached, now()->addMinutes(10));
        }
    }

    /** Oublie la consommation en cache (le prochain calcul relit l'API). */
    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** Faux aussi quand la consommation est illisible : on ne prend pas le risque. */
    public function canSend(int $characters): bool
    {
        try {
            return $this->hasRoomFor($characters);
        } catch (TranslationFailed|TranslationTemporarilyUnavailable) {
            return false;
        }
    }

    /**
     * Comme canSend(), mais une consommation illisible lève l'erreur (panne passagère ou refus)
     * au lieu de répondre « non » : la tâche de traduction peut alors réessayer ou échouer.
     *
     * @throws TranslationFailed
     * @throws TranslationTemporarilyUnavailable
     * @throws Exceptions\QuotaExceeded
     */
    public function hasRoomFor(int $characters): bool
    {
        ['used' => $used, 'limit' => $limit] = $this->usage();

        return $used + $characters <= (int) floor(0.95 * $limit);
    }
}
