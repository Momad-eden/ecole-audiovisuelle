<?php

namespace Tests\Feature\Api;

use App\Enums\PublicationStatus;
use App\Models\Application;
use App\Models\Offering;
use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationCampusTest extends TestCase
{
    use RefreshDatabase;

    private function payload(Offering $offering, array $extra = []): array
    {
        return array_merge([
            'offeringId' => $offering->id, 'firstName' => 'Awa', 'lastName' => 'Ndiaye',
            'phone' => '+221 77 123 45 67', 'consent' => true,
        ], $extra);
    }

    private function campus(string $name): Place
    {
        return Place::create(['name' => $name, 'kind' => 'campus', 'city' => $name, 'status' => PublicationStatus::PUBLISHED]);
    }

    public function test_application_for_an_offering_absent_from_the_campus_is_rejected(): void
    {
        $dakar = $this->campus('Dakar');
        $saintLouis = $this->campus('Saint-Louis');
        $offering = Offering::factory()->create();
        $offering->cohort->program->campuses()->attach($dakar->id);

        $this->postJson('/api/v1/public/applications', $this->payload($offering, ['placeId' => $saintLouis->id]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['offeringId' => 'Cette formation n\'est pas proposée dans ce campus.']);
        $this->assertSame(0, Application::count());

        $this->postJson('/api/v1/public/applications', $this->payload($offering, ['placeId' => $dakar->id]))->assertCreated();
    }

    public function test_single_campus_school_needs_no_place(): void
    {
        $offering = Offering::factory()->create();

        $this->postJson('/api/v1/public/applications', $this->payload($offering))->assertCreated();
    }
}
