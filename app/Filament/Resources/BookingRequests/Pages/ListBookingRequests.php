<?php

namespace App\Filament\Resources\BookingRequests\Pages;

use App\Enums\BookingStatus;
use App\Filament\Resources\BookingRequests\BookingRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBookingRequests extends ListRecords
{
    protected static string $resource = BookingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Saisir une demande (téléphone)')];
    }

    public function getTabs(): array
    {
        return [
            'todo' => Tab::make('À traiter')->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', BookingStatus::open())),
            'done' => Tab::make('Réalisées')->modifyQueryUsing(fn (Builder $query) => $query->where('status', BookingStatus::DONE)),
            'cancelled' => Tab::make('Annulées')->modifyQueryUsing(fn (Builder $query) => $query->where('status', BookingStatus::CANCELLED)),
            'all' => Tab::make('Toutes'),
        ];
    }
}
