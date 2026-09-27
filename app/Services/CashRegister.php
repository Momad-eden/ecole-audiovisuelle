<?php

namespace App\Services;

use App\Enums\CashDirection;
use App\Enums\PaymentMethod;
use App\Enums\TransactionCategory;
use App\Exceptions\BusinessRuleException;
use App\Models\CashClosing;
use App\Models\CashTransaction;
use App\Models\Enrollment;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Caisse de l'école : écritures inaltérables, numérotées par année,
 * annulées par contre-écriture et verrouillées par les clôtures.
 */
class CashRegister
{
    public function __construct(protected SequenceService $sequences) {}

    /**
     * @param  array{direction: string, category: string, amount: int, method: string, occurred_on: string,
     *     enrollment_id?: int|null, payee?: string|null, label?: string|null, external_reference?: string|null, notes?: string|null}  $data
     */
    public function record(array $data, User $by): CashTransaction
    {
        $direction = CashDirection::from($data['direction']);
        $category = TransactionCategory::from($data['category']);
        $method = PaymentMethod::from($data['method']);
        $occurredOn = Carbon::parse($data['occurred_on'])->startOfDay();
        $amount = (int) $data['amount'];

        if ($category->direction() !== $direction) {
            throw new BusinessRuleException("La catégorie « {$category->getLabel()} » ne correspond pas à un {$direction->getLabel()}.");
        }
        if ($amount < 1 || $amount > 99_999_999) {
            throw new BusinessRuleException('Le montant doit être compris entre 1 et 99 999 999 FCFA.');
        }
        if ($occurredOn->isAfter(today())) {
            throw new BusinessRuleException('La date de l\'opération ne peut pas être dans le futur.');
        }
        $this->ensureNotClosed($occurredOn);

        $enrollment = isset($data['enrollment_id']) ? Enrollment::with('student')->find($data['enrollment_id']) : null;
        if ($category->requiresEnrollment() && ! $enrollment) {
            throw new BusinessRuleException('Un paiement de scolarité ou d\'inscription doit être rattaché à une inscription.');
        }

        $label = filled($data['label'] ?? null)
            ? $data['label']
            : $category->getLabel().($enrollment ? ' — '.$enrollment->student->full_name : '');

        return DB::transaction(fn () => CashTransaction::create([
            'number' => $this->nextNumber($direction, $occurredOn),
            'direction' => $direction,
            'category' => $category,
            'amount' => $amount,
            'method' => $method,
            'external_reference' => $data['external_reference'] ?? null,
            'occurred_on' => $occurredOn->toDateString(),
            'enrollment_id' => $enrollment?->id,
            'payee' => $data['payee'] ?? null,
            'label' => $label,
            'notes' => $data['notes'] ?? null,
            'created_by' => $by->id,
        ]));
    }

    /** Annule une écriture par une contre-écriture datée du jour. */
    public function cancel(CashTransaction $transaction, User $by, string $reason): CashTransaction
    {
        if ($transaction->isCancelled()) {
            throw new BusinessRuleException("L'écriture {$transaction->number} est déjà annulée.");
        }
        if ($transaction->reverses_id !== null) {
            throw new BusinessRuleException('Une contre-écriture ne peut pas être annulée.');
        }
        if (blank($reason)) {
            throw new BusinessRuleException('Indiquez le motif de l\'annulation.');
        }
        $this->ensureNotClosed(today());

        return DB::transaction(function () use ($transaction, $by, $reason) {
            $direction = $transaction->direction === CashDirection::IN ? CashDirection::OUT : CashDirection::IN;

            $reversal = CashTransaction::create([
                'number' => $this->nextNumber($direction, today()),
                'direction' => $direction,
                'category' => $transaction->category,
                'amount' => $transaction->amount,
                'method' => $transaction->method,
                'occurred_on' => today()->toDateString(),
                'enrollment_id' => $transaction->enrollment_id,
                'payee' => $transaction->payee,
                'label' => "Annulation de {$transaction->number}",
                'notes' => $reason,
                'reverses_id' => $transaction->id,
                'created_by' => $by->id,
            ]);

            $transaction->update(['cancelled_at' => now(), 'cancel_reason' => $reason]);

            return $reversal;
        });
    }

    /** Clôture la période qui suit la dernière clôture, jusqu'à `$periodEnd` inclus. */
    public function close(CarbonInterface $periodEnd, ?int $countedCash, User $by, ?string $notes = null): CashClosing
    {
        $last = CashClosing::orderByDesc('period_end')->first();
        $periodStart = $last
            ? $last->period_end->copy()->addDay()
            : Carbon::parse((string) (CashTransaction::min('occurred_on') ?? $periodEnd->toDateString()));

        if ($periodEnd->lt($periodStart)) {
            throw new BusinessRuleException('Cette période est déjà clôturée.');
        }
        if ($periodEnd->isAfter(today())) {
            throw new BusinessRuleException('On ne peut pas clôturer une période future.');
        }

        $opening = $last?->closing_balance ?? 0;
        $inPeriod = CashTransaction::whereBetween('occurred_on', [$periodStart->toDateString(), $periodEnd->toDateString()]);
        $totalIn = (int) (clone $inPeriod)->where('direction', CashDirection::IN)->sum('amount');
        $totalOut = (int) (clone $inPeriod)->where('direction', CashDirection::OUT)->sum('amount');

        return CashClosing::create([
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'opening_balance' => $opening,
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'closing_balance' => $opening + $totalIn - $totalOut,
            'counted_cash' => $countedCash,
            'notes' => $notes,
            'closed_by' => $by->id,
        ]);
    }

    /** Solde de caisse (toutes écritures, contre-écritures incluses) jusqu'à une date. */
    public function balance(?CarbonInterface $until = null): int
    {
        $query = CashTransaction::query()->when($until, fn ($q) => $q->where('occurred_on', '<=', $until->toDateString()));

        return (int) (clone $query)->where('direction', CashDirection::IN)->sum('amount')
            - (int) (clone $query)->where('direction', CashDirection::OUT)->sum('amount');
    }

    private function ensureNotClosed(CarbonInterface $date): void
    {
        $lastEnd = CashClosing::max('period_end');
        if ($lastEnd && $date->lte(Carbon::parse($lastEnd))) {
            throw new BusinessRuleException('Cette date appartient à une période de caisse déjà clôturée.');
        }
    }

    private function nextNumber(CashDirection $direction, CarbonInterface $date): string
    {
        $prefix = $direction === CashDirection::IN ? 'REC' : 'DEP';
        $year = $date->year;
        $base = "{$prefix}-{$year}-";
        $number = $this->sequences->next(
            "cash:{$prefix}:{$year}",
            fn () => SequenceService::maxSuffix('cash_transactions', 'number', $base)
        );

        return $base.str_pad((string) $number, 5, '0', STR_PAD_LEFT);
    }
}
