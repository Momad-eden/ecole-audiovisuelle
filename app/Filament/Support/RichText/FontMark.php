<?php

namespace App\Filament\Support\RichText;

class FontMark extends ChoiceMark
{
    public static $name = 'emsiFont';

    protected static function attribute(): string
    {
        return 'data-font';
    }

    public static function choices(): array
    {
        return [
            'display' => 'Titre (large)',
            'serif' => 'Élégante',
            'mono' => 'Machine à écrire',
        ];
    }
}
