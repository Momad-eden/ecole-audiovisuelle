<?php

namespace Database\Factories;

use App\Enums\Audience;
use App\Enums\ProgramKind;
use App\Enums\PublicationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => 'Programme '.fake()->unique()->words(2, true),
            'audience' => Audience::SCHOOL,
            'kind' => ProgramKind::CERTIFICATE,
            'summary' => fake()->sentence(),
            'status' => PublicationStatus::PUBLISHED,
            'published_at' => now()->subDay(),
        ];
    }

    public function professional(): static
    {
        return $this->state(['audience' => Audience::PROFESSIONAL, 'kind' => ProgramKind::VAE_BTS]);
    }

    public function draft(): static
    {
        return $this->state(['status' => PublicationStatus::DRAFT, 'published_at' => null]);
    }
}
