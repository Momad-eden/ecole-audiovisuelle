<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    public function getTitle(): string
    {
        return "{$this->record->full_name} ({$this->record->student_number})";
    }

    protected function getHeaderActions(): array
    {
        return [EditAction::make()->label('Modifier')];
    }
}
