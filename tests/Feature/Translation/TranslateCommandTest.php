<?php

namespace Tests\Feature\Translation;

use App\Enums\PublicationStatus;
use App\Enums\TranslationStatus;
use App\Jobs\TranslateRecord;
use App\Models\Faq;
use App\Models\MenuItem;
use App\Models\News;
use App\Models\Page;
use App\Models\Program;
use App\Models\Setting;
use App\Services\Translation\Translator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\Support\FakeTranslator;
use Tests\TestCase;

/** Commande de lancement emsi:translate : comptage, quota, confirmation, mise en file. */
class TranslateCommandTest extends TestCase
{
    use RefreshDatabase;

    /** Réponse factice de /v2/usage : [corps, statut]. */
    private array $usage = [['character_count' => 1000, 'character_limit' => 500000], 200];

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['services.deepl.key' => 'secret-key', 'services.deepl.url' => 'https://api-free.deepl.com']);
        $this->app->instance(Translator::class, new FakeTranslator);
        Http::fake(['api-free.deepl.com/v2/usage' => fn () => Http::response(...$this->usage)]);
        Queue::fake();
    }

    private function page(string $slug, string $title, bool $published = true): Page
    {
        $page = Page::create(['title' => $title, 'slug' => $slug, 'type' => 'free', 'draft_blocks' => [
            ['type' => 'hero', 'data' => ['title' => 'Bienvenue', 'subtitle' => 'Le son', 'image' => 'a.jpg']],
        ]]);
        if ($published) {
            $page->publish();
        }

        return $page;
    }

    private function news(string $title, string $when, bool $published = true): News
    {
        return News::create(['title' => $title, 'slug' => str($title)->slug()->toString(), 'excerpt' => 'Résumé', 'content' => '<p>Texte</p>',
            'is_published' => $published, 'published_at' => $when]);
    }

    private function program(string $title, bool $published = true): Program
    {
        return Program::factory()->create(['title' => $title, 'status' => $published ? PublicationStatus::PUBLISHED : PublicationStatus::DRAFT]);
    }

    /** Remet la file à zéro après la création des fiches (qui a pu mettre des tâches en file). */
    private function freshQueue(): void
    {
        Queue::fake();
    }

    private function pushedIds(): array
    {
        return Queue::pushed(TranslateRecord::class)->map(fn (TranslateRecord $j) => $j->uniqueId())->values()->all();
    }

    public function test_dry_run_counts_characters_and_queues_nothing(): void
    {
        $page = $this->page('accueil', 'Accueil');
        $expected = array_sum(array_map('mb_strlen', TranslateRecord::pendingTexts($page)));
        $this->freshQueue();

        $this->artisan('emsi:translate', ['--all' => true, '--dry-run' => true])
            ->expectsOutputToContain('Pages : 1 fiche, '.number_format($expected, 0, ',', ' ').' caractères')
            ->expectsOutputToContain('Quota DeepL du mois : utilisés 1 000 / 500 000')
            ->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_all_force_queues_one_job_per_record_in_order_skipping_drafts_and_up_to_date(): void
    {
        $this->news('Ancienne', '2026-01-01 10:00:00');
        $this->news('Récente', '2026-06-01 10:00:00');
        $this->news('Brouillon', '2026-07-01 10:00:00', false);
        $p1 = $this->program('Montage');
        $this->program('Brouillon formation', false);
        $page = $this->page('accueil', 'Accueil');
        $this->page('secret', 'Secret', false);
        $done = $this->page('a-jour', 'À jour');
        foreach ($done->outdatedFields('en') as $field) {
            $done->translations()->create(['field' => $field, 'locale' => 'en', 'value' => 'x', 'source_hash' => $done->sourceHash($field), 'status' => TranslationStatus::AUTO]);
        }
        $this->freshQueue();

        $this->artisan('emsi:translate', ['--all' => true, '--force' => true])->assertSuccessful();

        $ids = $this->pushedIds();
        $this->assertSame([
            'page:'.$page->id,
            'program:'.$p1->id,
            'news:'.News::where('title', 'Récente')->value('id'),
            'news:'.News::where('title', 'Ancienne')->value('id'),
        ], $ids);
    }

    public function test_only_visible_menu_items_and_faqs_count(): void
    {
        MenuItem::create(['location' => 'main', 'label' => 'Accueil', 'url' => '/', 'position' => 1, 'is_visible' => true]);
        MenuItem::create(['location' => 'main', 'label' => 'Caché', 'url' => '/x', 'position' => 2, 'is_visible' => false]);
        Faq::create(['group' => 'a', 'question' => 'Q ?', 'answer' => 'R.', 'position' => 1, 'is_visible' => false]);
        $this->freshQueue();

        $this->artisan('emsi:translate', ['--all' => true, '--force' => true])->assertSuccessful();

        $this->assertCount(1, $this->pushedIds());
    }

    public function test_model_option_limits_to_one_type_by_alias_or_label(): void
    {
        $this->news('Une', '2026-06-01 10:00:00');
        $this->page('accueil', 'Accueil');
        $this->freshQueue();

        $this->artisan('emsi:translate', ['--model' => 'News', '--force' => true])->assertSuccessful();
        $this->assertCount(1, $this->pushedIds());
        $this->assertStringStartsWith('news:', $this->pushedIds()[0]);

        $this->freshQueue();
        $this->artisan('emsi:translate', ['--model' => 'Pages', '--force' => true])->assertSuccessful();
        $this->assertStringStartsWith('page:', $this->pushedIds()[0]);
    }

    public function test_unknown_model_lists_valid_values_and_fails(): void
    {
        $this->artisan('emsi:translate', ['--model' => 'nimportequoi'])
            ->expectsOutputToContain('Modèle inconnu')
            ->expectsOutputToContain('news')
            ->assertFailed();
    }

    public function test_without_key_prints_counts_and_message_and_queues_nothing(): void
    {
        config(['services.deepl.key' => null]);
        $this->app->instance(Translator::class, new FakeTranslator(false));
        $this->page('accueil', 'Accueil');
        $this->freshQueue();

        $this->artisan('emsi:translate', ['--all' => true, '--force' => true])
            ->expectsOutputToContain('Pages : 1 fiche')
            ->expectsOutputToContain('Traduction automatique non configurée : ajoutez DEEPL_API_KEY ou traduisez à la main dans l\'onglet Anglais.')
            ->assertSuccessful();

        Queue::assertNothingPushed();
        Http::assertNothingSent();
    }

    public function test_dry_run_without_key_never_calls_usage(): void
    {
        config(['services.deepl.key' => null]);
        $this->app->instance(Translator::class, new FakeTranslator(false));
        $this->page('accueil', 'Accueil');

        $this->artisan('emsi:translate', ['--dry-run' => true])->assertSuccessful();

        Http::assertNothingSent();
    }

    public function test_warns_when_total_exceeds_remaining_quota(): void
    {
        $this->usage = [['character_count' => 499990, 'character_limit' => 500000], 200];
        $this->page('accueil', 'Accueil');

        $this->artisan('emsi:translate', ['--all' => true, '--dry-run' => true])
            ->expectsOutputToContain('Au-delà du quota restant, la traduction reprendra automatiquement dès qu\'il se renouvelle.')
            ->assertSuccessful();
    }

    public function test_unreadable_quota_is_reported(): void
    {
        $this->usage = ['boom', 500];
        $this->page('accueil', 'Accueil');

        $this->artisan('emsi:translate', ['--all' => true, '--dry-run' => true])
            ->expectsOutputToContain('Quota indisponible pour le moment')
            ->assertSuccessful();
    }

    public function test_asks_confirmation_and_respects_refusal(): void
    {
        $this->page('accueil', 'Accueil');
        $this->freshQueue();

        $this->artisan('emsi:translate', ['--all' => true])
            ->expectsConfirmation('Mettre en file 1 fiches à traduire ?', 'no')
            ->assertSuccessful();
        Queue::assertNothingPushed();

        $this->artisan('emsi:translate', ['--all' => true])
            ->expectsConfirmation('Mettre en file 1 fiches à traduire ?', 'yes')
            ->assertSuccessful();
        Queue::assertPushed(TranslateRecord::class, 1);
    }

    public function test_syncs_pending_glossary_once_before_dispatching(): void
    {
        Http::fake([
            'api-free.deepl.com/v2/glossaries' => Http::response(['glossary_id' => 'g-1']),
        ]);
        Setting::current()->forceFill(['translation_glossary' => [['fr' => 'filière', 'en' => 'track']], 'deepl_glossary_id' => null])->save();
        $this->page('accueil', 'Accueil');
        $this->freshQueue();

        $this->artisan('emsi:translate', ['--all' => true, '--force' => true])->assertSuccessful();

        $this->assertSame('g-1', Setting::current()->deepl_glossary_id);
        Queue::assertPushed(TranslateRecord::class, 1);
    }

    public function test_glossary_failure_only_warns(): void
    {
        Http::fake([
            'api-free.deepl.com/v2/glossaries' => Http::response('no', 403),
        ]);
        Setting::current()->forceFill(['translation_glossary' => [['fr' => 'filière', 'en' => 'track']], 'deepl_glossary_id' => null])->save();
        $this->page('accueil', 'Accueil');
        $this->freshQueue();

        $this->artisan('emsi:translate', ['--all' => true, '--force' => true])->assertSuccessful();

        Queue::assertPushed(TranslateRecord::class, 1);
    }
}
