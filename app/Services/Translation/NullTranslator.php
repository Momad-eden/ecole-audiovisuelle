<?php

namespace App\Services\Translation;

use App\Services\Translation\Exceptions\TranslationFailed;

/** Sans clé DeepL : rien n'est traduit, le site reste en français. */
class NullTranslator implements Translator
{
    public function isAvailable(): bool
    {
        return false;
    }

    public function translate(array $texts, array $htmlKeys = []): array
    {
        throw new TranslationFailed('Aucun service de traduction configuré.');
    }
}
