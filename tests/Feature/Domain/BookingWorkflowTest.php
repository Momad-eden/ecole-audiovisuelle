<?php

namespace Tests\Feature\Domain;

use App\Enums\BookingStatus;
use App\Exceptions\BusinessRuleException;
use App\Models\BookingRequest;
use App\Models\User;
use App\Services\BookingWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function request(): BookingRequest
    {
        return BookingRequest::create(['type' => 'event_service', 'name' => 'Label Ndar', 'phone' => '+221 77 000 00 00']);
    }

    public function test_a_request_moves_through_quote_confirmation_and_completion_with_history(): void
    {
        $user = User::factory()->create(['role' => 'commercial']);
        $request = $this->request();
        $workflow = app(BookingWorkflow::class);

        $workflow->transition($request, BookingStatus::QUOTED, $user, 'Devis de 1 200 000 FCFA envoyé');
        $workflow->transition($request, BookingStatus::CONFIRMED, $user);
        $workflow->transition($request, BookingStatus::DONE, $user);

        $this->assertSame(BookingStatus::DONE, $request->fresh()->status);
        $this->assertSame(['new', 'quoted', 'confirmed', 'done'], $request->logs()->orderBy('id')->pluck('to_status')->all());
        $this->assertSame('Devis de 1 200 000 FCFA envoyé', $request->logs()->where('to_status', 'quoted')->value('comment'));
    }

    public function test_closed_requests_cannot_be_reopened(): void
    {
        $request = $this->request();
        $workflow = app(BookingWorkflow::class);
        $workflow->transition($request, BookingStatus::CANCELLED, null, 'Date indisponible');

        $this->expectException(BusinessRuleException::class);
        $workflow->transition($request, BookingStatus::CONFIRMED, null);
    }

    public function test_references_are_sequential_per_year(): void
    {
        $this->assertSame('DEM-'.now()->year.'-00001', $this->request()->reference);
        $this->assertSame('DEM-'.now()->year.'-00002', $this->request()->reference);
    }
}
