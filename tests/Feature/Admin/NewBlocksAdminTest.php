<?php

namespace Tests\Feature\Admin;

use App\Enums\PublicationStatus;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Support\PageBlocks;
use App\Models\Page;
use App\Models\Place;
use App\Models\User;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Nouveaux blocs de l'éditeur de pages : triptyque, formations d'un campus, documents, « Nous soutenir ». */
class NewBlocksAdminTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, Field> */
    private function fields(string $block): array
    {
        $found = collect(PageBlocks::all())->first(fn (Block $b) => $b->getName() === $block);
        $this->assertNotNull($found, "Bloc « {$block} » absent du catalogue.");

        return collect($found->getDefaultChildComponents())
            ->filter(fn ($c) => $c instanceof Field)->keyBy(fn (Field $f) => $f->getName())->all();
    }

    public function test_domains_block_has_exactly_three_panels_and_an_intro(): void
    {
        $fields = $this->fields('domains');

        $this->assertArrayHasKey('intro', $fields);
        /** @var Repeater $panels */
        $panels = $fields['panels'];
        $this->assertSame(3, $panels->getMinItems());
        $this->assertSame(3, $panels->getMaxItems());

        $names = collect($panels->getDefaultChildComponents())->filter(fn ($c) => $c instanceof Field)->map(fn (Field $f) => $f->getName())->values()->all();
        foreach (['domain', 'eyebrow', 'title', 'text', 'image', 'image_alt', 'url', 'label'] as $name) {
            $this->assertContains($name, $names, "Champ « {$name} » absent d'un panneau.");
        }
    }

    public function test_campus_programs_block_lists_published_campuses(): void
    {
        Place::create(['name' => 'EMSI Dakar', 'kind' => 'campus', 'city' => 'Dakar', 'status' => PublicationStatus::PUBLISHED]);
        Place::create(['name' => 'Studio', 'kind' => 'studio', 'status' => PublicationStatus::PUBLISHED]);

        /** @var Select $campus */
        $campus = $this->fields('campus_programs')['campus_id'];

        $this->assertSame(['EMSI Dakar'], array_values($campus->getOptions()));
        $this->assertTrue($campus->isRequired());
    }

    public function test_downloads_block_accepts_pdf_up_to_20_mb(): void
    {
        /** @var Repeater $files */
        $files = $this->fields('downloads')['files'];
        /** @var FileUpload $upload */
        $upload = collect($files->getDefaultChildComponents())->first(fn ($c) => $c instanceof FileUpload);

        $this->assertSame(['application/pdf'], $upload->getAcceptedFileTypes());
        $this->assertSame(20480, $upload->getMaxSize());
        $this->assertSame('pages/documents', $upload->getDirectory());
    }

    public function test_new_blocks_open_in_the_editor(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'directeur']));
        $this->assertArrayHasKey('title', $this->fields('support_form'));
        $page = Page::create(['title' => 'Essai', 'slug' => 'essai', 'type' => 'free', 'draft_blocks' => [
            ['type' => 'domains', 'data' => ['intro' => 'Phrase', 'panels' => [
                ['domain' => 'maison', 'title' => 'Maison'], ['domain' => 'emsi', 'title' => 'EMSI'], ['domain' => 'studio', 'title' => 'Studio'],
            ]]],
            ['type' => 'campus_programs', 'data' => ['title' => 'Formations']],
            ['type' => 'downloads', 'data' => ['title' => 'Documents', 'files' => []]],
            ['type' => 'support_form', 'data' => ['title' => 'Nous soutenir']],
        ]]);

        $this->get(PageResource::getUrl('edit', ['record' => $page]))->assertOk()
            ->assertSee('Nos trois maisons (triptyque)')
            ->assertSee('Formations de ce campus')
            ->assertSee('Documents à télécharger')
            ->assertSee('Nous soutenir (formulaire)');
    }
}
