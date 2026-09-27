<?php

namespace Tests\Feature\Admin;

use App\Enums\PublicationStatus;
use App\Filament\Resources\Productions\Pages\CreateProduction;
use App\Filament\Resources\Productions\ProductionResource;
use App\Models\Artwork;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/** Ajouter un titre à écouter sur la page du studio, depuis Impact Live › Productions du studio. */
class StudioProductionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_production_added_in_the_admin_can_be_heard_on_the_studio_page(): void
    {
        Queue::fake();
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'commercial']));
        Artwork::create(['title' => 'Film étudiant', 'kind' => 'video', 'origin' => 'school', 'status' => PublicationStatus::PUBLISHED]);

        Livewire::test(CreateProduction::class)
            ->fillForm([
                'title' => 'Ndar by Night',
                'audio_file' => UploadedFile::fake()->create('ndar.mp3', 20_000, 'audio/mpeg'),
                'summary' => 'Single enregistré au studio.',
                'credits' => [['person_name' => 'Awa', 'role' => 'Artiste']],
                'status' => PublicationStatus::PUBLISHED->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $production = Artwork::where('title', 'Ndar by Night')->firstOrFail();
        $this->assertSame('studio', $production->origin);
        $this->assertSame('audio', $production->kind->value);
        $this->get(ProductionResource::getUrl('index'))->assertOk()->assertSee('Ndar by Night')->assertDontSee('Film étudiant');

        Page::create(['title' => 'Studio', 'slug' => 'studio', 'draft_blocks' => [['type' => 'productions', 'data' => ['title' => 'Écouter']]]])->publish();
        $item = $this->getJson('/api/v1/public/pages/studio')->assertOk()->json('data.blocks.0.data.items.0');
        $this->assertSame('Ndar by Night', $item['title']);
        $this->assertStringEndsWith($production->audio_file, $item['audio']['url']);
    }
}
