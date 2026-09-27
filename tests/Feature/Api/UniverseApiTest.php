<?php

namespace Tests\Feature\Api;

use App\Enums\PublicationStatus;
use App\Models\Cohort;
use App\Models\Offering;
use App\Models\Page;
use App\Models\Program;
use App\Models\Room;
use App\Models\Track;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UniverseApiTest extends TestCase
{
    use RefreshDatabase;

    private function universe(array $attributes = []): Room
    {
        return Room::create($attributes + [
            'name' => 'Son', 'slug' => 'son', 'accent_color' => '#8B6CFF', 'visual' => 'sound',
            'status' => PublicationStatus::PUBLISHED, 'published_at' => now()->subDay(),
        ]);
    }

    public function test_universe_columns_migration_is_reversible(): void
    {
        $this->assertTrue(Schema::hasColumns('rooms', ['visual', 'is_upcoming']));

        $migration = require database_path('migrations/2026_09_27_070000_add_universe_fields_to_rooms_table.php');
        $migration->down();
        $this->assertFalse(Schema::hasColumn('rooms', 'visual'));
        $this->assertFalse(Schema::hasColumn('rooms', 'is_upcoming'));

        $migration->up();
        $this->assertTrue(Schema::hasColumns('rooms', ['visual', 'is_upcoming']));
    }

    public function test_universe_page_exposes_its_tracks_and_the_programs_teaching_them(): void
    {
        $room = $this->universe();
        $track = Track::factory()->create(['name' => 'Son', 'short_name' => 'Son', 'room_id' => $room->id, 'skills' => ['Mixage live'], 'outcomes' => ['Ingénieur du son live']]);
        Track::factory()->create(['name' => 'Inactive', 'room_id' => $room->id, 'is_active' => false]);
        $program = Program::factory()->create(['title' => 'BTS Son']);
        Offering::factory()->create(['track_id' => $track->id, 'cohort_id' => Cohort::factory()->create(['program_id' => $program->id])->id]);
        Program::factory()->create(['title' => 'Sans rapport']);
        Program::factory()->create(['title' => 'Brouillon', 'status' => PublicationStatus::DRAFT]);

        $this->getJson('/api/v1/public/rooms/son')
            ->assertOk()
            ->assertJsonPath('data.visual', 'sound')
            ->assertJsonPath('data.isUpcoming', false)
            ->assertJsonCount(1, 'data.tracks')
            ->assertJsonPath('data.tracks.0.shortName', 'Son')
            ->assertJsonPath('data.tracks.0.skills.0', 'Mixage live')
            ->assertJsonPath('data.tracks.0.outcomes.0', 'Ingénieur du son live')
            ->assertJsonCount(1, 'data.programs')
            ->assertJsonPath('data.programs.0.title', 'BTS Son');
    }

    public function test_site_and_rooms_block_describe_each_universe(): void
    {
        $room = $this->universe();
        $this->universe(['name' => 'Cinéma', 'slug' => 'cinema', 'visual' => 'cinema', 'is_upcoming' => true, 'position' => 1]);
        Track::factory()->create(['short_name' => 'Son', 'room_id' => $room->id]);
        Page::create(['title' => 'Accueil', 'slug' => 'accueil', 'type' => 'home', 'draft_blocks' => [['type' => 'rooms', 'data' => ['title' => 'Univers']]]])->publish();

        $this->getJson('/api/v1/public/site')
            ->assertOk()
            ->assertJsonPath('data.rooms.1.slug', 'cinema')
            ->assertJsonPath('data.rooms.1.isUpcoming', true)
            ->assertJsonPath('data.rooms.1.visual', 'cinema');

        $this->getJson('/api/v1/public/pages/accueil')
            ->assertOk()
            ->assertJsonPath('data.blocks.0.data.items.0.tracks.0.shortName', 'Son')
            ->assertJsonPath('data.blocks.0.data.items.1.isUpcoming', true);
    }

    public function test_new_blocks_resolve_their_images(): void
    {
        Page::create(['title' => 'École', 'slug' => 'ecole', 'draft_blocks' => [
            ['type' => 'hero', 'data' => ['title' => 'Faites vibrer', 'layout' => 'stage', 'words' => ['le son', 'l\'image']]],
            ['type' => 'venue', 'data' => ['title' => 'Au cœur du Grand Théâtre', 'image' => 'pages/salle.jpg', 'image_alt' => 'La grande salle', 'facts' => [['value' => '154 m²', 'label' => 'de studio']]]],
            ['type' => 'equipment', 'data' => ['title' => 'Le matériel', 'groups' => [['category' => 'Son', 'items' => ['DiGiCo', 'Midas'], 'image' => 'pages/console.jpg', 'image_alt' => 'Console']]]],
            ['type' => 'marquee', 'data' => ['words' => ['Son', 'Image']]],
        ]])->publish();

        $this->getJson('/api/v1/public/pages/ecole')
            ->assertOk()
            ->assertJsonPath('data.blocks.0.data.words.1', 'l\'image')
            ->assertJsonPath('data.blocks.1.data.image.url', url('/storage/pages/salle.jpg'))
            ->assertJsonPath('data.blocks.1.data.image.alt', 'La grande salle')
            ->assertJsonPath('data.blocks.1.data.facts.0.value', '154 m²')
            ->assertJsonPath('data.blocks.2.data.groups.0.image.url', url('/storage/pages/console.jpg'))
            ->assertJsonPath('data.blocks.2.data.groups.0.items.1', 'Midas')
            ->assertJsonPath('data.blocks.3.data.words.0', 'Son');
    }
}
