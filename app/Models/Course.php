<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'slug',
        'description',
        'duration',
        'level',
        'students_count',
        'price',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'students_count' => 'integer',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Student::class);
    }

    /**
     * Capacité cible / Quota d'apprenants défini pour la formation.
     */
    public function getCapacityAttribute(): int
    {
        return (int) ($this->attributes['students_count'] ?? 0);
    }

    /**
     * Nombre réel d'étudiants inscrits (gère le chargement direct ou par relation).
     */
    public function getEnrolledStudentsCountAttribute(): int
    {
        if ($this->relationLoaded('students')) {
            return $this->students->count();
        }
        return $this->students()->count();
    }

    /**
     * Taux de remplissage (%) par rapport à la capacité cible.
     */
    public function getFillingRateAttribute(): float
    {
        $cap = $this->capacity;
        if ($cap <= 0) {
            return 0.0;
        }
        $enrolled = $this->enrolled_students_count;
        return min(100.0, round(($enrolled / $cap) * 100, 1));
    }

    /**
     * Total attendu des scolarités pour les étudiants inscrits.
     */
    public function getTotalExpectedRevenueAttribute(): float
    {
        $enrolled = $this->enrolled_students_count;
        return (float) ($this->price * $enrolled);
    }

    /**
     * Total effectivement encaissé pour cette formation.
     */
    public function getTotalCollectedRevenueAttribute(): float
    {
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->where('type', 'inflow')->sum('amount');
        }
        return (float) $this->payments()->where('type', 'inflow')->sum('amount');
    }
}