<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\TransactionCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'title',
        'student_id',
        'amount',
        'payment_method',
        'reference',
        'payment_date',
        'notes',
        'receipt_number',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->type)) {
                $payment->type = 'inflow';
            }

            if (empty($payment->category)) {
                $payment->category = $payment->type === 'inflow' ? 'scolarite' : 'autre_depense';
            }

            if (empty($payment->receipt_number)) {
                $prefix = $payment->type === 'inflow' ? 'REC' : 'DEP';
                $period = date('Ym');
                $count = static::whereYear('created_at', date('Y'))
                    ->whereMonth('created_at', date('m'))
                    ->count() + 1;
                $payment->receipt_number = sprintf('%s-%s-%04d', $prefix, $period, $count);
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeInflows(Builder $query): Builder
    {
        return $query->where('type', 'inflow');
    }

    public function scopeOutflows(Builder $query): Builder
    {
        return $query->where('type', 'outflow');
    }

    public function isInflow(): bool
    {
        return $this->type === 'inflow';
    }

    public function isOutflow(): bool
    {
        return $this->type === 'outflow';
    }

    public function getCategoryLabelAttribute(): string
    {
        if ($this->category) {
            $catEnum = TransactionCategory::tryFrom($this->category);
            if ($catEnum) {
                return $catEnum->label();
            }
        }
        return $this->isInflow() ? 'Encaissement' : 'Décaissement';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        if ($this->payment_method) {
            $methodEnum = PaymentMethod::tryFrom($this->payment_method);
            if ($methodEnum) {
                return $methodEnum->label();
            }
        }
        return $this->payment_method ?? 'Non précisé';
    }
}