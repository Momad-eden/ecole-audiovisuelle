<?php

namespace Tests\Feature;

use App\Enums\PublicationStatus;
use App\Enums\SiteDomain;
use App\Models\Cohort;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\PageRevision;
use App\Models\Place;
use App\Models\Program;
use App\Models\Redirect;
use App\Models\Setting;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** emsi:site-v4 : du site EMSI au site des trois domaines, sans perte et relançable. */
class SiteV4CommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Une base comme celle de l'école : accueil, école, studio, events, Espace Habib Faye, professionnels…
        $this->seed(ContentSeeder::class);
    }

    public function test_pages_are_moved_with_their_blocks_and_history(): void
    {
        $moves = ['studio' => ['maison-habib-faye/studio', SiteDomain::STUDIO], 'ecole' => ['emsi', SiteDomain::EMSI],
            'espace-habib-faye' => ['maison-habib-faye', SiteDomain::MAISON], 'professionnels' => ['emsi/professionnels', SiteDomain::EMSI]];
        $before = collect($moves)->map(fn ($target, $slug) => Page::where('slug', $slug)->firstOrFail());
        $revisionIds = $before->map(fn (Page $page) => $page->revisions()->pluck('id')->sort()->values()->all());

        $this->artisan('emsi:site-v4')->assertSuccessful();

        foreach ($moves as $slug => [$target, $domain]) {
            $this->assertFalse(Page::where('slug', $slug)->exists(), $slug);
            $page = Page::where('slug', $target)->firstOrFail();
            $old = $before[$slug];
            $this->assertSame($old->id, $page->id, $slug);
            $this->assertSame($domain, $page->domain, $slug);
            $this->assertSame($old->status, $page->status);
            // La page EMSI gagne les univers et les réalisations ; le lieu du Grand Théâtre part sur /emsi/dakar.
            $types = fn (array $blocks) => array_values(array_diff(array_column($blocks, 'type'), $slug === 'ecole' ? ['rooms', 'artworks', 'venue'] : []));
            $this->assertSame($types($old->blocks), $types($page->blocks), $slug);
            $this->assertEmpty(array_diff($revisionIds[$slug], $page->revisions()->pluck('id')->all()), $slug);
        }
        $this->getJson('/api/v1/public/pages/maison-habib-faye/studio')->assertOk()->assertJsonPath('data.domain', 'studio');
        $this->getJson('/api/v1/public/pages/emsi/professionnels')->assertOk()->assertJsonPath('data.domain', 'emsi');

        // Pages créées, publiées, avec leurs blocs de départ.
        $created = ['maison-habib-faye/agenda' => 'maison', 'maison-habib-faye/espaces' => 'maison', 'emsi/dakar' => 'emsi', 'emsi/saint-louis' => 'emsi',
            'mission' => 'general', 'partenaires' => 'general', 'soutenir' => 'general', 'presse' => 'general'];
        foreach ($created as $slug => $domain) {
            $page = Page::where('slug', $slug)->firstOrFail();
            $this->assertTrue($page->isPublished(), $slug);
            $this->assertSame($domain, $page->domain->value, $slug);
        }
        $types = fn (string $slug) => array_column(Page::where('slug', $slug)->firstOrFail()->blocks, 'type');
        $dakar = Page::where('slug', 'emsi/dakar')->firstOrFail()->blocks;
        $this->assertSame(Place::where('slug', 'emsi-dakar')->value('id'), collect($dakar)->firstWhere('type', 'campus_programs')['data']['campus_id']);
        $this->assertContains('venue', array_column($dakar, 'type'));
        $this->assertContains('partners', array_column($dakar, 'type'));
        $this->assertSame(Place::where('slug', 'emsi-saint-louis')->value('id'),
            collect(Page::where('slug', 'emsi/saint-louis')->firstOrFail()->blocks)->firstWhere('type', 'campus_programs')['data']['campus_id']);
        $this->assertContains('places', $types('maison-habib-faye/espaces'));
        $this->assertSame('space_rental', collect(Page::where('slug', 'maison-habib-faye/espaces')->first()->blocks)->firstWhere('type', 'booking_form')['data']['booking_type']);
        $this->assertContains('agenda', $types('maison-habib-faye/agenda'));
        $this->assertContains('support_form', $types('soutenir'));
        // Pas de bloc « Documents » vide : l'admin exige un document, la page ne pourrait plus être enregistrée.
        $this->assertNotContains('downloads', $types('presse'));
        $this->assertStringContainsString('Documents à télécharger', json_encode(Page::where('slug', 'presse')->first()->blocks, JSON_UNESCAPED_UNICODE));
        $this->assertStringContainsString('À compléter', json_encode(Page::where('slug', 'mission')->first()->blocks, JSON_UNESCAPED_UNICODE));

        // Impact Live Events : page gardée mais plus servie.
        $this->assertSame(PublicationStatus::DRAFT, Page::where('slug', 'events')->firstOrFail()->status);
        $this->getJson('/api/v1/public/pages/events')->assertNotFound();

        // Formations : les deux campus cochés ; sessions laissées sur « les deux campus ».
        $campusIds = Place::campuses()->orderBy('id')->pluck('id')->all();
        Program::all()->each(fn (Program $program) => $this->assertSame($campusIds, $program->campuses()->orderBy('places.id')->pluck('places.id')->all()));
        $this->assertSame(0, Cohort::whereNotNull('place_id')->count());

        // Redirections de l'admin : plus de chaîne vers une ancienne adresse.
        $this->assertSame('/emsi/professionnels', Redirect::where('from_path', '/projet')->value('to_path'));
    }

    public function test_running_twice_gives_the_same_result(): void
    {
        $this->artisan('emsi:site-v4', ['--home' => true])->assertSuccessful();
        $first = $this->snapshot();

        $this->artisan('emsi:site-v4', ['--home' => true])->assertSuccessful();
        $this->artisan('emsi:site-v4')->assertSuccessful();

        $this->assertSame($first, $this->snapshot());
    }

    public function test_a_page_already_at_the_target_address_is_not_overwritten(): void
    {
        $maison = Page::create(['title' => 'La Maison', 'slug' => 'maison-habib-faye', 'type' => 'free', 'domain' => 'maison',
            'draft_blocks' => [['type' => 'text', 'data' => ['title' => 'Écrit par l\'équipe', 'body' => '<p>Notre maison.</p>']]]]);
        $maison->publish();
        $blocks = $maison->fresh()->blocks;

        $this->artisan('emsi:site-v4')->expectsOutputToContain('« /maison-habib-faye » est déjà prise')->assertSuccessful();

        $this->assertSame($blocks, $maison->fresh()->blocks);
        $this->assertSame(1, $maison->revisions()->count());
        $this->assertTrue(Page::where('slug', 'espace-habib-faye')->exists());

        // L'équipe retouche une page créée par la commande : une nouvelle exécution n'y touche pas.
        $mission = Page::where('slug', 'mission')->firstOrFail();
        $mission->update(['title' => 'Notre mission', 'draft_blocks' => [['type' => 'text', 'data' => ['title' => 'Mission', 'body' => '<p>Texte définitif.</p>']]]]);
        $mission->publish();
        $revisions = PageRevision::count();

        MenuItem::where('location', 'main')->where('label', 'Programmation')->update(['label' => 'Agenda']);
        MenuItem::where('location', 'footer')->where('label', 'Presse')->update(['is_visible' => false]);

        $this->artisan('emsi:site-v4')->expectsOutputToContain('pages, menus et campus des formations ne sont plus modifiés')->assertSuccessful();

        $this->assertTrue(MenuItem::where('location', 'main')->where('label', 'Agenda')->where('url', '/maison-habib-faye/agenda')->exists());
        $this->assertFalse(MenuItem::where('location', 'main')->where('label', 'Programmation')->exists());
        $this->assertFalse(MenuItem::where('location', 'footer')->where('label', 'Presse')->value('is_visible'));
        $this->assertSame('<p>Texte définitif.</p>', $mission->fresh()->blocks[0]['data']['body']);
        $this->assertSame('Notre mission', $mission->fresh()->title);
        $this->assertSame($revisions, PageRevision::count());
    }

    public function test_home_is_only_rebuilt_with_the_home_option(): void
    {
        $home = Page::where('slug', 'accueil')->firstOrFail();
        $types = array_column($home->blocks, 'type');

        $this->artisan('emsi:site-v4')->assertSuccessful();

        $home->refresh();
        $this->assertSame($types, array_column($home->blocks, 'type'));
        $this->assertSame('Dakar · Saint-Louis', $home->blocks[0]['data']['eyebrow']);
        // « Choisir mon univers » (/univers) mène au bloc des univers de la page EMSI.
        $this->assertSame('/emsi#univers', $home->blocks[0]['data']['buttons'][0]['url']);

        // Photos existantes réutilisées pour les trois diapositives.
        $this->setHeroImage('accueil', 'pages/accueil.jpg');
        $this->setHeroImage('maison-habib-faye', 'pages/maison.jpg');
        $this->setHeroImage('maison-habib-faye/studio', 'pages/studio.jpg');
        $previous = $home->fresh()->blocks;

        $this->artisan('emsi:site-v4', ['--home' => true])->assertSuccessful();

        $home->refresh();
        $this->assertSame(['hero', 'domains', 'stats', 'agenda', 'news', 'partners'], array_column($home->blocks, 'type'));
        $hero = $home->blocks[0]['data'];
        $this->assertSame('cinema', $hero['layout']);
        $this->assertSame(['/maison-habib-faye', '/emsi', '/maison-habib-faye/studio'], array_column($hero['slides'], 'link_url'));
        $this->assertSame(['pages/maison.jpg', 'pages/accueil.jpg', 'pages/studio.jpg'], array_column($hero['slides'], 'image'));
        $panels = $home->blocks[1]['data']['panels'];
        $this->assertSame(['maison', 'emsi', 'studio'], array_column($panels, 'domain'));
        $this->assertSame(['/maison-habib-faye', '/emsi', '/maison-habib-faye/studio'], array_column($panels, 'url'));
        $this->assertSame($home->blocks, $home->draft_blocks);
        $this->assertTrue($home->revisions()->get()->contains(fn (PageRevision $revision) => $revision->blocks === $previous));
        $this->getJson('/api/v1/public/pages/accueil')->assertOk()->assertJsonPath('data.blocks.1.type', 'domains');
    }

    public function test_the_new_home_is_built_from_the_live_version_and_the_pending_draft_is_kept(): void
    {
        $home = Page::where('slug', 'accueil')->firstOrFail();
        $live = $home->blocks;
        $draft = $live;
        array_unshift($draft, ['type' => 'news', 'data' => ['title' => 'Brouillon non relu', 'limit' => 3]]);
        $home->update(['draft_blocks' => $draft]);

        $this->artisan('emsi:site-v4', ['--home' => true])->assertSuccessful();

        $home->refresh();
        $this->assertStringNotContainsString('Brouillon non relu', json_encode($home->blocks, JSON_UNESCAPED_UNICODE));
        $this->assertSame(collect($live)->firstWhere('type', 'news'), collect($home->blocks)->firstWhere('type', 'news'));
        $kept = $home->revisions()->get()->first(fn (PageRevision $revision) => str_contains(json_encode($revision->blocks, JSON_UNESCAPED_UNICODE), 'Brouillon non relu'));
        $this->assertNotNull($kept);
        $this->assertSame('Brouillon non relu', $kept->blocks[0]['data']['title']);
    }

    public function test_the_public_site_is_refreshed_once_after_the_upgrade(): void
    {
        config(['services.frontend.url' => 'https://emsi.test', 'services.frontend.revalidate_secret' => 'secret']);
        Http::fake(['emsi.test/*' => Http::response(['revalidated' => true])]);

        $this->artisan('emsi:site-v4', ['--home' => true])->assertSuccessful();

        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request->url() === 'https://emsi.test/api/revalidate' && $request['tags'] === ['content']);
    }

    public function test_an_unreachable_site_only_gives_a_warning(): void
    {
        config(['services.frontend.url' => 'https://emsi.test', 'services.frontend.revalidate_secret' => 'secret']);
        Http::fake(['emsi.test/*' => Http::response('down', 502)]);

        $this->artisan('emsi:site-v4')->expectsOutputToContain('pas pu être rafraîchi')->assertSuccessful();

        $this->assertTrue(Page::where('slug', 'emsi/dakar')->exists());
    }

    public function test_without_photos_the_home_opens_on_the_three_domains(): void
    {
        $this->artisan('emsi:site-v4', ['--home' => true])->expectsOutputToContain('photo')->assertSuccessful();

        $types = array_column(Page::where('slug', 'accueil')->firstOrFail()->blocks, 'type');
        $this->assertSame(['domains', 'stats', 'agenda', 'news', 'partners'], $types);
    }

    public function test_main_menu_has_maison_and_emsi_with_their_children(): void
    {
        $before = MenuItem::count();

        $this->artisan('emsi:site-v4')->assertSuccessful();

        $visible = fn (string $location, ?int $parent = null) => MenuItem::where('location', $location)->where('is_visible', true)
            ->where('parent_id', $parent)->orderBy('position')->get();
        $main = $visible('main');
        $this->assertSame(['Accueil', 'Maison Habib Faye', 'EMSI', 'À propos', 'Candidater'], $main->pluck('label')->all());
        $this->assertSame(['/', '/maison-habib-faye', '/emsi', '/mission', '/candidater'], $main->pluck('url')->all());
        $this->assertTrue($main->last()->is_button);
        $this->assertSame(['La Maison', 'Programmation', 'Impact Live Studio', 'Les espaces'], $visible('main', $main[1]->id)->pluck('label')->all());
        $this->assertSame(['/maison-habib-faye', '/maison-habib-faye/agenda', '/maison-habib-faye/studio', '/maison-habib-faye/espaces'], $visible('main', $main[1]->id)->pluck('url')->all());
        $this->assertSame(['L\'école', 'Campus de Dakar', 'Campus de Saint-Louis', 'Formations', 'VAE et professionnels', 'Réalisations'], $visible('main', $main[2]->id)->pluck('label')->all());
        $this->assertSame(['/emsi', '/emsi/dakar', '/emsi/saint-louis', '/emsi/formations', '/emsi/professionnels', '/emsi/realisations'], $visible('main', $main[2]->id)->pluck('url')->all());
        $this->assertSame(['Mission et impact', 'Partenaires et soutiens', 'Nous soutenir', 'Actualités', 'Presse', 'Contact'], $visible('main', $main[3]->id)->pluck('label')->all());
        $this->assertSame('Toutes les formations, campus par campus', $visible('main', $main[2]->id)[3]->description);
        $this->assertSame(['Mission et impact', 'Partenaires et soutiens', 'Nous soutenir', 'Actualités', 'Presse', 'Contact'], $visible('footer')->pluck('label')->all());
        $this->assertSame(['Mentions légales', 'Protection des données'], $visible('legal')->pluck('label')->all());

        // Rien n'est supprimé : les anciennes entrées sont masquées.
        $this->assertGreaterThanOrEqual($before, MenuItem::count());
        $this->assertFalse(MenuItem::where('label', 'Events')->firstOrFail()->is_visible);
        $this->getJson('/api/v1/public/site')->assertOk();
    }

    public function test_links_to_events_are_rewritten(): void
    {
        $page = Page::create(['title' => 'Nos tarifs', 'slug' => 'nos-tarifs', 'type' => 'free', 'draft_blocks' => [
            ['type' => 'hero', 'data' => ['eyebrow' => 'Tarifs · Dakar · Grand Théâtre National', 'title' => 'Nos tarifs', 'layout' => 'compact',
                'buttons' => [['label' => 'Le matériel', 'url' => '/events/materiel', 'style' => 'primary'], ['label' => 'Réserver', 'url' => '/studio#reserver', 'style' => 'secondary']]]],
            ['type' => 'cards', 'data' => ['title' => 'Formations', 'items' => [['title' => 'Lumière', 'url' => '/formations/technicien-lumiere']]]],
            ['type' => 'text', 'data' => ['title' => 'Grand Théâtre National', 'body' => '<p>Voir <a href="/events#devis">nos prestations</a>.</p>']],
        ]]);
        $page->publish();
        // Modification en cours, non publiée : la commande ne la publie pas à la place de l'équipe.
        $draft = $page->draft_blocks;
        $draft[] = ['type' => 'cta', 'data' => ['title' => 'Brouillon', 'buttons' => [['label' => 'Agenda', 'url' => '/agenda', 'style' => 'primary']]]];
        $page->update(['draft_blocks' => $draft]);
        MenuItem::create(['location' => 'footer', 'label' => 'Location de sono', 'url' => '/events#devis', 'position' => 9, 'is_visible' => true]);

        $this->artisan('emsi:site-v4')->assertSuccessful();

        $page->refresh();
        $this->assertSame('Tarifs · Dakar · Saint-Louis', $page->blocks[0]['data']['eyebrow']);
        $this->assertSame(['/maison-habib-faye', '/maison-habib-faye/studio#reserver'], array_column($page->blocks[0]['data']['buttons'], 'url'));
        $this->assertSame('/emsi/formations/technicien-lumiere', $page->blocks[1]['data']['items'][0]['url']);
        $this->assertSame('Grand Théâtre National', $page->blocks[2]['data']['title']);
        $this->assertSame('<p>Voir <a href="/maison-habib-faye">nos prestations</a>.</p>', $page->blocks[2]['data']['body']);
        $this->assertCount(3, $page->blocks);
        $this->assertCount(4, $page->draft_blocks);
        $this->assertSame('/maison-habib-faye/agenda', $page->draft_blocks[3]['data']['buttons'][0]['url']);
        $this->assertSame('/maison-habib-faye', MenuItem::where('label', 'Location de sono')->value('url'));

        // Plus aucun lien vers Impact Live Events ni vers une ancienne adresse sur les pages en ligne.
        $live = json_encode(Page::published()->pluck('blocks'), JSON_UNESCAPED_SLASHES);
        foreach (['"/events', '"/studio', '"/ecole', '"/formations', '"/professionnels', '"/espace-habib-faye', '"/agenda', '"/univers'] as $old) {
            $this->assertStringNotContainsString($old, $live, $old);
        }
        $this->assertSame(0, MenuItem::where('is_visible', true)->where('url', 'like', '/events%')->count());
    }

    public function test_the_upgrade_is_recorded_and_a_later_run_recreates_nothing(): void
    {
        $this->artisan('emsi:site-v4', ['--home' => true])->assertSuccessful();
        $this->assertSame(4, (int) Setting::current()->site_version);

        // L'équipe renomme des pages, change le menu et le pied de page, décoche une formation de tous les campus.
        Page::where('slug', 'presse')->update(['slug' => 'espace-presse']);
        Page::where('slug', 'emsi/dakar')->update(['slug' => 'emsi/campus-dakar']);
        $maison = MenuItem::where('location', 'main')->whereNull('parent_id')->where('url', '/maison-habib-faye')->firstOrFail();
        $maison->update(['label' => 'La Maison', 'url' => '/la-maison']);
        $added = MenuItem::create(['location' => 'main', 'parent_id' => $maison->id, 'label' => 'Résidences', 'url' => '/maison-habib-faye/residences', 'position' => 9]);
        MenuItem::where('location', 'footer')->where('url', '/mission')->delete();
        $program = Program::orderBy('id')->firstOrFail();
        $program->campuses()->detach();
        $pages = Page::count();
        $menus = MenuItem::orderBy('id')->get(['id', 'parent_id', 'label', 'url', 'position', 'is_visible'])->toArray();

        $this->artisan('emsi:site-v4', ['--home' => true])->expectsOutputToContain('--force')->assertSuccessful();

        $this->assertSame($pages, Page::count());
        $this->assertFalse(Page::where('slug', 'presse')->exists());
        $this->assertFalse(Page::where('slug', 'emsi/dakar')->exists());
        $this->assertSame($menus, MenuItem::orderBy('id')->get(['id', 'parent_id', 'label', 'url', 'position', 'is_visible'])->toArray());
        $this->assertTrue($added->fresh()->is_visible);
        $this->assertSame(0, $program->campuses()->count());
    }

    public function test_force_runs_the_whole_upgrade_again(): void
    {
        $this->artisan('emsi:site-v4')->assertSuccessful();
        Page::where('slug', 'presse')->delete();
        MenuItem::where('location', 'footer')->where('url', '/mission')->delete();

        $this->artisan('emsi:site-v4', ['--force' => true])->assertSuccessful();

        $this->assertTrue(Page::where('slug', 'presse')->exists());
        $this->assertTrue(MenuItem::where('location', 'footer')->where('url', '/mission')->where('is_visible', true)->exists());
    }

    public function test_an_upgraded_base_without_the_marker_is_recognised_and_its_emsi_page_completed(): void
    {
        // Base mise à niveau par la première version de la commande (celle de l'école) : pas de repère,
        // page EMSI sans univers ni réalisations et avec le lieu du Grand Théâtre, déjà copié sur /emsi/dakar.
        $this->artisan('emsi:site-v4', ['--home' => true])->assertSuccessful();
        Setting::current()->forceFill(['site_version' => null])->save();
        $emsi = Page::where('slug', 'emsi')->firstOrFail();
        $old = collect($emsi->blocks)->reject(fn ($b) => in_array($b['type'], ['rooms', 'artworks'], true))->values()->all();
        $old[] = ['type' => 'venue', 'data' => ['title' => 'Au cœur du Grand Théâtre National']];
        $emsi->forceFill(['blocks' => $old, 'draft_blocks' => $old])->save();
        $emsi->revisions()->delete();
        $emsi->revisions()->create(['title' => $emsi->title, 'blocks' => $old]);
        Page::where('slug', 'presse')->update(['slug' => 'espace-presse']);
        MenuItem::where('location', 'main')->whereNull('parent_id')->where('url', '/maison-habib-faye')->update(['url' => '/la-maison']);
        $menus = MenuItem::orderBy('id')->get(['id', 'parent_id', 'label', 'url', 'position', 'is_visible'])->toArray();
        // Les univers de l'ancien accueil, retouchés par l'équipe, sont repris tels quels.
        $home = Page::where('slug', 'accueil')->firstOrFail();
        $rooms = $home->revisions()->get()->map(fn (PageRevision $r) => collect($r->blocks)->firstWhere('type', 'rooms'))->filter()->first();
        $this->assertNotNull($rooms);

        $this->artisan('emsi:site-v4', ['--home' => true])->expectsOutputToContain('déjà au format des trois domaines')->assertSuccessful();

        $this->assertSame(4, (int) Setting::current()->site_version);
        $this->assertFalse(Page::where('slug', 'presse')->exists());
        $this->assertSame($menus, MenuItem::orderBy('id')->get(['id', 'parent_id', 'label', 'url', 'position', 'is_visible'])->toArray());
        $emsi->refresh();
        $types = array_column($emsi->blocks, 'type');
        $this->assertContains('rooms', $types);
        $this->assertContains('artworks', $types);
        $this->assertNotContains('venue', $types);
        $this->assertSame($rooms['data'], collect($emsi->blocks)->firstWhere('type', 'rooms')['data']);
        $this->assertSame($emsi->blocks, $emsi->draft_blocks);
        $this->assertSame(2, $emsi->revisions()->count());
        $this->assertContains('venue', array_column(Page::where('slug', 'emsi/dakar')->firstOrFail()->blocks, 'type'));

        // Relancée : plus rien ne change.
        $revisions = PageRevision::count();
        $this->artisan('emsi:site-v4', ['--home' => true])->assertSuccessful();
        $this->assertSame($revisions, PageRevision::count());
    }

    public function test_the_emsi_page_shows_universes_artworks_and_leaves_the_venue_to_dakar(): void
    {
        $this->artisan('emsi:site-v4', ['--home' => true])->assertSuccessful();

        $types = array_column(Page::where('slug', 'emsi')->firstOrFail()->blocks, 'type');
        $this->assertContains('rooms', $types);
        $this->assertContains('artworks', $types);
        $this->assertNotContains('venue', $types);
        // Univers et réalisations juste après les cartes des campus.
        $at = array_search('campuses', $types, true);
        $this->assertSame(['rooms', 'artworks'], array_slice($types, $at + 1, 2));
        $this->assertContains('venue', array_column(Page::where('slug', 'emsi/dakar')->firstOrFail()->blocks, 'type'));
        $this->getJson('/api/v1/public/pages/emsi')->assertOk()->assertJsonFragment(['type' => 'rooms']);
    }

    public function test_emsi_completion_respects_a_pending_draft_and_a_removal_by_the_team(): void
    {
        $this->artisan('emsi:site-v4')->assertSuccessful();
        Setting::current()->forceFill(['site_version' => null])->save();
        $emsi = Page::where('slug', 'emsi')->firstOrFail();
        $live = collect($emsi->blocks)->reject(fn ($b) => in_array($b['type'], ['rooms', 'artworks'], true))->values()->all();
        $emsi->revisions()->delete();
        $draft = [...$live, ['type' => 'text', 'data' => ['title' => 'Brouillon de l\'équipe', 'body' => '<p>En cours.</p>']]];
        $emsi->forceFill(['blocks' => $live, 'draft_blocks' => $draft])->save();

        $this->artisan('emsi:site-v4')->assertSuccessful();

        $emsi->refresh();
        $this->assertContains('rooms', array_column($emsi->blocks, 'type'));
        $this->assertContains('rooms', array_column($emsi->draft_blocks, 'type'));
        $titles = fn (array $blocks) => array_column(array_column($blocks, 'data'), 'title');
        $this->assertContains('Brouillon de l\'équipe', $titles($emsi->draft_blocks));
        $this->assertNotContains('Brouillon de l\'équipe', $titles($emsi->blocks));

        // L'équipe retire ensuite les univers de la page : une nouvelle exécution ne les remet pas.
        $emsi->update(['draft_blocks' => collect($emsi->draft_blocks)->reject(fn ($b) => $b['type'] === 'rooms')->values()->all()]);
        $emsi->publish();

        $this->artisan('emsi:site-v4')->assertSuccessful();

        $this->assertNotContains('rooms', array_column($emsi->fresh()->blocks, 'type'));
    }

    private function setHeroImage(string $slug, string $image): void
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        $blocks = $page->draft_blocks;
        $index = collect($blocks)->search(fn ($block) => $block['type'] === 'hero');
        $blocks[$index]['data']['image'] = $image;
        $blocks[$index]['data']['image_alt'] = 'Photo';
        $page->update(['draft_blocks' => $blocks]);
        $page->publish();
    }

    private function snapshot(): array
    {
        return [
            'pages' => Page::orderBy('id')->get()->map(fn (Page $p) => [$p->id, $p->slug, $p->title, $p->domain->value, $p->status->value, $p->blocks, $p->draft_blocks])->all(),
            'revisions' => PageRevision::count(),
            'menus' => MenuItem::orderBy('id')->get(['id', 'location', 'parent_id', 'label', 'url', 'is_button', 'position', 'is_visible'])->toArray(),
            'campuses' => Program::with('campuses')->orderBy('id')->get()->map(fn (Program $p) => $p->campuses->modelKeys())->all(),
            'redirects' => Redirect::orderBy('id')->get(['from_path', 'to_path'])->toArray(),
        ];
    }
}
