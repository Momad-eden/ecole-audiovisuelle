<?php

namespace App\Enums;

enum Gender: string
{
    case HOMME = 'Homme';
    case FEMME = 'Femme';

    public static function fromAdmissionCode(?string $code): ?string
    {
        return match (strtoupper((string) $code)) {
            'M', 'HOMME' => self::HOMME->value,
            'F', 'FEMME' => self::FEMME->value,
            default => null,
        };
    }

    public static function toAdmissionCode(?string $gender): ?string
    {
        return match ($gender) {
            'Homme', 'M' => 'M',
            'Femme', 'F' => 'F',
            default => null,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
