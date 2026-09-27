<?php

namespace Database\Factories;

use App\Enums\FundingMode;
use App\Models\Cohort;
use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cohort_id' => Cohort::factory(),
            'track_id' => Track::factory(),
            'capacity' => 10,
            'fee_amount' => 500000,
            'registration_fee_amount' => 25000,
            'funding_mode' => FundingMode::PAID,
            'is_open' => true,
        ];
    }
}
