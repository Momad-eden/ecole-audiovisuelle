<?php

namespace Tests\Feature\Admin;

use App\Enums\PublicationStatus;
use App\Filament\Support\LinkTargets;
use App\Models\Page;
use App\Models\Place;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Destinations proposées pour les boutons et liens des pages : des noms clairs au lieu d'adresses à taper. */
class LinkTargetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_destinations_include_pages_sections_universes_and_campuses_with_readable_names(): void
    {
        Room::create(['name' => 'Son', 'slug' => 'son', 'accent_color' => '#8B6CFF', 'status' => PublicationStatus::PUBLISHED]);
        Place::create(['name' => 'EMSI Saint-Louis', 'slug' => 'emsi-saint-louis', 'kind' => 'campus', 'city' => 'Saint-Louis', 'status' => PublicationStatus::PUBLISHED]);
        Page::create(['title' => 'Nos tarifs', 'slug' => 'nos-tarifs', 'type' => 'free', 'draft_blocks' => []])->publish();

        $flat = LinkTargets::flat();

        $this->assertSame('Studio › Réserver une session', $flat['/studio#reserver']);
        $this->assertSame('Studio › Écouter les productions', $flat['/studio#productions']);
        $this->assertArrayHasKey('/events#devis', $flat);
        $this->assertSame('Univers › Son', $flat['/univers/son']);
        $this->assertSame('Candidater à Saint-Louis', $flat['/candidater?campus=emsi-saint-louis']);
        $this->assertSame('Nos tarifs', $flat['/nos-tarifs']);
    }

    public function test_searching_finds_a_destination_by_name_and_accepts_a_typed_address(): void
    {
        $this->assertArrayHasKey('/studio#reserver', LinkTargets::search('réserver'));
        $this->assertSame(['https://wa.me/221776807062' => 'Utiliser l\'adresse « https://wa.me/221776807062 »'], LinkTargets::search('https://wa.me/221776807062'));
        $this->assertSame([], LinkTargets::search('rien de tel'));
        $this->assertSame('Studio › Réserver une session', LinkTargets::label('/studio#reserver'));
        $this->assertSame('https://youtube.com/x', LinkTargets::label('https://youtube.com/x'));
    }
}
