<?php

namespace App\Filament\Resources\Artworks;

use App\Enums\ArtworkKind;
use App\Enums\PublicationStatus;
use App\Filament\Resources\Artworks\Pages\CreateArtwork;
use App\Filament\Resources\Artworks\Pages\EditArtwork;
use App\Filament\Resources\Artworks\Pages\ListArtworks;
use App\Filament\Support\Fields;
use App\Filament\Support\FrenchLabels;
use App\Filament\Support\RichText\TypographyPlugin;
use App\Models\Artwork;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArtworkResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Artwork::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|\UnitEnum|null $navigationGroup = 'Univers & réalisations';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'réalisation';

    protected static ?string $pluralModelLabel = 'réalisations';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('1. Médias')->icon('heroicon-o-photo')->schema([
                    Select::make('kind')->label('Type de réalisation')->options(ArtworkKind::class)->required()->live(),
                    Select::make('origin')->label('Réalisée par')->options(['school' => 'Des étudiants de l\'EMSI', 'studio' => 'Impact Live Studio (production du studio)'])
                        ->default('school')->required()->helperText('Les productions du studio s\'écoutent sur la page du studio.'),
                    Section::make('Image principale')->columns(2)->schema(Fields::image('cover_image', 'artworks', 'Image principale (vignette, affiche, photo)')),
                    FileUpload::make('audio_file')->label('Fichier son')
                        ->disk('public')->directory('artworks/audio')->visibility('public')
                        ->acceptedFileTypes(['audio/mpeg', 'audio/mp4', 'audio/x-m4a', 'audio/wav', 'audio/x-wav', 'audio/ogg'])
                        ->maxSize(51200)
                        ->helperText('MP3, M4A, WAV ou OGG, 50 Mo maximum. La forme d\'onde est calculée automatiquement.'),
                    TextInput::make('video_url')->label('Lien de la vidéo (YouTube ou Vimeo)')->url()
                        ->regex('#^https?://(www\.)?(youtube\.com|youtu\.be|vimeo\.com)/#i')
                        ->helperText('Les vidéos longues sont hébergées sur YouTube ou Vimeo ; le site les charge seulement au clic.'),
                    TextInput::make('duration_seconds')->label('Durée (en secondes)')->integer()->minValue(1),
                    FileUpload::make('gallery')->label('Galerie (images secondaires, making-of)')
                        ->image()->multiple()->reorderable()->maxFiles(24)->disk('public')->directory('artworks/gallery')->maxSize(8192),
                ]),
                Tab::make('2. Description')->icon('heroicon-o-document-text')->schema([
                    TextInput::make('title')->label('Titre')->required()->maxLength(150),
                    TextInput::make('year')->label('Année')->integer()->minValue(2000)->maxValue(now()->year + 1),
                    Textarea::make('summary')->label('Présentation courte (cartel)')->rows(3)->maxLength(300),
                    TypographyPlugin::editor('creation_story', 'Récit de création'),
                    Textarea::make('transcript')->label('Transcription ou description du son (accessibilité)')->rows(4),
                    TagsInput::make('equipment')->label('Matériel utilisé')->placeholder('Ex. Console DiGiCo SD12'),
                ]),
                Tab::make('3. Classement et crédits')->icon('heroicon-o-users')->schema([
                    Section::make()->columns(3)->schema([
                        Select::make('room_id')->label('Univers')->relationship('room', 'name')->preload(),
                        Select::make('track_id')->label('Filière')->relationship('track', 'name')->preload(),
                        Select::make('cohort_id')->label('Promotion / session')->relationship('cohort', 'name')->preload(),
                        Select::make('exhibitions')->label('Expositions')->relationship('exhibitions', 'title')->multiple()->preload()->columnSpan(2),
                        Toggle::make('is_featured')->label('Mettre à la une')->inline(false),
                    ]),
                    Repeater::make('credits')->label('Crédits')->relationship('credits')->orderColumn('position')
                        ->addActionLabel('Ajouter une personne')->columns(3)->defaultItems(0)
                        ->schema([
                            TextInput::make('person_name')->label('Nom')->required(),
                            TextInput::make('role')->label('Rôle')->required()->placeholder('Ex. Mixage façade'),
                            Select::make('student_id')->label('Étudiant EMSI (facultatif)')->relationship('student', 'last_name')
                                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->full_name} ({$record->student_number})")
                                ->searchable(['first_name', 'last_name', 'student_number']),
                        ])
                        ->helperText('Vérifiez que chaque personne a donné son accord pour la diffusion de la réalisation.'),
                ]),
                Tab::make('4. Publication')->icon('heroicon-o-globe-alt')->schema([Fields::publication()]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['room', 'track']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('cover_image')->label('')->disk('public')->square(),
                TextColumn::make('title')->label('Réalisation')->searchable()->weight('bold')->description(fn (Artwork $r) => $r->kind?->getLabel()),
                TextColumn::make('room.name')->label('Univers')->placeholder('—'),
                TextColumn::make('track.name')->label('Filière')->placeholder('—')->toggleable(),
                IconColumn::make('is_featured')->label('À la une')->boolean(),
                TextColumn::make('status')->label('État')->badge(),
            ])
            ->filters([
                SelectFilter::make('room_id')->label('Univers')->relationship('room', 'name'),
                SelectFilter::make('kind')->label('Type')->options(ArtworkKind::class),
                SelectFilter::make('status')->label('État')->options(PublicationStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtworks::route('/'),
            'create' => CreateArtwork::route('/create'),
            'edit' => EditArtwork::route('/{record}/edit'),
        ];
    }
}
