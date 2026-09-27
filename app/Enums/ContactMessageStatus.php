<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ContactMessageStatus: string implements HasColor, HasLabel
{
    case NEW = 'new';
    case HANDLED = 'handled';
    case SPAM = 'spam';

    public function getLabel(): string
    {
        return match ($this) {
            self::NEW => 'Nouveau',
            self::HANDLED => 'Traité',
            self::SPAM => 'Indésirable',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NEW => 'warning',
            self::HANDLED => 'success',
            self::SPAM => 'gray',
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
