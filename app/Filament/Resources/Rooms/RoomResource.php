<?php

namespace App\Filament\Resources\Rooms;

use App\Filament\Resources\Rooms\Pages\CreateRoom;
use App\Filament\Resources\Rooms\Pages\EditRoom;
use App\Filament\Resources\Rooms\Pages\ListRooms;
use App\Filament\Support\Fields;
use App\Filament\Support\FrenchLabels;
use App\Models\Room;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Room::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static string|\UnitEnum|null $navigationGroup = 'Musée';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'salle';

    protected static ?string $pluralModelLabel = 'salles';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Salle')->columns(2)->schema([
                TextInput::make('name')->label('Nom')->required()->placeholder('Ex. Salle du Son'),
                ColorPicker::make('accent_color')->label('Couleur de lumière')->required()->default('#F5B83D')
                    ->helperText('Couleur des halos et des accents de la salle sur le site.'),
                TextInput::make('tagline')->label('Accroche')->maxLength(140)->columnSpanFull(),
                Textarea::make('intro')->label('Texte d\'introduction')->rows(4)->maxLength(1200)->columnSpanFull(),
                ...Fields::image('cover_image', 'rooms', 'Image d\'ambiance'),
            ]),
            Fields::publication(),
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
                TextColumn::make('name')->label('Salle')->weight('bold')->searchable(),
                TextColumn::make('artworks_count')->label('Œuvres'),
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
