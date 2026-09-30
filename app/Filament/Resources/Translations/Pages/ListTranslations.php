<?php

namespace App\Filament\Resources\Translations\Pages;

use App\Filament\Resources\Translations\TranslationResource;
use Filament\Resources\Pages\ListRecords;

class ListTranslations extends ListRecords
{
    protected static string $resource = TranslationResource::class;

    public function getSubheading(): ?string
    {
        return 'Traductions anglaises à vérifier. Cliquez sur une fiche pour ouvrir son onglet « Anglais ».';
    }
}
