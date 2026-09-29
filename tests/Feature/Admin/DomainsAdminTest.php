<?php

namespace Tests\Feature\Admin;

use App\Enums\PublicationStatus;
use App\Enums\SiteDomain;
use App\Filament\Resources\Cohorts\CohortResource;
use App\Filament\Resources\Cohorts\Pages\CreateCohort;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\EquipmentCategories\EquipmentCategoryResource;
use App\Filament\Resources\EquipmentItems\EquipmentItemResource;
use App\Filament\Resources\MenuItems\MenuItemResource;
use App\Filament\Resources\MenuItems\Pages\CreateMenuItem;
use App\Filament\Resources\MenuItems\Pages\EditMenuItem;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Programs\Pages\CreateProgram;
use App\Filament\Resources\Programs\ProgramResource;
use App\Filament\Resources\RentalPacks\RentalPackResource;
use App\Filament\Support\PageBlocks;
use App\Models\Cohort;
use App\Models\ContactMessage;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Place;
use App\Models\Program;
use App\Models\User;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/** Domaine des pages, sous-menus, campus des formations, retrait d'Impact Live Events. */
class DomainsAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'directeur']));
    }

    private function campus(string $name, PublicationStatus $status = PublicationStatus::PUBLISHED): Place
    {
        return Place::create(['name' => $name, 'slug' => str($name)->slug(), 'kind' => 'campus', 'status' => $status]);
    }

    public function test_page_form_offers_the_domain_field(): void
    {
        $page = Page::create(['title' => 'Essai', 'slug' => 'essai', 'type' => 'free']);
        $this->get(PageResource::getUrl('edit', ['record' => $page]))->assertOk()
            ->assertSee('Domaine')->assertSee('Donne sa couleur et son menu à la page')
            ->assertSeeInOrder(['Général', 'Maison Habib Faye', 'EMSI', 'Impact Live Studio'])->assertSee('Maison Habib Faye')->assertSee('EMSI')->assertSee('Impact Live Studio')->assertSee('Général');
    }

    public function test_menu_item_form_offers_a_parent_for_main_menu(): void
    {
        $top = MenuItem::create(['location' => 'main', 'label' => 'Racine', 'url' => '/a']);
        MenuItem::create(['location' => 'main', 'label' => 'Enfant', 'url' => '/b', 'parent_id' => $top->id]);
        MenuItem::create(['location' => 'footer', 'label' => 'Pied', 'url' => '/c']);
        $free = MenuItem::create(['location' => 'main', 'label' => 'Libre', 'url' => '/d']);

        $this->get(MenuItemResource::getUrl('create'))->assertOk()->assertSee('Sous-menu de');

        Livewire::test(CreateMenuItem::class)
            ->assertFormFieldExists('parent_id', fn (Select $field) => $field->getOptions() === [$free->id => 'Libre']);
    }

    public function test_program_form_offers_campus_availability(): void
    {
        $this->campus('Dakar');
        $this->campus('Campus fantome', PublicationStatus::DRAFT);

        $this->get(ProgramResource::getUrl('create'))->assertOk()->assertSee('Disponible à')->assertSee('Dakar')->assertDontSee('Campus fantome');
    }

    public function test_cohort_form_offers_a_campus(): void
    {
        $this->campus('Saint-Louis');

        $this->get(CohortResource::getUrl('create'))->assertOk()->assertSee('Les deux campus')->assertSee('Saint-Louis');
    }

    public function test_events_resources_are_hidden_from_navigation(): void
    {
        $this->assertFalse(EquipmentItemResource::shouldRegisterNavigation());
        $this->assertFalse(EquipmentCategoryResource::shouldRegisterNavigation());
        $this->assertFalse(RentalPackResource::shouldRegisterNavigation());
        $this->get(EquipmentItemResource::getUrl('index'))->assertOk();
    }

    public function test_contact_messages_show_organization(): void
    {
        ContactMessage::create(['subject' => 'support', 'name' => 'Awa', 'organization' => 'Fondation Sahel', 'message' => 'Bonjour']);
        $this->get(ContactMessageResource::getUrl('index').'?tableFilters[status][value]=')->assertOk()->assertSee('Organisation')->assertSee('Fondation Sahel');
    }

    public function test_block_library_drops_events_blocks_and_limits_booking_types(): void
    {
        $blocks = collect(PageBlocks::all())->keyBy(fn (Block $b) => $b->getName());
        $this->assertFalse($blocks->has('equipment_list'));
        $this->assertFalse($blocks->has('packs'));

        $type = collect($blocks['booking_form']->getDefaultChildComponents())->first(fn ($c) => $c->getName() === 'booking_type');
        $this->assertSame(['studio_session' => 'Session studio', 'space_rental' => 'Location de l\'Espace Habib Faye'], $type->getOptions());
    }

    public function test_menu_parent_options_on_edit_exclude_self_and_items_with_children(): void
    {
        $parent = MenuItem::create(['location' => 'main', 'label' => 'Parent', 'url' => '/p']);
        MenuItem::create(['location' => 'main', 'label' => 'Enfant', 'url' => '/e', 'parent_id' => $parent->id]);
        $plain = MenuItem::create(['location' => 'main', 'label' => 'Simple', 'url' => '/s']);
        $other = MenuItem::create(['location' => 'main', 'label' => 'Autre', 'url' => '/o']);

        Livewire::test(EditMenuItem::class, ['record' => $plain->id])
            ->assertFormFieldExists('parent_id', fn (Select $f) => $f->getOptions() === [$other->id => 'Autre']);

        Livewire::test(EditMenuItem::class, ['record' => $parent->id])
            ->assertFormFieldIsDisabled('parent_id');
    }

    public function test_program_is_saved_with_its_campus(): void
    {
        $dakar = $this->campus('Dakar');
        $this->campus('Saint-Louis');

        Livewire::test(CreateProgram::class)
            ->fillForm(['title' => 'Régie son', 'audience' => 'school', 'kind' => 'certificate', 'campuses' => [$dakar->id], 'status' => 'published'])
            ->call('create')->assertHasNoFormErrors();

        $this->assertSame([$dakar->id], Program::where('title', 'Régie son')->firstOrFail()->campuses()->pluck('places.id')->all());
    }

    public function test_cohort_is_saved_with_a_campus_and_unpublished_ones_are_not_offered(): void
    {
        $dakar = $this->campus('Dakar');
        $this->campus('Campus fantome', PublicationStatus::DRAFT);
        $program = Program::factory()->create();

        Livewire::test(CreateCohort::class)
            ->assertFormFieldExists('place_id', fn (Select $f) => array_values($f->getOptions()) === ['Dakar'])
            ->fillForm(['program_id' => $program->id, 'name' => 'Volet 1', 'status' => 'planned', 'place_id' => $dakar->id])
            ->call('create')->assertHasNoFormErrors();

        $this->assertSame($dakar->id, Cohort::where('name', 'Volet 1')->firstOrFail()->place_id);
    }

    public function test_page_is_saved_with_its_domain(): void
    {
        $page = Page::create(['title' => 'Essai', 'slug' => 'essai', 'type' => 'free']);

        Livewire::test(EditPage::class, ['record' => $page->id])
            ->fillForm(['domain' => 'maison'])->call('save')->assertHasNoFormErrors();

        $this->assertSame(SiteDomain::MAISON, $page->fresh()->domain);
    }
}
