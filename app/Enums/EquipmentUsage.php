<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EquipmentUsage: string implements HasLabel
{
    case RENTAL = 'rental';
    case STUDIO = 'studio';

    public function getLabel(): string
    {
        return match ($this) {
            self::RENTAL => 'À louer (Impact Live Events)',
            self::STUDIO => 'Équipement du studio',
        };
    }
}
