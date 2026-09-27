<?php

namespace Database\Factories;

use App\Enums\EnrollmentStatus;
use App\Enums\FundingMode;
use App\Models\Offering;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'offering_id' => Offering::factory(),
            'enrolled_on' => now()->toDateString(),
            'status' => EnrollmentStatus::ENROLLED,
            'fee_amount_due' => 500000,
            'funding_mode' => FundingMode::PAID,
        ];
    }
}
