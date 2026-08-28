<?php

namespace App\Enums;

enum StudentStatus: string
{
    case INSCRIT = 'Inscrit';
    case DIPLOME = 'Diplômé';
    case SUSPENDU = 'Suspendu';
    case ABANDONNE = 'Abandonné';

    public function label(): string
    {
        return match ($this) {
            self::INSCRIT => 'Inscrit',
            self::DIPLOME => 'Diplômé',
            self::SUSPENDU => 'Suspendu',
            self::ABANDONNE => 'Abandonné',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
