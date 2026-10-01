<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Page;
use Database\Seeders\MenuDefaults;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomepageV5Test extends TestCase
{
    use RefreshDatabase;

    private function home(array $blocks): Page
    {
        $page = Page::create(['title' => 'Accueil', 'slug' => 'accueil', 'type' => 'home', 'draft_blocks' => $blocks]);
        $page->publish();

        return $page->fresh();
    }

    public function test_the_showcase_block_serves_images_videos_and_links(): void
    {
        $this->home([['type' => 'showcase', 'data' => [
            'title' => 'Le centre en images', 'button_label' => 'Voir', 'button_url' => '/maison-habib-faye/agenda',
            'items' => [
                ['image' => 'pages/scene.jpg', 'image_alt' => 'La scène', 'video' => 'pages/video/concert.mp4', 'caption' => 'Sur scène', 'url' => '/maison-habib-faye/agenda'],
                ['image' => null, 'caption' => 'Sans image'],
            ],
        ]]]);

        $this->getJson('/api/v1/public/pages/accueil')->assertOk()
            ->assertJsonPath('data.blocks.0.type', 'showcase')
            ->assertJsonCount(1, 'data.blocks.0.data.items')
            ->assertJsonPath('data.blocks.0.data.items.0.image.alt', 'La scène')
            ->assertJsonPath('data.blocks.0.data.items.0.video', Storage::disk('public')->url('pages/video/concert.mp4'))
            ->assertJsonPath('data.blocks.0.data.items.0.url', '/maison-habib-faye/agenda')
            ->assertJsonPath('data.blocks.0.data.buttonUrl', '/maison-habib-faye/agenda');
    }

    public function test_the_film_hero_serves_its_photos_and_film_link(): void
    {
        $this->home([['type' => 'hero', 'data' => [
            'layout' => 'film', 'title' => 'La culture comme héritage', 'images' => ['pages/a.jpg', 'pages/b.jpg'],
            'film_url' => 'https://youtu.be/dQw4w9WgXcQ',
        ]]]);

        $this->getJson('/api/v1/public/pages/accueil')->assertOk()
            ->assertJsonPath('data.blocks.0.data.layout', 'film')
            ->assertJsonCount(2, 'data.blocks.0.data.images')
            ->assertJsonPath('data.blocks.0.data.filmUrl', 'https://youtu.be/dQw4w9WgXcQ');
    }

    public function test_the_new_homepage_is_prepared_as_a_draft_only(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('pages/01M3R5AKQTX1B0PBT018R88CEN.jpg', 'x');
        $published = [
            ['type' => 'domains', 'data' => ['intro' => 'La culture comme héritage', 'panels' => []]],
            ['type' => 'stats', 'data' => ['title' => 'Repères', 'items' => []]],
            ['type' => 'partners', 'data' => ['title' => 'Partenaires', 'categories' => []]],
        ];
        $page = $this->home($published);

        $this->artisan('emsi:accueil-brouillon')->assertSuccessful();

        $page->refresh();
        $this->assertSame($published, $page->blocks, 'La version en ligne ne change pas.');
        $types = array_column($page->draft_blocks, 'type');
        $this->assertSame(['hero', 'domains', 'agenda', 'showcase', 'stats', 'cta', 'partners'], $types);
        $this->assertSame('film', $page->draft_blocks[0]['data']['layout']);
        $this->assertSame('La culture comme héritage', $page->draft_blocks[0]['data']['title']);
        $this->assertSame(['pages/01M3R5AKQTX1B0PBT018R88CEN.jpg'], $page->draft_blocks[0]['data']['images']);
        $this->assertNull($page->draft_blocks[1]['data']['intro']);
        $this->assertCount(1, $page->draft_blocks[3]['data']['items'], 'Seules les photos présentes sur le disque sont proposées.');
    }

    public function test_a_draft_in_progress_is_never_replaced_without_force(): void
    {
        $page = $this->home([['type' => 'text', 'data' => ['body' => '<p>En ligne</p>']]]);
        $page->update(['draft_blocks' => [['type' => 'text', 'data' => ['body' => '<p>Brouillon de l\'équipe</p>']]]]);

        $this->artisan('emsi:accueil-brouillon')->assertFailed();
        $this->assertSame('<p>Brouillon de l\'équipe</p>', $page->fresh()->draft_blocks[0]['data']['body']);

        $this->artisan('emsi:accueil-brouillon', ['--force' => true])->assertSuccessful();
        $this->assertSame('hero', $page->fresh()->draft_blocks[0]['type']);
    }

    public function test_menu_sections_get_their_photo_and_tagline_without_overwriting(): void
    {
        $this->home([['type' => 'domains', 'data' => ['panels' => [
            ['url' => '/maison-habib-faye', 'image' => 'pages/maison.jpg', 'title' => 'Maison'],
        ]]]]);
        $maison = MenuItem::create(['location' => 'main', 'label' => 'Maison Habib Faye', 'url' => '/maison-habib-faye']);
        MenuItem::create(['location' => 'main', 'parent_id' => $maison->id, 'label' => 'La Maison', 'url' => '/maison-habib-faye']);
        $emsi = MenuItem::create(['location' => 'main', 'label' => 'EMSI', 'url' => '/emsi', 'description' => 'Écrit par l\'équipe']);
        MenuItem::create(['location' => 'main', 'parent_id' => $emsi->id, 'label' => 'Formations', 'url' => '/emsi/formations']);
        $single = MenuItem::create(['location' => 'main', 'label' => 'Accueil', 'url' => '/']);

        MenuDefaults::fillParents();

        $this->assertSame('pages/maison.jpg', $maison->fresh()->image);
        $this->assertSame(MenuDefaults::PARENT_DESCRIPTIONS['/maison-habib-faye'], $maison->fresh()->description);
        $this->assertSame('Écrit par l\'équipe', $emsi->fresh()->description);
        $this->assertNull($emsi->fresh()->image);
        $this->assertNull($single->fresh()->description);

        $this->getJson('/api/v1/public/site')->assertOk()
            ->assertJsonPath('data.menus.main.0.description', MenuDefaults::PARENT_DESCRIPTIONS['/maison-habib-faye'])
            ->assertJsonPath('data.menus.main.0.image.url', Storage::disk('public')->url('pages/maison.jpg'));
    }
}
