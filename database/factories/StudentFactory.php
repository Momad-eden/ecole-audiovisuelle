<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'student_number'    => 'EMSI-' . date('Y') . '-' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'photo'             => null,
            'first_name'        => fake()->firstName(),
            'last_name'         => fake()->lastName(),
            'gender'            => fake()->randomElement(Gender::values()),
            'birth_date'        => fake()->date('Y-m-d', '-19 years'),
            'birth_place'       => 'Dakar',
            'nationality'       => 'Sénégalaise',
            'phone'             => fake()->phoneNumber(),
            'email'             => fake()->unique()->safeEmail(),
            'address'           => fake()->address(),
            'course_id'         => Course::factory(),
            'registration_date' => now()->toDateString(),
            'status'            => StudentStatus::INSCRIT->value,
            'notes'             => null,
        ];
    }
}
