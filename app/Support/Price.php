<?php

namespace App\Support;

use App\Enums\PriceUnit;

final class Price
{
    /** « À partir de 150 000 FCFA / jour », ou « Sur devis » sans prix. */
    public static function label(?int $from, ?PriceUnit $unit = null): string
    {
        if ($from === null) {
            return 'Sur devis';
        }

        return 'À partir de '.Money::fcfa($from).($unit ? ' / '.$unit->suffix() : '');
    }
}
