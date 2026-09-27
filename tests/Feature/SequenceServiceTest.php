<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Student;
use App\Services\SequenceService;
use App\Services\StudentNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SequenceServiceTest extends TestCase
{
    use RefreshDatabase;

    private function payment(string $type, array $extra = []): Payment
    {
        return Payment::create(array_merge([
            'type' => $type,
            'category' => $type === 'inflow' ? 'autre_recette' : 'autre_depense',
            'amount' => 1000,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
        ], $extra));
    }

    public function test_sequence_service_increments_per_key(): void
    {
        $sequences = app(SequenceService::class);

        $this->assertSame(1, $sequences->next('a'));
        $this->assertSame(2, $sequences->next('a'));
        $this->assertSame(1, $sequences->next('b'));
        $this->assertSame(11, $sequences->next('c', fn () => 10));
        $this->assertSame(12, $sequences->next('c', fn () => 10));
    }

    public function test_receipts_and_disbursements_are_numbered_independently(): void
    {
        $period = now()->format('Ym');

        $this->payment('inflow');
        $this->payment('inflow');
        $outflow = $this->payment('outflow');

        $this->assertSame("DEP-{$period}-0001", $outflow->receipt_number);
        $this->assertSame("REC-{$period}-0003", $this->payment('inflow')->receipt_number);
    }

    public function test_a_deleted_receipt_number_is_never_reused(): void
    {
        $period = now()->format('Ym');
        $this->payment('inflow');
        $second = $this->payment('inflow');
        $second->forceDelete();

        $this->assertSame("REC-{$period}-0003", $this->payment('inflow')->receipt_number);
    }

    public function test_numbering_continues_after_existing_receipts(): void
    {
        $period = now()->format('Ym');
        $this->payment('inflow', ['receipt_number' => "REC-{$period}-0007"]);

        $this->assertSame("REC-{$period}-0008", $this->payment('inflow')->receipt_number);
    }

    public function test_student_numbers_continue_after_existing_ones(): void
    {
        $year = (int) date('Y');
        Student::factory()->create(['student_number' => "EMSI-{$year}-0042"]);

        $service = app(StudentNumberService::class);

        $this->assertSame("EMSI-{$year}-0043", $service->generate());
        $this->assertSame("EMSI-{$year}-0044", $service->generate());
    }
}
