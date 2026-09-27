<?php

namespace App\Filament\Resources\EquipmentItems\Pages;

use App\Filament\Resources\EquipmentItems\EquipmentItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEquipmentItem extends CreateRecord
{
    protected static string $resource = EquipmentItemResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
