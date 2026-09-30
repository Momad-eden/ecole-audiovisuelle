<?php

namespace Tests\Feature\Translation;

use App\Models\Setting;
use App\Services\Translation\DeepLGlossary;
use App\Services\Translation\DeepLTranslator;
use App\Services\Translation\Exceptions\QuotaExceeded;
use App\Services\Translation\Exceptions\TranslationFailed;
use App\Services\Translation\Exceptions\TranslationTemporarilyUnavailable;
use App\Services\Translation\NullTranslator;
use App\Services\Translation\TranslationQuota;
use App\Services\Translation\Translator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\Support\FakeTranslator;
use Tests\TestCase;

class DeepLTranslatorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.deepl.key' => 'secret-key', 'services.deepl.url' => 'https://api-free.deepl.com']);
        Cache::flush();
    }

    private function echoTranslations(): void
    {
        Http::fake(['api-free.deepl.com/v2/translate' => fn (Request $r) => Http::response([
            'translations' => array_map(fn ($t) => ['detected_source_language' => 'FR', 'text' => "EN $t"], $r['text']),
        ])]);
    }

    /** Compte les appels DeepL (les rafraîchissements du site public sont ignorés). */
    private function assertDeepLCount(int $expected): void
    {
        $n = Http::recorded()->filter(fn ($pair) => str_contains($pair[0]->url(), 'deepl.com'))->count();
        $this->assertSame($expected, $n);
    }

    private function translator(): Translator
    {
        return app(Translator::class);
    }

    public function test_sends_exact_request_with_glossary(): void
    {
        Setting::current()->forceFill(['deepl_glossary_id' => 'gl-1'])->save();
        $this->echoTranslations();

        $out = $this->translator()->translate(['a' => 'Bonjour', 'b' => 'Salut']);

        $this->assertSame(['a' => 'EN Bonjour', 'b' => 'EN Salut'], $out);
        Http::assertSent(fn (Request $r) => $r->url() === 'https://api-free.deepl.com/v2/translate'
            && $r->method() === 'POST'
            && $r->hasHeader('Authorization', 'DeepL-Auth-Key secret-key')
            && $r->isJson()
            && $r['text'] === ['Bonjour', 'Salut']
            && $r['source_lang'] === 'FR' && $r['target_lang'] === 'EN-GB'
            && $r['preserve_formatting'] === true
            && $r['glossary_id'] === 'gl-1'
            && ! isset($r['tag_handling']));
    }

    public function test_batches_of_fifty_and_empty_strings_not_sent(): void
    {
        $this->echoTranslations();
        $texts = [];
        for ($i = 0; $i < 120; $i++) {
            $texts["k$i"] = "t$i";
        }
        $texts['vide'] = '';

        $out = $this->translator()->translate($texts);

        $this->assertSame(array_keys($texts), array_keys($out));
        $this->assertSame('', $out['vide']);
        $this->assertSame('EN t119', $out['k119']);
        $sizes = [];
        Http::assertSent(function (Request $r) use (&$sizes) {
            $r->url() === 'https://api-free.deepl.com/v2/translate' && $sizes[] = count($r['text']);

            return true;
        });
        $this->assertSame([50, 50, 20], $sizes);
    }

    public function test_html_sent_separately_with_tag_handling_and_returned_intact(): void
    {
        Http::fake(['api-free.deepl.com/v2/translate' => fn (Request $r) => Http::response([
            'translations' => array_map(fn ($t) => ['text' => str_contains($t, '<a') ? '<p>See <a href="/contact">us</a></p>' : 'Hello'], $r['text']),
        ])]);

        $out = $this->translator()->translate(['plain' => 'Salut', 'rich' => '<p>Voir <a href="/contact">nous</a></p>'], ['rich']);

        $this->assertSame('<p>See <a href="/contact">us</a></p>', $out['rich']);
        $this->assertSame('Hello', $out['plain']);
        $this->assertSame(['plain', 'rich'], array_keys($out) === ['plain', 'rich'] ? ['plain', 'rich'] : []);
        $this->assertDeepLCount(2);
        Http::assertSent(fn (Request $r) => $r->method() === 'POST' && str_contains($r->url(), 'translate') && ($r['tag_handling'] ?? null) === 'html' && $r['text'] === ['<p>Voir <a href="/contact">nous</a></p>']);
        Http::assertSent(fn (Request $r) => str_contains($r->url(), 'translate') && ! isset($r['tag_handling']) && $r['text'] === ['Salut']);
    }

    public function test_inconsistent_count_fails(): void
    {
        Http::fake(['*' => Http::response(['translations' => [['text' => 'One']]])]);
        $this->expectException(TranslationFailed::class);
        $this->translator()->translate(['a' => 'un', 'b' => 'deux']);
    }

    public function test_empty_translation_fails(): void
    {
        Http::fake(['*' => Http::response(['translations' => [['text' => '']]])]);
        $this->expectException(TranslationFailed::class);
        $this->translator()->translate(['a' => 'un']);
    }

    public function test_456_is_quota_exceeded(): void
    {
        Http::fake(['*' => Http::response([], 456)]);
        $this->expectException(QuotaExceeded::class);
        $this->translator()->translate(['a' => 'un']);
    }

    public function test_429_and_5xx_are_retryable_and_400_is_not(): void
    {
        foreach ([429, 503] as $status) {
            Http::swap(new Factory);
            Http::fake(['api-free.deepl.com/*' => Http::response([], $status)]);
            try {
                $this->translator()->translate(['a' => 'un']);
                $this->fail("HTTP $status");
            } catch (TranslationTemporarilyUnavailable) {
                $this->assertTrue(true);
            }
        }
        Http::swap(new Factory);
        Http::fake(['api-free.deepl.com/*' => Http::response([], 400)]);
        $this->expectException(TranslationFailed::class);
        $this->translator()->translate(['a' => 'un']);
    }

    public function test_quota_thresholds(): void
    {
        Http::fake(['api-free.deepl.com/v2/usage' => Http::response(['character_count' => 900, 'character_limit' => 1000])]);
        $quota = app(TranslationQuota::class);
        $this->assertTrue($quota->canSend(40));   // 94 %
        $this->assertFalse($quota->canSend(60));  // 96 %
        $this->assertSame(['used' => 900, 'limit' => 1000], $quota->usage());
        $this->assertDeepLCount(1); // mis en cache
        Http::assertSent(fn (Request $r) => $r->method() === 'GET' && $r->hasHeader('Authorization', 'DeepL-Auth-Key secret-key'));
    }

    public function test_quota_unreadable_refuses(): void
    {
        Http::fake(['*' => Http::response([], 500)]);
        $this->assertFalse(app(TranslationQuota::class)->canSend(1));
    }

    public function test_without_key_null_translator_is_bound(): void
    {
        config(['services.deepl.key' => null]);
        $t = app(Translator::class);
        $this->assertInstanceOf(NullTranslator::class, $t);
        $this->assertFalse($t->isAvailable());
        $this->expectException(TranslationFailed::class);
        $t->translate(['a' => 'un']);
    }

    public function test_with_key_deepl_is_bound(): void
    {
        $this->assertInstanceOf(DeepLTranslator::class, app(Translator::class));
        $this->assertTrue(app(Translator::class)->isAvailable());
    }

    public function test_fake_translator_prefixes_and_records(): void
    {
        $fake = new FakeTranslator;
        $this->assertSame(['a' => 'EN: un'], $fake->translate(['a' => 'un'], ['a']));
        $this->assertSame([['texts' => ['a' => 'un'], 'htmlKeys' => ['a']]], $fake->calls);
    }

    public function test_glossary_sync_recreates(): void
    {
        Setting::current()->forceFill(['deepl_glossary_id' => 'old'])->save();
        Http::fake([
            'api-free.deepl.com/v2/glossaries/old' => Http::response('', 404),
            'api-free.deepl.com/v2/glossaries' => Http::response(['glossary_id' => 'new-id'], 201),
        ]);

        $id = app(DeepLGlossary::class)->sync([
            ['fr' => 'Régie', 'en' => "Control\troom"],
            ['fr' => 'Vide', 'en' => ''],
            ['fr' => 'Mixage', 'en' => 'Mixing'],
        ]);

        $this->assertSame('new-id', $id);
        $this->assertSame('new-id', Setting::current()->deepl_glossary_id);
        Http::assertSent(fn (Request $r) => $r->method() === 'DELETE' && $r->url() === 'https://api-free.deepl.com/v2/glossaries/old');
        Http::assertSent(fn (Request $r) => $r->method() === 'POST' && $r->url() === 'https://api-free.deepl.com/v2/glossaries'
            && $r->hasHeader('Authorization', 'DeepL-Auth-Key secret-key')
            && str_starts_with($r['name'], 'emsi-')
            && $r['source_lang'] === 'fr' && $r['target_lang'] === 'en'
            && $r['entries_format'] === 'tsv'
            && $r['entries'] === "Régie\tControl room\nMixage\tMixing");
    }

    public function test_glossary_sync_empty_deletes_and_stores_null(): void
    {
        Setting::current()->forceFill(['deepl_glossary_id' => 'old'])->save();
        Http::fake(['api-free.deepl.com/*' => Http::response('', 204)]);

        $this->assertNull(app(DeepLGlossary::class)->sync([['fr' => 'a', 'en' => '']]));
        $this->assertNull(Setting::current()->deepl_glossary_id);
        $this->assertDeepLCount(1);
        Http::assertSent(fn (Request $r) => $r->method() === 'DELETE');
    }

    public function test_glossary_migration_rolls_back_cleanly(): void
    {
        $path = 'database/migrations/2026_09_30_130000_add_deepl_glossary_id_to_settings.php';
        $this->artisan('migrate:rollback', ['--path' => $path])->assertSuccessful();
        $this->assertFalse(Schema::hasColumn('settings', 'deepl_glossary_id'));
        $this->artisan('migrate', ['--path' => $path])->assertSuccessful();
        $this->assertTrue(Schema::hasColumn('settings', 'deepl_glossary_id'));
    }
}
