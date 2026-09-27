<?php

namespace App\Filament\Resources\RentalPacks\Pages;

use App\Filament\Resources\RentalPacks\RentalPackResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRentalPacks extends ListRecords
{
    protected static string $resource = RentalPackResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nouveau pack')];
    }
}
