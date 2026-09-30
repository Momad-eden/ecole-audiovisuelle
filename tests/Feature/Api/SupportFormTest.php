<?php

namespace Tests\Feature\Api;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportFormTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $extra = []): array
    {
        return array_merge([
            'name' => 'Fatou Sow', 'organization' => 'Fondation X', 'email' => 'fatou@example.com',
            'supportType' => 'sponsorship', 'message' => 'Nous souhaitons soutenir.', 'consent' => true,
        ], $extra);
    }

    public function test_support_request_is_stored_with_its_type(): void
    {
        $this->postJson('/api/v1/public/support', $this->payload())->assertCreated()->assertJsonPath('data.ok', true);

        $message = ContactMessage::firstOrFail();
        $this->assertSame('support', $message->subject);
        $this->assertSame('Fondation X', $message->organization);
        $this->assertSame("Type de soutien : Mécénat\n\nNous souhaitons soutenir.", $message->message);
    }

    public function test_support_requires_contact_channel_type_and_consent(): void
    {
        $this->postJson('/api/v1/public/support', $this->payload(['email' => null, 'supportType' => 'x', 'consent' => false]))
            ->assertUnprocessable()->assertJsonValidationErrors(['email', 'supportType', 'consent']);
        $this->postJson('/api/v1/public/support', $this->payload(['email' => null, 'phone' => '+221771234567']))->assertCreated();
    }

    public function test_support_honeypot_stores_nothing(): void
    {
        $this->postJson('/api/v1/public/support', $this->payload(['website' => 'http://spam']))->assertCreated();

        $this->assertSame(0, ContactMessage::count());
    }
}
