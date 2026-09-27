<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BookingType: string implements HasLabel
{
    case STUDIO_SESSION = 'studio_session';
    case EQUIPMENT_RENTAL = 'equipment_rental';
    case EVENT_SERVICE = 'event_service';
    case SPACE_RENTAL = 'space_rental';

    public function getLabel(): string
    {
        return match ($this) {
            self::STUDIO_SESSION => 'Session studio',
            self::EQUIPMENT_RENTAL => 'Location de matériel',
            self::EVENT_SERVICE => 'Prestation événementielle',
            self::SPACE_RENTAL => 'Location de l\'Espace Habib Faye',
        };
    }
}
