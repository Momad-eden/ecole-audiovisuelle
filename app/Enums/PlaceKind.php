<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PlaceKind: string implements HasLabel
{
    case CAMPUS = 'campus';
    case STUDIO = 'studio';
    case CULTURAL_CENTER = 'cultural_center';

    public function getLabel(): string
    {
        return match ($this) {
            self::CAMPUS => 'Campus de l\'EMSI',
            self::STUDIO => 'Studio',
            self::CULTURAL_CENTER => 'Centre culturel',
        };
    }
}
