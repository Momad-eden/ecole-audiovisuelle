<?php

namespace App\Enums;

enum AdmissionStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::APPROVED => 'Acceptée',
            self::REJECTED => 'Refusée',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
