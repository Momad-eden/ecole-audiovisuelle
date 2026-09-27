<?php

namespace App\Models;

use App\Enums\CashDirection;
use App\Enums\EnrollmentStatus;
use App\Enums\FundingMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** Inscription d'un étudiant à une offre, avec les frais figés au moment de l'inscription. */
class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id', 'offering_id', 'application_id', 'enrolled_on', 'status', 'fee_amount_due',
        'discount_amount', 'funding_mode', 'certificate_issued_at', 'notes',
    ];

    protected $casts = [
        'status' => EnrollmentStatus::class,
        'funding_mode' => FundingMode::class,
        'enrolled_on' => 'date',
        'certificate_issued_at' => 'datetime',
        'fee_amount_due' => 'integer',
        'discount_amount' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class)->withTrashed();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }

    /** Montant net à payer (frais − remise). */
    public function amountDue(): int
    {
        return max(0, $this->fee_amount_due - $this->discount_amount);
    }

    /** Total encaissé net des annulations. */
    public function amountPaid(): int
    {
        $in = (int) $this->transactions()->where('direction', CashDirection::IN)->sum('amount');
        $out = (int) $this->transactions()->where('direction', CashDirection::OUT)->sum('amount');

        return $in - $out;
    }

    public function balance(): int
    {
        return $this->amountDue() - $this->amountPaid();
    }
}
