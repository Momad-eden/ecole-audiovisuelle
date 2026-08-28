<?php

namespace Database\Factories;

use App\Enums\AdmissionStatus;
use App\Models\Admission;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Admission>
 */
class AdmissionFactory extends Factory
{
    protected $model = Admission::class;

    public function definition(): array
    {
        return [
            'first_name'      => fake()->firstName(),
            'last_name'       => fake()->lastName(),
            'birth_date'      => fake()->date('Y-m-d', '-18 years'),
            'birth_place'     => fake()->city(),
            'gender'          => fake()->randomElement(['M', 'F']),
            'nationality'     => 'Sénégalaise',
            'phone'           => fake()->phoneNumber(),
            'email'           => fake()->unique()->safeEmail(),
            'address'         => fake()->address(),
            'last_diploma'    => 'Baccalauréat',
            'graduation_year' => (int) fake()->year(),
            'previous_school' => 'Lycée Lamine Guèye',
            'academic_field'  => 'Scientifique',
            'course_id'       => Course::factory(),
            'student_id'      => null,
            'status'          => AdmissionStatus::PENDING->value,
            'message'         => fake()->sentence(),
            'processed_at'    => null,
        ];
    }
}
