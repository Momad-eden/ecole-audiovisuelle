<?php

namespace App\Filament\Resources\Pages;

use App\Enums\SiteDomain;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Support\Fields;
use App\Filament\Support\FrenchLabels;
use App\Filament\Support\PageBlocks;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static string|\UnitEnum|null $navigationGroup = 'Site';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'page';

    protected static ?string $pluralModelLabel = 'pages du site';

    protected static ?string $recordTitleAttribute = 'title';

    public const TYPES = [
        'home' => 'Accueil',
        'system' => 'Page du site (école, contact…)',
        'professional' => 'Espace Professionnels',
        'free' => 'Page libre',
    ];

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()->columns(4)->schema([
                TextInput::make('title')->label('Titre de la page')->required()->maxLength(120),
                TextInput::make('slug')->label('Adresse')->prefix('/')
                    ->helperText('Générée automatiquement si vide.')
                    ->unique(ignoreRecord: true)->regex('/^[a-z0-9]+(?:[-\/][a-z0-9]+)*$/')
                    ->disabled(fn (?Page $record) => $record?->is_locked),
                Select::make('domain')->label('Domaine')->options(SiteDomain::class)->default(SiteDomain::GENERAL->value)->required()
                    ->helperText('Donne sa couleur et son menu à la page'),
                Select::make('type')->label('Type')->options(self::TYPES)->default('free')->required()
                    ->disabled(fn (?Page $record) => $record?->is_locked),
            ]),
            Builder::make('draft_blocks')->label('Contenu de la page (brouillon)')
                ->blocks(PageBlocks::all())
                ->blockPickerColumns(3)
                ->collapsible()->cloneable()->reorderableWithButtons()
                ->addActionLabel('Ajouter un bloc')
                ->helperText('Vos modifications restent en brouillon tant que vous n\'avez pas cliqué sur « Publier ».')
                ->columnSpanFull(),
            Fields::seo(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('title')
            ->columns([
                TextColumn::make('title')->label('Page')->searchable()->weight('bold')
                    ->description(fn (Page $r) => '/'.($r->type === 'home' ? '' : $r->slug)),
                TextColumn::make('type')->label('Type')->formatStateUsing(fn (string $state) => self::TYPES[$state] ?? $state),
                TextColumn::make('status')->label('État')->badge(),
                TextColumn::make('pending')->label('')
                    ->state(fn (Page $r) => $r->hasUnpublishedChanges() ? 'Modifications à publier' : null)
                    ->badge()->color('warning'),
                TextColumn::make('updated_at')->label('Modifiée le')->since(),
            ])
            ->recordActions([EditAction::make()->label('Modifier')]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
