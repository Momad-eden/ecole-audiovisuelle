<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Pages d'essai des héros pour les parcours Playwright : créées puis supprimées sans toucher au reste. */
class HeroShowcaseCommandTest extends TestCase
{
    use RefreshDatabase;

    private const SLUGS = ['essai-heros-projection', 'essai-heros-studio', 'essai-heros-cinema'];

    public function test_it_creates_three_published_pages_with_the_new_heroes(): void
    {
        Storage::fake('public');

        $this->artisan('emsi:hero-showcase')->assertSuccessful();

        $pages = Page::whereIn('slug', self::SLUGS)->get()->keyBy('slug');
        $this->assertCount(3, $pages);
        $this->assertTrue($pages->every(fn (Page $page) => $page->blocks !== null));

        $hero = fn (string $slug) => $pages[$slug]->blocks[0]['data'];
        $this->assertSame('projection', $hero('essai-heros-projection')['layout']);
        $this->assertSame('studio', $hero('essai-heros-studio')['layout']);
        $this->assertCount(2, $hero('essai-heros-studio')['tracks']);
        $this->assertCount(3, $hero('essai-heros-studio')['hotspots']);
        $this->assertSame('cinema', $hero('essai-heros-cinema')['layout']);
        $this->assertCount(3, $hero('essai-heros-cinema')['slides']);
        $this->assertCount(4, $hero('essai-heros-cinema')['facts']);

        Storage::disk('public')->assertExists($hero('essai-heros-studio')['image']);
        Storage::disk('public')->assertExists($hero('essai-heros-studio')['tracks'][0]['file']);
    }

    public function test_running_it_twice_keeps_three_pages(): void
    {
        Storage::fake('public');

        $this->artisan('emsi:hero-showcase')->assertSuccessful();
        $this->artisan('emsi:hero-showcase')->assertSuccessful();

        $this->assertSame(3, Page::whereIn('slug', self::SLUGS)->count());
    }

    public function test_remove_deletes_only_the_showcase_pages_and_files(): void
    {
        Storage::fake('public');
        Page::create(['title' => 'Nos tarifs', 'slug' => 'nos-tarifs', 'type' => 'free', 'draft_blocks' => []])->publish();

        $this->artisan('emsi:hero-showcase')->assertSuccessful();
        $this->artisan('emsi:hero-showcase', ['--remove' => true])->assertSuccessful();

        $this->assertSame(0, Page::whereIn('slug', self::SLUGS)->count());
        $this->assertTrue(Page::where('slug', 'nos-tarifs')->exists());
        $this->assertSame([], Storage::disk('public')->allFiles('pages/essai-heros'));
    }
}
