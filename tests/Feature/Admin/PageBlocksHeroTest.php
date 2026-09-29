<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Pages\PageResource;
use App\Filament\Support\PageBlocks;
use App\Models\Page;
use App\Models\User;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Bloc « Grand titre (héros) » : mises en page Projection et Cinéma, Studio avec points et morceaux. */
class PageBlocksHeroTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, Field> champs de premier niveau du bloc héros, par nom */
    private function heroFields(): array
    {
        $hero = collect(PageBlocks::all())->first(fn (Block $block) => $block->getName() === 'hero');

        return collect($hero->getDefaultChildComponents())
            ->filter(fn ($component) => $component instanceof Field)
            ->keyBy(fn (Field $field) => $field->getName())
            ->all();
    }

    public function test_hero_block_offers_projection_and_cinema_layouts(): void
    {
        /** @var Radio $layout */
        $layout = $this->heroFields()['layout'];
        $options = $layout->getOptions();

        $this->assertArrayHasKey('masterpiece', $options);
        $this->assertSame('Projection (photo de fond, rubans de lumière, cartel)', $options['projection']);
        $this->assertSame('Cinéma (diaporama plein écran et chiffres clés)', $options['cinema']);
        $this->assertSame('Studio (photo, matériel commenté et écoute)', $options['studio']);
    }

    public function test_studio_and_cinema_heroes_open_in_the_editor(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'directeur']));
        Storage::fake('public');
        Storage::disk('public')->put('pages/studio.jpg', 'jpg');
        $page = Page::create(['title' => 'Essai', 'slug' => 'essai', 'type' => 'free', 'draft_blocks' => [
            ['type' => 'hero', 'data' => ['title' => 'Le studio', 'layout' => 'studio', 'image' => 'pages/studio.jpg', 'image_alt' => 'Studio',
                'hotspots' => [['x' => 50, 'y' => 50, 'label' => 'Console']], 'tracks' => [['file' => 'pages/audio/a.mp3', 'title' => 'Démo']]]],
            ['type' => 'hero', 'data' => ['title' => 'Cinéma', 'layout' => 'cinema', 'slides' => [
                ['image' => 'pages/a.jpg', 'image_alt' => 'A', 'title' => 'Un'], ['image' => 'pages/b.jpg', 'image_alt' => 'B', 'title' => 'Deux'],
            ], 'facts' => [['value' => '5', 'label' => 'filières']]]],
        ]]);

        $this->get(PageResource::getUrl('edit', ['record' => $page]))->assertOk()
            ->assertSee('Points sur la photo')
            ->assertSee('Cliquez sur la photo pour ajouter un point')
            ->assertSee('Morceaux à écouter')
            ->assertSee('Diapositives')
            ->assertSee('Chiffres clés');
    }

    public function test_hero_block_declares_new_fields(): void
    {
        $fields = $this->heroFields();

        foreach (['highlight', 'hotspots', 'tracks', 'slides', 'facts'] as $name) {
            $this->assertArrayHasKey($name, $fields, "Champ « {$name} » absent du bloc héros.");
        }

        /** @var Repeater $tracks */
        $tracks = $fields['tracks'];
        /** @var Repeater $slides */
        $slides = $fields['slides'];
        /** @var Repeater $facts */
        $facts = $fields['facts'];
        $this->assertSame(3, $tracks->getMaxItems());
        $this->assertSame(2, $slides->getMinItems());
        $this->assertSame(5, $slides->getMaxItems());
        $this->assertSame(4, $facts->getMaxItems());
    }
}
