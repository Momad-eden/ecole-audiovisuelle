<?php

namespace App\Enums;

enum TranslationStatus: string
{
    case AUTO = 'auto';
    case REVIEWED = 'reviewed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::AUTO => 'Traduction automatique',
            self::REVIEWED => 'Relue',
            self::FAILED => 'Échec',
        };
    }
}
