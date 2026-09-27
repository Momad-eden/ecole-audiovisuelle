<?php

namespace App\Filament\Resources\AgendaEvents\Pages;

use App\Filament\Resources\AgendaEvents\AgendaEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAgendaEvents extends ListRecords
{
    protected static string $resource = AgendaEventResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nouvel événement')];
    }
}
