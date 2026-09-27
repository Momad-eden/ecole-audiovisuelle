<?php

namespace App\Models;

use App\Enums\CashDirection;
use App\Enums\PaymentMethod;
use App\Enums\TransactionCategory;
use App\Models\Concerns\BelongsToCampus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LogicException;

/**
 * Écriture de caisse inaltérable : une erreur se corrige par une contre-écriture
 * (voir CashRegister::cancel), jamais par modification ou suppression.
 */
class CashTransaction extends Model
{
    use BelongsToCampus, HasFactory;

    protected $fillable = [
        'place_id', 'number', 'direction', 'category', 'amount', 'method', 'external_reference', 'occurred_on', 'enrollment_id',
        'payee', 'label', 'notes', 'reverses_id', 'cancelled_at', 'cancel_reason', 'created_by',
    ];

    protected $casts = [
        'direction' => CashDirection::class,
        'category' => TransactionCategory::class,
        'method' => PaymentMethod::class,
        'amount' => 'integer',
        'occurred_on' => 'date',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(function (CashTransaction $transaction) {
            $allowed = ['notes', 'cancelled_at', 'cancel_reason', 'updated_at'];
            if (array_diff(array_keys($transaction->getDirty()), $allowed) !== []) {
                throw new LogicException('Une écriture de caisse ne peut pas être modifiée : annulez-la puis saisissez-la à nouveau.');
            }
        });

        static::deleting(function () {
            throw new LogicException('Une écriture de caisse ne peut pas être supprimée : annulez-la.');
        });
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class)->withTrashed();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reverses(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reverses_id');
    }

    public function reversal(): HasOne
    {
        return $this->hasOne(self::class, 'reverses_id');
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    /** Montant signé : positif pour un encaissement, négatif pour un décaissement. */
    public function signedAmount(): int
    {
        return $this->direction === CashDirection::IN ? $this->amount : -$this->amount;
    }
}
