<?php

namespace Tests\Feature\Domain;

use App\Enums\ApplicationStatus;
use App\Enums\EnrollmentStatus;
use App\Exceptions\BusinessRuleException;
use App\Models\Application;
use App\Models\Offering;
use App\Models\Student;
use App\Models\User;
use App\Services\ApplicationWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private ApplicationWorkflow $workflow;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workflow = app(ApplicationWorkflow::class);
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_new_application_gets_a_reference_and_is_submitted(): void
    {
        $application = Application::factory()->create();

        $this->assertMatchesRegularExpression('/^CAND-\d{4}-\d{5}$/', $application->reference);
        $this->assertSame(ApplicationStatus::SUBMITTED, $application->status);
        $this->assertNotNull($application->uuid);
        $this->assertNotNull($application->audience);
    }

    public function test_transition_is_recorded_in_history(): void
    {
        $application = Application::factory()->create();
        $secretaire = $this->user('secretaire');

        $this->workflow->transition($application, ApplicationStatus::UNDER_REVIEW, $secretaire, 'Dossier complet');

        $this->assertSame(ApplicationStatus::UNDER_REVIEW, $application->fresh()->status);
        $this->assertDatabaseHas('application_events', [
            'application_id' => $application->id,
            'from_status' => 'submitted',
            'to_status' => 'under_review',
            'comment' => 'Dossier complet',
            'user_id' => $secretaire->id,
        ]);
    }

    public function test_forbidden_transition_is_refused(): void
    {
        $application = Application::factory()->status(ApplicationStatus::REJECTED)->create();

        $this->expectException(BusinessRuleException::class);
        $this->workflow->transition($application, ApplicationStatus::ACCEPTED, $this->user('directeur'));
    }

    public function test_only_deciders_can_accept_or_reject(): void
    {
        $application = Application::factory()->create();

        try {
            $this->workflow->transition($application, ApplicationStatus::ACCEPTED, $this->user('secretaire'));
            $this->fail('La secrétaire ne doit pas pouvoir décider.');
        } catch (BusinessRuleException) {
            $this->assertSame(ApplicationStatus::SUBMITTED, $application->fresh()->status);
        }

        $gestionnaire = $this->user('gestionnaire');
        $this->workflow->transition($application, ApplicationStatus::ACCEPTED, $gestionnaire);

        $application->refresh();
        $this->assertSame(ApplicationStatus::ACCEPTED, $application->status);
        $this->assertSame($gestionnaire->id, $application->decided_by);
        $this->assertNotNull($application->decided_at);
    }

    public function test_enrolling_an_accepted_application_creates_student_and_enrollment_with_frozen_fees(): void
    {
        $offering = Offering::factory()->create(['fee_amount' => 750000]);
        $application = Application::factory()->for($offering)->status(ApplicationStatus::ACCEPTED)->create();

        $enrollment = $this->workflow->enroll($application, $this->user('gestionnaire'));

        $offering->update(['fee_amount' => 900000]);
        $application->refresh();

        $this->assertSame(ApplicationStatus::ENROLLED, $application->status);
        $this->assertNotNull($application->student_id);
        $this->assertSame(750000, $enrollment->fresh()->fee_amount_due);
        $this->assertSame(EnrollmentStatus::ENROLLED, $enrollment->status);
        $this->assertMatchesRegularExpression('/^EMSI-\d{4}-\d{4}$/', $application->student->student_number);
    }

    public function test_enrolling_reuses_an_existing_student_with_the_same_email(): void
    {
        $student = Student::factory()->create(['email' => 'awa@example.com']);
        $application = Application::factory()->status(ApplicationStatus::ACCEPTED)->create(['email' => 'AWA@example.com']);

        $this->workflow->enroll($application, $this->user('gestionnaire'));

        $this->assertSame($student->id, $application->fresh()->student_id);
        $this->assertSame(1, Student::count());
    }

    public function test_cannot_enroll_an_application_that_is_not_accepted(): void
    {
        $application = Application::factory()->create();

        $this->expectException(BusinessRuleException::class);
        $this->workflow->enroll($application, $this->user('gestionnaire'));
    }

    public function test_cannot_enroll_beyond_capacity(): void
    {
        $offering = Offering::factory()->create(['capacity' => 1]);
        $gestionnaire = $this->user('gestionnaire');
        $first = Application::factory()->for($offering)->status(ApplicationStatus::ACCEPTED)->create();
        $second = Application::factory()->for($offering)->status(ApplicationStatus::ACCEPTED)->create();

        $this->workflow->enroll($first, $gestionnaire);

        $this->expectException(BusinessRuleException::class);
        $this->workflow->enroll($second, $gestionnaire);
    }
}
