<?php

namespace Tests\Feature\Domain;

use App\Models\Offering;
use App\Models\Page;
use App\Models\Program;
use App\Models\Room;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_reference_content_is_idempotent_and_served_by_the_api(): void
    {
        $this->seed(ContentSeeder::class);
        $this->seed(ContentSeeder::class);

        $this->assertSame(2, Program::count());
        $this->assertSame(9, Offering::count());
        $this->assertSame(40, (int) Offering::whereHas('cohort', fn ($q) => $q->where('name', 'Volet 1 — 2026'))->sum('capacity'));
        $this->assertSame(60, (int) Offering::whereHas('cohort', fn ($q) => $q->where('name', 'Volet 2 — 2027'))->sum('capacity'));

        $this->assertSame(['son', 'image', 'design', 'scene', 'cinema'], Room::orderBy('position')->pluck('slug')->all());
        $this->getJson('/api/v1/public/pages/accueil')->assertOk()
            ->assertJsonPath('data.blocks.0.type', 'hero')
            ->assertJsonPath('data.blocks.0.data.layout', 'stage')
            ->assertJsonPath('data.blocks.2.type', 'rooms')
            ->assertJsonCount(5, 'data.blocks.2.data.items');
        $this->getJson('/api/v1/public/rooms/scene')->assertOk()->assertJsonCount(2, 'data.tracks')->assertJsonCount(2, 'data.programs');
        $this->getJson('/api/v1/public/pages/professionnels')->assertOk();
        $this->getJson('/api/v1/public/programs/bts-vae')->assertOk()->assertJsonPath('data.levelLabel', 'Certification de niveau BTS (équivalent Bac+2)');
        $this->getJson('/api/v1/public/pages/confidentialite')->assertNotFound();
        $this->assertFalse(Page::where('slug', 'confidentialite')->firstOrFail()->isPublished());
    }
}
