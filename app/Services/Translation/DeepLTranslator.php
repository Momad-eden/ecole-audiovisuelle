<?php

namespace App\Services\Translation;

use App\Models\Setting;
use App\Services\Translation\Exceptions\TranslationFailed;

class DeepLTranslator implements Translator
{
    public const BATCH_SIZE = 50;

    public function __construct(private DeepLClient $client) {}

    public function isAvailable(): bool
    {
        return filled(config('services.deepl.key'));
    }

    public function translate(array $texts, array $htmlKeys = []): array
    {
        $result = [];
        $html = [];
        $plain = [];
        foreach ($texts as $key => $text) {
            if (trim((string) $text) === '') {
                $result[$key] = (string) $text;
            } elseif (in_array($key, $htmlKeys, true)) {
                $html[$key] = (string) $text;
            } else {
                $plain[$key] = (string) $text;
            }
        }

        $glossaryId = ($html || $plain) ? Setting::current()->deepl_glossary_id : null;

        $translated = [];
        foreach ([[$html, true], [$plain, false]] as [$group, $isHtml]) {
            foreach (array_chunk($group, self::BATCH_SIZE, true) as $batch) {
                $translated += $this->sendBatch($batch, $isHtml, $glossaryId);
            }
        }

        $ordered = [];
        foreach ($texts as $key => $_) {
            $ordered[$key] = $result[$key] ?? $translated[$key];
        }

        return $ordered;
    }

    /** @param array<int|string, string> $batch */
    private function sendBatch(array $batch, bool $html, ?string $glossaryId): array
    {
        $body = [
            'text' => array_values($batch),
            'source_lang' => 'FR',
            'target_lang' => 'EN-GB',
            'preserve_formatting' => true,
        ];
        if ($html) {
            $body['tag_handling'] = 'html';
        }
        if ($glossaryId) {
            $body['glossary_id'] = $glossaryId;
        }

        $translations = $this->client->send('POST', '/v2/translate', $body)->json('translations');
        if (! is_array($translations) || count($translations) !== count($batch)) {
            throw new TranslationFailed('Réponse DeepL invalide : nombre de textes inattendu.');
        }

        $out = [];
        foreach (array_keys($batch) as $i => $key) {
            $text = $translations[$i]['text'] ?? null;
            if (! is_string($text) || trim($text) === '') {
                throw new TranslationFailed('Réponse DeepL invalide : traduction vide.');
            }
            $out[$key] = $text;
        }

        return $out;
    }
}
