<?php

namespace App\Filament\Resources\Cohorts;

use App\Enums\CohortStatus;
use App\Filament\Resources\Cohorts\Pages\CreateCohort;
use App\Filament\Resources\Cohorts\Pages\EditCohort;
use App\Filament\Resources\Cohorts\Pages\ListCohorts;
use App\Filament\Resources\Cohorts\RelationManagers\OfferingsRelationManager;
use App\Filament\Support\FrenchLabels;
use App\Models\Cohort;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CohortResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Cohort::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|\UnitEnum|null $navigationGroup = 'Formations';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'session';

    protected static ?string $pluralModelLabel = 'sessions';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Session')->columns(2)->schema([
                Select::make('program_id')->label('Formation')->relationship('program', 'title')->searchable()->preload()->required()
                    ->visible(fn ($livewire) => ! $livewire instanceof RelationManager),
                TextInput::make('name')->label('Nom de la session')->required()->placeholder('Ex. Volet 2 — 2027'),
                DatePicker::make('starts_on')->label('Début des cours'),
                DatePicker::make('ends_on')->label('Fin des cours')->afterOrEqual('starts_on'),
            ]),
            Section::make('Candidatures en ligne')->columns(2)->schema([
                Select::make('status')->label('État')->options(CohortStatus::class)->default(CohortStatus::PLANNED)->required()
                    ->helperText('« Candidatures ouvertes » affiche le formulaire sur le site (dans les dates ci-dessous).')
                    ->columnSpanFull(),
                DateTimePicker::make('applications_open_at')->label('Ouverture le')->seconds(false),
                DateTimePicker::make('applications_close_at')->label('Fermeture le')->seconds(false)->afterOrEqual('applications_open_at'),
                Textarea::make('notes')->label('Notes internes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('program')->withCount(['offerings', 'offerings as open_offerings_count' => fn (Builder $q) => $q->where('is_open', true)]))
            ->defaultSort('starts_on', 'desc')
            ->columns([
                TextColumn::make('program.title')->label('Formation')->searchable()->wrap(),
                TextColumn::make('name')->label('Session')->searchable()->weight('bold'),
                TextColumn::make('starts_on')->label('Début')->date('d/m/Y')->sortable(),
                TextColumn::make('status')->label('Candidatures')->badge(),
                TextColumn::make('applications_close_at')->label('Clôture des candidatures')->dateTime('d/m/Y')->placeholder('—'),
                TextColumn::make('offerings_count')->label('Offres'),
            ])
            ->filters([SelectFilter::make('status')->label('État')->options(CohortStatus::class)])
            ->recordActions([
                Action::make('toggle')
                    ->label(fn (Cohort $record) => $record->status === CohortStatus::OPEN ? 'Fermer les candidatures' : 'Ouvrir les candidatures')
                    ->icon(fn (Cohort $record) => $record->status === CohortStatus::OPEN ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                    ->color(fn (Cohort $record) => $record->status === CohortStatus::OPEN ? 'warning' : 'success')
                    ->visible(fn (Cohort $record) => in_array($record->status, [CohortStatus::PLANNED, CohortStatus::OPEN, CohortStatus::CLOSED], true)
                        && auth()->user()->can('update', $record))
                    ->requiresConfirmation()
                    ->action(function (Cohort $record) {
                        $open = $record->status !== CohortStatus::OPEN;
                        $record->update(['status' => $open ? CohortStatus::OPEN : CohortStatus::CLOSED]);
                        Notification::make()->title($open ? 'Candidatures ouvertes : le formulaire est visible sur le site.' : 'Candidatures fermées.')->success()->send();
                    }),
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [OfferingsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCohorts::route('/'),
            'create' => CreateCohort::route('/create'),
            'edit' => EditCohort::route('/{record}/edit'),
        ];
    }
}
