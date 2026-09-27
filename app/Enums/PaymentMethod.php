<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case CASH = 'cash';
    case WAVE = 'wave';
    case ORANGE_MONEY = 'orange_money';
    case FREE_MONEY = 'free_money';
    case BANK = 'bank';
    case CHEQUE = 'cheque';
    case CARD = 'card';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return $this->label();
    }

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Espèces',
            self::WAVE => 'Wave',
            self::ORANGE_MONEY => 'Orange Money',
            self::FREE_MONEY => 'Free Money',
            self::BANK => 'Virement bancaire',
            self::CHEQUE => 'Chèque',
            self::CARD => 'Carte bancaire',
            self::OTHER => 'Autre moyen',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
