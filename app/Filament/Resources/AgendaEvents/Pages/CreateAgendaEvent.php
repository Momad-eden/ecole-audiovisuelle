<?php

namespace App\Filament\Resources\AgendaEvents\Pages;

use App\Filament\Resources\AgendaEvents\AgendaEventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAgendaEvent extends CreateRecord
{
    protected static string $resource = AgendaEventResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
