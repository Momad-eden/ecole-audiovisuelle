<?php

namespace Tests\Feature\Translation;

use App\Enums\TranslationStatus;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Programs\Pages\EditProgram;
use App\Filament\Resources\Translations\Pages\ListTranslations;
use App\Filament\Support\TranslationTab;
use App\Jobs\TranslateRecord;
use App\Models\Page;
use App\Models\Program;
use App\Models\Translation;
use App\Models\User;
use App\Services\Translation\Translator;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use RuntimeException;
use Tests\Support\FakeTranslator;
use Tests\TestCase;

/**
 * État par texte des champs structurés (blocs, listes, SEO) : une correction humaine ne fige pas les
 * autres textes, et un changement du français ne retraduit que le texte concerné (spec R2 §3.3).
 */
class TranslationLeavesTest extends TestCase
{
    use RefreshDatabase;

    private FakeTranslator $translator;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.deepl.key' => 'secret-key', 'services.deepl.url' => 'https://api-free.deepl.com']);
        Cache::flush();
        $this->translator = new FakeTranslator;
        $this->app->instance(Translator::class, $this->translator);
        Http::fake(['api-free.deepl.com/v2/usage' => Http::response(['character_count' => 0, 'character_limit' => 500000])]);
        $this->actingAs(User::factory()->create(['role' => 'directeur']));
    }

    private function blocks(string $title = 'Bienvenue', string $subtitle = 'Le son et l\'image'): array
    {
        return [
            ['type' => 'hero', 'data' => ['title' => $title, 'subtitle' => $subtitle]],
            ['type' => 'text', 'data' => ['body' => '<p>Une école</p>']],
        ];
    }

    private function publishedPage(): Page
    {
        $page = Page::create(['title' => 'Accueil', 'slug' => 'accueil', 'type' => 'free', 'draft_blocks' => $this->blocks()]);
        $page->publish();

        return $page->fresh();
    }

    private function republish(Page $page, array $blocks): Page
    {
        $page->update(['draft_blocks' => $blocks]);
        $page->fresh()->publish();

        return $page->fresh();
    }

    private function row(Model $model, string $field): ?Translation
    {
        return $model->translations()->where('field', $field)->where('locale', 'en')->first();
    }

    private function sentTexts(): array
    {
        return array_merge([], ...array_map(fn ($call) => array_values($call['texts']), $this->translator->calls));
    }

    /** Exécute la tâche maintenant (Queue::fake intercepte aussi dispatchSync). */
    private function runJob(Model $record): void
    {
        $this->app->call([new TranslateRecord($record), 'handle']);
    }

    private function correct(Page $page, string $key, string $english): void
    {
        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->fillForm(['english.blocks.'.TranslationTab::slot($key) => $english])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    public function test_leaves_migration_rolls_back_cleanly(): void
    {
        $path = 'database/migrations/2026_09_30_160000_add_leaves_to_translations.php';
        $this->artisan('migrate:rollback', ['--path' => $path])->assertSuccessful();
        $this->assertFalse(Schema::hasColumn('translations', 'leaves'));

        $this->artisan('migrate', ['--path' => $path])->assertSuccessful();
        $this->assertTrue(Schema::hasColumn('translations', 'leaves'));
    }

    public function test_job_records_one_state_per_text(): void
    {
        $page = $this->publishedPage();

        $leaves = $this->row($page, 'blocks')->leaves;
        // MySQL réordonne les clés d'un JSON stocké : on compare l'ensemble des clés, pas leur ordre.
        $this->assertEqualsCanonicalizing(['hero#0:title', 'hero#0:subtitle', 'text#0:body'], array_keys($leaves));
        $this->assertSame(['h' => hash('sha256', 'Bienvenue'), 's' => 'auto'], $leaves['hero#0:title']);
    }

    /** Sonde (a) : corriger un texte laisse les autres « Traduction automatique », toujours à relire. */
    public function test_correcting_one_block_text_leaves_the_others_to_review(): void
    {
        $page = $this->publishedPage();

        $this->correct($page, 'hero#0:title', 'Welcome');

        $row = $this->row($page, 'blocks');
        $this->assertSame('reviewed', $row->leaves['hero#0:title']['s']);
        $this->assertSame('auto', $row->leaves['hero#0:subtitle']['s']);
        $this->assertSame(TranslationStatus::AUTO, $row->status);
        $this->assertSame('Welcome', json_decode($row->value, true)['hero#0:title']);
        $this->assertSame('EN: Le son et l\'image', json_decode($row->value, true)['hero#0:subtitle']);
        $this->assertSame([], $page->fresh()->outdatedFields());

        Livewire::test(ListTranslations::class)->assertCanSeeTableRecords([$row])->assertSee('2 textes à relire sur 3');
        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])->assertSee('Relue')->assertSee('Traduction automatique');
    }

    /** Sonde (b) : un changement du français ne retraduit que le texte concerné ; la correction reste. */
    public function test_changing_one_french_text_retranslates_only_that_text(): void
    {
        $page = $this->publishedPage();
        $this->correct($page, 'hero#0:title', 'Welcome');
        $this->translator->calls = [];

        $page = $this->republish($page, $this->blocks(subtitle: 'Le son, la lumière'));

        $this->assertSame(['Le son, la lumière'], $this->sentTexts());
        $row = $this->row($page, 'blocks');
        $map = json_decode($row->value, true);
        $this->assertSame('Welcome', $map['hero#0:title']);
        $this->assertSame('EN: Le son, la lumière', $map['hero#0:subtitle']);
        $this->assertSame('reviewed', $row->leaves['hero#0:title']['s']);
        $this->assertSame($page->sourceHash('blocks'), $row->source_hash);
    }

    public function test_changed_reviewed_text_goes_to_previous_value_for_that_key_only(): void
    {
        $page = $this->publishedPage();
        $this->correct($page, 'hero#0:title', 'Welcome');

        $page = $this->republish($page, $this->blocks(title: 'Bonjour'));

        $row = $this->row($page, 'blocks');
        $this->assertSame(['hero#0:title' => 'Welcome'], json_decode($row->previous_value, true));
        $this->assertSame('EN: Bonjour', json_decode($row->value, true)['hero#0:title']);
        $this->assertSame('auto', $row->leaves['hero#0:title']['s']);
    }

    public function test_removed_texts_are_dropped(): void
    {
        $page = $this->publishedPage();

        $page = $this->republish($page, [$this->blocks()[0]]);

        $this->assertSame(['hero#0:title', 'hero#0:subtitle'], array_keys(json_decode($this->row($page, 'blocks')->value, true)));
        $this->assertSame(['hero#0:title', 'hero#0:subtitle'], array_keys($this->row($page, 'blocks')->leaves));
    }

    public function test_partial_correction_of_an_outdated_field_keeps_it_outdated_and_the_job_finishes_it(): void
    {
        $page = $this->publishedPage();
        Queue::fake();
        $page = $this->republish($page, $this->blocks('Bonjour', 'Le son, la lumière'));
        $this->assertContains('blocks', $page->load('translations')->outdatedFields());

        $this->correct($page, 'hero#0:subtitle', 'Sound and light');

        $page = $page->fresh();
        $this->assertContains('blocks', $page->load('translations')->outdatedFields());

        $this->translator->calls = [];
        $this->runJob($page);

        $this->assertSame(['Bonjour'], $this->sentTexts());
        $row = $this->row($page, 'blocks');
        $this->assertSame('Sound and light', json_decode($row->value, true)['hero#0:subtitle']);
        $this->assertSame('reviewed', $row->leaves['hero#0:subtitle']['s']);
        $this->assertSame([], $page->fresh()->outdatedFields());
    }

    public function test_mark_as_reviewed_does_not_freeze_texts_without_english(): void
    {
        $page = $this->publishedPage();
        Queue::fake();
        $blocks = $this->blocks();
        $blocks[] = ['type' => 'quote', 'data' => ['quote' => 'Une citation']];
        $page = $this->republish($page, $blocks);

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->callAction(TestAction::make('markReviewed')->schemaComponent('english.english-blocks'));

        $row = $this->row($page, 'blocks');
        $this->assertSame('reviewed', $row->leaves['hero#0:title']['s']);
        $this->assertArrayNotHasKey('quote#0:quote', $row->leaves);
        $this->assertContains('blocks', $page->fresh()->load('translations')->outdatedFields());

        $this->translator->calls = [];
        $this->runJob($page->fresh());
        $this->assertSame(['Une citation'], $this->sentTexts());
    }

    public function test_list_fields_retranslate_only_the_changed_element(): void
    {
        $program = Program::factory()->create(['skills' => ['Mixage', 'Prise de son']]);
        $this->translator->calls = [];

        $program->update(['skills' => ['Mixage', 'Montage']]);

        $this->assertSame(['Montage'], $this->sentTexts());
        $this->assertSame(['EN: Mixage', 'EN: Montage'], $program->fresh()->translated('skills', 'en'));
    }

    /** Un nom de marque identique en anglais est une traduction relue comme une autre. */
    public function test_english_identical_to_french_is_kept_and_reviewed(): void
    {
        $program = Program::factory()->create(['skills' => ['DaVinci Resolve', 'Montage']]);

        Livewire::test(EditProgram::class, ['record' => $program->getRouteKey()])
            ->set('data.english.skills.0', 'DaVinci Resolve') // une case à la fois, comme dans le navigateur
            ->call('save')
            ->assertHasNoFormErrors();

        $row = $this->row($program, 'skills');
        $this->assertSame(['DaVinci Resolve', 'EN: Montage'], json_decode($row->value, true));
        $this->assertSame('reviewed', $row->leaves['0']['s']);

        $page = $this->publishedPage();
        $page->forceFill(['seo' => ['title' => 'EMSI', 'description' => 'École']])->save();
        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->fillForm(['english.seo.title' => 'EMSI'])
            ->call('save')
            ->assertHasNoFormErrors();
        $seo = $this->row($page, 'seo');
        $this->assertSame('EMSI', json_decode($seo->value, true)['title']);
        $this->assertSame('reviewed', $seo->leaves['title']['s']);
    }

    // --- Français vide ---------------------------------------------------------------------------------

    public function test_empty_html_and_empty_arrays_are_never_translated(): void
    {
        $program = Program::factory()->create([
            'description' => '<p></p>', 'summary' => '<p> &nbsp; </p>', 'skills' => [null, ''],
            'seo' => ['title' => null, 'description' => ''],
        ]);

        $fields = $program->load('translations')->outdatedFields();
        foreach (['description', 'summary', 'skills', 'seo'] as $field) {
            $this->assertNotContains($field, $fields);
            $this->assertNull($this->row($program, $field), $field);
        }
        $this->assertNotContains('<p></p>', $this->sentTexts());
    }

    public function test_saving_a_program_without_changes_queues_nothing(): void
    {
        $program = Program::factory()->create(['title' => 'Son', 'skills' => ['Mixage']]);
        $this->assertSame([], $program->load('translations')->outdatedFields());
        Queue::fake();

        Livewire::test(EditProgram::class, ['record' => $program->getRouteKey()])->call('save')->assertHasNoFormErrors();

        Queue::assertNothingPushed();
    }

    // --- Enregistrement interrompu ---------------------------------------------------------------------

    public function test_corrections_of_a_failed_save_are_never_written_later(): void
    {
        $page = $this->publishedPage();
        $fail = true;
        Event::listen('eloquent.saving: '.Page::class, function () use (&$fail) {
            if ($fail) {
                throw new RuntimeException('Panne');
            }
        });

        try {
            $this->correct($page, 'hero#0:title', 'Lost correction');
            $this->fail('La sauvegarde aurait dû échouer.');
        } catch (RuntimeException $e) {
            $this->assertSame('Panne', $e->getMessage());
        }
        $fail = false;

        $this->actingAs(User::factory()->create(['role' => 'communication']));
        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->assertFormSet(['english.blocks.'.TranslationTab::slot('hero#0:title') => 'EN: Bienvenue'])
            ->call('save')->assertHasNoFormErrors();

        $this->assertSame('EN: Bienvenue', json_decode($this->row($page, 'blocks')->value, true)['hero#0:title']);
    }
}
