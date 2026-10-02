<?php

namespace App\Support;

use App\Enums\PriceUnit;

final class Price
{
    /**
     * « À partir de 150 000 FCFA / jour », ou « Sur devis » sans prix ;
     * en anglais « From 150,000 FCFA per day », ou « On request ».
     */
    public static function label(?int $from, ?PriceUnit $unit = null, string $locale = 'fr'): string
    {
        if ($locale === 'en') {
            return $from === null ? 'On request' : 'From '.Money::fcfa($from, 'en').($unit ? ' '.$unit->labelFor('en') : '');
        }

        if ($from === null) {
            return 'Sur devis';
        }

        return 'À partir de '.Money::fcfa($from).($unit ? ' / '.$unit->suffix() : '');
    }
}
