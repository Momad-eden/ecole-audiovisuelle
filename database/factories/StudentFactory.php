<?php

namespace Database\Factories;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(Gender::cases()),
            'birth_date' => fake()->date('Y-m-d', '-19 years'),
            'birth_place' => 'Dakar',
            'nationality' => 'Sénégalaise',
            'phone' => '+221 77 '.fake()->numerify('### ## ##'),
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
