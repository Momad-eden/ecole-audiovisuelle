<?php

namespace Tests\Feature\Domain;

use App\Models\AgendaEvent;
use App\Models\EquipmentCategory;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Place;
use App\Models\Service;
use App\Models\Setting;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpactLiveContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_impact_live_content_is_added_to_an_existing_site_without_losing_anything(): void
    {
        $this->seed(ContentSeeder::class);
        Setting::current()->update(['logo' => 'settings/logo.png']);
        $home = Page::where('slug', 'accueil')->firstOrFail();
        $hero = $home->blocks[0];

        $this->artisan('emsi:impact-live')->assertSuccessful();
        $this->artisan('emsi:impact-live')->assertSuccessful();

        $this->assertSame(2, Place::campuses()->count());
        $this->assertSame(4, Place::count());
        $this->assertSame(['studio' => 3, 'events' => 3, 'space' => 1], Service::get()->groupBy(fn ($s) => $s->activity->value)->map->count()->all());
        $this->assertSame(3, EquipmentCategory::count());
        $this->assertTrue(AgendaEvent::where('title', 'Festival de Saint-Louis')->where('is_reference', true)->exists());

        foreach (['studio', 'events', 'espace-habib-faye'] as $slug) {
            $this->getJson("/api/v1/public/pages/{$slug}")->assertOk();
        }
        $this->getJson('/api/v1/public/pages/studio')->assertJsonPath('data.blocks.0.data.layout', 'studio');
        $this->getJson('/api/v1/public/pages/events')->assertJsonPath('data.blocks.0.data.layout', 'events');

        $home->refresh();
        $this->assertSame($hero, $home->blocks[0]);
        $this->assertSame(1, collect($home->blocks)->where('type', 'ecosystem')->count());
        $this->assertSame(1, collect(Page::where('slug', 'ecole')->first()->blocks)->where('type', 'places')->count());

        $main = MenuItem::where('location', 'main')->where('is_visible', true)->orderBy('position')->pluck('url')->all();
        $this->assertSame(['/univers', '/formations', '/studio', '/events', '/ecole', '/professionnels', '/candidater'], $main);
        $footer = MenuItem::where('location', 'footer')->where('is_visible', true)->pluck('url')->all();
        $this->assertEqualsCanonicalizing(['/realisations', '/agenda', '/espace-habib-faye', '/actualites', '/contact'], $footer);

        $this->assertSame('settings/logo.png', Setting::current()->logo);
    }

    public function test_a_fresh_install_includes_impact_live(): void
    {
        $this->seed(ContentSeeder::class);

        $this->assertSame(4, Place::count());
        $this->getJson('/api/v1/public/pages/studio')->assertOk();
    }
}
