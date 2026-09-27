<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;

    /**
     * Volets proposés dans le formulaire public (normalisés en Phase 2).
     */
    public const PUBLIC_VOLETS = [
        'Volet 1 — Perfectionnement intensif (3 mois)',
        'Volet 2 — Certification BTS-VAE (9 mois)',
    ];

    protected $fillable = [
        // Identité
        'first_name',
        'last_name',
        'birth_date',
        'birth_place',
        'gender',
        'nationality',
        'phone',
        'email',
        'address',

        // Parcours académique
        'last_diploma',
        'graduation_year',
        'previous_school',
        'academic_field',

        // Candidature
        'course_id',
        'volet',
        'student_id',
        'status',
        'message',
        'processed_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'graduation_year' => 'integer',
        'processed_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class)->withTrashed();
    }

    public function student()
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }
}
