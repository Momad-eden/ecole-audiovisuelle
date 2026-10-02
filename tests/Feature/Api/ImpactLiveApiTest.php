<?php

namespace Tests\Feature\Api;

use App\Enums\PublicationStatus;
use App\Models\AgendaEvent;
use App\Models\Artwork;
use App\Models\EquipmentCategory;
use App\Models\EquipmentItem;
use App\Models\Place;
use App\Models\RentalPack;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ImpactLiveApiTest extends TestCase
{
    use RefreshDatabase;

    private const PUBLISHED = ['status' => PublicationStatus::PUBLISHED];

    public function test_impact_live_migration_is_reversible(): void
    {
        $tables = ['places', 'services', 'equipment_categories', 'equipment_items', 'rental_packs', 'agenda_events', 'booking_requests', 'booking_request_logs'];
        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), $table);
        }
        $this->assertTrue(Schema::hasColumn('artworks', 'origin'));
        $this->assertTrue(Schema::hasColumn('applications', 'place_id'));

        // Retour arrière dans l'ordre réel : d'abord les migrations plus récentes qui dépendent des lieux.
        $later = [
            require database_path('migrations/2026_09_29_100000_add_domains_and_campus_availability.php'),
            require database_path('migrations/2026_09_28_100000_add_presentation_to_places.php'),
            require database_path('migrations/2026_09_28_090000_add_campus_to_accounting.php'),
        ];
        foreach ($later as $step) {
            $step->down();
        }
        $migration = require database_path('migrations/2026_09_27_120000_create_impact_live_tables.php');
        $migration->down();
        foreach ($tables as $table) {
            $this->assertFalse(Schema::hasTable($table), $table);
        }
        $this->assertFalse(Schema::hasColumn('artworks', 'origin'));
        $this->assertFalse(Schema::hasColumn('applications', 'place_id'));

        $migration->up();
        foreach (array_reverse($later) as $step) {
            $step->up();
        }
        $this->assertTrue(Schema::hasTable('booking_requests'));
    }

    public function test_services_show_a_from_price_or_on_quote(): void
    {
        Service::create(['name' => 'Mixage', 'activity' => 'studio', 'price_from' => 25000, 'price_unit' => 'track', ...self::PUBLISHED]);
        Service::create(['name' => 'Mastering', 'activity' => 'studio', ...self::PUBLISHED]);
        Service::create(['name' => 'Sonorisation', 'activity' => 'events', ...self::PUBLISHED]);
        Service::create(['name' => 'Brouillon', 'activity' => 'studio', 'status' => PublicationStatus::DRAFT]);

        $this->getJson('/api/v1/public/services?activity=studio')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Mixage')
            ->assertJsonPath('data.0.priceFrom', 25000)
            ->assertJsonPath('data.0.priceLabel', 'À partir de 25 000 FCFA / titre')
            ->assertJsonPath('data.1.priceLabel', 'Sur devis');
    }

    public function test_rental_catalogue_by_category_with_item_details(): void
    {
        $sound = EquipmentCategory::create(['name' => 'Sonorisation', 'position' => 0]);
        $light = EquipmentCategory::create(['name' => 'Lumière', 'position' => 1]);
        EquipmentItem::create(['name' => 'Line array K2', 'brand' => 'L-Acoustics', 'equipment_category_id' => $sound->id, 'usage' => 'rental', 'price_from' => 150000,
            'specs' => [['label' => 'Puissance', 'value' => '2 × 1 000 W']], 'quantity' => 12, ...self::PUBLISHED]);
        EquipmentItem::create(['name' => 'Lyre Spot', 'equipment_category_id' => $light->id, 'usage' => 'rental', ...self::PUBLISHED]);
        EquipmentItem::create(['name' => 'Console du studio', 'equipment_category_id' => $sound->id, 'usage' => 'studio', ...self::PUBLISHED]);

        $this->getJson('/api/v1/public/equipment-categories')->assertOk()
            ->assertJsonPath('data.0.slug', 'sonorisation')
            ->assertJsonPath('data.0.itemsCount', 1);

        $this->getJson('/api/v1/public/equipment?usage=rental&category=sonorisation')->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Line array K2')
            ->assertJsonPath('data.0.priceLabel', 'À partir de 150 000 FCFA / jour');

        $this->getJson('/api/v1/public/equipment?usage=studio')->assertOk()->assertJsonPath('data.0.name', 'Console du studio');

        $this->getJson('/api/v1/public/equipment/line-array-k2')->assertOk()
            ->assertJsonPath('data.brand', 'L-Acoustics')
            ->assertJsonPath('data.category.name', 'Sonorisation')
            ->assertJsonPath('data.specs.0.label', 'Puissance')
            ->assertJsonPath('data.quantity', 12);
    }

    public function test_packs_agenda_and_places(): void
    {
        RentalPack::create(['name' => 'Pack concert', 'capacity' => 'Jusqu\'à 500 personnes', 'contents' => ['2 têtes line array', 'Console numérique'], ...self::PUBLISHED]);
        AgendaEvent::create(['title' => 'Concert à venir', 'activity' => 'space', 'starts_at' => now()->addWeek(), ...self::PUBLISHED]);
        AgendaEvent::create(['title' => 'Concert passé', 'activity' => 'events', 'starts_at' => now()->subWeek(), ...self::PUBLISHED]);
        AgendaEvent::create(['title' => 'Festival de Saint-Louis', 'activity' => 'events', 'is_reference' => true, ...self::PUBLISHED]);
        Place::create(['name' => 'EMSI Dakar', 'kind' => 'campus', 'city' => 'Dakar', ...self::PUBLISHED]);
        Place::create(['name' => 'Impact Live Studio', 'kind' => 'studio', 'city' => 'Saint-Louis', ...self::PUBLISHED]);

        $this->getJson('/api/v1/public/packs')->assertOk()->assertJsonPath('data.0.contents.1', 'Console numérique')->assertJsonPath('data.0.priceLabel', 'Sur devis');

        $this->getJson('/api/v1/public/agenda')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Concert à venir')->assertJsonPath('data.0.activity', 'space');
        $this->getJson('/api/v1/public/agenda?scope=references')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Festival de Saint-Louis');
        $this->getJson('/api/v1/public/agenda/concert-a-venir')->assertOk()->assertJsonPath('data.activityLabel', 'Centre culturel Habib Faye');

        $this->getJson('/api/v1/public/places?kind=campus')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.city', 'Dakar');
        $this->getJson('/api/v1/public/site')->assertOk()->assertJsonCount(2, 'data.places');
    }

    public function test_studio_productions_and_student_realisations_are_listed_separately(): void
    {
        Artwork::create(['title' => 'Court métrage', 'kind' => 'video', 'origin' => 'school', ...self::PUBLISHED]);
        Artwork::create(['title' => 'Single enregistré au studio', 'kind' => 'audio', 'origin' => 'studio', ...self::PUBLISHED]);

        $this->getJson('/api/v1/public/artworks')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Court métrage');
        $this->getJson('/api/v1/public/artworks?origin=studio')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.title', 'Single enregistré au studio');
    }
}
