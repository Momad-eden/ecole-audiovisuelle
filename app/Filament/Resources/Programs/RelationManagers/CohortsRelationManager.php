<?php

namespace App\Filament\Resources\Programs\RelationManagers;

use App\Filament\Resources\Cohorts\CohortResource;
use App\Models\Cohort;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CohortsRelationManager extends RelationManager
{
    protected static string $relationship = 'cohorts';

    protected static ?string $title = 'Sessions';

    protected static ?string $modelLabel = 'session';

    public function form(Schema $schema): Schema
    {
        return CohortResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Session')->weight('bold'),
                TextColumn::make('starts_on')->label('Début')->date('d/m/Y'),
                TextColumn::make('ends_on')->label('Fin')->date('d/m/Y'),
                TextColumn::make('status')->label('Candidatures')->badge(),
                TextColumn::make('offerings_count')->label('Offres')->counts('offerings'),
            ])
            ->headerActions([CreateAction::make()->label('Nouvelle session')])
            ->recordActions([
                Action::make('open')->label('Gérer (offres, places, frais)')->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Cohort $record) => CohortResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
