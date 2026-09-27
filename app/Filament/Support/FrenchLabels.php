<?php

namespace App\Filament\Support;

use Illuminate\Support\Str;

/** En français, seule la première lettre d'un intitulé prend une majuscule (« Clôtures de caisse »). */
trait FrenchLabels
{
    public static function getTitleCaseModelLabel(): string
    {
        return Str::ucfirst(static::getModelLabel());
    }

    public static function getTitleCasePluralModelLabel(): string
    {
        return Str::ucfirst(static::getPluralModelLabel());
    }
}
