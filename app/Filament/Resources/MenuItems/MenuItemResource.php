<?php

namespace App\Filament\Resources\MenuItems;

use App\Filament\Resources\MenuItems\Pages\CreateMenuItem;
use App\Filament\Resources\MenuItems\Pages\EditMenuItem;
use App\Filament\Resources\MenuItems\Pages\ListMenuItems;
use App\Filament\Support\FrenchLabels;
use App\Filament\Support\LinkTargets;
use App\Filament\Support\TranslationTab;
use App\Models\MenuItem;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Français')->schema([
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
                        TextInput::make('description')->label('Description')->maxLength(90)->columnSpanFull()
                            ->helperText('Phrase courte affichée sous le lien dans le sous-menu (ex. « Concerts, résidences et rendez-vous à venir »).')
                            ->visible(fn (Get $get) => $get('location') === 'main' && filled($get('parent_id'))),
                        Textarea::make('description')->label('Phrase d\'accroche du panneau')->rows(2)->maxLength(140)->columnSpanFull()
                            ->helperText('Rubrique à sous-menus : affichée dans son grand panneau, avec la photo.')
                            ->visible(fn (Get $get) => $get('location') === 'main' && blank($get('parent_id')) && ! $get('is_button')),
                        FileUpload::make('image')->label('Photo du panneau')->image()->disk('public')->directory('menus')
                            ->imageEditor()->maxSize(8192)->columnSpanFull()
                            ->helperText('Rubrique à sous-menus : photo affichée à gauche de ses liens (format paysage).')
                            ->visible(fn (Get $get) => $get('location') === 'main' && blank($get('parent_id')) && ! $get('is_button')),
                        Toggle::make('is_button')->label('Afficher comme bouton')->helperText('Ex. « Candidater »')->inline(false),
                        Toggle::make('is_visible')->label('Visible')->default(true)->inline(false),
                    ]),
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
