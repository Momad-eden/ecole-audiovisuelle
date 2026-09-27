<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CashDirection: string implements HasColor, HasLabel
{
    case IN = 'in';
    case OUT = 'out';

    public function getLabel(): string
    {
        return match ($this) {
            self::IN => 'Encaissement',
            self::OUT => 'Décaissement',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::IN => 'success',
            self::OUT => 'danger',
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
