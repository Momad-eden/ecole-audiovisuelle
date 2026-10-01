<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use Database\Seeders\MenuDefaults;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_about_group_is_added_after_the_other_entries_with_its_six_links(): void
    {
        MenuItem::create(['location' => 'main', 'label' => 'EMSI', 'url' => '/emsi', 'position' => 2]);
        MenuItem::create(['location' => 'main', 'label' => 'Candidater', 'url' => '/candidater', 'is_button' => true, 'position' => 3]);

        MenuDefaults::addAboutGroup();

        $about = MenuItem::where('label', 'À propos')->sole();
        $this->assertSame('main', $about->location);
        $this->assertNull($about->parent_id);
        $this->assertSame(4, $about->position);
        $this->assertSame(['/mission', '/partenaires', '/soutenir', '/actualites', '/presse', '/contact'],
            MenuItem::where('parent_id', $about->id)->orderBy('position')->pluck('url')->all());
    }

    public function test_the_about_group_is_never_created_twice(): void
    {
        MenuDefaults::addAboutGroup();
        MenuDefaults::addAboutGroup();

        $this->assertSame(1, MenuItem::where('label', 'À propos')->count());
        $this->assertSame(6, MenuItem::whereNotNull('parent_id')->count());
    }

    public function test_descriptions_fill_only_empty_submenu_links(): void
    {
        $parent = MenuItem::create(['location' => 'main', 'label' => 'EMSI', 'url' => '/emsi']);
        $empty = MenuItem::create(['location' => 'main', 'parent_id' => $parent->id, 'label' => 'Formations', 'url' => '/emsi/formations']);
        $written = MenuItem::create(['location' => 'main', 'parent_id' => $parent->id, 'label' => 'Dakar', 'url' => '/emsi/dakar', 'description' => 'Écrit par l\'équipe']);
        $unknown = MenuItem::create(['location' => 'main', 'parent_id' => $parent->id, 'label' => 'Autre', 'url' => '/autre']);
        $footer = MenuItem::create(['location' => 'footer', 'label' => 'Contact', 'url' => '/contact']);

        MenuDefaults::fillDescriptions();

        $this->assertSame(MenuDefaults::DESCRIPTIONS['/emsi/formations'], $empty->fresh()->description);
        $this->assertSame('Écrit par l\'équipe', $written->fresh()->description);
        $this->assertNull($unknown->fresh()->description);
        $this->assertNull($footer->fresh()->description);
        $this->assertNull($parent->fresh()->description);
    }
}
