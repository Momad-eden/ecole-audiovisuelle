<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Audience: string implements HasColor, HasLabel
{
    case SCHOOL = 'school';
    case PROFESSIONAL = 'professional';

    public function getLabel(): string
    {
        return match ($this) {
            self::SCHOOL => 'École',
            self::PROFESSIONAL => 'Professionnels',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::SCHOOL => 'info',
            self::PROFESSIONAL => 'warning',
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
