<?php

namespace Tests\Feature;

use App\Enums\PublicationStatus;
use App\Models\Cohort;
use App\Models\Offering;
use App\Models\Page;
use App\Models\Place;
use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Pages et formation d'essai des nouveaux blocs pour Playwright : créées puis supprimées sans toucher au reste. */
class DomainsShowcaseCommandTest extends TestCase
{
    use RefreshDatabase;

    private const SLUGS = ['essai-domaines-accueil', 'essai-domaines-campus', 'essai-domaines-soutenir', 'essai-domaines-documents'];

    private Place $dakar;

    private Place $saintLouis;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->saintLouis = Place::create(['name' => 'EMSI Saint-Louis', 'kind' => 'campus', 'city' => 'Saint-Louis', 'position' => 1, 'status' => PublicationStatus::PUBLISHED]);
        $this->dakar = Place::create(['name' => 'EMSI Dakar', 'kind' => 'campus', 'city' => 'Dakar', 'position' => 0, 'status' => PublicationStatus::PUBLISHED]);
    }

    public function test_it_creates_the_pages_and_a_program_offered_only_at_the_first_campus(): void
    {
        $this->artisan('emsi:domains-showcase')->assertSuccessful();

        $pages = Page::whereIn('slug', self::SLUGS)->get()->keyBy('slug');
        $this->assertCount(4, $pages);
        $this->assertTrue($pages->every(fn (Page $page) => $page->isPublished()));

        $domains = $pages['essai-domaines-accueil']->blocks[0];
        $this->assertSame('domains', $domains['type']);
        $this->assertCount(3, $domains['data']['panels']);
        Storage::disk('public')->assertExists($domains['data']['panels'][0]['image']);

        $this->assertSame('campus_programs', $pages['essai-domaines-campus']->blocks[0]['type']);
        $this->assertSame($this->dakar->id, $pages['essai-domaines-campus']->blocks[0]['data']['campus_id']);
        $this->assertSame('support_form', $pages['essai-domaines-soutenir']->blocks[0]['type']);
        $file = $pages['essai-domaines-documents']->blocks[0]['data']['files'][0]['file'];
        Storage::disk('public')->assertExists($file);
        $this->assertStringStartsWith('%PDF', Storage::disk('public')->get($file));

        $program = Program::where('slug', 'essai-dakar-seulement')->firstOrFail();
        $this->assertSame('Essai — Dakar seulement', $program->title);
        $this->assertSame([$this->dakar->id], $program->campuses()->pluck('places.id')->all());
        $offering = $program->offerings()->firstOrFail();
        $this->assertTrue($offering->isAvailableAt($this->dakar));
        $this->assertFalse($offering->isAvailableAt($this->saintLouis));
    }

    public function test_running_it_twice_keeps_one_of_each(): void
    {
        $this->artisan('emsi:domains-showcase')->assertSuccessful();
        $this->artisan('emsi:domains-showcase')->assertSuccessful();

        $this->assertSame(4, Page::whereIn('slug', self::SLUGS)->count());
        $this->assertSame(1, Program::withTrashed()->where('slug', 'like', 'essai-dakar-seulement%')->count());
        $this->assertSame(1, Cohort::count());
        $this->assertSame(1, Offering::count());
    }

    public function test_remove_deletes_only_what_it_created(): void
    {
        Page::create(['title' => 'Nos tarifs', 'slug' => 'nos-tarifs', 'type' => 'free', 'draft_blocks' => []])->publish();
        $real = Offering::factory()->create();

        $this->artisan('emsi:domains-showcase')->assertSuccessful();
        $this->artisan('emsi:domains-showcase', ['--remove' => true])->assertSuccessful();

        $this->assertSame(0, Page::whereIn('slug', self::SLUGS)->count());
        $this->assertSame(0, Program::withTrashed()->where('slug', 'essai-dakar-seulement')->count());
        $this->assertTrue(Page::where('slug', 'nos-tarifs')->exists());
        $this->assertSame([$real->id], Offering::pluck('id')->all());
        $this->assertSame([$real->cohort_id], Cohort::pluck('id')->all());
        $this->assertSame(2, Place::count());
        $this->assertSame([], Storage::disk('public')->allFiles('pages/essai-domaines'));
    }
}
