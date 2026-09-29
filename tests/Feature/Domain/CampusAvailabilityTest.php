<?php

namespace Tests\Feature\Domain;

use App\Enums\CohortStatus;
use App\Enums\PublicationStatus;
use App\Models\Cohort;
use App\Models\Offering;
use App\Models\Place;
use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Une formation n'est proposée que dans les campus où elle est cochée, et selon la session. */
class CampusAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    private Place $dakar;

    private Place $saintLouis;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dakar = Place::create(['name' => 'EMSI Dakar', 'kind' => 'campus', 'city' => 'Dakar', 'status' => PublicationStatus::PUBLISHED]);
        $this->saintLouis = Place::create(['name' => 'EMSI Saint-Louis', 'kind' => 'campus', 'city' => 'Saint-Louis', 'status' => PublicationStatus::PUBLISHED]);
    }

    private function offering(array $campuses, ?Place $sessionCampus = null, array $cohort = []): Offering
    {
        $program = Program::factory()->create();
        $program->campuses()->attach(collect($campuses)->pluck('id'));
        $session = Cohort::factory()->create(['program_id' => $program->id, 'place_id' => $sessionCampus?->id] + $cohort);

        return Offering::factory()->create(['cohort_id' => $session->id]);
    }

    public function test_offering_is_available_where_its_program_is_offered_and_session_matches(): void
    {
        $offering = $this->offering([$this->dakar]);

        $this->assertTrue(Offering::availableAt($this->dakar)->whereKey($offering->id)->exists());
        $this->assertFalse(Offering::availableAt($this->saintLouis)->whereKey($offering->id)->exists());
        $this->assertTrue($offering->isAvailableAt($this->dakar));
        $this->assertFalse($offering->isAvailableAt($this->saintLouis));
    }

    public function test_a_session_tied_to_one_campus_is_only_offered_there(): void
    {
        $offering = $this->offering([$this->dakar, $this->saintLouis], $this->saintLouis);

        $this->assertFalse(Offering::availableAt($this->dakar)->whereKey($offering->id)->exists());
        $this->assertTrue(Offering::availableAt($this->saintLouis)->whereKey($offering->id)->exists());
    }

    public function test_closed_offerings_are_never_available(): void
    {
        $offering = $this->offering([$this->dakar], null, ['status' => CohortStatus::CLOSED]);

        $this->assertFalse(Offering::availableAt($this->dakar)->whereKey($offering->id)->exists());
    }

    public function test_without_campus_an_offering_is_available_only_when_a_single_campus_exists(): void
    {
        $offering = $this->offering([$this->dakar]);
        $this->assertFalse($offering->isAvailableAt(null));

        $this->saintLouis->update(['status' => PublicationStatus::DRAFT]);
        $this->assertTrue($offering->fresh()->isAvailableAt(null));
    }
}
