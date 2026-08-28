<?php

namespace App\Services;

use App\Enums\AdmissionStatus;
use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Models\Admission;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AdmissionService
{
    public function __construct(
        protected StudentNumberService $studentNumberService
    ) {}

    /**
     * Crée une nouvelle candidature.
     */
    public function createAdmission(array $data): Admission
    {
        $data['status'] = $data['status'] ?? AdmissionStatus::PENDING->value;
        return Admission::create($data);
    }

    /**
     * Met à jour une candidature existante.
     */
    public function updateAdmission(Admission $admission, array $data): Admission
    {
        $currentStatus = $admission->status;
        $newStatus = $data['status'] ?? $currentStatus;

        if ($currentStatus !== $newStatus) {
            if (in_array($newStatus, [AdmissionStatus::APPROVED->value, AdmissionStatus::REJECTED->value], true)) {
                $data['processed_at'] = now();
            } elseif ($newStatus === AdmissionStatus::PENDING->value) {
                $data['processed_at'] = null;
            }
        }

        $admission->update($data);

        return $admission;
    }

    /**
     * Approuve une candidature.
     */
    public function approve(Admission $admission): bool
    {
        if ($admission->status !== AdmissionStatus::PENDING->value) {
            return false;
        }

        return $admission->update([
            'status' => AdmissionStatus::APPROVED->value,
            'processed_at' => now(),
        ]);
    }

    /**
     * Rejette une candidature.
     */
    public function reject(Admission $admission): bool
    {
        if ($admission->status !== AdmissionStatus::PENDING->value) {
            return false;
        }

        return $admission->update([
            'status' => AdmissionStatus::REJECTED->value,
            'processed_at' => now(),
        ]);
    }

    /**
     * Transforme une candidature acceptée en étudiant (enrôlement).
     */
    public function enroll(Admission $admission): Student
    {
        if ($admission->status !== AdmissionStatus::APPROVED->value) {
            throw new InvalidArgumentException('Cette admission doit être acceptée avant l’inscription.');
        }

        if ($admission->student_id) {
            throw new InvalidArgumentException('Ce candidat est déjà inscrit comme étudiant.');
        }

        if (!$admission->course_id) {
            throw new InvalidArgumentException('Aucune formation n’est associée à cette candidature.');
        }

        return DB::transaction(function () use ($admission) {
            $studentNumber = $this->studentNumberService->generate();

            // Résolution du bug de genre : conversion propre du format M/F vers Homme/Femme
            $studentGender = Gender::fromAdmissionCode($admission->gender) ?? Gender::HOMME->value;

            $student = Student::create([
                'student_number'    => $studentNumber,
                'first_name'        => $admission->first_name,
                'last_name'         => $admission->last_name,
                'birth_date'        => $admission->birth_date,
                'birth_place'       => $admission->birth_place,
                'gender'            => $studentGender,
                'nationality'       => $admission->nationality ?? 'Sénégalaise',
                'phone'             => $admission->phone,
                'email'             => $admission->email,
                'address'           => $admission->address,
                'course_id'         => $admission->course_id,
                'registration_date' => now()->toDateString(),
                'status'            => StudentStatus::INSCRIT->value,
            ]);

            $admission->update([
                'student_id' => $student->id,
            ]);

            return $student;
        });
    }
}
