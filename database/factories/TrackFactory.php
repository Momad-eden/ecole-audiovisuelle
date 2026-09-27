<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TrackFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Filière '.fake()->unique()->word(),
            'summary' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
