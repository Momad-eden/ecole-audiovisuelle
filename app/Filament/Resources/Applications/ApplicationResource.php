<?php

namespace App\Filament\Resources\Applications;

use App\Enums\ApplicationStatus;
use App\Enums\Audience;
use App\Enums\DocumentType;
use App\Enums\Gender;
use App\Filament\Resources\Applications\Pages\CreateApplication;
use App\Filament\Resources\Applications\Pages\EditApplication;
use App\Filament\Resources\Applications\Pages\ListApplications;
use App\Filament\Resources\Applications\Pages\ViewApplication;
use App\Filament\Support\FrenchLabels;
use App\Models\Application;
use App\Models\Offering;
use App\Models\Program;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApplicationResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Application::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static string|\UnitEnum|null $navigationGroup = 'Scolarité';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'candidature';

    protected static ?string $pluralModelLabel = 'candidatures';

    protected static ?string $recordTitleAttribute = 'reference';

    public static function getNavigationBadge(): ?string
    {
        $count = Application::whereIn('status', ApplicationStatus::open())->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Candidatures à traiter';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'first_name', 'last_name', 'email', 'phone'];
    }

    public static function offeringOptions(): array
    {
        return Offering::with(['cohort.program', 'track'])->get()
            ->mapWithKeys(fn (Offering $offering) => [$offering->id => $offering->label])
            ->sort()
            ->all();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Formation demandée')->columns(2)->schema([
                Select::make('offering_id')
                    ->label('Offre (formation, session, filière)')
                    ->options(fn () => static::offeringOptions())
                    ->searchable()
                    ->required(),
                Select::make('place_id')->label('Campus')
                    ->relationship('place', 'name', fn (Builder $query) => $query->where('kind', 'campus'))
                    ->preload(),
            ]),
            Section::make('Identité')->columns(3)->schema([
                TextInput::make('first_name')->label('Prénom')->required()->maxLength(100),
                TextInput::make('last_name')->label('Nom')->required()->maxLength(100),
                Select::make('gender')->label('Genre')->options(Gender::class),
                DatePicker::make('birth_date')->label('Date de naissance')->maxDate(now()),
                TextInput::make('birth_place')->label('Lieu de naissance')->maxLength(150),
                TextInput::make('nationality')->label('Nationalité')->maxLength(100)->default('Sénégalaise'),
            ]),
            Section::make('Coordonnées')->columns(2)->schema([
                TextInput::make('phone')->label('Téléphone')->tel()->required()
                    ->regex('/^\+?[0-9][0-9 ().-]{7,19}$/')->placeholder('+221 77 000 00 00'),
                TextInput::make('whatsapp')->label('WhatsApp (si différent)')->tel(),
                TextInput::make('email')->label('Adresse e-mail')->email(),
                TextInput::make('address')->label('Adresse')->maxLength(255),
            ]),
            Section::make('Parcours')->columns(2)->schema([
                Select::make('education.last_diploma')->label('Dernier diplôme')->options(static::diplomaOptions()),
                TextInput::make('education.year')->label('Année d\'obtention')->numeric()->minValue(1960)->maxValue(now()->year),
                TextInput::make('education.school')->label('Établissement'),
                TextInput::make('education.field')->label('Filière ou série'),
                Textarea::make('motivation')->label('Motivation')->rows(4)->maxLength(3000)->columnSpanFull(),
                TextInput::make('portfolio_url')->label('Lien vers un portfolio')->url()->columnSpanFull(),
            ]),
            Section::make('Pièces justificatives')->schema([
                Repeater::make('documents')
                    ->hiddenLabel()
                    ->addActionLabel('Ajouter une pièce')
                    ->columns(2)
                    ->defaultItems(0)
                    ->schema([
                        Select::make('type')->label('Type de pièce')->options(DocumentType::class)->required(),
                        FileUpload::make('path')->label('Fichier')
                            ->disk('local')->directory('applications')->visibility('private')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(10240)->required()
                            ->storeFileNamesIn('name'),
                    ]),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Grid::make(3)->schema([
                Section::make('Candidature')->columnSpan(2)->columns(2)->schema([
                    TextEntry::make('reference')->label('Numéro'),
                    TextEntry::make('status')->label('Étape')->badge(),
                    TextEntry::make('offering.label')->label('Formation demandée')->columnSpanFull(),
                    TextEntry::make('place.name')->label('Campus')->placeholder('—'),
                    TextEntry::make('audience')->label('Public')->badge(),
                    TextEntry::make('submitted_at')->label('Reçue le')->dateTime('d/m/Y à H:i'),
                    TextEntry::make('interview_at')->label('Entretien')->dateTime('d/m/Y à H:i')->placeholder('Non planifié'),
                    TextEntry::make('interview_location')->label('Lieu de l\'entretien')->placeholder('—'),
                    TextEntry::make('decider.name')->label('Décision prise par')->placeholder('—'),
                    TextEntry::make('student.student_number')->label('Matricule')->placeholder('Pas encore inscrit'),
                ]),
                Section::make('Contact')->columnSpan(1)->schema([
                    TextEntry::make('phone')->label('Téléphone')->url(fn (Application $r) => 'tel:'.preg_replace('/[^0-9+]/', '', $r->phone)),
                    TextEntry::make('whatsapp')->label('WhatsApp')->placeholder('—'),
                    TextEntry::make('email')->label('E-mail')->placeholder('—')->copyable(),
                    TextEntry::make('address')->label('Adresse')->placeholder('—'),
                ]),
            ]),
            Section::make('Identité et parcours')->columns(3)->schema([
                TextEntry::make('full_name')->label('Nom complet'),
                TextEntry::make('gender')->label('Genre')->placeholder('—'),
                TextEntry::make('birth_date')->label('Né(e) le')->date('d/m/Y')->placeholder('—'),
                TextEntry::make('birth_place')->label('Lieu de naissance')->placeholder('—'),
                TextEntry::make('nationality')->label('Nationalité')->placeholder('—'),
                TextEntry::make('education.last_diploma')->label('Dernier diplôme')->placeholder('—'),
                TextEntry::make('education.school')->label('Établissement')->placeholder('—'),
                TextEntry::make('education.year')->label('Année')->placeholder('—'),
                TextEntry::make('portfolio_url')->label('Portfolio')->url(fn (Application $r) => $r->portfolio_url, true)->placeholder('—'),
                TextEntry::make('motivation')->label('Motivation')->columnSpanFull()->placeholder('—'),
            ]),
            Section::make('Pièces justificatives')->schema([
                RepeatableEntry::make('documents')->hiddenLabel()->columns(3)->placeholder('Aucune pièce jointe.')->schema([
                    TextEntry::make('type')->label('Type')->formatStateUsing(fn (?string $state) => DocumentType::tryFrom((string) $state)?->getLabel() ?? '—'),
                    TextEntry::make('name')->label('Nom du fichier')->placeholder('—'),
                    TextEntry::make('path')->label('Fichier joint')->formatStateUsing(fn () => 'Télécharger')
                        ->url(fn (?string $state, Application $record) => $state ? route('admin.applications.document', ['application' => $record, 'path' => $state]) : null, true)
                        ->color('primary'),
                ]),
            ]),
            Section::make('Historique')->collapsible()->schema([
                RepeatableEntry::make('events')->hiddenLabel()->columns(4)->placeholder('Aucun événement.')->schema([
                    TextEntry::make('created_at')->label('Date')->dateTime('d/m/Y H:i'),
                    TextEntry::make('to_status')->label('Étape')->formatStateUsing(fn (?string $state) => ApplicationStatus::tryFrom((string) $state)?->getLabel() ?? 'Note')->placeholder('Note'),
                    TextEntry::make('comment')->label('Commentaire')->placeholder('—'),
                    TextEntry::make('user.name')->label('Par')->placeholder('Candidat (en ligne)'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['offering.cohort.program', 'offering.track']))
            ->defaultSort('submitted_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label('N°')->searchable()->sortable(),
                TextColumn::make('last_name')->label('Candidat')
                    ->formatStateUsing(fn (Application $record) => $record->full_name)
                    ->description(fn (Application $record) => $record->phone)
                    ->searchable(['first_name', 'last_name', 'phone', 'email']),
                TextColumn::make('offering.label')->label('Formation')->wrap(),
                TextColumn::make('place.city')->label('Campus')->placeholder('—')->toggleable(),
                TextColumn::make('audience')->label('Public')->badge()->toggleable(),
                TextColumn::make('status')->label('Étape')->badge()->sortable(),
                TextColumn::make('submitted_at')->label('Reçue le')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('place_id')->label('Campus')->relationship('place', 'name', fn (Builder $query) => $query->where('kind', 'campus')),
                SelectFilter::make('status')->label('Étape')->options(ApplicationStatus::class)->multiple(),
                SelectFilter::make('audience')->label('Public')->options(Audience::class),
                SelectFilter::make('program')->label('Formation')
                    ->options(fn () => Program::orderBy('title')->pluck('title', 'id')->all())
                    ->query(fn (Builder $query, array $data) => $query->when($data['value'] ?? null,
                        fn (Builder $q, $id) => $q->whereHas('offering.cohort', fn (Builder $c) => $c->where('program_id', $id)))),
                TrashedFilter::make(),
            ])
            ->recordActions([ViewAction::make()->label('Ouvrir')])
            ->recordUrl(fn (Application $record) => static::getUrl('view', ['record' => $record]));
    }

    public static function diplomaOptions(): array
    {
        return [
            'CPS' => 'CPS — Certificat de Professionnalisation Spécialisée',
            'CS' => 'CS — Certificat de Spécialité',
            'CAP' => 'CAP / BEP',
            'BFEM' => 'BFEM',
            'Baccalauréat' => 'Baccalauréat',
            'BTS' => 'BTS / DUT (Bac+2)',
            'Licence' => 'Licence (Bac+3)',
            'Master' => 'Master (Bac+5)',
            'Autre' => 'Autre',
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['offering.cohort.program', 'offering.track', 'place', 'student', 'decider', 'events.user']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApplications::route('/'),
            'create' => CreateApplication::route('/create'),
            'view' => ViewApplication::route('/{record}'),
            'edit' => EditApplication::route('/{record}/edit'),
        ];
    }
}
