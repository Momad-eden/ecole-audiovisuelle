<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAdmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_admission_form_is_accessible(): void
    {
        $course = Course::factory()->create(['is_active' => true]);

        $response = $this->get(route('public.admissions.create'));

        $response->assertStatus(200);
        $response->assertSee($course->title);
    }

    public function test_candidate_can_submit_admission_successfully(): void
    {
        $course = Course::factory()->create(['is_active' => true]);

        $payload = [
            'first_name' => 'Amadou',
            'last_name' => 'Diallo',
            'birth_date' => '2000-05-15',
            'birth_place' => 'Dakar',
            'gender' => 'M',
            'nationality' => 'Sénégalaise',
            'phone' => '+221 77 123 45 67',
            'email' => 'amadou.diallo@example.com',
            'address' => 'Médina, Dakar',
            'last_diploma' => 'Baccalauréat',
            'graduation_year' => 2022,
            'course_id' => $course->id,
            'volet' => 'Volet 1 — Perfectionnement intensif (3 mois)',
            'message' => 'Passionné par le montage vidéo et la réalisation.',
        ];

        $response = $this->post(route('public.admissions.store'), $payload);

        $response->assertRedirect(route('public.admissions.success'));
        $response->assertSessionHas('candidate_name', 'Amadou');

        $this->assertDatabaseHas('admissions', [
            'first_name' => 'Amadou',
            'last_name' => 'Diallo',
            'gender' => 'M',
            'status' => 'pending',
            'course_id' => $course->id,
            'volet' => 'Volet 1 — Perfectionnement intensif (3 mois)',
        ]);
    }

    public function test_admission_submission_requires_mandatory_fields(): void
    {
        $response = $this->post(route('public.admissions.store'), []);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'phone',
            'course_id',
        ]);
    }

    public function test_cannot_submit_admission_for_inactive_course(): void
    {
        $inactiveCourse = Course::factory()->create(['is_active' => false]);

        $payload = [
            'first_name' => 'Fatou',
            'last_name' => 'Sow',
            'phone' => '+221 78 987 65 43',
            'course_id' => $inactiveCourse->id,
        ];

        $response = $this->post(route('public.admissions.store'), $payload);

        $response->assertSessionHasErrors(['course_id']);
        $this->assertDatabaseMissing('admissions', [
            'first_name' => 'Fatou',
            'last_name' => 'Sow',
        ]);
    }

    private function validPayload(array $overrides = []): array
    {
        $course = Course::factory()->create(['is_active' => true]);

        return array_merge([
            'first_name' => 'Awa',
            'last_name' => 'Ndiaye',
            'phone' => '+221 77 123 45 67',
            'course_id' => $course->id,
            'volet' => 'Volet 2 — Certification BTS-VAE (9 mois)',
        ], $overrides);
    }

    public function test_submissions_are_rate_limited_per_ip(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('public.admissions.store'), $this->validPayload(['first_name' => "Awa{$i}"]))
                ->assertRedirect(route('public.admissions.success'));
        }

        $this->post(route('public.admissions.store'), $this->validPayload())->assertStatus(429);
        $this->assertDatabaseCount('admissions', 5);
    }

    public function test_honeypot_submission_looks_successful_but_is_not_saved(): void
    {
        $this->post(route('public.admissions.store'), $this->validPayload(['website' => 'http://spam.example']))
            ->assertRedirect(route('public.admissions.success'));

        $this->assertDatabaseCount('admissions', 0);
    }

    public function test_phone_number_must_look_like_a_phone_number(): void
    {
        $this->post(route('public.admissions.store'), $this->validPayload(['phone' => 'abc']))
            ->assertSessionHasErrors('phone');

        $this->post(route('public.admissions.store'), $this->validPayload(['phone' => '+221 77 123 45 67']))
            ->assertSessionHasNoErrors();
    }

    public function test_volet_must_be_one_of_the_offered_values(): void
    {
        $this->post(route('public.admissions.store'), $this->validPayload(['volet' => "n'importe quoi"]))
            ->assertSessionHasErrors('volet');

        $this->assertDatabaseCount('admissions', 0);
    }

    public function test_success_page_requires_a_submission(): void
    {
        $this->get(route('public.admissions.success'))->assertRedirect(route('public.admissions.create'));
    }

    public function test_diploma_list_offers_cps_and_cs(): void
    {
        $this->get(route('public.admissions.create'))
            ->assertOk()
            ->assertSee('value="CPS"', false)
            ->assertSee('value="CS"', false);
    }
}
