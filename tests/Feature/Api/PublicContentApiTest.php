<?php

namespace Tests\Feature\Api;

use App\Enums\Audience;
use App\Enums\PublicationStatus;
use App\Models\Artwork;
use App\Models\MenuItem;
use App\Models\News;
use App\Models\Offering;
use App\Models\Page;
use App\Models\Program;
use App\Models\Room;
use App\Models\Setting;
use App\Support\PreviewToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicContentApiTest extends TestCase
{
    use RefreshDatabase;

    private function room(array $attributes = []): Room
    {
        return Room::create($attributes + ['name' => 'Salle du Son', 'accent_color' => '#F5B83D', 'status' => PublicationStatus::PUBLISHED]);
    }

    public function test_site_returns_settings_menus_and_rooms(): void
    {
        Setting::current()->update(['phone' => '+221 77 680 70 62']);
        MenuItem::create(['label' => 'Le Musée', 'url' => '/musee', 'location' => 'main']);
        MenuItem::create(['label' => 'Caché', 'url' => '/x', 'location' => 'main', 'is_visible' => false]);
        $this->room();
        $this->room(['name' => 'Brouillon', 'status' => PublicationStatus::DRAFT]);

        $this->getJson('/api/v1/public/site')
            ->assertOk()
            ->assertJsonPath('data.settings.phone', '+221 77 680 70 62')
            ->assertJsonCount(1, 'data.menus.main')
            ->assertJsonPath('data.menus.main.0.label', 'Le Musée')
            ->assertJsonCount(1, 'data.rooms')
            ->assertJsonPath('data.rooms.0.accentColor', '#F5B83D');
    }

    public function test_only_published_pages_are_served_with_resolved_blocks(): void
    {
        $room = $this->room();
        Artwork::create(['title' => 'Paysage sonore', 'kind' => 'audio', 'room_id' => $room->id, 'is_featured' => true, 'status' => PublicationStatus::PUBLISHED]);
        Program::factory()->create(['title' => 'Formation école', 'audience' => Audience::SCHOOL]);
        Program::factory()->professional()->create(['title' => 'Programme pro']);
        $page = Page::create(['title' => 'Accueil', 'slug' => 'accueil', 'type' => 'home', 'draft_blocks' => [
            ['type' => 'hero', 'data' => ['title' => 'Bienvenue', 'image' => 'pages/hero.jpg', 'image_alt' => 'Studio']],
            ['type' => 'artworks', 'data' => ['source' => 'featured', 'limit' => 6]],
            ['type' => 'programs', 'data' => ['audience' => 'school', 'limit' => 6]],
        ]]);

        $this->getJson('/api/v1/public/pages/accueil')->assertNotFound();

        $page->publish();

        $this->getJson('/api/v1/public/pages/accueil')
            ->assertOk()
            ->assertJsonPath('data.title', 'Accueil')
            ->assertJsonPath('data.blocks.0.type', 'hero')
            ->assertJsonPath('data.blocks.0.data.image.url', url('/storage/pages/hero.jpg'))
            ->assertJsonPath('data.blocks.0.data.image.alt', 'Studio')
            ->assertJsonPath('data.blocks.1.data.items.0.title', 'Paysage sonore')
            ->assertJsonCount(1, 'data.blocks.2.data.items')
            ->assertJsonPath('data.blocks.2.data.items.0.title', 'Formation école');
    }

    public function test_draft_preview_requires_a_valid_token(): void
    {
        $page = Page::create(['title' => 'École', 'slug' => 'ecole', 'draft_blocks' => [['type' => 'text', 'data' => ['body' => '<p>Brouillon</p>']]]]);

        $this->getJson('/api/v1/public/preview?token=invalide')->assertForbidden();
        $this->getJson('/api/v1/public/preview?token='.PreviewToken::make('page', $page->id))
            ->assertOk()
            ->assertJsonPath('data.slug', 'ecole')
            ->assertJsonPath('data.blocks.0.data.body', '<p>Brouillon</p>');
    }

    public function test_rooms_and_artworks(): void
    {
        $room = $this->room();
        Artwork::create(['title' => 'Œuvre publiée', 'kind' => 'image', 'room_id' => $room->id, 'status' => PublicationStatus::PUBLISHED]);
        Artwork::create(['title' => 'Brouillon', 'kind' => 'image', 'room_id' => $room->id, 'status' => PublicationStatus::DRAFT]);

        $this->getJson("/api/v1/public/rooms/{$room->slug}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Salle du Son')
            ->assertJsonCount(1, 'data.artworks');

        $this->getJson('/api/v1/public/artworks/oeuvre-publiee')->assertOk()->assertJsonPath('data.room.slug', $room->slug);
        $this->getJson('/api/v1/public/artworks/brouillon')->assertNotFound();
        $this->getJson('/api/v1/public/artworks')->assertOk()->assertJsonPath('meta.total', 1);
    }

    public function test_programs_are_filtered_by_audience_and_expose_open_offerings(): void
    {
        $offering = Offering::factory()->create();
        $program = $offering->cohort->program;
        Program::factory()->draft()->create(['title' => 'Invisible']);

        $this->getJson('/api/v1/public/programs?audience=school')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', $program->slug);

        $this->getJson("/api/v1/public/programs/{$program->slug}")
            ->assertOk()
            ->assertJsonPath('data.acceptsApplications', true)
            ->assertJsonPath('data.cohorts.0.offerings.0.feeAmount', 500000);

        $this->getJson('/api/v1/public/offerings?audience=school')->assertOk()->assertJsonPath('data.0.id', $offering->id);
    }

    public function test_news_list_and_detail(): void
    {
        News::create(['title' => 'Publiée', 'content' => '<p>x</p>', 'is_published' => true, 'published_at' => now()->subDay()]);
        News::create(['title' => 'Programmée', 'content' => '<p>x</p>', 'is_published' => true, 'published_at' => now()->addDay()]);

        $this->getJson('/api/v1/public/news')->assertOk()->assertJsonPath('meta.total', 1);
        $this->getJson('/api/v1/public/news/publiee')->assertOk()->assertJsonPath('data.content', '<p>x</p>');
        $this->getJson('/api/v1/public/news/programmee')->assertNotFound();
    }

    public function test_sitemap_lists_published_urls(): void
    {
        Page::create(['title' => 'École', 'slug' => 'ecole', 'draft_blocks' => []])->publish();
        $this->room();

        $this->getJson('/api/v1/public/sitemap')
            ->assertOk()
            ->assertJsonFragment(['path' => '/ecole'])
            ->assertJsonFragment(['path' => '/musee/salle-du-son']);
    }
}
