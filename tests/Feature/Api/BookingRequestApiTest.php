<?php

namespace Tests\Feature\Api;

use App\Enums\BookingStatus;
use App\Enums\PublicationStatus;
use App\Models\BookingRequest;
use App\Models\EquipmentCategory;
use App\Models\EquipmentItem;
use App\Models\RentalPack;
use App\Models\Setting;
use App\Notifications\BookingRequestReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookingRequestApiTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return $overrides + [
            'type' => 'equipment_rental',
            'name' => 'Awa Ndiaye',
            'organization' => 'Festival du Fleuve',
            'phone' => '+221 77 123 45 67',
            'email' => 'awa@example.test',
            'startsOn' => now()->addMonth()->toDateString(),
            'endsOn' => now()->addMonth()->addDays(2)->toDateString(),
            'location' => 'Saint-Louis',
            'attendees' => 800,
            'message' => 'Concert en plein air.',
            'consent' => true,
        ];
    }

    public function test_a_quote_request_is_recorded_with_named_items_and_notifies_the_team(): void
    {
        Notification::fake();
        Setting::current()->update(['email' => 'contact@emsi.test']);
        $item = EquipmentItem::create(['name' => 'Line array K2', 'equipment_category_id' => EquipmentCategory::create(['name' => 'Sonorisation'])->id, 'usage' => 'rental', 'status' => PublicationStatus::PUBLISHED]);
        $pack = RentalPack::create(['name' => 'Pack concert', 'status' => PublicationStatus::PUBLISHED]);

        $response = $this->postJson('/api/v1/public/booking-requests', $this->payload([
            'items' => [['kind' => 'equipment', 'id' => $item->id, 'quantity' => 4], ['kind' => 'pack', 'id' => $pack->id, 'quantity' => 1]],
        ]))->assertCreated();

        $reference = $response->json('data.reference');
        $this->assertMatchesRegularExpression('/^DEM-'.now()->year.'-00001$/', $reference);

        $request = BookingRequest::where('reference', $reference)->firstOrFail();
        $this->assertSame(BookingStatus::NEW, $request->status);
        // MySQL réordonne les clés JSON : on compare le contenu, pas l'ordre des clés.
        $this->assertEquals([['kind' => 'equipment', 'id' => $item->id, 'name' => 'Line array K2', 'quantity' => 4], ['kind' => 'pack', 'id' => $pack->id, 'name' => 'Pack concert', 'quantity' => 1]], $request->items);
        $this->assertSame(800, $request->attendees);
        $this->assertCount(1, $request->logs);

        Notification::assertSentOnDemand(BookingRequestReceived::class, fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'contact@emsi.test');
        Notification::assertSentOnDemand(BookingRequestReceived::class, fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'awa@example.test');
    }

    public function test_unknown_or_unpublished_items_are_refused(): void
    {
        $draft = EquipmentItem::create(['name' => 'Brouillon', 'equipment_category_id' => EquipmentCategory::create(['name' => 'Lumière'])->id, 'usage' => 'rental', 'status' => PublicationStatus::DRAFT]);

        $this->postJson('/api/v1/public/booking-requests', $this->payload(['items' => [['kind' => 'equipment', 'id' => $draft->id, 'quantity' => 1]]]))
            ->assertUnprocessable()->assertJsonValidationErrors('items.0.id');
        $this->postJson('/api/v1/public/booking-requests', $this->payload(['items' => [['kind' => 'equipment', 'id' => 999, 'quantity' => 1]]]))
            ->assertUnprocessable();
        $this->assertSame(0, BookingRequest::count());
    }

    public function test_required_fields_consent_dates_and_honeypot(): void
    {
        $this->postJson('/api/v1/public/booking-requests', ['type' => 'studio_session'])
            ->assertUnprocessable()->assertJsonValidationErrors(['name', 'phone', 'consent']);

        $this->postJson('/api/v1/public/booking-requests', $this->payload(['startsOn' => now()->addDays(5)->toDateString(), 'endsOn' => now()->addDays(2)->toDateString()]))
            ->assertUnprocessable()->assertJsonValidationErrors('endsOn');

        $this->postJson('/api/v1/public/booking-requests', $this->payload(['website' => 'spam']))->assertCreated()->assertJsonPath('data.reference', null);
        $this->assertSame(0, BookingRequest::count());
    }

    public function test_a_failing_mail_server_never_loses_a_request(): void
    {
        Notification::shouldReceive('route')->andThrow(new \RuntimeException('SMTP en panne'));

        $this->postJson('/api/v1/public/booking-requests', $this->payload(['type' => 'studio_session']))->assertCreated();
        $this->assertSame(1, BookingRequest::count());
    }
}
