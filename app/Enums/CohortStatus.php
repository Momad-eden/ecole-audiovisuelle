<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CohortStatus: string implements HasColor, HasLabel
{
    case PLANNED = 'planned';
    case OPEN = 'open';
    case CLOSED = 'closed';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PLANNED => 'Programmée',
            self::OPEN => 'Candidatures ouvertes',
            self::CLOSED => 'Candidatures fermées',
            self::RUNNING => 'En cours',
            self::COMPLETED => 'Terminée',
            self::CANCELLED => 'Annulée',
        };
    }

    /** Libellé dans la langue de l'API publique (`en` : anglais) ; en français, identique à getLabel() (admin). */
    public function labelFor(string $locale): string
    {
        if ($locale !== 'en') {
            return $this->getLabel();
        }

        return match ($this) {
            self::PLANNED => 'Scheduled',
            self::OPEN => 'Applications open',
            self::CLOSED => 'Applications closed',
            self::RUNNING => 'In progress',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PLANNED => 'gray',
            self::OPEN => 'success',
            self::CLOSED => 'warning',
            self::RUNNING => 'info',
            self::COMPLETED => 'gray',
            self::CANCELLED => 'danger',
        };
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
