<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number',
        'photo',
        'first_name',
        'last_name',
        'gender',
        'birth_date',
        'birth_place',
        'nationality',
        'phone',
        'email',
        'address',
        'course_id',
        'registration_date',
        'status',
        'notes',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getMatriculeAttribute(): ?string
    {
        return $this->student_number;
    }

    public function getTotalPaidAttribute(): float
    {
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->where('type', 'inflow')->sum('amount');
        }
        return (float) $this->payments()->where('type', 'inflow')->sum('amount');
    }

    public function getRemainingDueAttribute(): float
    {
        $price = (float) ($this->course?->price ?? 0);
        return max(0, $price - $this->total_paid);
    }

    public function getPaymentPercentageAttribute(): float
    {
        $price = (float) ($this->course?->price ?? 0);
        if ($price <= 0) {
            return 100.0;
        }
        return min(100.0, round(($this->total_paid / $price) * 100, 1));
    }

    public function getPaymentStatusAttribute(): string
    {
        $price = (float) ($this->course?->price ?? 0);
        $paid = $this->total_paid;

        if ($price > 0 && $paid >= $price) {
            return 'paid'; // Soldé
        } elseif ($paid > 0) {
            return 'partial'; // Partiel
        }
        return 'unpaid'; // Non payé
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function admission()
    {
        return $this->hasOne(Admission::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
