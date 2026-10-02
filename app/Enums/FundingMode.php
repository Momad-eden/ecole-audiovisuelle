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

    /** Libellé dans la langue de l'API publique (`en` : anglais) ; en français, identique à getLabel() (admin). */
    public function labelFor(string $locale): string
    {
        if ($locale !== 'en') {
            return $this->getLabel();
        }

        return match ($this) {
            self::PAID => 'Fee-paying',
            self::SPONSORED => 'Funded',
            self::SCHOLARSHIP => 'Scholarship',
            self::MIXED => 'Mixed',
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
