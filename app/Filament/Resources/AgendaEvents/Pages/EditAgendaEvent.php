<?php

namespace App\Filament\Resources\AgendaEvents\Pages;

use App\Filament\Resources\AgendaEvents\AgendaEventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAgendaEvent extends EditRecord
{
    protected static string $resource = AgendaEventResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
