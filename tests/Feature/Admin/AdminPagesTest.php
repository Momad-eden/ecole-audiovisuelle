<?php

namespace Tests\Feature\Admin;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\Applications\ApplicationResource;
use App\Filament\Resources\BookingRequests\BookingRequestResource;
use App\Filament\Resources\CashTransactions\CashTransactionResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Rooms\RoomResource;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Application;
use App\Models\BookingRequest;
use App\Models\Enrollment;
use App\Models\Page;
use App\Models\Room;
use App\Models\User;
use App\Services\CashRegister;
use Database\Seeders\ContentSeeder;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Chaque écran de l'administration s'ouvre sans erreur pour le directeur. */
class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    private User $directeur;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directeur = User::factory()->create(['role' => 'directeur']);
    }

    public function test_every_resource_index_and_create_page_renders(): void
    {
        $this->actingAs($this->directeur);

        /** @var class-string<resource> $resource */
        foreach (Filament::getPanel('admin')->getResources() as $resource) {
            $this->assertSame(200, $this->get($resource::getUrl('index'))->getStatusCode(), "Liste {$resource}");

            if ($resource::hasPage('create') && $resource::canCreate()) {
                $this->assertSame(200, $this->get($resource::getUrl('create'))->getStatusCode(), "Création {$resource}");
            }
        }
    }

    public function test_dashboard_renders_for_every_role(): void
    {
        foreach (['directeur', 'gestionnaire', 'secretaire', 'communication', 'commercial'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))->get('/admin')->assertOk();
        }
    }

    public function test_record_pages_render(): void
    {
        $this->actingAs($this->directeur);
        $application = Application::factory()->status(ApplicationStatus::ACCEPTED)->create(['documents' => []]);
        $enrollment = Enrollment::factory()->create();
        $transaction = app(CashRegister::class)->record([
            'direction' => 'in', 'category' => 'scolarite', 'amount' => 1000, 'method' => 'cash',
            'occurred_on' => now()->toDateString(), 'enrollment_id' => $enrollment->id,
        ], $this->directeur);

        $this->get(ApplicationResource::getUrl('view', ['record' => $application]))->assertOk()->assertSee($application->reference);
        $this->get(ApplicationResource::getUrl('edit', ['record' => $application]))->assertOk();
        $this->get(StudentResource::getUrl('view', ['record' => $enrollment->student]))->assertOk();
        $this->get(CashTransactionResource::getUrl('view', ['record' => $transaction]))->assertOk()->assertSee($transaction->number);
        $this->get(route('admin.cash.receipt', $transaction))->assertOk()->assertSee($transaction->number)->assertSee('1 000 FCFA');
        $this->get(route('admin.cash.export', ['from' => now()->startOfMonth()->toDateString(), 'until' => now()->toDateString()]))->assertOk();
    }

    public function test_v2_home_page_and_universe_open_in_the_editor(): void
    {
        $this->seed(ContentSeeder::class);
        $this->actingAs($this->directeur);

        $home = Page::where('slug', 'accueil')->firstOrFail();
        $this->get(PageResource::getUrl('edit', ['record' => $home]))->assertOk()
            ->assertSee('Mots qui défilent à la fin du titre')
            ->assertSee('Le lieu (Grand Théâtre)')
            ->assertSee('Le matériel');

        $cinema = Room::where('slug', 'cinema')->firstOrFail();
        $this->get(RoomResource::getUrl('edit', ['record' => $cinema]))->assertOk()
            ->assertSee('Animation de l&#039;univers', false)
            ->assertSee('Bientôt à l&#039;EMSI', false);
    }

    public function test_a_booking_request_opens_with_its_items_and_history(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'commercial']));
        $request = BookingRequest::create(['type' => 'equipment_rental', 'name' => 'Label Ndar', 'phone' => '+221 77 000 00 00',
            'items' => [['kind' => 'equipment', 'id' => 1, 'name' => 'Line array K2', 'quantity' => 4]]]);

        $this->get(BookingRequestResource::getUrl('view', ['record' => $request]))->assertOk()
            ->assertSee($request->reference)->assertSee('Line array K2')->assertSee('Demande reçue')->assertSee('Devis envoyé');
        $this->get('/admin')->assertOk()->assertSee('Impact Live');
    }
}
