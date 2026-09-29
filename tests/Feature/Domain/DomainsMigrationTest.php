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
        $this->artisan('migrate:rollback', ['--step' => 1])->assertSuccessful();

        $this->assertFalse(Schema::hasColumn('pages', 'domain'));
        $this->assertFalse(Schema::hasColumn('menu_items', 'parent_id'));
        $this->assertFalse(Schema::hasColumn('cohorts', 'place_id'));
        $this->assertFalse(Schema::hasColumn('contact_messages', 'organization'));
        $this->assertFalse(Schema::hasTable('place_program'));

        $this->artisan('migrate')->assertSuccessful();

        $this->assertTrue(Schema::hasColumn('pages', 'domain'));
        $this->assertTrue(Schema::hasColumn('menu_items', 'parent_id'));
        $this->assertTrue(Schema::hasColumn('cohorts', 'place_id'));
        $this->assertTrue(Schema::hasColumn('contact_messages', 'organization'));
        $this->assertTrue(Schema::hasTable('place_program'));
    }
}
