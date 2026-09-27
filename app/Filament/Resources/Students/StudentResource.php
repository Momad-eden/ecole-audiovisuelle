<?php

namespace App\Filament\Resources\Students;

use App\Enums\Gender;
use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Pages\ViewStudent;
use App\Filament\Resources\Students\RelationManagers\EnrollmentsRelationManager;
use App\Filament\Support\FrenchLabels;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Scolarité';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'étudiant';

    protected static ?string $pluralModelLabel = 'étudiants';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['student_number', 'first_name', 'last_name', 'email', 'phone'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Identité')->columns(3)->schema([
                TextInput::make('first_name')->label('Prénom')->required()->maxLength(100),
                TextInput::make('last_name')->label('Nom')->required()->maxLength(100),
                Select::make('gender')->label('Genre')->options(Gender::class),
                DatePicker::make('birth_date')->label('Date de naissance')->maxDate(now()),
                TextInput::make('birth_place')->label('Lieu de naissance'),
                TextInput::make('nationality')->label('Nationalité')->default('Sénégalaise'),
            ]),
            Section::make('Coordonnées')->columns(2)->schema([
                TextInput::make('phone')->label('Téléphone')->tel()->regex('/^\+?[0-9][0-9 ().-]{7,19}$/'),
                TextInput::make('email')->label('E-mail')->email(),
                TextInput::make('address')->label('Adresse')->columnSpanFull(),
                Textarea::make('notes')->label('Observations')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()->columns(4)->schema([
                TextEntry::make('student_number')->label('Matricule')->copyable(),
                TextEntry::make('full_name')->label('Nom complet'),
                TextEntry::make('gender')->label('Genre')->placeholder('—'),
                TextEntry::make('birth_date')->label('Né(e) le')->date('d/m/Y')->placeholder('—'),
                TextEntry::make('phone')->label('Téléphone')->placeholder('—'),
                TextEntry::make('email')->label('E-mail')->placeholder('—'),
                TextEntry::make('address')->label('Adresse')->placeholder('—'),
                TextEntry::make('nationality')->label('Nationalité')->placeholder('—'),
                TextEntry::make('notes')->label('Observations')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('enrollments'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('student_number')->label('Matricule')->searchable()->sortable(),
                TextColumn::make('last_name')->label('Nom')
                    ->formatStateUsing(fn (Student $record) => $record->full_name)
                    ->searchable(['first_name', 'last_name'])->sortable(),
                TextColumn::make('phone')->label('Téléphone')->searchable(),
                TextColumn::make('email')->label('E-mail')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('enrollments_count')->label('Inscriptions')->alignCenter(),
            ])
            ->filters([TrashedFilter::make()])
            ->recordActions([ViewAction::make()->label('Ouvrir')]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getRelations(): array
    {
        return [EnrollmentsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'view' => ViewStudent::route('/{record}'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}
