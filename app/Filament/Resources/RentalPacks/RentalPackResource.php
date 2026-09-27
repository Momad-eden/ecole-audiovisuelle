<?php

namespace App\Filament\Resources\RentalPacks;

use App\Enums\PriceUnit;
use App\Filament\Resources\RentalPacks\Pages\CreateRentalPack;
use App\Filament\Resources\RentalPacks\Pages\EditRentalPack;
use App\Filament\Resources\RentalPacks\Pages\ListRentalPacks;
use App\Filament\Support\Fields;
use App\Filament\Support\FrenchLabels;
use App\Models\RentalPack;
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

class RentalPackResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = RentalPack::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|\UnitEnum|null $navigationGroup = 'Impact Live';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'pack';

    protected static ?string $pluralModelLabel = 'packs';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Pack')->columns(2)->schema([
                TextInput::make('name')->label('Nom')->required()->maxLength(120)->placeholder('Ex. Pack concert'),
                TextInput::make('capacity')->label('Pour qui')->maxLength(80)->placeholder('Ex. Jusqu\'à 500 personnes'),
                Textarea::make('summary')->label('Présentation courte')->rows(2)->maxLength(300)->columnSpanFull(),
                TagsInput::make('contents')->label('Ce que comprend le pack')->placeholder('Ex. 4 têtes line array')->columnSpanFull(),
                TextInput::make('price_from')->label('Prix « à partir de » (FCFA)')->numeric()->minValue(0)->step(1000)
                    ->helperText('Laisser vide pour afficher « Sur devis ».'),
                Select::make('price_unit')->label('Unité du prix')->options(PriceUnit::class)->default('event'),
                ...Fields::image('image', 'packs', 'Photo', '4:3'),
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
                TextColumn::make('name')->label('Pack')->weight('bold')->searchable()->description(fn (RentalPack $r) => $r->capacity),
                TextColumn::make('price_from')->label('Prix')->formatStateUsing(fn (RentalPack $r) => $r->priceLabel()),
                TextColumn::make('status')->label('État')->badge(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRentalPacks::route('/'),
            'create' => CreateRentalPack::route('/create'),
            'edit' => EditRentalPack::route('/{record}/edit'),
        ];
    }
}
