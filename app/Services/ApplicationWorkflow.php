<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\UserRole;
use App\Exceptions\BusinessRuleException;
use App\Models\Application;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Traitement d'une candidature : étapes successives (historisées) puis inscription.
 */
class ApplicationWorkflow
{
    public function transition(Application $application, ApplicationStatus $to, User $by, ?string $comment = null): Application
    {
        $from = $application->status;

        if (! $from->canTransitionTo($to)) {
            throw new BusinessRuleException("Impossible de passer de « {$from->getLabel()} » à « {$to->getLabel()} ».");
        }

        if ($to->isDecision() && ! $by->hasRole(UserRole::DIRECTEUR, UserRole::GESTIONNAIRE)) {
            throw new BusinessRuleException('Seuls le directeur et le gestionnaire peuvent décider d\'une candidature.');
        }

        return DB::transaction(function () use ($application, $from, $to, $by, $comment) {
            $attributes = ['status' => $to];
            if ($to->isDecision()) {
                $attributes += ['decided_at' => now(), 'decided_by' => $by->id];
            }
            $application->update($attributes);

            $application->events()->create([
                'type' => 'status_changed',
                'from_status' => $from->value,
                'to_status' => $to->value,
                'comment' => $comment,
                'user_id' => $by->id,
            ]);

            return $application;
        });
    }

    public function addNote(Application $application, User $by, string $comment): void
    {
        $application->events()->create(['type' => 'note', 'comment' => $comment, 'user_id' => $by->id]);
    }

    /**
     * Inscrit un candidat accepté : crée (ou retrouve) l'étudiant et l'inscription,
     * avec les frais de l'offre figés à cette date.
     */
    public function enroll(Application $application, User $by): Enrollment
    {
        if ($application->status !== ApplicationStatus::ACCEPTED) {
            throw new BusinessRuleException('Seule une candidature acceptée peut donner lieu à une inscription.');
        }

        if (! $by->hasRole(UserRole::DIRECTEUR, UserRole::GESTIONNAIRE)) {
            throw new BusinessRuleException('Seuls le directeur et le gestionnaire peuvent inscrire un candidat.');
        }

        return DB::transaction(function () use ($application, $by) {
            $offering = $application->offering()->lockForUpdate()->firstOrFail();

            $enrolled = $offering->enrollments()->where('status', EnrollmentStatus::ENROLLED)->count();
            if ($offering->capacity !== null && $enrolled >= $offering->capacity) {
                throw new BusinessRuleException("Plus de place disponible pour cette offre ({$offering->capacity} places).");
            }

            $student = $this->findOrCreateStudent($application);

            if ($student->enrollments()->where('offering_id', $offering->id)->exists()) {
                throw new BusinessRuleException('Cet étudiant est déjà inscrit à cette offre.');
            }

            $enrollment = $student->enrollments()->create([
                'offering_id' => $offering->id,
                'application_id' => $application->id,
                'enrolled_on' => now()->toDateString(),
                'status' => EnrollmentStatus::ENROLLED,
                'fee_amount_due' => $offering->fee_amount,
                'funding_mode' => $offering->funding_mode,
            ]);

            $from = $application->status;
            $application->update(['status' => ApplicationStatus::ENROLLED, 'student_id' => $student->id]);
            $application->events()->create([
                'type' => 'status_changed',
                'from_status' => $from->value,
                'to_status' => ApplicationStatus::ENROLLED->value,
                'comment' => "Inscription {$student->student_number}",
                'user_id' => $by->id,
            ]);

            return $enrollment;
        });
    }

    /** Dédoublonnage : même e-mail, ou même téléphone et même date de naissance. */
    private function findOrCreateStudent(Application $application): Student
    {
        $existing = Student::query()
            ->when($application->email, fn ($q) => $q->whereRaw('LOWER(email) = ?', [mb_strtolower($application->email)]))
            ->when(! $application->email, fn ($q) => $q->where('phone', $application->phone)->whereDate('birth_date', $application->birth_date))
            ->first();

        return $existing ?? Student::create([
            'place_id' => $application->place_id,
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'gender' => $application->gender,
            'birth_date' => $application->birth_date,
            'birth_place' => $application->birth_place,
            'nationality' => $application->nationality,
            'phone' => $application->phone,
            'email' => $application->email,
            'address' => $application->address,
        ]);
    }
}
