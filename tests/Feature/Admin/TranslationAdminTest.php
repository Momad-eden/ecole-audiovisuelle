<?php

namespace Tests\Feature\Admin;

use App\Enums\PublicationStatus;
use App\Enums\TranslationStatus;
use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\AgendaEvents\Pages\EditAgendaEvent;
use App\Filament\Resources\Artworks\Pages\EditArtwork;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\MenuItems\Pages\EditMenuItem;
use App\Filament\Resources\News\Pages\EditNews;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Places\Pages\EditPlace;
use App\Filament\Resources\Programs\Pages\EditProgram;
use App\Filament\Resources\Rooms\Pages\EditRoom;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Tracks\Pages\EditTrack;
use App\Filament\Resources\Translations\Pages\ListTranslations;
use App\Filament\Resources\Translations\TranslationResource;
use App\Filament\Support\TranslationTab;
use App\Filament\Widgets\TranslationsOverview;
use App\Jobs\TranslateRecord;
use App\Models\AgendaEvent;
use App\Models\Artwork;
use App\Models\Faq;
use App\Models\MenuItem;
use App\Models\News;
use App\Models\Page;
use App\Models\Place;
use App\Models\Program;
use App\Models\Room;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Track;
use App\Models\Translation;
use App\Models\User;
use App\Services\Translation\Translator;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\Support\FakeTranslator;
use Tests\TestCase;

/** Onglet « Anglais », liste « Traductions à relire », widget et droits (spec R2 §6). */
class TranslationAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $directeur;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directeur = User::factory()->create(['role' => 'directeur']);
        $this->actingAs($this->directeur);
    }

    private function blocks(): array
    {
        return [
            ['type' => 'hero', 'data' => ['title' => 'Bienvenue', 'subtitle' => 'Le son et l\'image']],
            ['type' => 'text', 'data' => ['body' => '<p>Un texte riche</p>']],
        ];
    }

    private function page(): Page
    {
        $draft = $this->blocks();
        $draft[0]['data']['title'] = 'Brouillon secret';

        return Page::create([
            'title' => 'Accueil', 'slug' => 'accueil', 'type' => 'free', 'status' => PublicationStatus::PUBLISHED,
            'blocks' => $this->blocks(), 'draft_blocks' => $draft, 'seo' => ['title' => 'Titre SEO', 'description' => 'Description SEO'],
        ]);
    }

    private function translate(Model $record, string $field, string $value, TranslationStatus $status = TranslationStatus::AUTO, array $extra = []): Translation
    {
        return $record->translations()->create([
            'field' => $field, 'locale' => 'en', 'value' => $value, 'status' => $status,
            'source_hash' => $record->sourceHash($field), ...$extra,
        ]);
    }

    private function row(Model $record, string $field): ?Translation
    {
        return $record->translations()->where('field', $field)->where('locale', 'en')->first();
    }

    // --- Onglet « Anglais » -------------------------------------------------------------------------

    public function test_page_editor_shows_english_tab_with_published_block_texts(): void
    {
        $page = $this->page();

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->assertOk()
            ->assertSee('Anglais')
            ->assertSee('Bloc 1 · Grand titre (héros) · Titre')
            ->assertSee('Bloc 2 · Texte · Texte')
            ->assertSee('Bienvenue')
            ->assertSee('Pas encore traduit')
            ->assertDontSee('Brouillon secret');
    }

    public function test_english_tab_shows_status_and_previous_reviewed_version(): void
    {
        $page = $this->page();
        $this->translate($page, 'title', 'Home', TranslationStatus::AUTO, ['previous_value' => 'Homepage']);

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->assertFormSet(['english.title' => 'Home'])
            ->assertSee('Traduction automatique')
            ->assertSee('Ancienne version relue')
            ->assertSee('Homepage');
    }

    public function test_saving_an_english_block_text_marks_it_reviewed_under_its_stable_key(): void
    {
        Queue::fake();
        $page = $this->page();
        $this->translate($page, 'blocks', json_encode(['hero#0:title' => 'Welcome', 'hero#0:subtitle' => 'Sound and image']));

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->assertFormSet(['english.blocks.'.TranslationTab::slot('hero#0:title') => 'Welcome'])
            ->fillForm(['english.blocks.'.TranslationTab::slot('hero#0:title') => 'Hello and welcome'])
            ->call('save')
            ->assertHasNoFormErrors();

        $row = $this->row($page, 'blocks');
        // Seul le texte corrigé est « Relue » ; l'autre reste automatique, donc la ligne aussi (état par texte).
        $this->assertSame('reviewed', $row->leaves['hero#0:title']['s']);
        $this->assertSame('auto', $row->leaves['hero#0:subtitle']['s']);
        $this->assertSame(TranslationStatus::AUTO, $row->status);
        $this->assertSame(['hero#0:title' => 'Hello and welcome', 'hero#0:subtitle' => 'Sound and image'], json_decode($row->value, true));
        $this->assertSame($this->directeur->id, $row->reviewed_by);
        $this->assertNotNull($row->reviewed_at);
        $this->assertSame($page->fresh()->sourceHash('blocks'), $row->source_hash);
        Queue::assertNotPushed(TranslateRecord::class);
    }

    public function test_saving_a_rich_block_text_keeps_html(): void
    {
        $page = $this->page();

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->fillForm(['english.blocks.'.TranslationTab::slot('text#0:body') => '<p>A rich text</p>'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(['text#0:body' => '<p>A rich text</p>'], json_decode($this->row($page, 'blocks')->value, true));
    }

    public function test_saving_without_touching_english_leaves_translations_untouched(): void
    {
        $page = $this->page();
        $title = $this->translate($page, 'title', 'Home');
        $blocks = $this->translate($page, 'blocks', json_encode(['text#0:body' => '<p>Some <strong>rich</strong> text</p>']));

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])->call('save')->assertHasNoFormErrors();

        $this->assertSame(TranslationStatus::AUTO, $title->fresh()->status);
        $this->assertSame(TranslationStatus::AUTO, $blocks->fresh()->status);
        $this->assertSame($blocks->value, $blocks->fresh()->value);
    }

    public function test_a_translation_written_while_the_form_was_open_is_not_erased(): void
    {
        $page = $this->page();
        $component = Livewire::test(EditPage::class, ['record' => $page->getRouteKey()]);

        $this->translate($page, 'title', 'Home'); // la tâche automatique termine pendant la saisie
        $component->call('save')->assertHasNoFormErrors();

        $this->assertSame('Home', $this->row($page, 'title')->value);
    }

    public function test_clearing_an_english_text_falls_back_to_french(): void
    {
        $page = $this->page();
        $this->translate($page, 'title', 'Home');
        $this->translate($page, 'blocks', json_encode(['hero#0:title' => 'Welcome', 'hero#0:subtitle' => 'Sound']));

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->fillForm(['english.title' => '', 'english.blocks.'.TranslationTab::slot('hero#0:title') => ''])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull($this->row($page, 'title'));
        $this->assertSame(['hero#0:subtitle' => 'Sound'], json_decode($this->row($page, 'blocks')->value, true));
    }

    public function test_seo_translation_keeps_the_other_seo_settings(): void
    {
        $page = $this->page();

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->fillForm(['english.seo.title' => 'SEO title'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(['title' => 'SEO title', 'description' => 'Description SEO'], json_decode($this->row($page, 'seo')->value, true));
    }

    public function test_list_fields_have_one_input_per_element(): void
    {
        $program = Program::factory()->create(['skills' => ['Mixage', 'Prise de son']]);

        Livewire::test(EditProgram::class, ['record' => $program->getRouteKey()])
            ->assertSee('Prise de son')
            ->fillForm(['english.skills.1' => 'Sound recording'])
            ->call('save')
            ->assertHasNoFormErrors();

        $row = $this->row($program, 'skills');
        $this->assertSame(['Mixage', 'Sound recording'], json_decode($row->value, true));
        $this->assertSame(TranslationStatus::REVIEWED, $row->status);
    }

    public function test_mark_as_reviewed_keeps_the_text(): void
    {
        $page = $this->page();
        $row = $this->translate($page, 'title', 'Home');

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->callAction(TestAction::make('markReviewed')->schemaComponent('english.english-title'))
            ->assertNotified();

        $row->refresh();
        $this->assertSame(TranslationStatus::REVIEWED, $row->status);
        $this->assertSame('Home', $row->value);
        $this->assertSame($this->directeur->id, $row->reviewed_by);
    }

    public function test_retranslate_queues_the_record_for_that_field(): void
    {
        Queue::fake();
        $this->app->instance(Translator::class, new FakeTranslator);
        $page = $this->page();
        $row = $this->translate($page, 'title', 'Home', TranslationStatus::REVIEWED);

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->callAction(TestAction::make('retranslate')->schemaComponent('english.english-title'))
            ->assertNotified();

        Queue::assertPushed(TranslateRecord::class, fn (TranslateRecord $job) => $job->record->is($page));
        $this->assertNull($row->fresh()->source_hash);
        $this->assertContains('title', $page->fresh()->load('translations')->outdatedFields('en'));
    }

    public function test_retranslate_is_disabled_without_automatic_translation(): void
    {
        $page = $this->page();

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->assertActionDisabled(TestAction::make('retranslate')->schemaComponent('english.english-title'));
    }

    public function test_settings_have_an_english_section(): void
    {
        Setting::current()->update(['description' => 'École de Dakar']);

        Livewire::test(SiteSettings::class)
            ->assertSee('Anglais')
            ->fillForm(['english.description' => 'Dakar school'])
            ->call('save')
            ->assertHasNoFormErrors();

        $row = $this->row(Setting::current(), 'description');
        $this->assertSame('Dakar school', $row->value);
        $this->assertSame(TranslationStatus::REVIEWED, $row->status);
    }

    public function test_every_translated_resource_renders_its_english_tab(): void
    {
        $records = [
            EditProgram::class => Program::factory()->create(),
            EditTrack::class => Track::factory()->create(),
            EditRoom::class => Room::create(['name' => 'Son', 'slug' => 'son', 'tagline' => 'Le son']),
            EditArtwork::class => Artwork::create(['title' => 'Œuvre', 'slug' => 'oeuvre', 'kind' => 'audio', 'summary' => 'Résumé']),
            EditNews::class => News::create(['title' => 'Actu', 'slug' => 'actu', 'content' => '<p>Texte</p>']),
            EditFaq::class => Faq::create(['group' => 'general', 'question' => 'Pourquoi ?', 'answer' => 'Parce que.']),
            EditAgendaEvent::class => AgendaEvent::create(['title' => 'Concert', 'slug' => 'concert', 'activity' => 'events']),
            EditService::class => Service::create(['name' => 'Mixage', 'slug' => 'mixage', 'activity' => 'studio']),
            EditPlace::class => Place::create(['name' => 'Dakar', 'slug' => 'dakar', 'kind' => 'campus', 'tagline' => 'Au Grand Théâtre']),
            EditMenuItem::class => MenuItem::create(['label' => 'Accueil', 'url' => '/', 'location' => 'main']),
        ];

        foreach ($records as $page => $record) {
            Livewire::test($page, ['record' => $record->getRouteKey()])->assertOk()->assertSee('Anglais')->assertSee('Pas encore traduit');
        }
    }

    public function test_translated_fields_match_the_models(): void
    {
        foreach (TranslationTab::FIELDS as $type => $fields) {
            $model = Relation::getMorphedModel($type);
            $this->assertSame((new $model)->translatableFields(), array_keys($fields), $type);
        }
        $this->assertCount(count(TranslationTab::FIELDS), TranslationResource::TYPES);
    }

    public function test_english_tab_can_be_opened_from_a_link(): void
    {
        $page = $this->page();

        $this->get(PageResource::getUrl('edit', ['record' => $page, 'tab' => TranslationTab::TAB_QUERY]))
            ->assertOk()->assertSee(TranslationTab::TAB_QUERY, false);
    }

    // --- Liste « Traductions à relire » -----------------------------------------------------------

    public function test_list_shows_automatic_and_failed_translations_by_default(): void
    {
        $page = $this->page();
        $auto = $this->translate($page, 'title', 'Home');
        $failed = $this->translate($page, 'seo', '{}', TranslationStatus::FAILED);
        $reviewed = $this->translate($page, 'blocks', '{}', TranslationStatus::REVIEWED);

        Livewire::test(ListTranslations::class)
            ->assertCanSeeTableRecords([$auto, $failed])
            ->assertCanNotSeeTableRecords([$reviewed])
            ->assertSee('Accueil')
            ->assertSee('Titre');

        $this->assertStringContainsString('tab=anglais', TranslationResource::recordUrl($auto));
        $this->assertSame(2, (int) TranslationResource::getNavigationBadge());
    }

    // --- Droits -------------------------------------------------------------------------------------

    public function test_only_directeur_and_communication_edit_translations(): void
    {
        foreach (['directeur' => true, 'communication' => true, 'gestionnaire' => false, 'secretaire' => false, 'commercial' => false] as $role => $allowed) {
            $this->assertSame($allowed, Gate::forUser(User::factory()->create(['role' => $role]))->allows('manage', Translation::class), $role);
        }
    }

    public function test_secretaire_sees_the_list_read_only_and_commercial_does_not(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'secretaire']));
        $this->get(TranslationResource::getUrl('index'))->assertOk()->assertSee('Traductions à relire');

        $this->actingAs(User::factory()->create(['role' => 'commercial']));
        $this->get(TranslationResource::getUrl('index'))->assertForbidden();
        $this->assertFalse(TranslationsOverview::canView());
    }

    public function test_a_role_without_translation_rights_cannot_change_english(): void
    {
        Queue::fake();
        $this->app->instance(Translator::class, new FakeTranslator);
        $program = Program::factory()->create(['title' => 'Son']);
        $this->translate($program, 'title', 'Sound');
        $this->actingAs(User::factory()->create(['role' => 'gestionnaire']));

        Livewire::test(EditProgram::class, ['record' => $program->getRouteKey()])
            ->assertSee('Anglais')
            ->assertFormFieldIsDisabled('english.english-title.title')
            ->assertActionDoesNotExist(TestAction::make('markReviewed')->schemaComponent('english.english-title'))
            ->assertActionDoesNotExist(TestAction::make('retranslate')->schemaComponent('english.english-title'))
            ->set('data.english.title', 'Hacked')
            ->call('save')
            ->assertHasNoFormErrors();

        $row = $this->row($program, 'title');
        $this->assertSame('Sound', $row->value);
        $this->assertSame(TranslationStatus::AUTO, $row->status);
    }

    // --- Widget ---------------------------------------------------------------------------------------

    public function test_widget_without_key_says_not_configured(): void
    {
        $page = $this->page();
        $this->translate($page, 'title', 'Home');
        $this->translate($page, 'seo', '{}', TranslationStatus::FAILED);

        Livewire::test(TranslationsOverview::class)
            ->assertSee('À relire')
            ->assertSee('En échec')
            ->assertSee('Traduction automatique non configurée');
    }

    public function test_widget_shows_the_monthly_quota(): void
    {
        $this->app->instance(Translator::class, new FakeTranslator);
        Http::fake(['api-free.deepl.com/v2/usage' => Http::response(['character_count' => 1234, 'character_limit' => 500000])]);

        Livewire::test(TranslationsOverview::class)->assertSeeInOrder(['Quota DeepL', '1 234 / 500 000', 'caractères ce mois-ci']);
    }

    public function test_widget_survives_an_unreachable_quota(): void
    {
        $this->app->instance(Translator::class, new FakeTranslator);
        Http::fake(['api-free.deepl.com/v2/usage' => Http::response([], 500)]);

        Livewire::test(TranslationsOverview::class)->assertSee('Quota indisponible pour le moment');
    }
}
