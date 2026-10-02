<?php

namespace App\Filament\Resources\Rooms;

use App\Filament\Resources\Rooms\Pages\CreateRoom;
use App\Filament\Resources\Rooms\Pages\EditRoom;
use App\Filament\Resources\Rooms\Pages\ListRooms;
use App\Filament\Support\Fields;
use App\Filament\Support\FrenchLabels;
use App\Filament\Support\TranslationTab;
use App\Models\Room;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Room::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|\UnitEnum|null $navigationGroup = 'Univers & réalisations';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'univers';

    protected static ?string $pluralModelLabel = 'univers';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Français')->schema([
                    Section::make('Univers')->description('Une discipline de l\'école (Son, Image, Scène…) : sa page présente ses filières, ses métiers, ses formations et les réalisations des étudiants.')->columns(2)->schema([
                        TextInput::make('name')->label('Nom')->required()->placeholder('Ex. Son'),
                        ColorPicker::make('accent_color')->label('Couleur de lumière')->required()->default('#FF7A1A')
                            ->helperText('Couleur des halos et des accents de l\'univers sur le site.'),
                        Select::make('visual')->label('Animation de l\'univers')->options(Room::VISUALS)->required()->default('sound')
                            ->helperText('Le motif animé qui représente l\'univers sur le site.'),
                        Toggle::make('is_upcoming')->label('Bientôt à l\'EMSI')->inline(false)
                            ->helperText('Annonce un univers en préparation (ex. Cinéma) : il est présenté sans formation ni candidature.'),
                        TextInput::make('tagline')->label('Accroche')->maxLength(140)->columnSpanFull(),
                        Textarea::make('intro')->label('Texte d\'introduction')->rows(4)->maxLength(1200)->columnSpanFull(),
                        ...Fields::image('cover_image', 'rooms', 'Image d\'ambiance'),
                    ]),
                    Fields::publication(),
                ]),
                TranslationTab::make(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->modifyQueryUsing(fn ($query) => $query->withCount('artworks'))
            ->columns([
                ColorColumn::make('accent_color')->label(''),
                TextColumn::make('name')->label('Univers')->weight('bold')->searchable(),
                TextColumn::make('artworks_count')->label('Réalisations'),
                IconColumn::make('is_upcoming')->label('Bientôt')->boolean(),
                TextColumn::make('status')->label('État')->badge(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRooms::route('/'),
            'create' => CreateRoom::route('/create'),
            'edit' => EditRoom::route('/{record}/edit'),
        ];
    }
}
