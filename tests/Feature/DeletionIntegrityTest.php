<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeletionIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private User $directeur;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directeur = User::factory()->create(['role' => 'directeur']);
    }

    public function test_deleting_a_student_keeps_his_payments(): void
    {
        $student = Student::factory()->create();
        $payment = Payment::create([
            'type' => 'inflow', 'category' => 'scolarite', 'student_id' => $student->id,
            'amount' => 150000, 'payment_method' => 'cash', 'payment_date' => now()->toDateString(),
        ]);

        $this->actingAs($this->directeur)->delete(route('students.destroy', $student))->assertRedirect();

        $this->assertSoftDeleted($student);
        $this->assertNotNull($payment->fresh());
        $this->assertEquals(150000, (int) Payment::inflows()->sum('amount'));
        $this->actingAs($this->directeur)->get(route('payments.index'))
            ->assertOk()
            ->assertSee($student->last_name);
    }

    public function test_deleting_a_course_keeps_its_admissions(): void
    {
        $course = Course::factory()->create();
        $admission = Admission::create([
            'first_name' => 'Awa', 'last_name' => 'Ndiaye', 'phone' => '+221770000000',
            'course_id' => $course->id, 'status' => 'pending',
        ]);

        $this->actingAs($this->directeur)->delete(route('courses.destroy', $course))->assertRedirect();

        $this->assertSoftDeleted($course);
        $this->assertNotNull($admission->fresh());
        $this->actingAs($this->directeur)->get(route('admissions.show', $admission))->assertOk();
    }

    public function test_database_refuses_to_delete_a_course_that_has_students(): void
    {
        $student = Student::factory()->create();

        $this->expectException(QueryException::class);
        DB::table('courses')->where('id', $student->course_id)->delete();
    }

    public function test_database_refuses_to_delete_a_student_that_has_payments(): void
    {
        $student = Student::factory()->create();
        Payment::create([
            'type' => 'inflow', 'category' => 'scolarite', 'student_id' => $student->id,
            'amount' => 1000, 'payment_method' => 'cash', 'payment_date' => now()->toDateString(),
        ]);

        $this->expectException(QueryException::class);
        DB::table('students')->where('id', $student->id)->delete();
    }

    public function test_integrity_migration_is_reversible(): void
    {
        $this->artisan('migrate:rollback', ['--step' => 1])->assertSuccessful();
        $this->artisan('migrate')->assertSuccessful();

        $this->assertTrue(\Schema::hasColumn('students', 'deleted_at'));
    }

    public function test_a_course_can_reuse_the_title_of_a_deleted_course(): void
    {
        $payload = ['title' => 'Technicien Lumière', 'price' => 0, 'students_count' => 10, 'is_active' => 1];

        $this->actingAs($this->directeur)->post(route('courses.store'), $payload)->assertRedirect();
        $first = Course::where('title', 'Technicien Lumière')->firstOrFail();
        $this->actingAs($this->directeur)->delete(route('courses.destroy', $first))->assertRedirect();

        $this->actingAs($this->directeur)->post(route('courses.store'), $payload)->assertRedirect();

        $this->assertSame(2, Course::withTrashed()->where('title', 'Technicien Lumière')->count());
        $this->assertNotSame($first->slug, Course::where('title', 'Technicien Lumière')->firstOrFail()->slug);
    }
}
