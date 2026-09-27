<?php

namespace Tests\Feature\Api;

use App\Enums\CohortStatus;
use App\Models\Application;
use App\Models\ContactMessage;
use App\Models\Offering;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicFormsApiTest extends TestCase
{
    use RefreshDatabase;

    private function payload(Offering $offering, array $overrides = []): array
    {
        return array_merge([
            'offeringId' => $offering->id,
            'firstName' => 'Awa',
            'lastName' => 'Ndiaye',
            'phone' => '+221 77 123 45 67',
            'email' => 'awa@example.com',
            'birthDate' => '2001-04-12',
            'gender' => 'female',
            'education' => ['lastDiploma' => 'CPS', 'year' => 2022],
            'motivation' => 'Passionnée de son live.',
            'consent' => true,
        ], $overrides);
    }

    public function test_candidate_can_apply_with_documents(): void
    {
        Storage::fake('local');
        $offering = Offering::factory()->create();

        $response = $this->post('/api/v1/public/applications', $this->payload($offering, [
            'documents' => [
                ['type' => 'diploma', 'file' => UploadedFile::fake()->create('cps.pdf', 200, 'application/pdf')],
            ],
        ]), ['Accept' => 'application/json']);

        $response->assertCreated()->assertJsonStructure(['data' => ['reference']]);

        $application = Application::firstOrFail();
        $this->assertSame('Awa', $application->first_name);
        $this->assertSame('CPS', $application->education['last_diploma']);
        $this->assertNotNull($application->consent_at);
        $this->assertSame('diploma', $application->documents[0]['type']);
        Storage::disk('local')->assertExists($application->documents[0]['path']);
        $this->assertDatabaseHas('application_events', ['application_id' => $application->id, 'to_status' => 'submitted']);
    }

    public function test_application_is_refused_when_offering_is_closed_or_consent_missing(): void
    {
        $offering = Offering::factory()->create();
        $offering->cohort->update(['status' => CohortStatus::CLOSED]);

        $this->postJson('/api/v1/public/applications', $this->payload($offering))->assertUnprocessable()->assertJsonValidationErrors('offeringId');

        $open = Offering::factory()->create();
        $this->postJson('/api/v1/public/applications', $this->payload($open, ['consent' => false]))->assertUnprocessable()->assertJsonValidationErrors('consent');
        $this->postJson('/api/v1/public/applications', $this->payload($open, ['phone' => 'abc']))->assertUnprocessable()->assertJsonValidationErrors('phone');
    }

    public function test_application_validation_messages_are_french(): void
    {
        $this->postJson('/api/v1/public/applications', [])
            ->assertUnprocessable()
            ->assertJsonPath('errors.firstName.0', 'Le champ prénom est obligatoire.');
    }

    public function test_honeypot_and_rate_limit(): void
    {
        $offering = Offering::factory()->create();

        $this->postJson('/api/v1/public/applications', $this->payload($offering, ['website' => 'spam']))->assertCreated();
        $this->assertDatabaseCount('applications', 0);

        for ($i = 0; $i < 4; $i++) {
            $this->postJson('/api/v1/public/applications', $this->payload($offering, ['email' => "a{$i}@example.com"]))->assertCreated();
        }
        $this->postJson('/api/v1/public/applications', $this->payload($offering))->assertStatus(429);
    }

    public function test_contact_message(): void
    {
        $this->postJson('/api/v1/public/contact-messages', ['subject' => 'visit', 'name' => 'Moussa', 'email' => 'm@example.com', 'message' => 'Puis-je visiter ?', 'consent' => true])
            ->assertCreated();

        $this->assertSame('Moussa', ContactMessage::firstOrFail()->name);
        $this->postJson('/api/v1/public/contact-messages', ['subject' => 'x', 'name' => '', 'message' => ''])->assertUnprocessable();
    }

    public function test_a_mail_failure_does_not_break_the_submission(): void
    {
        $offering = Offering::factory()->create();
        \Illuminate\Support\Facades\Notification::shouldReceive('route')->andThrow(new \RuntimeException('SMTP injoignable'));

        $this->postJson('/api/v1/public/applications', $this->payload($offering))->assertCreated();
        $this->assertDatabaseCount('applications', 1);
    }

    public function test_acknowledgement_is_queued(): void
    {
        \Illuminate\Support\Facades\Notification::fake();
        $offering = Offering::factory()->create();

        $this->postJson('/api/v1/public/applications', $this->payload($offering))->assertCreated();

        \Illuminate\Support\Facades\Notification::assertSentOnDemand(\App\Notifications\ApplicationReceived::class);
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, new \App\Notifications\ApplicationReceived(Application::firstOrFail()));
    }
}
