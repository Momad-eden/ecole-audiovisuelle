<?php

namespace Tests\Feature\Domain;

use App\Enums\PublicationStatus;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Redirect;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Track;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteV2UpgradeTest extends TestCase
{
    use RefreshDatabase;

    /** État d'une base installée avec le contenu « musée » (v1) puis modifiée dans l'admin. */
    private function legacySite(): void
    {
        foreach (['Salle du Son' => 'salle-du-son', 'Salle de la Lumière' => 'salle-de-la-lumiere', 'Salle de l\'Image' => 'salle-de-limage', 'Salle du Visuel' => 'salle-du-visuel'] as $name => $slug) {
            Room::create(['name' => $name, 'slug' => $slug, 'accent_color' => '#F5B83D', 'status' => PublicationStatus::PUBLISHED]);
        }
        Track::factory()->create(['name' => 'Technicien Lumière', 'slug' => 'technicien-lumiere', 'room_id' => Room::where('slug', 'salle-de-la-lumiere')->value('id')]);
        Track::factory()->create(['name' => 'Cadrage Sportif et Régie Vidéo', 'slug' => 'cadrage-sportif-et-regie-video', 'room_id' => Room::where('slug', 'salle-de-limage')->value('id')]);

        Page::create(['title' => 'Accueil', 'slug' => 'accueil', 'type' => 'home', 'draft_blocks' => [
            ['type' => 'hero', 'data' => ['title' => 'Mon titre', 'image' => 'pages/mine.jpg', 'image_alt' => 'Ma photo', 'layout' => 'full']],
        ]])->publish();
        Page::create(['title' => 'Contact', 'slug' => 'contact', 'type' => 'system', 'draft_blocks' => [['type' => 'contact', 'data' => ['title' => 'Écrivez-nous']]]])->publish();

        MenuItem::create(['label' => 'Le Musée', 'url' => '/musee', 'location' => 'main']);
        MenuItem::create(['label' => 'Espace Professionnels', 'url' => '/professionnels', 'location' => 'footer']);
        Redirect::create(['from_path' => '/galerie', 'to_path' => '/musee', 'status_code' => 301]);
        Setting::current()->update(['logo' => 'settings/logo.png', 'school_name' => 'Mon école']);
    }

    public function test_upgrade_turns_rooms_into_universes_and_republishes_home_without_losing_anything(): void
    {
        $this->legacySite();

        $this->artisan('emsi:site-v2')->assertSuccessful();
        $this->artisan('emsi:site-v2')->assertSuccessful();

        $this->assertSame(['son', 'image', 'design', 'scene', 'cinema'], Room::orderBy('position')->pluck('slug')->all());
        $this->assertTrue(Room::where('slug', 'cinema')->value('is_upcoming'));
        $this->assertSame('stage', Room::where('slug', 'scene')->value('visual'));
        $this->assertSame('scene', Track::where('slug', 'technicien-lumiere')->first()->room->slug);
        $this->assertSame('image', Track::where('slug', 'cadrage-sportif-et-regie-video')->first()->room->slug);

        $home = Page::where('slug', 'accueil')->firstOrFail();
        $this->assertSame('stage', $home->blocks[0]['data']['layout']);
        $this->assertSame('pages/mine.jpg', $home->blocks[0]['data']['image']);
        $this->assertSame('Ma photo', $home->blocks[0]['data']['image_alt']);
        $this->assertContains('venue', array_column($home->blocks, 'type'));
        $this->assertTrue($home->revisions()->get()->contains(fn ($revision) => ($revision->blocks[0]['data']['title'] ?? null) === 'Mon titre'));
        $this->assertSame('Écrivez-nous', Page::where('slug', 'contact')->first()->blocks[0]['data']['title']);

        $main = MenuItem::where('location', 'main')->where('is_visible', true)->orderBy('position')->pluck('url')->all();
        $this->assertSame(['/univers', '/formations', '/ecole', '/realisations', '/professionnels', '/candidater'], $main);
        $this->assertFalse(MenuItem::where('location', 'footer')->where('url', '/professionnels')->value('is_visible'));
        $this->assertSame('/realisations', Redirect::where('from_path', '/galerie')->value('to_path'));

        $this->assertSame('settings/logo.png', Setting::current()->logo);
        $this->assertSame('Mon école', Setting::current()->school_name);

        $this->getJson('/api/v1/public/rooms/scene')->assertOk()->assertJsonPath('data.tracks.0.slug', 'technicien-lumiere');
    }
}
