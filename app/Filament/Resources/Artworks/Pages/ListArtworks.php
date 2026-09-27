<?php

namespace App\Filament\Resources\Artworks\Pages;

use App\Filament\Resources\Artworks\ArtworkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListArtworks extends ListRecords
{
    protected static string $resource = ArtworkResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Ajouter une réalisation')];
    }

    public function getTabs(): array
    {
        return [
            'school' => Tab::make('Étudiants')->modifyQueryUsing(fn (Builder $query) => $query->where('origin', 'school')),
            'studio' => Tab::make('Productions du studio')->modifyQueryUsing(fn (Builder $query) => $query->where('origin', 'studio')),
            'all' => Tab::make('Toutes'),
        ];
    }
}
