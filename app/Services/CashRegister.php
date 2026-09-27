<?php

namespace App\Services;

use App\Enums\CashDirection;
use App\Enums\PaymentMethod;
use App\Enums\TransactionCategory;
use App\Exceptions\BusinessRuleException;
use App\Models\CashClosing;
use App\Models\CashTransaction;
use App\Models\Enrollment;
use App\Models\Place;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Caisse de l'école : écritures inaltérables, numérotées par année,
 * annulées par contre-écriture et verrouillées par les clôtures.
 * Chaque campus (Dakar, Saint-Louis) tient sa propre caisse : numérotation
 * (REC-DKR-2026-00001), solde et clôtures séparés.
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
        $direction = $data['direction'] instanceof CashDirection ? $data['direction'] : CashDirection::from($data['direction']);
        $category = $data['category'] instanceof TransactionCategory ? $data['category'] : TransactionCategory::from($data['category']);
        $method = $data['method'] instanceof PaymentMethod ? $data['method'] : PaymentMethod::from($data['method']);
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
        $enrollment = isset($data['enrollment_id']) ? Enrollment::with('student')->find($data['enrollment_id']) : null;
        if ($category->requiresEnrollment() && ! $enrollment) {
            throw new BusinessRuleException('Un paiement de scolarité ou d\'inscription doit être rattaché à une inscription.');
        }

        $place = $this->resolvePlace($data['place_id'] ?? null, $by, $enrollment);
        $this->ensureNotClosed($occurredOn, $place);

        $label = filled($data['label'] ?? null)
            ? $data['label']
            : $category->getLabel().($enrollment ? ' — '.$enrollment->student->full_name : '');

        return DB::transaction(fn () => CashTransaction::create([
            'place_id' => $place?->id,
            'number' => $this->nextNumber($direction, $occurredOn, $place),
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
        if (! $transaction->isVisibleTo($by)) {
            throw new BusinessRuleException('Cette écriture appartient à la caisse d\'un autre campus.');
        }
        $place = $transaction->place;
        $this->ensureNotClosed(today(), $place);

        return DB::transaction(function () use ($transaction, $by, $reason, $place) {
            $direction = $transaction->direction === CashDirection::IN ? CashDirection::OUT : CashDirection::IN;

            $reversal = CashTransaction::create([
                'place_id' => $place?->id,
                'number' => $this->nextNumber($direction, today(), $place),
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

    /** Clôture la caisse d'un campus, de la dernière clôture jusqu'à `$periodEnd` inclus. */
    public function close(?Place $place, CarbonInterface $periodEnd, ?int $countedCash, User $by, ?string $notes = null): CashClosing
    {
        if ($by->place_id && $place?->id !== $by->place_id) {
            throw new BusinessRuleException('Vous ne pouvez clôturer que la caisse de votre campus.');
        }

        $last = $this->forPlace(CashClosing::query(), $place)->orderByDesc('period_end')->first();
        $periodStart = $last
            ? $last->period_end->copy()->addDay()
            : Carbon::parse((string) ($this->forPlace(CashTransaction::query(), $place)->min('occurred_on') ?? $periodEnd->toDateString()));

        if ($periodEnd->lt($periodStart)) {
            throw new BusinessRuleException('Cette période est déjà clôturée.');
        }
        if ($periodEnd->isAfter(today())) {
            throw new BusinessRuleException('On ne peut pas clôturer une période future.');
        }

        $opening = $last?->closing_balance ?? 0;
        $inPeriod = $this->forPlace(CashTransaction::query(), $place)->whereBetween('occurred_on', [$periodStart->toDateString(), $periodEnd->toDateString()]);
        $totalIn = (int) (clone $inPeriod)->where('direction', CashDirection::IN)->sum('amount');
        $totalOut = (int) (clone $inPeriod)->where('direction', CashDirection::OUT)->sum('amount');

        return CashClosing::create([
            'place_id' => $place?->id,
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

    /** Solde de caisse d'un campus (ou de tous les campus), contre-écritures incluses, jusqu'à une date. */
    public function balance(?Place $place = null, ?CarbonInterface $until = null): int
    {
        $query = CashTransaction::query()
            ->when($place, fn ($q) => $q->where('place_id', $place->id))
            ->when($until, fn ($q) => $q->where('occurred_on', '<=', $until->toDateString()));

        return (int) (clone $query)->where('direction', CashDirection::IN)->sum('amount')
            - (int) (clone $query)->where('direction', CashDirection::OUT)->sum('amount');
    }

    /**
     * Campus de l'écriture : celui de l'étudiant pour une scolarité, sinon celui choisi,
     * sinon celui de l'agent ; obligatoire dès que l'école a plusieurs campus.
     */
    private function resolvePlace(mixed $placeId, User $by, ?Enrollment $enrollment): ?Place
    {
        $placeId = filled($placeId) ? (int) $placeId : null;
        $studentPlace = $enrollment?->student?->place_id;

        if ($studentPlace && $placeId && $placeId !== (int) $studentPlace) {
            throw new BusinessRuleException('Cet étudiant est inscrit dans un autre campus : le paiement va dans la caisse de son campus.');
        }
        $placeId = $studentPlace ?? $placeId;

        if ($by->place_id) {
            if ($placeId && $placeId !== (int) $by->place_id) {
                throw new BusinessRuleException('Vous ne pouvez saisir que dans la caisse de votre campus.');
            }
            $placeId = (int) $by->place_id;
        }

        if (! $placeId) {
            $campuses = Place::campuses()->pluck('id');
            if ($campuses->count() > 1) {
                throw new BusinessRuleException('Choisissez le campus (la caisse) de cette opération.');
            }
            $placeId = $campuses->first();
        }

        return $placeId ? Place::find($placeId) : null;
    }

    private function forPlace($query, ?Place $place)
    {
        return $place ? $query->where('place_id', $place->id) : $query->whereNull('place_id');
    }

    private function ensureNotClosed(CarbonInterface $date, ?Place $place): void
    {
        $lastEnd = $this->forPlace(CashClosing::query(), $place)->max('period_end');
        if ($lastEnd && $date->lte(Carbon::parse($lastEnd))) {
            throw new BusinessRuleException('Cette date appartient à une période de caisse déjà clôturée.');
        }
    }

    private function nextNumber(CashDirection $direction, CarbonInterface $date, ?Place $place = null): string
    {
        $prefix = $direction === CashDirection::IN ? 'REC' : 'DEP';
        $year = $date->year;
        $code = $place?->code;
        $base = $code ? "{$prefix}-{$code}-{$year}-" : "{$prefix}-{$year}-";
        $number = $this->sequences->next(
            $code ? "cash:{$prefix}:{$code}:{$year}" : "cash:{$prefix}:{$year}",
            fn () => SequenceService::maxSuffix('cash_transactions', 'number', $base)
        );

        return $base.str_pad((string) $number, 5, '0', STR_PAD_LEFT);
    }
}
