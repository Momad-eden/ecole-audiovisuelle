<?php

namespace Tests\Feature\Domain;

use App\Enums\ApplicationStatus;
use App\Enums\PublicationStatus;
use App\Exceptions\BusinessRuleException;
use App\Filament\Resources\CashTransactions\CashTransactionResource;
use App\Filament\Resources\Students\StudentResource;
use App\Filament\Widgets\CashOverview;
use App\Models\Application;
use App\Models\CashTransaction;
use App\Models\Enrollment;
use App\Models\Place;
use App\Models\Student;
use App\Models\User;
use App\Services\ApplicationWorkflow;
use App\Services\CashRegister;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/** Chaque école (Dakar, Saint-Louis) tient sa propre caisse ; le personnel ne voit que son campus. */
class CampusAccountingTest extends TestCase
{
    use RefreshDatabase;

    private Place $dakar;

    private Place $saintLouis;

    private CashRegister $cash;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dakar = Place::create(['name' => 'EMSI Dakar', 'kind' => 'campus', 'code' => 'DKR', 'city' => 'Dakar', 'status' => PublicationStatus::PUBLISHED]);
        $this->saintLouis = Place::create(['name' => 'EMSI Saint-Louis', 'kind' => 'campus', 'code' => 'STL', 'city' => 'Saint-Louis', 'status' => PublicationStatus::PUBLISHED]);
        $this->cash = app(CashRegister::class);
    }

    private function user(string $role = 'gestionnaire', ?Place $campus = null): User
    {
        return User::factory()->create(['role' => $role, 'place_id' => $campus?->id]);
    }

    private function expense(Place $campus, int $amount, User $by, array $extra = []): CashTransaction
    {
        return $this->cash->record(array_merge(['direction' => 'out', 'category' => 'logistique', 'amount' => $amount, 'method' => 'cash',
            'occurred_on' => today()->toDateString(), 'place_id' => $campus->id, 'payee' => 'Librairie'], $extra), $by);
    }

    public function test_campus_columns_migration_is_reversible(): void
    {
        $migration = require database_path('migrations/2026_09_28_090000_add_campus_to_accounting.php');
        $migration->down();
        foreach (['cash_transactions', 'cash_closings', 'students', 'users'] as $table) {
            $this->assertFalse(Schema::hasColumn($table, 'place_id'), $table);
        }
        $this->assertFalse(Schema::hasColumn('places', 'code'));

        $migration->up();
        foreach (['cash_transactions', 'cash_closings', 'students', 'users'] as $table) {
            $this->assertTrue(Schema::hasColumn($table, 'place_id'), $table);
        }
    }

    public function test_each_campus_has_its_own_numbering_and_balance(): void
    {
        $director = $this->user('directeur');
        $year = now()->year;

        $d1 = $this->expense($this->dakar, 10_000, $director);
        $s1 = $this->expense($this->saintLouis, 3_000, $director);
        $d2 = $this->expense($this->dakar, 5_000, $director);

        $this->assertSame("DEP-DKR-{$year}-00001", $d1->number);
        $this->assertSame("DEP-STL-{$year}-00001", $s1->number);
        $this->assertSame("DEP-DKR-{$year}-00002", $d2->number);
        $this->assertSame(-15_000, $this->cash->balance($this->dakar));
        $this->assertSame(-3_000, $this->cash->balance($this->saintLouis));
        $this->assertSame(-18_000, $this->cash->balance());

        $reversal = $this->cash->cancel($d1, $director, 'Doublon');
        $this->assertSame($this->dakar->id, $reversal->place_id);
        $this->assertSame("REC-DKR-{$year}-00001", $reversal->number);
    }

    public function test_closings_are_per_campus(): void
    {
        $director = $this->user('directeur');
        $this->expense($this->dakar, 10_000, $director, ['occurred_on' => today()->subDays(3)->toDateString()]);
        $this->expense($this->saintLouis, 2_000, $director, ['occurred_on' => today()->subDays(3)->toDateString()]);

        $closing = $this->cash->close($this->dakar, today()->subDay(), null, $director);
        $this->assertSame(-10_000, $closing->closing_balance);
        $this->assertSame($this->dakar->id, $closing->place_id);

        // La période close à Dakar reste ouverte à Saint-Louis.
        $this->expense($this->saintLouis, 1_000, $director, ['occurred_on' => today()->subDays(2)->toDateString()]);
        $this->assertSame(-3_000, $this->cash->close($this->saintLouis, today()->subDay(), null, $director)->closing_balance);

        $this->expectException(BusinessRuleException::class);
        $this->expense($this->dakar, 1_000, $director, ['occurred_on' => today()->subDays(2)->toDateString()]);
    }

    public function test_tuition_is_booked_at_the_student_campus(): void
    {
        $director = $this->user('directeur');
        $student = Student::factory()->create(['place_id' => $this->saintLouis->id]);
        $enrollment = Enrollment::factory()->create(['student_id' => $student->id]);

        $receipt = $this->cash->record(['direction' => 'in', 'category' => 'scolarite', 'amount' => 50_000, 'method' => 'wave',
            'occurred_on' => today()->toDateString(), 'enrollment_id' => $enrollment->id], $director);
        $this->assertSame($this->saintLouis->id, $receipt->place_id);
        $this->assertStringStartsWith('REC-STL-', $receipt->number);

        $this->expectException(BusinessRuleException::class);
        $this->cash->record(['direction' => 'in', 'category' => 'scolarite', 'amount' => 50_000, 'method' => 'wave',
            'occurred_on' => today()->toDateString(), 'enrollment_id' => $enrollment->id, 'place_id' => $this->dakar->id], $director);
    }

    public function test_staff_bound_to_a_campus_only_use_and_see_their_campus(): void
    {
        $director = $this->user('directeur');
        $saintLouisStaff = $this->user('secretaire', $this->saintLouis);
        $dakarExpense = $this->expense($this->dakar, 10_000, $director);

        $own = $this->cash->record(['direction' => 'out', 'category' => 'logistique', 'amount' => 2_000, 'method' => 'cash',
            'occurred_on' => today()->toDateString(), 'payee' => 'Quincaillerie'], $saintLouisStaff);
        $this->assertSame($this->saintLouis->id, $own->place_id);

        $this->actingAs($saintLouisStaff)->get(CashTransactionResource::getUrl('index'))->assertOk()
            ->assertSee($own->number)->assertDontSee($dakarExpense->number);
        $this->actingAs($saintLouisStaff)->get(CashTransactionResource::getUrl('view', ['record' => $dakarExpense]))->assertNotFound();

        $dakarStudent = Student::factory()->create(['place_id' => $this->dakar->id, 'last_name' => 'Diallo-Dakar']);
        $this->actingAs($saintLouisStaff)->get(StudentResource::getUrl('index'))->assertOk()->assertDontSee('Diallo-Dakar');

        $this->expectException(BusinessRuleException::class);
        $this->expense($this->dakar, 1_000, $saintLouisStaff);
    }

    public function test_a_second_campus_requires_choosing_where_to_book(): void
    {
        $this->expectException(BusinessRuleException::class);
        $this->cash->record(['direction' => 'out', 'category' => 'logistique', 'amount' => 1_000, 'method' => 'cash',
            'occurred_on' => today()->toDateString(), 'payee' => 'X'], $this->user('directeur'));
    }

    public function test_enrolling_keeps_the_campus_chosen_by_the_candidate(): void
    {
        $application = Application::factory()->status(ApplicationStatus::ACCEPTED)->create(['place_id' => $this->saintLouis->id, 'documents' => []]);

        $enrollment = app(ApplicationWorkflow::class)->enroll($application, $this->user('directeur'));

        $this->assertSame($this->saintLouis->id, $enrollment->student->place_id);
    }

    public function test_the_cash_export_can_be_limited_to_one_campus(): void
    {
        $director = $this->user('directeur');
        $dakar = $this->expense($this->dakar, 10_000, $director);
        $saintLouis = $this->expense($this->saintLouis, 2_000, $director);

        $csv = $this->actingAs($director)->get(route('admin.cash.export', ['from' => today()->startOfMonth()->toDateString(), 'until' => today()->toDateString(), 'place' => $this->saintLouis->id]))
            ->assertOk()->streamedContent();

        $this->assertStringContainsString($saintLouis->number, $csv);
        $this->assertStringNotContainsString($dakar->number, $csv);
        $this->assertStringContainsString('EMSI Saint-Louis', $csv);
    }

    public function test_the_dashboard_shows_one_cash_balance_per_campus_and_receipts_name_the_campus(): void
    {
        $director = $this->user('directeur');
        $expense = $this->expense($this->saintLouis, 2_000, $director);

        $this->actingAs($director);
        Livewire::test(CashOverview::class)->assertSee('Caisse Dakar')->assertSee('Caisse Saint-Louis')->assertSee('-2 000 FCFA');
        $this->actingAs($this->user('gestionnaire', $this->dakar));
        Livewire::test(CashOverview::class)->assertSee('Caisse Dakar')->assertDontSee('Caisse Saint-Louis');
        $this->actingAs($director);
        $this->actingAs($director)->get(route('admin.cash.receipt', $expense))->assertOk()->assertSee('EMSI Saint-Louis')->assertSee($expense->number);
    }
}
