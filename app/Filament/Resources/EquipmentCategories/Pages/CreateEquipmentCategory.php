<?php

namespace App\Filament\Resources\EquipmentCategories\Pages;

use App\Filament\Resources\EquipmentCategories\EquipmentCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEquipmentCategory extends CreateRecord
{
    protected static string $resource = EquipmentCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
