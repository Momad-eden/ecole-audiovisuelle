<?php

namespace App\Filament\Support\RichText;

class SizeMark extends ChoiceMark
{
    public static $name = 'emsiSize';

    protected static function attribute(): string
    {
        return 'data-size';
    }

    public static function choices(): array
    {
        return [
            'sm' => 'Petit',
            'lg' => 'Grand',
            'xl' => 'Très grand',
            '2xl' => 'Énorme',
        ];
    }
}
