<?php

namespace App\Services\Translation;

interface Translator
{
    /** Vrai si un service de traduction est configuré. */
    public function isAvailable(): bool;

    /**
     * Traduit du français vers l'anglais. Renvoie les mêmes clés, dans le même ordre.
     *
     * @param  array<int|string, string>  $texts
     * @param  array<int, int|string>  $htmlKeys  clés dont le texte est du HTML
     * @return array<int|string, string>
     *
     * @throws Exceptions\TranslationFailed
     * @throws Exceptions\TranslationTemporarilyUnavailable
     * @throws Exceptions\QuotaExceeded
     */
    public function translate(array $texts, array $htmlKeys = []): array;
}
