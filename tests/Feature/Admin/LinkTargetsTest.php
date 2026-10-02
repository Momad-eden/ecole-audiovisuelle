<?php

namespace Tests\Feature\Admin;

use App\Enums\PublicationStatus;
use App\Filament\Support\LinkTargets;
use App\Models\Page;
use App\Models\Place;
use App\Models\Room;
use App\Support\LegacyPaths;
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

        $this->assertSame('Centre culturel Habib Faye › Impact Live Studio › Réserver une séance', $flat['/centre-culturel/studio#reserver']);
        $this->assertSame('Centre culturel Habib Faye › Impact Live Studio › Écouter les productions', $flat['/centre-culturel/studio#productions']);
        $this->assertSame('EMSI › Campus de Dakar', $flat['/emsi/dakar']);
        $this->assertSame('EMSI › Campus de Saint-Louis', $flat['/emsi/saint-louis']);
        $this->assertSame('Centre culturel Habib Faye › Programmation', $flat['/centre-culturel/agenda']);
        $this->assertSame('Nous soutenir', $flat['/soutenir']);
        $this->assertSame('EMSI › Univers › Son', $flat['/emsi/univers/son']);
        $this->assertSame('EMSI › Candidater à Saint-Louis', $flat['/candidater?campus=emsi-saint-louis']);
        $this->assertSame('Nos tarifs', $flat['/nos-tarifs']);

        // Plus d'anciennes adresses ni d'Impact Live Events.
        foreach (array_keys($flat) as $url) {
            $this->assertSame($url, LegacyPaths::rewrite($url), $url);
            $this->assertStringNotContainsString('/events', $url);
        }
        $this->assertArrayNotHasKey('/demande', $flat);
    }

    public function test_searching_finds_a_destination_by_name_and_accepts_a_typed_address(): void
    {
        $this->assertArrayHasKey('/centre-culturel/studio#reserver', LinkTargets::search('réserver'));
        $this->assertArrayHasKey('/emsi/dakar', LinkTargets::search('campus de dakar'));
        $this->assertSame(['https://wa.me/221776807062' => 'Utiliser l\'adresse « https://wa.me/221776807062 »'], LinkTargets::search('https://wa.me/221776807062'));
        $this->assertSame([], LinkTargets::search('rien de tel'));
        $this->assertSame('Centre culturel Habib Faye › Impact Live Studio › Réserver une séance', LinkTargets::label('/centre-culturel/studio#reserver'));
        $this->assertSame('https://youtube.com/x', LinkTargets::label('https://youtube.com/x'));
    }
}
