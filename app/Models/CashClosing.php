<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCampus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Clôture de caisse : après clôture, aucune écriture ne peut être datée dans la période. */
class CashClosing extends Model
{
    use BelongsToCampus;

    protected $fillable = ['place_id', 'period_start', 'period_end', 'opening_balance', 'total_in', 'total_out', 'closing_balance', 'counted_cash', 'notes', 'closed_by'];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'opening_balance' => 'integer',
        'total_in' => 'integer',
        'total_out' => 'integer',
        'closing_balance' => 'integer',
        'counted_cash' => 'integer',
    ];

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function difference(): ?int
    {
        return $this->counted_cash === null ? null : $this->counted_cash - $this->closing_balance;
    }
}
