<?php

namespace App\Filament\Resources\Exhibitions\Pages;

use App\Filament\Resources\Exhibitions\ExhibitionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExhibition extends CreateRecord
{
    protected static string $resource = ExhibitionResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
