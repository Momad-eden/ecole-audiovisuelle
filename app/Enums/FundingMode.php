<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FundingMode: string implements HasLabel
{
    case PAID = 'paid';
    case SPONSORED = 'sponsored';
    case SCHOLARSHIP = 'scholarship';
    case MIXED = 'mixed';

    public function getLabel(): string
    {
        return match ($this) {
            self::PAID => 'Payant',
            self::SPONSORED => 'Pris en charge',
            self::SCHOLARSHIP => 'Bourse',
            self::MIXED => 'Mixte',
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
