<?php

namespace Tests\Feature;

use App\Enums\AdmissionStatus;
use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\Admission;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAdmissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => UserRole::GESTIONNAIRE->value,
        ]);
    }

    public function test_admin_can_view_admissions_list(): void
    {
        $admission = Admission::factory()->create();

        $response = $this->actingAs($this->adminUser)->get(route('admissions.index'));

        $response->assertStatus(200);
        $response->assertSee($admission->first_name);
    }

    public function test_admin_can_approve_pending_admission(): void
    {
        $admission = Admission::factory()->create([
            'status' => AdmissionStatus::PENDING->value,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admissions.approve', $admission));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(AdmissionStatus::APPROVED->value, $admission->fresh()->status);
        $this->assertNotNull($admission->fresh()->processed_at);
    }

    public function test_admin_can_reject_pending_admission(): void
    {
        $admission = Admission::factory()->create([
            'status' => AdmissionStatus::PENDING->value,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admissions.reject', $admission));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(AdmissionStatus::REJECTED->value, $admission->fresh()->status);
        $this->assertNotNull($admission->fresh()->processed_at);
    }

    public function test_admin_can_enroll_approved_admission_and_converts_gender_properly(): void
    {
        $course = Course::factory()->create(['is_active' => true]);

        $admission = Admission::factory()->create([
            'course_id'   => $course->id,
            'status'      => AdmissionStatus::APPROVED->value,
            'gender'      => 'M',
            'first_name'  => 'Moussa',
            'last_name'   => 'Ndiaye',
            'student_id'  => null,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admissions.enroll', $admission));

        $this->assertDatabaseHas('students', [
            'first_name' => 'Moussa',
            'last_name'  => 'Ndiaye',
            'gender'     => Gender::HOMME->value, // Homme (not M)
            'status'     => StudentStatus::INSCRIT->value,
            'course_id'  => $course->id,
        ]);

        $student = Student::where('first_name', 'Moussa')->first();

        $this->assertNotNull($student);
        $this->assertStringStartsWith('EMSI-' . date('Y') . '-', $student->student_number);
        $this->assertEquals($student->id, $admission->fresh()->student_id);

        $response->assertRedirect(route('students.show', $student));
    }

    public function test_cannot_enroll_pending_or_already_enrolled_admission(): void
    {
        $pendingAdmission = Admission::factory()->create([
            'status' => AdmissionStatus::PENDING->value,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admissions.enroll', $pendingAdmission));

        $response->assertSessionHas('error');
        $this->assertNull($pendingAdmission->fresh()->student_id);
    }
}
