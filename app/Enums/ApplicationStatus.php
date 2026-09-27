<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ApplicationStatus: string implements HasColor, HasLabel
{
    case SUBMITTED = 'submitted';
    case INCOMPLETE = 'incomplete';
    case UNDER_REVIEW = 'under_review';
    case INTERVIEW_SCHEDULED = 'interview_scheduled';
    case INTERVIEWED = 'interviewed';
    case ACCEPTED = 'accepted';
    case WAITLISTED = 'waitlisted';
    case REJECTED = 'rejected';
    case WITHDRAWN = 'withdrawn';
    case ENROLLED = 'enrolled';

    public function getLabel(): string
    {
        return match ($this) {
            self::SUBMITTED => 'Reçue',
            self::INCOMPLETE => 'Dossier incomplet',
            self::UNDER_REVIEW => 'En cours d\'étude',
            self::INTERVIEW_SCHEDULED => 'Entretien planifié',
            self::INTERVIEWED => 'Entretien passé',
            self::ACCEPTED => 'Acceptée',
            self::WAITLISTED => 'Liste d\'attente',
            self::REJECTED => 'Refusée',
            self::WITHDRAWN => 'Désistement',
            self::ENROLLED => 'Inscrit',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::SUBMITTED, self::UNDER_REVIEW => 'info',
            self::INCOMPLETE, self::WAITLISTED, self::INTERVIEW_SCHEDULED => 'warning',
            self::INTERVIEWED => 'primary',
            self::ACCEPTED, self::ENROLLED => 'success',
            self::REJECTED => 'danger',
            self::WITHDRAWN => 'gray',
        };
    }

    /**
     * Étapes suivantes autorisées depuis ce statut.
     *
     * @return array<int, self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::SUBMITTED => [self::UNDER_REVIEW, self::INCOMPLETE, self::INTERVIEW_SCHEDULED, self::ACCEPTED, self::REJECTED, self::WITHDRAWN],
            self::INCOMPLETE => [self::SUBMITTED, self::UNDER_REVIEW, self::REJECTED, self::WITHDRAWN],
            self::UNDER_REVIEW => [self::INCOMPLETE, self::INTERVIEW_SCHEDULED, self::ACCEPTED, self::WAITLISTED, self::REJECTED, self::WITHDRAWN],
            self::INTERVIEW_SCHEDULED => [self::INTERVIEWED, self::WITHDRAWN],
            self::INTERVIEWED => [self::ACCEPTED, self::WAITLISTED, self::REJECTED, self::WITHDRAWN],
            self::WAITLISTED => [self::ACCEPTED, self::REJECTED, self::WITHDRAWN],
            self::ACCEPTED => [self::WITHDRAWN],
            self::REJECTED, self::WITHDRAWN, self::ENROLLED => [],
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return in_array($status, $this->allowedTransitions(), true);
    }

    /** Une décision (acceptation, refus, liste d'attente) est réservée aux rôles décisionnaires. */
    public function isDecision(): bool
    {
        return in_array($this, [self::ACCEPTED, self::REJECTED, self::WAITLISTED], true);
    }

    /** Statuts encore « à traiter » par l'équipe. */
    public static function open(): array
    {
        return [self::SUBMITTED, self::INCOMPLETE, self::UNDER_REVIEW, self::INTERVIEW_SCHEDULED, self::INTERVIEWED, self::WAITLISTED];
    }

    public function label(): string
    {
        return $this->getLabel();
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
