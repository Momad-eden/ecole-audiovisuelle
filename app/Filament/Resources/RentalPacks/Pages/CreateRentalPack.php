<?php

namespace App\Filament\Resources\RentalPacks\Pages;

use App\Filament\Resources\RentalPacks\RentalPackResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRentalPack extends CreateRecord
{
    protected static string $resource = RentalPackResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
