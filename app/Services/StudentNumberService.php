<?php

namespace App\Services;

use App\Models\Student;

class StudentNumberService
{
    /**
     * Génère un numéro de matricule unique pour un nouvel étudiant.
     * Format: EMSI-YYYY-XXXX (Ex: EMSI-2026-0001)
     */
    public function generate(?int $year = null): string
    {
        $year = $year ?? (int) date('Y');
        $prefix = "EMSI-{$year}-";

        // Récupérer le dernier étudiant enregistré pour cette année
        $lastStudent = Student::where('student_number', 'LIKE', "{$prefix}%")
            ->orderBy('student_number', 'desc')
            ->first();

        $sequence = 1;

        if ($lastStudent && preg_match('/-(\d{4})$/', $lastStudent->student_number, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        // Boucle de sécurité pour garantir l'unicité
        do {
            $studentNumber = $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $exists = Student::where('student_number', $studentNumber)->exists();
            if ($exists) {
                $sequence++;
            }
        } while ($exists);

        return $studentNumber;
    }
}
