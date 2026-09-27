<?php

namespace App\Filament\Resources\Productions;

use App\Enums\ArtworkKind;
use App\Filament\Resources\Productions\Pages\CreateProduction;
use App\Filament\Resources\Productions\Pages\EditProduction;
use App\Filament\Resources\Productions\Pages\ListProductions;
use App\Filament\Support\Fields;
use App\Filament\Support\FrenchLabels;
use App\Filament\Support\RichText\TypographyPlugin;
use App\Models\Artwork;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Productions d'Impact Live Studio : les titres enregistrés au studio, à écouter sur la page Studio.
 * Même fiche que les réalisations (lecteur, forme d'onde, crédits), réduite à l'essentiel.
 */
class ProductionResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Artwork::class;

    protected static ?string $slug = 'productions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMusicalNote;

    protected static string|\UnitEnum|null $navigationGroup = 'Impact Live';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'production du studio';

    protected static ?string $pluralModelLabel = 'productions du studio';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('origin', 'studio');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Le titre à écouter')->columns(2)->schema([
                TextInput::make('title')->label('Titre du morceau')->required()->maxLength(150),
                TextInput::make('year')->label('Année')->integer()->minValue(2000)->maxValue(now()->year + 1),
                FileUpload::make('audio_file')->label('Fichier son')->required()
                    ->disk('public')->directory('artworks/audio')->visibility('public')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp4', 'audio/x-m4a', 'audio/wav', 'audio/x-wav', 'audio/ogg'])
                    ->maxSize(51200)->columnSpanFull()
                    ->helperText('MP3, M4A, WAV ou OGG, 50 Mo maximum. Il s\'écoute sur la page du studio, avec sa forme d\'onde.'),
                Textarea::make('summary')->label('Présentation courte')->rows(2)->maxLength(300)->columnSpanFull()
                    ->placeholder('Ex. Single de l\'artiste, enregistré et mixé au studio en 2026.'),
                ...Fields::image('cover_image', 'artworks', 'Pochette', '1:1'),
            ]),
            Section::make('Crédits')->description('Vérifiez que l\'artiste a donné son accord pour la diffusion.')->schema([
                Repeater::make('credits')->hiddenLabel()->relationship('credits')->orderColumn('position')
                    ->addActionLabel('Ajouter une personne')->columns(2)->defaultItems(1)
                    ->schema([
                        TextInput::make('person_name')->label('Nom')->required(),
                        TextInput::make('role')->label('Rôle')->required()->placeholder('Ex. Artiste, mixage, mastering'),
                    ]),
            ]),
            Section::make('Pour aller plus loin')->collapsed()->schema([
                TypographyPlugin::editor('creation_story', 'Histoire de l\'enregistrement'),
                TagsInput::make('equipment')->label('Matériel utilisé')->placeholder('Ex. Neumann U87'),
                Textarea::make('transcript')->label('Paroles ou description (accessibilité)')->rows(3),
                Toggle::make('is_featured')->label('Mettre en avant'),
            ]),
            Fields::publication(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('cover_image')->label('')->disk('public')->square(),
                TextColumn::make('title')->label('Titre')->weight('bold')->searchable()->description(fn (Artwork $r) => $r->summary),
                IconColumn::make('audio_peaks')->label('Forme d\'onde')->boolean(fn (Artwork $r) => filled($r->audio_peaks))
                    ->tooltip(fn (Artwork $r) => filled($r->audio_peaks) ? 'Calculée' : 'En cours de calcul, ou ffmpeg absent du serveur'),
                TextColumn::make('status')->label('État')->badge(),
            ])
            ->recordActions([EditAction::make()]);
    }

    /** Une production est toujours un son, réalisé par le studio. */
    public static function prepareData(array $data): array
    {
        return ['origin' => 'studio', 'kind' => ArtworkKind::AUDIO->value, ...$data];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductions::route('/'),
            'create' => CreateProduction::route('/create'),
            'edit' => EditProduction::route('/{record}/edit'),
        ];
    }
}
