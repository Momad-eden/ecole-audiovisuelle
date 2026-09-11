<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\TransactionCategory;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashRegisterAndAccountingTest extends TestCase
{
    use RefreshDatabase;

    protected User $directeur;
    protected User $gestionnaire;
    protected User $communication;
    protected Course $course;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directeur = User::factory()->create([
            'role' => UserRole::DIRECTEUR->value,
        ]);

        $this->gestionnaire = User::factory()->create([
            'role' => UserRole::GESTIONNAIRE->value,
        ]);

        $this->communication = User::factory()->create([
            'role' => UserRole::COMMUNICATION->value,
        ]);

        $this->course = Course::create([
            'title' => 'Réalisation Cinéma',
            'slug' => 'realisation-cinema',
            'price' => 500000,
            'is_active' => true,
        ]);

        $this->student = Student::create([
            'student_number' => 'ETU-2026-001',
            'first_name' => 'Amadou',
            'last_name' => 'Diallo',
            'gender' => 'Homme',
            'email' => 'amadou@example.com',
            'course_id' => $this->course->id,
            'registration_date' => now(),
            'status' => 'Inscrit',
        ]);
    }

    public function test_directeur_and_gestionnaire_can_view_cash_register(): void
    {
        $this->actingAs($this->directeur)
            ->get(route('payments.index'))
            ->assertStatus(200)
            ->assertSee('Caisse & Trésorerie', false);

        $this->actingAs($this->gestionnaire)
            ->get(route('payments.index'))
            ->assertStatus(200);
    }

    public function test_can_record_tuition_inflow_and_generate_receipt(): void
    {
        $response = $this->actingAs($this->directeur)->post(route('payments.store'), [
            'type' => 'inflow',
            'category' => TransactionCategory::SCOLARITE->value,
            'student_id' => $this->student->id,
            'amount' => 200000,
            'payment_method' => PaymentMethod::WAVE->value,
            'payment_date' => now()->format('Y-m-d'),
            'reference' => 'WAVE-123456',
            'notes' => 'Acompte scolarité session 2026',
        ]);

        $this->assertDatabaseHas('payments', [
            'type' => 'inflow',
            'category' => TransactionCategory::SCOLARITE->value,
            'student_id' => $this->student->id,
            'amount' => 200000,
            'payment_method' => PaymentMethod::WAVE->value,
        ]);

        $payment = Payment::first();
        $this->assertNotNull($payment->receipt_number);
        $this->assertStringStartsWith('REC-', $payment->receipt_number);

        $response->assertRedirect(route('payments.show', $payment));
    }

    public function test_can_record_expense_outflow_without_student(): void
    {
        $response = $this->actingAs($this->directeur)->post(route('payments.store'), [
            'type' => 'outflow',
            'category' => TransactionCategory::ACHAT_MATERIEL->value,
            'title' => 'Achat câbles HDMI 4K régie',
            'amount' => 45000,
            'payment_method' => PaymentMethod::CASH->value,
            'payment_date' => now()->format('Y-m-d'),
            'reference' => 'FACT-9988',
        ]);

        $this->assertDatabaseHas('payments', [
            'type' => 'outflow',
            'category' => TransactionCategory::ACHAT_MATERIEL->value,
            'title' => 'Achat câbles HDMI 4K régie',
            'student_id' => null,
            'amount' => 45000,
        ]);

        $payment = Payment::latest('id')->first();
        $this->assertNotNull($payment->receipt_number);
        $this->assertStringStartsWith('DEP-', $payment->receipt_number);

        $response->assertRedirect(route('payments.show', $payment));
    }

    public function test_can_view_official_printable_receipt(): void
    {
        $payment = Payment::create([
            'type' => 'inflow',
            'category' => 'scolarite',
            'student_id' => $this->student->id,
            'amount' => 150000,
            'payment_method' => 'wave',
            'payment_date' => now(),
            'receipt_number' => 'REC-202608-0001',
        ]);

        $response = $this->actingAs($this->directeur)
            ->get(route('payments.receipt', $payment));

        $response->assertStatus(200);
        $response->assertSee('REÇU D\'ENCAISSEMENT', false);
        $response->assertSee('REC-202608-0001');
        $response->assertSee('Amadou Diallo');
        $response->assertSee('150 000');
    }

    public function test_can_view_accounting_grand_livre_and_student_balance(): void
    {
        // 1 inflow of 300 000 FCFA
        Payment::create([
            'type' => 'inflow',
            'category' => 'scolarite',
            'student_id' => $this->student->id,
            'amount' => 300000,
            'payment_method' => 'cash',
            'payment_date' => now(),
        ]);

        // 1 outflow of 50 000 FCFA
        Payment::create([
            'type' => 'outflow',
            'category' => 'achat_materiel',
            'title' => 'Cartes SD Sandisk',
            'amount' => 50000,
            'payment_method' => 'cash',
            'payment_date' => now(),
        ]);

        $response = $this->actingAs($this->directeur)
            ->get(route('accounting.index'));

        $response->assertStatus(200);
        $response->assertSee('Comptabilité & Bilan Financier', false);
        $response->assertSee('300 000');
        $response->assertSee('50 000');
        $response->assertSee('Amadou Diallo');
    }

    public function test_can_export_accounting_ledger_to_csv(): void
    {
        Payment::create([
            'type' => 'inflow',
            'category' => 'scolarite',
            'student_id' => $this->student->id,
            'amount' => 250000,
            'payment_method' => 'cash',
            'payment_date' => now(),
            'receipt_number' => 'REC-2026-EXP1',
        ]);

        $response = $this->actingAs($this->directeur)
            ->get(route('accounting.export'));

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'text/csv'));
    }

    public function test_unauthorized_user_cannot_access_caisse_and_accounting(): void
    {
        $this->actingAs($this->communication)
            ->get(route('payments.index'))
            ->assertStatus(403);

        $this->actingAs($this->communication)
            ->get(route('accounting.index'))
            ->assertStatus(403);
    }
}
