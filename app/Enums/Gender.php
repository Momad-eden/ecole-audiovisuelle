<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Gender: string implements HasLabel
{
    case FEMALE = 'female';
    case MALE = 'male';

    public function getLabel(): string
    {
        return match ($this) {
            self::FEMALE => 'Femme',
            self::MALE => 'Homme',
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
