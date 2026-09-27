<?php

namespace App\Filament\Resources\Cohorts\RelationManagers;

use App\Enums\FundingMode;
use App\Models\Offering;
use App\Support\Money;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OfferingsRelationManager extends RelationManager
{
    protected static string $relationship = 'offerings';

    protected static ?string $title = 'Offres (filières, places et frais)';

    protected static ?string $modelLabel = 'offre';

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('track_id')->label('Filière')->relationship('track', 'name')->preload()
                ->helperText('Laisser vide pour une formation sans filière.')->columnSpanFull(),
            TextInput::make('capacity')->label('Nombre de places')->integer()->minValue(1),
            Select::make('funding_mode')->label('Financement')->options(FundingMode::class)->default(FundingMode::PAID)->required(),
            TextInput::make('fee_amount')->label('Frais de formation')->integer()->minValue(0)->default(0)->suffix('FCFA')->required(),
            TextInput::make('registration_fee_amount')->label('Frais d\'inscription')->integer()->minValue(0)->default(0)->suffix('FCFA'),
            TextInput::make('funding_note')->label('Précision sur le financement')->placeholder('Ex. Pris en charge par le programme')->columnSpanFull(),
            Toggle::make('is_open')->label('Ouverte aux candidatures')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('track')->withCount(['enrollments', 'applications']))
            ->columns([
                TextColumn::make('track.name')->label('Filière')->placeholder('—')->weight('bold'),
                TextColumn::make('capacity')->label('Places')->placeholder('Illimité'),
                TextColumn::make('enrollments_count')->label('Inscrits'),
                TextColumn::make('applications_count')->label('Candidatures'),
                TextColumn::make('fee_amount')->label('Frais')->formatStateUsing(fn ($state) => Money::fcfa($state)),
                TextColumn::make('funding_mode')->label('Financement')->badge(),
                IconColumn::make('is_open')->label('Ouverte')->boolean(),
            ])
            ->headerActions([CreateAction::make()->label('Ajouter une offre')])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->visible(fn (Offering $record) => $record->enrollments_count === 0 && $record->applications_count === 0),
            ]);
    }
}
