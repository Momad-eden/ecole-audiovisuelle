<?php

namespace Tests\Feature\Translation;

use App\Enums\TranslationStatus;
use App\Filament\Pages\SiteSettings;
use App\Jobs\TranslateRecord;
use App\Models\Page;
use App\Models\Program;
use App\Models\Setting;
use App\Models\Translation;
use App\Models\User;
use App\Services\Translation\Exceptions\QuotaExceeded;
use App\Services\Translation\Exceptions\TranslationFailed;
use App\Services\Translation\Exceptions\TranslationTemporarilyUnavailable;
use App\Services\Translation\TranslationQuota;
use App\Services\Translation\Translator;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\Support\FakeTranslator;
use Tests\TestCase;

/** Chaîne de traduction : déclenchement, tâche, relecture respectée, réglages. */
class TranslationPipelineTest extends TestCase
{
    use RefreshDatabase;

    private FakeTranslator $translator;

    private int $used = 1000;

    private int $limit = 500000;

    private int $usageStatus = 200;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.deepl.key' => 'secret-key', 'services.deepl.url' => 'https://api-free.deepl.com']);
        Cache::flush();
        $this->translator = new FakeTranslator;
        $this->app->instance(Translator::class, $this->translator);
        Http::fake(['api-free.deepl.com/v2/usage' => fn () => Http::response(
            ['character_count' => $this->used, 'character_limit' => $this->limit], $this->usageStatus,
        )]);
    }

    private function fakeUsage(int $used, int $limit): void
    {
        [$this->used, $this->limit] = [$used, $limit];
        Cache::flush();
    }

    private function blocks(string $title = 'Bienvenue à l\'école'): array
    {
        return [
            ['type' => 'hero', 'data' => ['title' => $title, 'subtitle' => 'Le son et l\'image', 'image' => 'hero.jpg']],
            ['type' => 'text', 'data' => ['body' => '<p>Une école <strong>unique</strong>.</p>']],
        ];
    }

    private function page(): Page
    {
        return Page::create(['title' => 'Accueil', 'slug' => 'accueil', 'type' => 'free', 'draft_blocks' => $this->blocks()]);
    }

    private function row(object $model, string $field): ?Translation
    {
        return Translation::query()->where('translatable_type', $model->getMorphClass())
            ->where('translatable_id', $model->getKey())->where('field', $field)->where('locale', 'en')->first();
    }

    private function allTexts(): array
    {
        return array_merge(...array_map(fn ($c) => array_values($c['texts']), $this->translator->calls ?: [['texts' => []]]));
    }

    // --- Déclenchement ---

    public function test_publishing_a_page_translates_title_blocks_and_seo(): void
    {
        $page = $this->page();
        $page->forceFill(['seo' => ['title' => 'Titre Google', 'description' => 'Description Google', 'image' => 'seo.jpg']])->save();
        $page->publish();

        $title = $this->row($page, 'title');
        $this->assertSame('EN: Accueil', $title->value);
        $this->assertSame(TranslationStatus::AUTO, $title->status);
        $this->assertSame($page->sourceHash('title'), $title->source_hash);
        $this->assertNotNull($title->translated_at);

        $blocks = $this->row($page, 'blocks');
        $this->assertSame(TranslationStatus::AUTO, $blocks->status);
        $this->assertSame($page->fresh()->sourceHash('blocks'), $blocks->source_hash);
        $map = json_decode($blocks->value, true);
        $this->assertSame([
            'hero#0:title' => 'EN: Bienvenue à l\'école',
            'hero#0:subtitle' => 'EN: Le son et l\'image',
            'text#0:body' => 'EN: <p>Une école <strong>unique</strong>.</p>',
        ], $map);

        $seo = json_decode($this->row($page, 'seo')->value, true);
        $this->assertSame(['title' => 'EN: Titre Google', 'description' => 'EN: Description Google', 'image' => 'seo.jpg'], $seo);

        // Le HTML part en mode HTML, le reste en texte.
        $htmlTexts = [];
        foreach ($this->translator->calls as $call) {
            foreach ($call['htmlKeys'] as $key) {
                $htmlTexts[] = $call['texts'][$key];
            }
        }
        $this->assertContains('<p>Une école <strong>unique</strong>.</p>', $htmlTexts);
        $this->assertNotContains('Accueil', $htmlTexts);

        $this->assertSame([], $page->fresh()->outdatedFields('en'));
        // Le français ne bouge pas.
        $this->assertSame($this->blocks(), $page->fresh()->blocks);
    }

    public function test_publishing_dispatches_one_job(): void
    {
        Queue::fake();
        $page = $this->page();
        Queue::assertNothingPushed();

        $page->publish();

        Queue::assertPushed(TranslateRecord::class, 1);
        Queue::assertPushed(TranslateRecord::class, fn (TranslateRecord $job) => $job->uniqueId() === 'page:'.$page->id);
    }

    public function test_editing_draft_blocks_only_dispatches_nothing(): void
    {
        $page = $this->page();
        $page->publish();

        Queue::fake();
        $page->update(['draft_blocks' => $this->blocks('Brouillon modifié')]);

        Queue::assertNotPushed(TranslateRecord::class);
    }

    public function test_saving_non_translatable_fields_dispatches_nothing(): void
    {
        $setting = Setting::current();
        Queue::fake();

        $setting->update(['phone' => '+221 77 000 00 00']);
        $setting->forceFill(['deepl_glossary_id' => 'gl-1'])->save();

        Queue::assertNotPushed(TranslateRecord::class);
    }

    public function test_switch_off_dispatches_nothing(): void
    {
        Setting::current()->update(['auto_translate' => false]);
        Queue::fake();

        $this->page()->publish();
        Program::factory()->create();

        Queue::assertNotPushed(TranslateRecord::class);
    }

    public function test_missing_key_dispatches_nothing(): void
    {
        $this->app->instance(Translator::class, new FakeTranslator(available: false));
        Queue::fake();

        $this->page()->publish();

        Queue::assertNotPushed(TranslateRecord::class);
    }

    public function test_job_is_unique_per_record(): void
    {
        $page = $this->page();
        $job = new TranslateRecord($page);

        $this->assertInstanceOf(ShouldBeUnique::class, $job);
        $this->assertSame('page:'.$page->id, $job->uniqueId());
        $this->assertSame(3600, $job->uniqueFor);
        $this->assertSame(3, $job->maxExceptions);
        $this->assertSame([60, 300, 900], $job->backoff());
    }

    public function test_dispatch_waits_for_the_transaction_commit(): void
    {
        Queue::fake();
        $page = $this->page();

        DB::transaction(function () use ($page) {
            $page->publish();
            Queue::assertNotPushed(TranslateRecord::class);
        });

        Queue::assertPushed(TranslateRecord::class);
    }

    // --- Champs d'une fiche ---

    public function test_program_arrays_are_translated_element_by_element(): void
    {
        $program = Program::factory()->create([
            'title' => 'Son', 'summary' => 'Formation au son',
            'skills' => ['Mixage', 'Prise de son'],
            'description' => '<p>Tout sur le <em>son</em>.</p>',
        ]);

        $this->assertSame(['EN: Mixage', 'EN: Prise de son'], json_decode($this->row($program, 'skills')->value, true));
        $this->assertSame('EN: <p>Tout sur le <em>son</em>.</p>', $this->row($program, 'description')->value);
        $this->assertSame(['en' => ['EN: Mixage', 'EN: Prise de son']]['en'], $program->fresh()->translated('skills', 'en'));
    }

    // --- Relecture ---

    public function test_reviewed_field_with_unchanged_french_is_never_overwritten(): void
    {
        $page = $this->page();
        $page->publish();
        $blocks = $this->row($page, 'blocks');
        $blocks->update(['value' => json_encode(['hero#0:title' => 'Welcome (reviewed)']), 'status' => TranslationStatus::REVIEWED]);

        $this->translator->calls = [];
        $page->update(['title' => 'Accueil du site']);

        $this->assertSame('EN: Accueil du site', $this->row($page, 'title')->value);
        $blocks->refresh();
        $this->assertSame(TranslationStatus::REVIEWED, $blocks->status);
        $this->assertSame(['hero#0:title' => 'Welcome (reviewed)'], json_decode($blocks->value, true));
        $this->assertNotContains('Bienvenue à l\'école', $this->allTexts());
    }

    public function test_reviewed_field_with_changed_french_is_retranslated_and_kept_as_previous_value(): void
    {
        $page = $this->page();
        $page->publish();
        $this->row($page, 'title')->update(['value' => 'Home (reviewed)', 'status' => TranslationStatus::REVIEWED]);

        $page->update(['title' => 'Page d\'accueil']);

        $title = $this->row($page, 'title');
        $this->assertSame('EN: Page d\'accueil', $title->value);
        $this->assertSame(TranslationStatus::AUTO, $title->status);
        $this->assertSame('Home (reviewed)', $title->previous_value);
    }

    // --- Quota et erreurs ---

    public function test_insufficient_quota_releases_the_job_until_next_month(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-30 15:00:00', 'Africa/Dakar'));
        Queue::fake();
        $page = $this->page();
        $page->publish();
        $this->fakeUsage(474990, 500000); // 95 % = 475 000 : il reste 10 caractères

        $job = (new TranslateRecord($page))->withFakeQueueInteractions();
        $job->handle(app(Translator::class), app(TranslationQuota::class));

        $job->assertReleased(Carbon::parse('2026-10-01 00:05:00', 'Africa/Dakar'));
        $job->assertNotFailed();
        $this->assertSame([], $this->translator->calls);
        $this->assertSame(0, Translation::count());
        Carbon::setTestNow();
    }

    public function test_quota_exceeded_by_deepl_releases_the_job(): void
    {
        Queue::fake();
        $page = $this->page();
        $page->publish();
        $this->app->instance(Translator::class, $this->throwing(new QuotaExceeded('456')));

        $job = (new TranslateRecord($page))->withFakeQueueInteractions();
        $job->handle(app(Translator::class), app(TranslationQuota::class));

        $job->assertReleased();
        $job->assertNotFailed();
        $this->assertSame(0, Translation::where('status', 'failed')->count());
    }

    public function test_definitive_error_marks_fields_failed_without_retry(): void
    {
        $this->app->instance(Translator::class, $this->throwing(new TranslationFailed('Réponse invalide', 400)));
        Queue::fake();
        $page = $this->page();
        $page->publish();

        $job = (new TranslateRecord($page))->withFakeQueueInteractions();
        $job->handle(app(Translator::class), app(TranslationQuota::class));

        $job->assertFailed();
        $job->assertNotReleased();
        foreach (['title', 'blocks'] as $field) {
            $this->assertSame(TranslationStatus::FAILED, $this->row($page, $field)->status);
        }
    }

    public function test_temporary_errors_are_retried_three_times_then_marked_failed(): void
    {
        $translator = $this->throwing(new TranslationTemporarilyUnavailable('HTTP 503'));
        $this->app->instance(Translator::class, $translator);
        config(['queue.default' => 'database']);

        $page = $this->page();
        $page->publish();
        $this->assertSame(1, DB::table('jobs')->count());

        foreach ([61, 301, 901] as $i => $wait) {
            $this->artisan('queue:work', ['connection' => 'database', '--once' => true, '--stop-when-empty' => true]);
            $this->assertSame($i + 1, $translator->attempts);
            if ($i < 2) {
                $this->assertSame(0, Translation::where('status', 'failed')->count(), 'pas encore d\'échec');
                $this->travel($wait)->seconds();
            }
        }

        $this->assertSame(0, DB::table('jobs')->count());
        $this->assertSame(1, DB::table('failed_jobs')->count());
        $this->assertSame(TranslationStatus::FAILED, $this->row($page, 'title')->status);
        $this->assertSame(TranslationStatus::FAILED, $this->row($page, 'blocks')->status);
    }

    public function test_failure_keeps_reviewed_text_as_previous_value(): void
    {
        $page = $this->page();
        $page->publish();
        $this->row($page, 'title')->update(['value' => 'Home (reviewed)', 'status' => TranslationStatus::REVIEWED]);
        $page->forceFill(['title' => 'Nouvel accueil'])->saveQuietly();

        $this->app->instance(Translator::class, $this->throwing(new TranslationFailed('x', 400)));
        $job = (new TranslateRecord($page->fresh()))->withFakeQueueInteractions();
        $job->handle(app(Translator::class), app(TranslationQuota::class));

        $title = $this->row($page, 'title');
        $this->assertSame(TranslationStatus::FAILED, $title->status);
        $this->assertSame('Home (reviewed)', $title->previous_value);

        // Réussite ultérieure : l'ancienne valeur relue reste disponible.
        $this->app->instance(Translator::class, $this->translator);
        (new TranslateRecord($page->fresh()))->handle(app(Translator::class), app(TranslationQuota::class));
        $title->refresh();
        $this->assertSame('EN: Nouvel accueil', $title->value);
        $this->assertSame('Home (reviewed)', $title->previous_value);
        $this->assertSame(TranslationStatus::AUTO, $title->status);
    }

    private function throwing(\Throwable $e): Translator
    {
        return new class($e) implements Translator
        {
            public int $attempts = 0;

            public function __construct(private \Throwable $e) {}

            public function isAvailable(): bool
            {
                return true;
            }

            public function translate(array $texts, array $htmlKeys = []): array
            {
                $this->attempts++;

                throw $this->e;
            }
        };
    }

    // --- Réglages ---

    public function test_settings_migration_adds_prefilled_glossary_and_rolls_back(): void
    {
        $path = 'database/migrations/2026_09_30_140000_add_translation_settings.php';
        Setting::current();

        $this->artisan('migrate:rollback', ['--path' => $path])->assertSuccessful();
        $this->assertFalse(Schema::hasColumn('settings', 'auto_translate'));
        $this->assertFalse(Schema::hasColumn('settings', 'translation_glossary'));

        $this->artisan('migrate', ['--path' => $path])->assertSuccessful();
        $row = DB::table('settings')->first();
        $this->assertEquals(1, $row->auto_translate);
        $glossary = json_decode($row->translation_glossary, true);
        $this->assertContains(['fr' => 'EMSI', 'en' => 'EMSI'], $glossary);
        $this->assertContains(['fr' => 'Grand Théâtre National Doudou Ndiaye Coumba Rose', 'en' => 'Grand Théâtre National Doudou Ndiaye Coumba Rose'], $glossary);
        $this->assertContains(['fr' => 'VAE', 'en' => 'Recognition of Prior Learning (VAE)'], $glossary);
        $this->assertCount(9, $glossary);
    }

    private function admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'directeur']));
    }

    public function test_saving_the_lexicon_resyncs_the_deepl_glossary(): void
    {
        $this->admin();
        $this->fakeUsage(1200, 500000);
        Http::fake(['api-free.deepl.com/v2/glossaries' => Http::response(['glossary_id' => 'gl-new'])]);

        Livewire::test(SiteSettings::class)
            ->assertSee('Caractères traduits ce mois-ci : 1 200 / 500 000')
            ->set('data.translation_glossary', [
                ['fr' => 'EMSI', 'en' => 'EMSI'],
                ['fr' => 'filière', 'en' => 'programme track'],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $setting = Setting::current()->fresh();
        $this->assertSame('gl-new', $setting->deepl_glossary_id);
        $this->assertSame([['fr' => 'EMSI', 'en' => 'EMSI'], ['fr' => 'filière', 'en' => 'programme track']], $setting->translation_glossary);
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/v2/glossaries')
            && $r['entries'] === "EMSI\tEMSI\nfilière\tprogramme track");
    }

    public function test_unchanged_lexicon_does_not_call_deepl(): void
    {
        $this->admin();
        Livewire::test(SiteSettings::class)->set('data.phone', '+221 33 000 00 00')->call('save');

        Http::assertNotSent(fn (Request $r) => str_contains($r->url(), '/v2/glossaries'));
        $this->assertSame('+221 33 000 00 00', Setting::current()->phone);
    }

    public function test_glossary_failure_notifies_but_saves_other_settings(): void
    {
        $this->admin();
        Http::fake(['api-free.deepl.com/v2/glossaries' => Http::response([], 500)]);

        Livewire::test(SiteSettings::class)
            ->set('data.phone', '+221 33 111 11 11')
            ->set('data.auto_translate', false)
            ->set('data.translation_glossary', [['fr' => 'filière', 'en' => 'track']])
            ->call('save')
            ->assertNotified('Lexique enregistré, mais DeepL ne l\'a pas encore reçu');

        $setting = Setting::current()->fresh();
        $this->assertSame('+221 33 111 11 11', $setting->phone);
        $this->assertFalse($setting->auto_translate);
        $this->assertSame([['fr' => 'filière', 'en' => 'track']], $setting->translation_glossary);
    }

    public function test_settings_page_without_translator_explains_manual_translation(): void
    {
        $this->admin();
        $this->app->instance(Translator::class, new FakeTranslator(available: false));

        Livewire::test(SiteSettings::class)
            ->assertSee('Traduction automatique non configurée : traduisez à la main dans l\'onglet Anglais des fiches')
            ->set('data.translation_glossary', [['fr' => 'filière', 'en' => 'track']])
            ->call('save');

        Http::assertNotSent(fn (Request $r) => str_contains($r->url(), '/v2/glossaries'));
    }

    public function test_settings_page_survives_unreachable_deepl(): void
    {
        $this->admin();
        $this->usageStatus = 503;

        Livewire::test(SiteSettings::class)->assertSee('Quota indisponible pour le moment');
    }
}
