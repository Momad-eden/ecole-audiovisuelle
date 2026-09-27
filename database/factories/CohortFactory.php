<?php

namespace Database\Factories;

use App\Enums\CohortStatus;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class CohortFactory extends Factory
{
    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'name' => 'Session '.fake()->unique()->numberBetween(2026, 2099),
            'starts_on' => now()->addMonth()->toDateString(),
            'ends_on' => now()->addMonths(10)->toDateString(),
            'status' => CohortStatus::OPEN,
        ];
    }
}
