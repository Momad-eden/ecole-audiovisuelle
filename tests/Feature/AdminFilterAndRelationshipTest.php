<?php

namespace Tests\Feature;

use App\Enums\AdmissionStatus;
use App\Models\Admission;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFilterAndRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_students_by_course_and_payment_status(): void
    {
        $admin = User::factory()->create(['role' => 'directeur']);
        $courseA = Course::factory()->create(['title' => 'Son Avancé', 'price' => 500000]);
        $courseB = Course::factory()->create(['title' => 'Lumière', 'price' => 400000]);

        $studentA = Student::factory()->create([
            'first_name' => 'Amadou',
            'last_name' => 'Sow',
            'course_id' => $courseA->id,
            'status' => 'Inscrit',
        ]);

        $studentB = Student::factory()->create([
            'first_name' => 'Fatou',
            'last_name' => 'Diop',
            'course_id' => $courseB->id,
            'status' => 'Inscrit',
        ]);

        // Student A pays in full
        Payment::create([
            'receipt_number' => 'REC-0001',
            'type' => 'inflow',
            'category' => 'scolarite',
            'amount' => 500000,
            'payment_date' => now(),
            'payment_method' => 'cash',
            'student_id' => $studentA->id,
            'created_by' => $admin->id,
        ]);

        // Filter by course A
        $response = $this->actingAs($admin)->get(route('students.index', ['course_id' => $courseA->id]));
        $response->assertOk();
        $response->assertSee('Amadou');
        $response->assertDontSee('Fatou');

        // Filter by payment status: paid
        $response = $this->actingAs($admin)->get(route('students.index', ['payment_status' => 'paid']));
        $response->assertOk();
        $response->assertSee('Amadou');
        $response->assertDontSee('Fatou');

        // Filter by payment status: unpaid
        $response = $this->actingAs($admin)->get(route('students.index', ['payment_status' => 'unpaid']));
        $response->assertOk();
        $response->assertSee('Fatou');
        $response->assertDontSee('Amadou');
    }

    public function test_admin_can_filter_admissions_by_course_and_volet(): void
    {
        $admin = User::factory()->create(['role' => 'directeur']);
        $courseA = Course::factory()->create(['title' => 'Son Avancé']);
        $courseB = Course::factory()->create(['title' => 'Régie Vidéo']);

        Admission::factory()->create([
            'first_name' => 'Cheikh',
            'last_name' => 'Ndiaye',
            'course_id' => $courseA->id,
            'volet' => 'Volet 1 - Formations Pratiques',
            'status' => AdmissionStatus::PENDING->value,
        ]);

        Admission::factory()->create([
            'first_name' => 'Awa',
            'last_name' => 'Fall',
            'course_id' => $courseB->id,
            'volet' => "Volet 2 - Validation des Acquis de l'Expérience (VAE)",
            'status' => AdmissionStatus::APPROVED->value,
        ]);

        // Filter by course B
        $response = $this->actingAs($admin)->get(route('admissions.index', ['course_id' => $courseB->id]));
        $response->assertOk();
        $response->assertSee('Awa');
        $response->assertDontSee('Cheikh');

        // Filter by Volet 2
        $response = $this->actingAs($admin)->get(route('admissions.index', ['volet' => "Volet 2 - Validation des Acquis de l'Expérience (VAE)"]));
        $response->assertOk();
        $response->assertSee('Awa');
        $response->assertDontSee('Cheikh');
    }
}
