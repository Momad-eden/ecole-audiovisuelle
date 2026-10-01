<?php

namespace Tests\Feature\Api;

use App\Enums\PublicationStatus;
use App\Models\MenuItem;
use App\Models\Offering;
use App\Models\Page;
use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainsApiTest extends TestCase
{
    use RefreshDatabase;

    private function campus(string $name): Place
    {
        return Place::create(['name' => $name, 'kind' => 'campus', 'city' => $name, 'status' => PublicationStatus::PUBLISHED]);
    }

    public function test_menus_expose_visible_children_under_their_parent(): void
    {
        $parent = MenuItem::create(['location' => 'main', 'label' => 'EMSI', 'url' => '/emsi', 'position' => 1, 'is_visible' => true]);
        MenuItem::create(['location' => 'main', 'parent_id' => $parent->id, 'label' => 'Formations', 'url' => '/formations', 'position' => 2, 'is_visible' => true]);
        MenuItem::create(['location' => 'main', 'parent_id' => $parent->id, 'label' => 'Caché', 'url' => '/cache', 'position' => 1, 'is_visible' => false]);
        MenuItem::create(['location' => 'footer', 'label' => 'Contact', 'url' => '/contact', 'position' => 1, 'is_visible' => true]);

        $response = $this->getJson('/api/v1/public/site')->assertOk();

        $response->assertJsonCount(1, 'data.menus.main')
            ->assertJsonPath('data.menus.main.0.label', 'EMSI')
            ->assertJsonCount(1, 'data.menus.main.0.children')
            ->assertJsonPath('data.menus.main.0.children.0', ['label' => 'Formations', 'url' => '/formations', 'description' => null])
            ->assertJsonPath('data.menus.footer.0.children', []);
    }

    public function test_children_of_a_hidden_parent_are_not_served(): void
    {
        $parent = MenuItem::create(['location' => 'main', 'label' => 'Masqué', 'url' => '/masque', 'position' => 1, 'is_visible' => false]);
        MenuItem::create(['location' => 'main', 'parent_id' => $parent->id, 'label' => 'Enfant', 'url' => '/enfant', 'position' => 1, 'is_visible' => true]);

        $this->getJson('/api/v1/public/site')->assertOk()->assertJsonCount(0, 'data.menus.main');
    }

    public function test_children_pointing_to_an_unpublished_page_are_removed(): void
    {
        Page::create(['title' => 'Brouillon', 'slug' => 'brouillon', 'type' => 'standard', 'status' => PublicationStatus::DRAFT]);
        $parent = MenuItem::create(['location' => 'main', 'label' => 'EMSI', 'url' => '/emsi', 'position' => 1, 'is_visible' => true]);
        MenuItem::create(['location' => 'main', 'parent_id' => $parent->id, 'label' => 'Brouillon', 'url' => '/brouillon', 'position' => 1, 'is_visible' => true]);

        $this->getJson('/api/v1/public/site')->assertOk()->assertJsonCount(0, 'data.menus.main.0.children');
    }

    public function test_site_exposes_the_domains(): void
    {
        $this->getJson('/api/v1/public/site')->assertOk()
            ->assertJsonPath('data.domains.maison', ['label' => 'Maison Habib Faye', 'color' => '#e0a84a'])
            ->assertJsonPath('data.domains.emsi.color', '#ff7a1a')
            ->assertJsonPath('data.domains.studio.color', '#ff3b30')
            ->assertJsonStructure(['data' => ['domains' => ['general' => ['label', 'color']]]]);
    }

    public function test_a_page_exposes_its_domain(): void
    {
        Page::create(['title' => 'Maison', 'slug' => 'maison', 'type' => 'standard', 'blocks' => [], 'draft_blocks' => [], 'status' => PublicationStatus::PUBLISHED, 'domain' => 'maison']);

        $this->getJson('/api/v1/public/pages/maison')->assertOk()->assertJsonPath('data.domain', 'maison');
    }

    public function test_offerings_are_filtered_by_campus_and_expose_campus_ids(): void
    {
        $dakar = $this->campus('Dakar');
        $saintLouis = $this->campus('Saint-Louis');
        $both = Offering::factory()->create();
        $both->cohort->program->campuses()->attach([$dakar->id, $saintLouis->id]);
        $onlyDakar = Offering::factory()->create();
        $onlyDakar->cohort->program->campuses()->attach($dakar->id);
        $slOnlyCohort = Offering::factory()->create();
        $slOnlyCohort->cohort->program->campuses()->attach([$dakar->id, $saintLouis->id]);
        $slOnlyCohort->cohort->update(['place_id' => $saintLouis->id]);

        $all = $this->getJson('/api/v1/public/offerings')->assertOk()->json('data');
        $ids = collect($all)->keyBy('id');
        $this->assertEqualsCanonicalizing([$dakar->id, $saintLouis->id], $ids[$both->id]['campusIds']);
        $this->assertSame([$dakar->id], $ids[$onlyDakar->id]['campusIds']);
        $this->assertSame([$saintLouis->id], $ids[$slOnlyCohort->id]['campusIds']);

        $sl = collect($this->getJson('/api/v1/public/offerings?campus='.$saintLouis->slug)->json('data'))->pluck('id')->all();
        $this->assertEqualsCanonicalizing([$both->id, $slOnlyCohort->id], $sl);

        $this->getJson('/api/v1/public/offerings?campus=inconnu')->assertOk()->assertExactJson(['data' => []]);
    }
}
