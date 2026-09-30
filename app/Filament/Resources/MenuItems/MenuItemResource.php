<?php

namespace App\Filament\Resources\MenuItems;

use App\Filament\Resources\MenuItems\Pages\CreateMenuItem;
use App\Filament\Resources\MenuItems\Pages\EditMenuItem;
use App\Filament\Resources\MenuItems\Pages\ListMenuItems;
use App\Filament\Support\FrenchLabels;
use App\Filament\Support\LinkTargets;
use App\Models\MenuItem;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MenuItemResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = MenuItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static string|\UnitEnum|null $navigationGroup = 'Site';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'lien de menu';

    protected static ?string $pluralModelLabel = 'menus du site';

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()->columns(2)->schema([
                Select::make('location')->label('Emplacement')->options(MenuItem::LOCATIONS)->default('main')->required()->live(),
                Select::make('parent_id')->label('Sous-menu de')->placeholder('Aucun (lien de premier niveau)')
                    ->options(fn (?MenuItem $record) => MenuItem::where(fn ($q) => $q
                        ->where(fn ($q) => $q->where('location', 'main')->whereNull('parent_id')->where('is_button', false))
                        ->when($record?->parent_id, fn ($q, $parentId) => $q->orWhereKey($parentId)))
                        ->when($record, fn ($q) => $q->whereKeyNot($record->id))->orderBy('position')->orderBy('id')->pluck('label', 'id'))
                    ->helperText(fn (?MenuItem $record) => $record?->children()->exists()
                        ? 'Cet élément a déjà des sous-menus.'
                        : 'Choisissez un lien du menu principal pour l\'afficher dans son sous-menu.')
                    ->disabled(fn (?MenuItem $record) => (bool) $record?->children()->exists())
                    ->visible(fn (Get $get) => $get('location') === 'main')->live(),
                TextInput::make('label')->label('Texte du lien')->required()->maxLength(40),
                LinkTargets::field('url', 'Destination')->required(),
                Toggle::make('is_button')->label('Afficher comme bouton')->helperText('Ex. « Candidater »')->inline(false),
                Toggle::make('is_visible')->label('Visible')->default(true)->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('label')->label('Lien')->weight('bold'),
                TextColumn::make('url')->label('Adresse'),
                TextColumn::make('location')->label('Emplacement')->formatStateUsing(fn (string $state) => MenuItem::LOCATIONS[$state] ?? $state)->badge(),
                IconColumn::make('is_visible')->label('Visible')->boolean(),
            ])
            ->filters([SelectFilter::make('location')->label('Emplacement')->options(MenuItem::LOCATIONS)])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenuItems::route('/'),
            'create' => CreateMenuItem::route('/create'),
            'edit' => EditMenuItem::route('/{record}/edit'),
        ];
    }
}
