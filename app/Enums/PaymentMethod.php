<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case WAVE = 'wave';
    case ORANGE_MONEY = 'orange_money';
    case BANK = 'bank';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Espèces',
            self::WAVE => 'Wave',
            self::ORANGE_MONEY => 'Orange Money',
            self::BANK => 'Virement / Banque',
            self::OTHER => 'Autre',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
