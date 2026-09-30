<?php

namespace App\Filament\Resources\EquipmentCategories;

use App\Filament\Resources\EquipmentCategories\Pages\CreateEquipmentCategory;
use App\Filament\Resources\EquipmentCategories\Pages\EditEquipmentCategory;
use App\Filament\Resources\EquipmentCategories\Pages\ListEquipmentCategories;
use App\Filament\Support\FrenchLabels;
use App\Models\EquipmentCategory;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EquipmentCategoryResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = EquipmentCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|\UnitEnum|null $navigationGroup = 'Impact Live';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'catégorie de matériel';

    protected static ?string $pluralModelLabel = 'catégories de matériel';

    protected static ?string $recordTitleAttribute = 'name';

    /** Impact Live Events n'est plus proposé sur le site : les données restent, l'écran est masqué du menu. */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Catégorie')->schema([
                TextInput::make('name')->label('Nom')->required()->maxLength(80)->placeholder('Ex. Sonorisation'),
                Textarea::make('summary')->label('Présentation courte')->rows(2)->maxLength(300),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->modifyQueryUsing(fn ($query) => $query->withCount('items'))
            ->columns([
                TextColumn::make('name')->label('Catégorie')->weight('bold')->searchable(),
                TextColumn::make('items_count')->label('Matériel'),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEquipmentCategories::route('/'),
            'create' => CreateEquipmentCategory::route('/create'),
            'edit' => EditEquipmentCategory::route('/{record}/edit'),
        ];
    }
}
