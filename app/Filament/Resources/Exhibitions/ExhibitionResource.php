<?php

namespace App\Filament\Resources\Exhibitions;

use App\Filament\Resources\Exhibitions\Pages\CreateExhibition;
use App\Filament\Resources\Exhibitions\Pages\EditExhibition;
use App\Filament\Resources\Exhibitions\Pages\ListExhibitions;
use App\Filament\Support\Fields;
use App\Filament\Support\FrenchLabels;
use App\Models\Exhibition;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExhibitionResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Exhibition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|\UnitEnum|null $navigationGroup = 'Univers & réalisations';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'exposition';

    protected static ?string $pluralModelLabel = 'expositions';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Exposition')->columns(2)->schema([
                TextInput::make('title')->label('Titre')->required()->maxLength(150)->columnSpanFull(),
                TextInput::make('subtitle')->label('Sous-titre')->maxLength(200)->columnSpanFull(),
                DatePicker::make('starts_on')->label('Du'),
                DatePicker::make('ends_on')->label('Au')->afterOrEqual('starts_on'),
                TextInput::make('venue')->label('Lieu')->placeholder('Ex. Grand Théâtre National, Dakar')->columnSpanFull(),
                RichEditor::make('curatorial_text')->label('Texte de présentation')
                    ->toolbarButtons([['bold', 'italic', 'link'], ['h2', 'h3'], ['bulletList', 'blockquote'], ['undo', 'redo']])
                    ->columnSpanFull(),
                Select::make('artworks')->label('Réalisations exposées')->relationship('artworks', 'title')->multiple()->searchable()->preload()->columnSpanFull(),
                ...Fields::image('cover_image', 'exhibitions', 'Affiche / visuel'),
            ]),
            Fields::publication(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('starts_on', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->withCount('artworks'))
            ->columns([
                TextColumn::make('title')->label('Exposition')->weight('bold')->searchable(),
                TextColumn::make('starts_on')->label('Du')->date('d/m/Y'),
                TextColumn::make('ends_on')->label('Au')->date('d/m/Y'),
                TextColumn::make('artworks_count')->label('Réalisations'),
                TextColumn::make('status')->label('État')->badge(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExhibitions::route('/'),
            'create' => CreateExhibition::route('/create'),
            'edit' => EditExhibition::route('/{record}/edit'),
        ];
    }
}
