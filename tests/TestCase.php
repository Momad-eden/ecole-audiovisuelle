<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Aucun appel réseau réel pendant les tests (DeepL, rafraîchissement du site…) : chaque test simule ses réponses.
        Http::preventStrayRequests();
    }
}
