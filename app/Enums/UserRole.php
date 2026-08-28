<?php

namespace App\Enums;

enum UserRole: string
{
    case DIRECTEUR = 'directeur';
    case GESTIONNAIRE = 'gestionnaire';
    case SECRETAIRE = 'secretaire';
    case COMMUNICATION = 'communication';

    public function label(): string
    {
        return match ($this) {
            self::DIRECTEUR => 'Directeur',
            self::GESTIONNAIRE => 'Gestionnaire',
            self::SECRETAIRE => 'Secrétaire',
            self::COMMUNICATION => 'Communication',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::DIRECTEUR->value => self::DIRECTEUR->label(),
            self::GESTIONNAIRE->value => self::GESTIONNAIRE->label(),
            self::SECRETAIRE->value => self::SECRETAIRE->label(),
            self::COMMUNICATION->value => self::COMMUNICATION->label(),
        ];
    }
}
