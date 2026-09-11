<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Admission;
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
            'first_name'      => 'Amadou',
            'last_name'       => 'Diallo',
            'birth_date'      => '2000-05-15',
            'birth_place'     => 'Dakar',
            'gender'          => 'M',
            'nationality'     => 'Sénégalaise',
            'phone'           => '+221 77 123 45 67',
            'email'           => 'amadou.diallo@example.com',
            'address'         => 'Médina, Dakar',
            'last_diploma'    => 'Baccalauréat',
            'graduation_year' => 2022,
            'course_id'       => $course->id,
            'volet'           => 'Volet 1 — Perfectionnement intensif (3 mois)',
            'message'         => 'Passionné par le montage vidéo et la réalisation.',
        ];

        $response = $this->post(route('public.admissions.store'), $payload);

        $response->assertRedirect(route('public.admissions.success'));
        $response->assertSessionHas('candidate_name', 'Amadou');

        $this->assertDatabaseHas('admissions', [
            'first_name' => 'Amadou',
            'last_name'  => 'Diallo',
            'gender'     => 'M',
            'status'     => 'pending',
            'course_id'  => $course->id,
            'volet'      => 'Volet 1 — Perfectionnement intensif (3 mois)',
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
            'last_name'  => 'Sow',
            'phone'      => '+221 78 987 65 43',
            'course_id'  => $inactiveCourse->id,
        ];

        $response = $this->post(route('public.admissions.store'), $payload);

        $response->assertSessionHasErrors(['course_id']);
        $this->assertDatabaseMissing('admissions', [
            'first_name' => 'Fatou',
            'last_name'  => 'Sow',
        ]);
    }
}
