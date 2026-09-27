<?php

namespace App\Filament\Resources\Places;

use App\Enums\PlaceKind;
use App\Filament\Resources\Places\Pages\CreatePlace;
use App\Filament\Resources\Places\Pages\EditPlace;
use App\Filament\Resources\Places\Pages\ListPlaces;
use App\Filament\Support\Fields;
use App\Filament\Support\FrenchLabels;
use App\Models\Place;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlaceResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Place::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'lieu';

    protected static ?string $pluralModelLabel = 'lieux';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Lieu')->description('Campus de l\'EMSI, studio ou centre culturel : ses coordonnées s\'affichent sur le site.')->columns(2)->schema([
                TextInput::make('name')->label('Nom')->required()->maxLength(120),
                Select::make('kind')->label('Type')->options(PlaceKind::class)->required()->live(),
                TextInput::make('code')->label('Code de caisse')->maxLength(5)->alphaNum()->unique(ignoreRecord: true)
                    ->placeholder('Ex. DKR')->helperText('Campus : préfixe des reçus (REC-DKR-2026-00001). Ne plus le changer après la première opération.')
                    ->dehydrateStateUsing(fn (?string $state) => $state ? strtoupper($state) : null)
                    ->visible(fn ($get) => ($get('kind') instanceof PlaceKind ? $get('kind')->value : $get('kind')) === 'campus'),
                TextInput::make('city')->label('Ville')->maxLength(80),
                TextInput::make('address')->label('Adresse')->maxLength(255),
                TextInput::make('phone')->label('Téléphone')->tel()->maxLength(30),
                TextInput::make('whatsapp')->label('WhatsApp')->tel()->maxLength(30),
                TextInput::make('email')->label('E-mail')->email(),
                TextInput::make('opening_hours')->label('Horaires')->maxLength(255),
                TextInput::make('map_url')->label('Lien Google Maps')->url()->maxLength(500)->columnSpanFull(),
                TextInput::make('tagline')->label('Accroche')->maxLength(200)->columnSpanFull()
                    ->placeholder('Ex. Au cœur du Grand Théâtre National'),
                Textarea::make('description')->label('Présentation')->rows(3)->maxLength(1200)->columnSpanFull(),
                TagsInput::make('highlights')->label('Points forts')->placeholder('Ex. Studio d\'enregistrement sur place')->columnSpanFull()
                    ->helperText('Quelques atouts courts, affichés en liste sur la page L\'École.'),
                ...Fields::image('image', 'places', 'Photo du lieu', '4:3'),
            ]),
            Fields::publication(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('name')->label('Lieu')->weight('bold')->searchable(),
                TextColumn::make('kind')->label('Type')->badge(),
                TextColumn::make('city')->label('Ville'),
                TextColumn::make('status')->label('État')->badge(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlaces::route('/'),
            'create' => CreatePlace::route('/create'),
            'edit' => EditPlace::route('/{record}/edit'),
        ];
    }
}
