<?php

namespace App\Filament\Resources\BookingRequests\Pages;

use App\Filament\Resources\BookingRequests\BookingRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBookingRequest extends CreateRecord
{
    protected static string $resource = BookingRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->record]);
    }
}
