<?php

namespace Tests\Feature\Api;

use App\Enums\PublicationStatus;
use App\Models\AgendaEvent;
use App\Models\EquipmentCategory;
use App\Models\EquipmentItem;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Détails relevés à l'analyse du projet : liens morts, plan du site, envoi de gros fichiers. */
class SiteHygieneTest extends TestCase
{
    use RefreshDatabase;

    public function test_menus_never_link_to_an_unpublished_page(): void
    {
        Page::create(['title' => 'Mentions légales', 'slug' => 'mentions-legales', 'draft_blocks' => []]);
        Page::create(['title' => 'Contact', 'slug' => 'contact', 'draft_blocks' => []])->publish();
        MenuItem::create(['label' => 'Mentions légales', 'url' => '/mentions-legales', 'location' => 'legal']);
        MenuItem::create(['label' => 'Contact', 'url' => '/contact', 'location' => 'legal']);
        MenuItem::create(['label' => 'Formations', 'url' => '/formations', 'location' => 'legal']);

        $this->getJson('/api/v1/public/site')->assertOk()
            ->assertJsonCount(2, 'data.menus.legal')
            ->assertJsonPath('data.menus.legal.0.url', '/contact')
            ->assertJsonPath('data.menus.legal.1.url', '/formations');
    }

    public function test_the_sitemap_lists_agenda_events_but_no_equipment(): void
    {
        AgendaEvent::create(['title' => 'Concert', 'activity' => 'space', 'starts_at' => now()->addWeek(), 'status' => PublicationStatus::PUBLISHED]);
        EquipmentItem::create(['name' => 'Line array', 'equipment_category_id' => EquipmentCategory::create(['name' => 'Son'])->id, 'usage' => 'rental', 'status' => PublicationStatus::PUBLISHED]);

        $this->getJson('/api/v1/public/sitemap')->assertOk()
            ->assertJsonFragment(['path' => '/maison-habib-faye/agenda/concert'])
            ->assertJsonMissing(['path' => '/events/materiel/line-array']);
    }

    public function test_the_admin_accepts_audio_files_up_to_50_mb(): void
    {
        $this->assertContains('max:51200', config('livewire.temporary_file_upload.rules'));
    }
}
