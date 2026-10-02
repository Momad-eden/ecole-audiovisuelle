<?php

namespace Tests\Feature\Translation;

use App\Jobs\TranslateRecord;
use App\Models\Page;
use App\Services\Translation\Translator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** Les mots mis en valeur d'un titre (*mot*) passent par DeepL comme balises <em> et reviennent en astérisques. */
class TitleEmphasisTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_emphasized_words_survive_translation(): void
    {
        config(['services.deepl.key' => 'secret-key', 'services.deepl.url' => 'https://api-free.deepl.com']);
        Cache::flush();
        Http::fake(['api-free.deepl.com/v2/usage' => Http::response(['character_count' => 0, 'character_limit' => 500000])]);
        $translator = new class implements Translator
        {
            public array $sent = [];

            public array $html = [];

            public function isAvailable(): bool
            {
                return true;
            }

            public function translate(array $texts, array $htmlKeys = []): array
            {
                $this->sent = $texts;
                $this->html = $htmlKeys;

                // DeepL garde la balise autour du mot traduit et échappe l'esperluette en mode HTML.
                return array_map(fn (string $t) => str_replace(['culture', 'héritage', 'Son & image'], ['culture', 'heritage', 'Sound &amp; image'], $t), $texts);
            }
        };
        $this->app->instance(Translator::class, $translator);

        $page = Page::create(['title' => 'Accueil', 'slug' => 'accueil', 'type' => 'free', 'draft_blocks' => [
            ['type' => 'hero', 'data' => ['title' => 'La culture comme *héritage*', 'subtitle' => 'Son & image']],
        ]]);
        $page->publish();
        TranslateRecord::dispatchSync($page->fresh());

        $key = 'blocks::hero#0:title';
        $this->assertSame('La culture comme <em>héritage</em>', $translator->sent[$key]);
        $this->assertContains($key, $translator->html);
        $this->assertNotContains('blocks::hero#0:subtitle', $translator->html, 'Un texte sans mise en valeur part en texte simple.');

        $english = $page->fresh()->load('translations')->translated('blocks', 'en');
        $this->assertSame('La culture comme *heritage*', $english['hero#0:title']);
    }
}
