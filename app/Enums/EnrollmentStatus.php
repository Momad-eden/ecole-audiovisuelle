<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EnrollmentStatus: string implements HasColor, HasLabel
{
    case ENROLLED = 'enrolled';
    case SUSPENDED = 'suspended';
    case WITHDRAWN = 'withdrawn';
    case GRADUATED = 'graduated';
    case FAILED = 'failed';

    public function getLabel(): string
    {
        return match ($this) {
            self::ENROLLED => 'Inscrit',
            self::SUSPENDED => 'Suspendu',
            self::WITHDRAWN => 'Abandon',
            self::GRADUATED => 'Diplômé',
            self::FAILED => 'Non certifié',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ENROLLED => 'success',
            self::SUSPENDED => 'warning',
            self::WITHDRAWN => 'danger',
            self::GRADUATED => 'info',
            self::FAILED => 'gray',
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
