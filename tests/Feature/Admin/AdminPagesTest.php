<?php

namespace Tests\Feature\Admin;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\Applications\ApplicationResource;
use App\Filament\Resources\CashTransactions\CashTransactionResource;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Application;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\CashRegister;
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
        foreach (['directeur', 'gestionnaire', 'secretaire', 'communication'] as $role) {
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
}
