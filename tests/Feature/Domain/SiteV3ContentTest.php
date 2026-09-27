<?php

namespace Tests\Feature\Domain;

use App\Models\Page;
use App\Models\Place;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/** Site v3 : deux écoles (Dakar, Saint-Louis) présentées côte à côte, accueil enrichi. */
class SiteV3ContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_place_presentation_migration_is_reversible(): void
    {
        $migration = require database_path('migrations/2026_09_28_100000_add_presentation_to_places.php');
        $migration->down();
        $this->assertFalse(Schema::hasColumn('places', 'tagline'));
        $this->assertFalse(Schema::hasColumn('places', 'highlights'));
        $migration->up();
        $this->assertTrue(Schema::hasColumns('places', ['tagline', 'highlights']));
    }

    public function test_the_school_page_presents_both_campuses_and_the_home_page_is_enriched(): void
    {
        $this->seed(ContentSeeder::class);
        $home = Page::where('slug', 'accueil')->firstOrFail();
        $hero = $home->blocks[0];
        Place::where('slug', 'emsi-dakar')->update(['tagline' => 'Texte saisi par l\'école']);

        $this->artisan('emsi:site-v3')->assertSuccessful();
        $this->artisan('emsi:site-v3')->assertSuccessful();

        $school = $this->getJson('/api/v1/public/pages/ecole')->assertOk()->json('data.blocks');
        $types = array_column($school, 'type');
        $this->assertSame('hero', $types[0]);
        $this->assertSame('editorial', $school[0]['data']['layout']);
        $this->assertSame(1, count(array_keys($types, 'campuses')));
        $this->assertNotContains('places', $types);
        $campuses = $school[array_search('campuses', $types)]['data']['items'];
        $this->assertSame(['Dakar', 'Saint-Louis'], array_column($campuses, 'city'));
        $this->assertSame('Texte saisi par l\'école', $campuses[0]['tagline']);
        $this->assertNotEmpty($campuses[1]['highlights']);

        $home->refresh();
        $this->assertSame($hero, $home->blocks[0]);
        $homeTypes = array_column($home->blocks, 'type');
        foreach (['stats', 'campuses', 'agenda'] as $type) {
            $this->assertSame(1, count(array_keys($homeTypes, $type)), $type);
        }
        $this->assertLessThan(array_search('rooms', $homeTypes), array_search('stats', $homeTypes));
    }
}
