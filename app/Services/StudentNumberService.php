<?php

namespace App\Services;

class StudentNumberService
{
    public function __construct(
        protected SequenceService $sequences
    ) {}

    /**
     * Génère un numéro de matricule unique pour un nouvel étudiant.
     * Format: EMSI-YYYY-XXXX (Ex: EMSI-2026-0001)
     */
    public function generate(?int $year = null): string
    {
        $year = $year ?? (int) date('Y');
        $prefix = "EMSI-{$year}-";

        $number = $this->sequences->next(
            "student:{$year}",
            fn () => SequenceService::maxSuffix('students', 'student_number', $prefix)
        );

        return $prefix.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }
}
