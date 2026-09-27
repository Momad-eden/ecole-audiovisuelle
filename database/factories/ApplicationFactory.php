<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\Offering;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'offering_id' => Offering::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => '+221 77 '.fake()->numerify('### ## ##'),
            'email' => fake()->unique()->safeEmail(),
            'birth_date' => fake()->date('Y-m-d', '-20 years'),
            'status' => ApplicationStatus::SUBMITTED,
            'consent_at' => now(),
            'consent_version' => '2026-09',
        ];
    }

    public function status(ApplicationStatus $status): static
    {
        return $this->state(['status' => $status]);
    }
}
