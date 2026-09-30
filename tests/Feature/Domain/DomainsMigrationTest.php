<?php

namespace Tests\Feature\Domain;

use App\Enums\SiteDomain;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/** Domaines de page, sous-menus et disponibilité par campus : structure et réversibilité. */
class DomainsMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_a_parent_menu_removes_its_children(): void
    {
        $parent = MenuItem::create(['location' => 'main', 'label' => 'EMSI', 'url' => '/emsi', 'position' => 1]);
        $child = MenuItem::create(['location' => 'main', 'label' => 'Formations', 'url' => '/emsi/formations', 'position' => 1, 'parent_id' => $parent->id]);

        $this->assertTrue($parent->children->first()->is($child));
        $this->assertTrue($child->parent->is($parent));

        $parent->delete();
        $this->assertNull(MenuItem::find($child->id));
    }

    public function test_page_domain_defaults_to_general(): void
    {
        $page = Page::create(['title' => 'Test', 'slug' => 'test', 'type' => 'standard'])->fresh();

        $this->assertSame(SiteDomain::GENERAL, $page->domain);
        $this->assertSame('#ff7a1a', SiteDomain::EMSI->color());
        $this->assertSame('Maison Habib Faye', SiteDomain::MAISON->label());
    }

    /** Classe sans données : sous MySQL une modification de structure validerait la transaction du test. */
    public function test_migration_rolls_back_cleanly(): void
    {
        $path = 'database/migrations/2026_09_29_100000_add_domains_and_campus_availability.php';
        $this->artisan('migrate:rollback', ['--path' => $path])->assertSuccessful();

        $this->assertFalse(Schema::hasColumn('pages', 'domain'));
        $this->assertFalse(Schema::hasColumn('menu_items', 'parent_id'));
        $this->assertFalse(Schema::hasColumn('cohorts', 'place_id'));
        $this->assertFalse(Schema::hasColumn('contact_messages', 'organization'));
        $this->assertFalse(Schema::hasTable('place_program'));

        $this->artisan('migrate', ['--path' => $path])->assertSuccessful();

        $this->assertTrue(Schema::hasColumn('pages', 'domain'));
        $this->assertTrue(Schema::hasColumn('menu_items', 'parent_id'));
        $this->assertTrue(Schema::hasColumn('cohorts', 'place_id'));
        $this->assertTrue(Schema::hasColumn('contact_messages', 'organization'));
        $this->assertTrue(Schema::hasTable('place_program'));
    }

    /** Repère de version du site (emsi:site-v4 déjà faite) : colonne ajoutée puis retirée proprement. */
    public function test_site_version_migration_rolls_back_cleanly(): void
    {
        $path = 'database/migrations/2026_09_30_100000_add_site_version_to_settings.php';
        $this->artisan('migrate:rollback', ['--path' => $path])->assertSuccessful();
        $this->assertFalse(Schema::hasColumn('settings', 'site_version'));

        $this->artisan('migrate', ['--path' => $path])->assertSuccessful();
        $this->assertTrue(Schema::hasColumn('settings', 'site_version'));
    }
}
