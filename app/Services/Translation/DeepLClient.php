<?php

namespace App\Services\Translation;

use App\Services\Translation\Exceptions\QuotaExceeded;
use App\Services\Translation\Exceptions\TranslationFailed;
use App\Services\Translation\Exceptions\TranslationTemporarilyUnavailable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/** Accès HTTP à DeepL API Free (clé DEEPL_API_KEY), partagé par la traduction, le quota et le glossaire. */
class DeepLClient
{
    public function request(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('services.deepl.url'), '/'))
            ->withHeaders(['Authorization' => 'DeepL-Auth-Key '.config('services.deepl.key')])
            ->acceptJson()
            ->timeout(30);
    }

    /** Envoie la requête ; convertit les erreurs en exceptions typées. */
    public function send(string $method, string $path, array $data = [], bool $allow404 = false): Response
    {
        try {
            $response = $this->request()->asJson()->send($method, $path, $data ? ['json' => $data] : []);
        } catch (ConnectionException $e) {
            throw new TranslationTemporarilyUnavailable('DeepL injoignable : '.$e->getMessage(), 0, $e);
        }

        $status = $response->status();
        if ($response->successful() || ($allow404 && $status === 404)) {
            return $response;
        }

        throw match (true) {
            $status === 456 => new QuotaExceeded('Quota DeepL dépassé.'),
            $status === 429, $status >= 500 => new TranslationTemporarilyUnavailable("DeepL indisponible (HTTP $status)."),
            default => new TranslationFailed("Requête DeepL refusée (HTTP $status).", $status),
        };
    }
}
