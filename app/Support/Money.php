<?php

namespace App\Support;

final class Money
{
    /** 1250000 → « 1 250 000 FCFA » (montants entiers, sans décimales) ; en anglais « 1,250,000 FCFA ». */
    public static function fcfa(int|float|string|null $amount, string $locale = 'fr'): string
    {
        return $locale === 'en'
            ? number_format((int) $amount, 0, '.', ',').' FCFA'
            : number_format((int) $amount, 0, ',', ' ').' FCFA';
    }
}
