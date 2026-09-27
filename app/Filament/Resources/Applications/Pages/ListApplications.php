<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\Applications\ApplicationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Saisir une candidature')];
    }

    public function getTabs(): array
    {
        return [
            'todo' => Tab::make('À traiter')->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ApplicationStatus::open())),
            'accepted' => Tab::make('Acceptées')->modifyQueryUsing(fn (Builder $query) => $query->where('status', ApplicationStatus::ACCEPTED)),
            'enrolled' => Tab::make('Inscrits')->modifyQueryUsing(fn (Builder $query) => $query->where('status', ApplicationStatus::ENROLLED)),
            'closed' => Tab::make('Refusées / désistements')->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', [ApplicationStatus::REJECTED, ApplicationStatus::WITHDRAWN])),
            'all' => Tab::make('Toutes'),
        ];
    }
}
