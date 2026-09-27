<?php

namespace App\Support;

final class Money
{
    /** 1250000 → « 1 250 000 FCFA » (montants entiers, sans décimales). */
    public static function fcfa(int|float|string|null $amount): string
    {
        return number_format((int) $amount, 0, ',', ' ').' FCFA';
    }
}
