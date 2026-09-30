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

    /** Faux aussi quand la consommation est illisible : on ne prend pas le risque. */
    public function canSend(int $characters): bool
    {
        try {
            ['used' => $used, 'limit' => $limit] = $this->usage();
        } catch (TranslationFailed|TranslationTemporarilyUnavailable) {
            return false;
        }

        return $used + $characters <= (int) floor(0.95 * $limit);
    }
}
