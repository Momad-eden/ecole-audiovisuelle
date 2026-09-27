<?php

namespace Tests\Feature\Domain;

use App\Enums\CashDirection;
use App\Exceptions\BusinessRuleException;
use App\Models\CashTransaction;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\CashRegister;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use LogicException;
use Tests\TestCase;

class CashRegisterTest extends TestCase
{
    use RefreshDatabase;

    private CashRegister $cash;

    private User $gestionnaire;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cash = app(CashRegister::class);
        $this->gestionnaire = User::factory()->create(['role' => 'gestionnaire']);
    }

    private function tuition(Enrollment $enrollment, int $amount, array $extra = []): CashTransaction
    {
        return $this->cash->record(array_merge([
            'direction' => 'in',
            'category' => 'scolarite',
            'amount' => $amount,
            'method' => 'wave',
            'occurred_on' => now()->toDateString(),
            'enrollment_id' => $enrollment->id,
        ], $extra), $this->gestionnaire);
    }

    private function expense(int $amount, array $extra = []): CashTransaction
    {
        return $this->cash->record(array_merge([
            'direction' => 'out',
            'category' => 'maintenance',
            'amount' => $amount,
            'method' => 'cash',
            'occurred_on' => now()->toDateString(),
            'label' => 'Réparation console',
        ], $extra), $this->gestionnaire);
    }

    public function test_receipts_and_disbursements_have_independent_yearly_numbers(): void
    {
        $enrollment = Enrollment::factory()->create();
        $year = now()->year;

        $this->assertSame("REC-{$year}-00001", $this->tuition($enrollment, 1000)->number);
        $this->assertSame("DEP-{$year}-00001", $this->expense(500)->number);
        $this->assertSame("REC-{$year}-00002", $this->tuition($enrollment, 1000)->number);
    }

    public function test_tuition_payment_reduces_the_enrollment_balance(): void
    {
        $enrollment = Enrollment::factory()->create(['fee_amount_due' => 500000, 'discount_amount' => 50000]);

        $transaction = $this->tuition($enrollment, 150000);

        $this->assertSame(150000, $enrollment->amountPaid());
        $this->assertSame(300000, $enrollment->balance());
        $this->assertStringContainsString($enrollment->student->full_name, $transaction->label);
        $this->assertSame($this->gestionnaire->id, $transaction->created_by);
    }

    public function test_category_must_match_the_direction(): void
    {
        $this->expectException(BusinessRuleException::class);
        $this->expense(1000, ['category' => 'scolarite']);
    }

    public function test_tuition_requires_an_enrollment(): void
    {
        $this->expectException(BusinessRuleException::class);
        $this->cash->record([
            'direction' => 'in', 'category' => 'scolarite', 'amount' => 1000, 'method' => 'cash',
            'occurred_on' => now()->toDateString(),
        ], $this->gestionnaire);
    }

    public function test_future_dates_are_refused(): void
    {
        $this->expectException(BusinessRuleException::class);
        $this->expense(1000, ['occurred_on' => now()->addDay()->toDateString()]);
    }

    public function test_a_transaction_cannot_be_modified_or_deleted(): void
    {
        $transaction = $this->expense(1000);

        try {
            $transaction->update(['amount' => 2000]);
            $this->fail('Modification acceptée');
        } catch (LogicException) {
        }

        $this->expectException(LogicException::class);
        $transaction->delete();
    }

    public function test_cancelling_creates_a_reversal_and_restores_the_balance(): void
    {
        $enrollment = Enrollment::factory()->create(['fee_amount_due' => 500000]);
        $payment = $this->tuition($enrollment, 200000);

        $reversal = $this->cash->cancel($payment, $this->gestionnaire, 'Montant erroné');

        $this->assertSame(CashDirection::OUT, $reversal->direction);
        $this->assertSame(200000, $reversal->amount);
        $this->assertSame($payment->id, $reversal->reverses_id);
        $this->assertTrue($payment->fresh()->isCancelled());
        $this->assertSame('Montant erroné', $payment->fresh()->cancel_reason);
        $this->assertSame(0, $enrollment->amountPaid());
        $this->assertSame(0, $this->cash->balance());
    }

    public function test_a_transaction_cannot_be_cancelled_twice_nor_a_reversal_cancelled(): void
    {
        $payment = $this->expense(1000);
        $reversal = $this->cash->cancel($payment, $this->gestionnaire, 'Erreur');

        foreach ([$payment->fresh(), $reversal] as $transaction) {
            try {
                $this->cash->cancel($transaction, $this->gestionnaire, 'Encore');
                $this->fail('Double annulation acceptée');
            } catch (BusinessRuleException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function test_closing_a_period_computes_balances_and_locks_it(): void
    {
        Carbon::setTestNow('2026-09-30 18:00');
        $enrollment = Enrollment::factory()->create();
        $this->tuition($enrollment, 300000, ['occurred_on' => '2026-09-10']);
        $this->expense(50000, ['occurred_on' => '2026-09-15']);

        $closing = $this->cash->close(null, Carbon::parse('2026-09-30'), 250000, $this->gestionnaire);

        $this->assertSame(0, $closing->opening_balance);
        $this->assertSame(300000, $closing->total_in);
        $this->assertSame(50000, $closing->total_out);
        $this->assertSame(250000, $closing->closing_balance);
        $this->assertSame(0, $closing->difference());

        $this->expectException(BusinessRuleException::class);
        $this->expense(1000, ['occurred_on' => '2026-09-20']);
    }

    public function test_next_closing_starts_from_previous_balance(): void
    {
        Carbon::setTestNow('2026-10-31 18:00');
        $this->expense(10000, ['occurred_on' => '2026-09-05']);
        $this->cash->close(null, Carbon::parse('2026-09-30'), null, $this->gestionnaire);
        $this->expense(5000, ['occurred_on' => '2026-10-05']);

        $closing = $this->cash->close(null, Carbon::parse('2026-10-31'), null, $this->gestionnaire);

        $this->assertSame('2026-10-01', $closing->period_start->toDateString());
        $this->assertSame(-10000, $closing->opening_balance);
        $this->assertSame(-15000, $closing->closing_balance);
    }
}
