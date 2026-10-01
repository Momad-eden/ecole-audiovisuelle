<?php

namespace Tests\Feature\Api;

use App\Enums\PublicationStatus;
use App\Models\Cohort;
use App\Models\Offering;
use App\Models\Page;
use App\Models\Place;
use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Blocs « Nos trois lieux », « Formations de ce campus », « Documents à télécharger » et « Nous soutenir ». */
class NewBlocksTest extends TestCase
{
    use RefreshDatabase;

    private function page(array $blocks): void
    {
        Page::create(['title' => 'Essai', 'slug' => 'essai', 'type' => 'free', 'draft_blocks' => $blocks])->publish();
    }

    private function campus(string $name): Place
    {
        return Place::create(['name' => "EMSI {$name}", 'kind' => 'campus', 'city' => $name, 'status' => PublicationStatus::PUBLISHED]);
    }

    public function test_domains_block_resolves_panels_with_their_colour_and_image(): void
    {
        $panel = fn (string $domain, string $title) => [
            'domain' => $domain, 'eyebrow' => 'Surtitre', 'title' => $title, 'text' => 'Texte court',
            'image' => "pages/{$domain}.jpg", 'image_alt' => "Photo {$title}", 'url' => "/{$domain}", 'label' => 'Découvrir',
        ];
        $this->page([['type' => 'domains', 'data' => ['intro' => 'La culture comme héritage', 'panels' => [
            $panel('maison', 'Centre culturel Habib Faye'), $panel('emsi', 'EMSI'), $panel('studio', 'Impact Live Studio'),
        ]]]]);

        $this->getJson('/api/v1/public/pages/essai')->assertOk()
            ->assertJsonPath('data.blocks.0.type', 'domains')
            ->assertJsonPath('data.blocks.0.data.intro', 'La culture comme héritage')
            ->assertJsonCount(3, 'data.blocks.0.data.panels')
            ->assertJsonPath('data.blocks.0.data.panels.0.color', '#e0a84a')
            ->assertJsonPath('data.blocks.0.data.panels.1.color', '#ff7a1a')
            ->assertJsonPath('data.blocks.0.data.panels.2.color', '#ff3b30')
            ->assertJsonPath('data.blocks.0.data.panels.2.title', 'Impact Live Studio')
            ->assertJsonPath('data.blocks.0.data.panels.2.url', '/studio')
            ->assertJsonPath('data.blocks.0.data.panels.2.label', 'Découvrir')
            ->assertJsonPath('data.blocks.0.data.panels.0.image.url', url('/storage/pages/maison.jpg'))
            ->assertJsonPath('data.blocks.0.data.panels.0.image.alt', 'Photo Centre culturel Habib Faye');
    }

    public function test_campus_programs_lists_only_programs_available_at_that_campus(): void
    {
        $this->travelTo('2026-09-29');
        $dakar = $this->campus('Dakar');
        $saintLouis = $this->campus('Saint-Louis');

        $both = Program::factory()->create(['title' => 'Son live', 'slug' => 'son-live', 'summary' => 'Sonoriser un concert.']);
        $both->campuses()->attach([$dakar->id, $saintLouis->id]);
        $soon = Cohort::factory()->for($both)->create(['starts_on' => '2026-10-05']);
        Offering::factory()->for($soon)->create();
        Offering::factory()->for(Cohort::factory()->for($both)->create(['starts_on' => '2027-01-11']))->create();
        // Session réservée à Saint-Louis : sa date ne compte pas pour Dakar.
        Offering::factory()->for(Cohort::factory()->for($both)->create(['starts_on' => '2026-10-01', 'place_id' => $saintLouis->id]))->create();

        $elsewhere = Program::factory()->create(['title' => 'Lumière', 'slug' => 'lumiere']);
        $elsewhere->campuses()->attach($saintLouis->id);
        Offering::factory()->for(Cohort::factory()->for($elsewhere))->create();

        $this->page([['type' => 'campus_programs', 'data' => ['title' => 'Formations à Dakar', 'campus_id' => $dakar->id]]]);

        $this->getJson('/api/v1/public/pages/essai')->assertOk()
            ->assertJsonPath('data.blocks.0.data.title', 'Formations à Dakar')
            ->assertJsonPath('data.blocks.0.data.campus.slug', $dakar->slug)
            ->assertJsonPath('data.blocks.0.data.campus.city', 'Dakar')
            ->assertJsonCount(1, 'data.blocks.0.data.items')
            ->assertJsonPath('data.blocks.0.data.items.0.title', 'Son live')
            ->assertJsonPath('data.blocks.0.data.items.0.summary', 'Sonoriser un concert.')
            ->assertJsonPath('data.blocks.0.data.items.0.nextStart', '2026-10-05')
            ->assertJsonPath('data.blocks.0.data.items.0.applyUrl', "/candidater?campus={$dakar->slug}&formation=son-live");
    }

    public function test_campus_programs_without_available_program_or_campus_is_empty(): void
    {
        $dakar = $this->campus('Dakar');
        $this->page([
            ['type' => 'campus_programs', 'data' => ['title' => 'Formations', 'campus_id' => $dakar->id]],
            ['type' => 'campus_programs', 'data' => ['title' => 'Formations', 'campus_id' => 9999]],
        ]);

        $this->getJson('/api/v1/public/pages/essai')->assertOk()
            ->assertJsonPath('data.blocks.0.data.items', [])
            ->assertJsonPath('data.blocks.1.data.campus', null)
            ->assertJsonPath('data.blocks.1.data.items', []);
    }

    public function test_downloads_block_gives_url_size_and_extension_and_skips_missing_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('pages/documents/brochure.pdf', str_repeat('x', 1234));
        $this->page([['type' => 'downloads', 'data' => ['title' => 'Documents', 'files' => [
            ['file' => 'pages/documents/brochure.pdf', 'title' => 'Brochure 2026', 'description' => 'Toutes nos formations'],
            ['file' => 'pages/documents/absent.pdf', 'title' => 'Absent'],
        ]]]]);

        $this->getJson('/api/v1/public/pages/essai')->assertOk()
            ->assertJsonCount(1, 'data.blocks.0.data.files')
            ->assertJsonPath('data.blocks.0.data.files.0.title', 'Brochure 2026')
            ->assertJsonPath('data.blocks.0.data.files.0.description', 'Toutes nos formations')
            ->assertJsonPath('data.blocks.0.data.files.0.url', Storage::disk('public')->url('pages/documents/brochure.pdf'))
            ->assertJsonPath('data.blocks.0.data.files.0.size', 1234)
            ->assertJsonPath('data.blocks.0.data.files.0.extension', 'pdf');
    }

    public function test_support_form_block_keeps_its_title_and_text(): void
    {
        $this->page([['type' => 'support_form', 'data' => ['title' => 'Nous soutenir', 'text' => 'Partenaires, mécènes, donateurs.']]]);

        $this->getJson('/api/v1/public/pages/essai')->assertOk()
            ->assertJsonPath('data.blocks.0.type', 'support_form')
            ->assertJsonPath('data.blocks.0.data.title', 'Nous soutenir')
            ->assertJsonPath('data.blocks.0.data.text', 'Partenaires, mécènes, donateurs.');
    }

    public function test_campus_cards_link_to_the_campus_page(): void
    {
        $dakar = $this->campus('Dakar');
        $saintLouis = $this->campus('Saint-Louis');
        $thies = $this->campus('Thiès');
        // Page du campus repérée par son bloc « Formations de ce campus », même renommée par l'équipe…
        Page::create(['title' => 'Campus de Dakar', 'slug' => 'emsi/campus-dakar', 'type' => 'free', 'draft_blocks' => [
            ['type' => 'campus_programs', 'data' => ['title' => 'Formations', 'campus_id' => $dakar->id]],
        ]])->publish();
        // … sinon par l'adresse /emsi/{ville}.
        Page::create(['title' => 'Campus de Saint-Louis', 'slug' => 'emsi/saint-louis', 'type' => 'free', 'draft_blocks' => [
            ['type' => 'text', 'data' => ['title' => 'Saint-Louis', 'body' => '<p>Campus.</p>']],
        ]])->publish();
        // Page en brouillon : pas de lien vers une page absente du site.
        Page::create(['title' => 'Campus de Thiès', 'slug' => 'emsi/thies', 'type' => 'free', 'draft_blocks' => [
            ['type' => 'campus_programs', 'data' => ['title' => 'Formations', 'campus_id' => $thies->id]],
        ]]);
        $this->page([['type' => 'campuses', 'data' => ['title' => 'Nos campus']]]);

        $items = collect($this->getJson('/api/v1/public/pages/essai')->assertOk()->json('data.blocks.0.data.items'))->keyBy('id');

        $this->assertSame('/emsi/campus-dakar', $items[$dakar->id]['pageUrl']);
        $this->assertSame('/emsi/saint-louis', $items[$saintLouis->id]['pageUrl']);
        $this->assertNull($items[$thies->id]['pageUrl']);
    }
}
