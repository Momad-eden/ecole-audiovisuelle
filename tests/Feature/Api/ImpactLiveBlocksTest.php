<?php

namespace Tests\Feature\Api;

use App\Enums\PublicationStatus;
use App\Models\AgendaEvent;
use App\Models\Artwork;
use App\Models\EquipmentCategory;
use App\Models\EquipmentItem;
use App\Models\Page;
use App\Models\Place;
use App\Models\RentalPack;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Les blocs des pages Studio, Events et Centre culturel Habib Faye incluent leurs données. */
class ImpactLiveBlocksTest extends TestCase
{
    use RefreshDatabase;

    private const PUBLISHED = ['status' => PublicationStatus::PUBLISHED];

    public function test_impact_live_blocks_are_resolved_with_live_data(): void
    {
        Service::create(['name' => 'Mixage', 'activity' => 'studio', ...self::PUBLISHED]);
        Service::create(['name' => 'Sonorisation', 'activity' => 'events', ...self::PUBLISHED]);
        $sound = EquipmentCategory::create(['name' => 'Sonorisation']);
        EquipmentItem::create(['name' => 'Console Neve', 'equipment_category_id' => $sound->id, 'usage' => 'studio', ...self::PUBLISHED]);
        EquipmentItem::create(['name' => 'Line array', 'equipment_category_id' => $sound->id, 'usage' => 'rental', ...self::PUBLISHED]);
        RentalPack::create(['name' => 'Pack concert', ...self::PUBLISHED]);
        AgendaEvent::create(['title' => 'Festival de Saint-Louis', 'activity' => 'events', 'is_reference' => true, ...self::PUBLISHED]);
        Artwork::create(['title' => 'Single du studio', 'kind' => 'audio', 'origin' => 'studio', ...self::PUBLISHED]);
        Artwork::create(['title' => 'Film étudiant', 'kind' => 'video', 'origin' => 'school', ...self::PUBLISHED]);
        Place::create(['name' => 'EMSI Saint-Louis', 'kind' => 'campus', 'city' => 'Saint-Louis', ...self::PUBLISHED]);

        Page::create(['title' => 'Studio', 'slug' => 'studio', 'draft_blocks' => [
            ['type' => 'hero', 'data' => ['title' => 'Enregistrer', 'layout' => 'studio']],
            ['type' => 'services', 'data' => ['title' => 'Nos services', 'activity' => 'studio']],
            ['type' => 'equipment_list', 'data' => ['title' => 'Le matériel', 'usage' => 'studio']],
            ['type' => 'productions', 'data' => ['title' => 'Écouter', 'limit' => 6]],
            ['type' => 'packs', 'data' => ['title' => 'Packs']],
            ['type' => 'agenda', 'data' => ['title' => 'Références', 'scope' => 'references']],
            ['type' => 'booking_form', 'data' => ['title' => 'Réserver', 'booking_type' => 'studio_session']],
            ['type' => 'ecosystem', 'data' => ['title' => 'Un écosystème', 'items' => [['name' => 'Impact Live Studio', 'activity' => 'studio', 'url' => '/studio', 'image' => 'pages/studio.jpg', 'image_alt' => 'Le studio']]]],
            ['type' => 'places', 'data' => ['title' => 'Nos campus', 'kind' => 'campus']],
        ]])->publish();

        $this->getJson('/api/v1/public/pages/studio')->assertOk()
            ->assertJsonPath('data.blocks.0.data.layout', 'studio')
            ->assertJsonCount(1, 'data.blocks.1.data.items')
            ->assertJsonPath('data.blocks.1.data.items.0.name', 'Mixage')
            ->assertJsonPath('data.blocks.2.data.items.0.name', 'Console Neve')
            ->assertJsonCount(1, 'data.blocks.2.data.items')
            ->assertJsonPath('data.blocks.3.data.items.0.title', 'Single du studio')
            ->assertJsonCount(1, 'data.blocks.3.data.items')
            ->assertJsonPath('data.blocks.4.data.items.0.name', 'Pack concert')
            ->assertJsonPath('data.blocks.5.data.items.0.title', 'Festival de Saint-Louis')
            ->assertJsonPath('data.blocks.6.data.bookingType', 'studio_session')
            ->assertJsonPath('data.blocks.7.data.items.0.image.url', url('/storage/pages/studio.jpg'))
            ->assertJsonPath('data.blocks.8.data.items.0.city', 'Saint-Louis');
    }
}
