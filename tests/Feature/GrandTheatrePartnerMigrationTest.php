<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GrandTheatrePartnerMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_grand_theatre_becomes_a_partner_like_the_others(): void
    {
        $theatre = Partner::create(['name' => 'Grand Théâtre National Doudou Ndiaye Coumba Rose', 'category' => 'co_organizer', 'is_active' => true,
            'description' => 'Co-porteur du programme : met à disposition ses salles, plateaux et équipements techniques.']);
        $page = Page::create(['title' => 'Campus de Dakar', 'slug' => 'emsi/dakar', 'draft_blocks' => [
            ['type' => 'partners', 'data' => ['title' => 'Notre partenaire, le Grand Théâtre National', 'categories' => ['co_organizer']]],
            ['type' => 'partners', 'data' => ['title' => 'Titre de l\'équipe', 'categories' => ['media']]],
        ]]);
        $page->publish();

        $migration = require database_path('migrations/2026_10_01_160000_grand_theatre_is_a_partner.php');
        $migration->up();

        $theatre->refresh();
        $this->assertSame('institutional', $theatre->category);
        $this->assertStringStartsWith('Partenaire du campus de Dakar', $theatre->description);
        $blocks = $page->fresh()->blocks;
        $this->assertSame(['title' => 'Nos partenaires', 'categories' => ['institutional']], $blocks[0]['data']);
        $this->assertSame(['title' => 'Titre de l\'équipe', 'categories' => ['media']], $blocks[1]['data'], 'Un bloc retouché par l\'équipe ne change pas.');
    }
}
