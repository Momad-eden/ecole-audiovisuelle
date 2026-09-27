<?php

namespace Tests\Feature\Domain;

use App\Models\Student;
use App\Services\SequenceService;
use App\Services\StudentNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SequenceServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sequences_increment_per_key_and_resume_from_initial_value(): void
    {
        $sequences = app(SequenceService::class);

        $this->assertSame(1, $sequences->next('a'));
        $this->assertSame(2, $sequences->next('a'));
        $this->assertSame(1, $sequences->next('b'));
        $this->assertSame(11, $sequences->next('c', fn () => 10));
        $this->assertSame(12, $sequences->next('c', fn () => 10));
    }

    public function test_student_numbers_continue_after_existing_ones_and_are_never_reused(): void
    {
        $year = (int) date('Y');
        Student::factory()->create(['student_number' => "EMSI-{$year}-0042"]);

        $student = Student::factory()->create();
        $this->assertSame("EMSI-{$year}-0043", $student->student_number);

        $student->forceDelete();
        $this->assertSame("EMSI-{$year}-0044", app(StudentNumberService::class)->generate());
    }
}
