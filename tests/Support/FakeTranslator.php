<?php

namespace Tests\Support;

use App\Services\Translation\Translator;

/** Traducteur factice : préfixe chaque texte par « EN: » et garde la trace des appels. */
class FakeTranslator implements Translator
{
    /** @var array<int, array{texts: array, htmlKeys: array}> */
    public array $calls = [];

    public function __construct(private bool $available = true) {}

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function translate(array $texts, array $htmlKeys = []): array
    {
        $this->calls[] = ['texts' => $texts, 'htmlKeys' => $htmlKeys];

        return array_map(fn ($t) => trim((string) $t) === '' ? (string) $t : 'EN: '.$t, $texts);
    }
}
