<?php

namespace App\Filament\Resources\EquipmentItems;

use App\Enums\EquipmentUsage;
use App\Enums\PriceUnit;
use App\Filament\Resources\EquipmentItems\Pages\CreateEquipmentItem;
use App\Filament\Resources\EquipmentItems\Pages\EditEquipmentItem;
use App\Filament\Resources\EquipmentItems\Pages\ListEquipmentItems;
use App\Filament\Support\Fields;
use App\Filament\Support\FrenchLabels;
use App\Filament\Support\RichText\TypographyPlugin;
use App\Models\EquipmentItem;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EquipmentItemResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = EquipmentItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSpeakerWave;

    protected static string|\UnitEnum|null $navigationGroup = 'Impact Live';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'matériel';

    protected static ?string $pluralModelLabel = 'matériel';

    protected static ?string $recordTitleAttribute = 'name';

    /** Impact Live Events n'est plus proposé sur le site : les données restent, l'écran est masqué du menu. */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Matériel')->columns(2)->schema([
                TextInput::make('name')->label('Nom')->required()->maxLength(150)->placeholder('Ex. Système line array K2'),
                TextInput::make('brand')->label('Marque')->maxLength(80)->placeholder('Ex. L-Acoustics'),
                Select::make('equipment_category_id')->label('Catégorie')->relationship('category', 'name')->required()->preload()
                    ->createOptionForm([TextInput::make('name')->label('Nom de la catégorie')->required()]),
                Select::make('usage')->label('Usage')->options(EquipmentUsage::class)->default('rental')->required()
                    ->helperText('« À louer » : catalogue d\'Impact Live Events. « Studio » : présenté sur la page du studio.'),
                Textarea::make('summary')->label('Présentation courte')->rows(2)->maxLength(300)->columnSpanFull(),
                TypographyPlugin::editor('description', 'Description détaillée')->columnSpanFull(),
            ]),
            Section::make('Caractéristiques techniques')->schema([
                Repeater::make('specs')->hiddenLabel()->columns(2)->defaultItems(0)->maxItems(20)->addActionLabel('Ajouter une caractéristique')
                    ->schema([
                        TextInput::make('label')->label('Caractéristique')->required()->maxLength(60)->placeholder('Ex. Puissance'),
                        TextInput::make('value')->label('Valeur')->required()->maxLength(120)->placeholder('Ex. 2 × 1 000 W'),
                    ]),
            ]),
            Section::make('Photos')->columns(2)->schema([
                ...Fields::image('image', 'equipment', 'Photo principale', '4:3'),
                FileUpload::make('gallery')->label('Autres photos')->image()->multiple()->reorderable()->maxFiles(12)
                    ->disk('public')->directory('equipment/gallery')->maxSize(8192)->columnSpanFull(),
            ]),
            Section::make('Location')->columns(2)->schema([
                TextInput::make('quantity')->label('Quantité disponible')->numeric()->minValue(0),
                TextInput::make('price_from')->label('Prix « à partir de » (FCFA)')->numeric()->minValue(0)->step(1000)
                    ->helperText('Laisser vide pour afficher « Sur devis ».'),
                Select::make('price_unit')->label('Unité du prix')->options(PriceUnit::class)->default('day'),
                Toggle::make('is_featured')->label('Mettre en avant sur la page Events'),
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
                ImageColumn::make('image')->label('')->disk('public')->square(),
                TextColumn::make('name')->label('Matériel')->weight('bold')->searchable()->description(fn (EquipmentItem $r) => $r->brand),
                TextColumn::make('category.name')->label('Catégorie'),
                TextColumn::make('usage')->label('Usage')->badge(),
                TextColumn::make('quantity')->label('Qté'),
                TextColumn::make('price_from')->label('Prix')->formatStateUsing(fn (EquipmentItem $r) => $r->priceLabel()),
                TextColumn::make('status')->label('État')->badge(),
            ])
            ->filters([
                SelectFilter::make('equipment_category_id')->label('Catégorie')->relationship('category', 'name'),
                SelectFilter::make('usage')->label('Usage')->options(EquipmentUsage::class),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEquipmentItems::route('/'),
            'create' => CreateEquipmentItem::route('/create'),
            'edit' => EditEquipmentItem::route('/{record}/edit'),
        ];
    }
}
