<?php

namespace App\Filament\Resources\RentalPacks\Pages;

use App\Filament\Resources\RentalPacks\RentalPackResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRentalPack extends EditRecord
{
    protected static string $resource = RentalPackResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
