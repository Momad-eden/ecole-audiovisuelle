<?php

namespace App\Filament\Resources\News;

use App\Filament\Resources\News\Pages\CreateNews;
use App\Filament\Resources\News\Pages\EditNews;
use App\Filament\Resources\News\Pages\ListNews;
use App\Filament\Support\FrenchLabels;
use App\Filament\Support\RichText\TypographyPlugin;
use App\Filament\Support\TranslationTab;
use App\Models\News;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewsResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = News::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|\UnitEnum|null $navigationGroup = 'Site';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'actualité';

    protected static ?string $pluralModelLabel = 'actualités';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Français')->schema([
                    Section::make()->columns(2)->schema([
                        TextInput::make('title')->label('Titre')->required()->maxLength(180)->columnSpanFull(),
                        Textarea::make('excerpt')->label('Résumé (cartes et référencement)')->rows(2)->maxLength(300)->columnSpanFull(),
                        TypographyPlugin::editor('content', 'Article', images: true)->required()
                            ->fileAttachmentsDisk('public')->fileAttachmentsDirectory('news/attachments')
                            ->columnSpanFull(),
                        FileUpload::make('image')->label('Image de couverture')->image()->disk('public')->directory('news')->imageEditor()->maxSize(8192),
                        Section::make('Publication')->schema([
                            Toggle::make('is_published')->label('Publiée sur le site')->default(false),
                            DateTimePicker::make('published_at')->label('Date de publication')->seconds(false)->default(now())
                                ->helperText('Une date future programme la publication.'),
                        ]),
                    ]),
                ]),
                TranslationTab::make(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('image')->label('')->disk('public')->square(),
                TextColumn::make('title')->label('Titre')->searchable()->wrap()->weight('bold'),
                IconColumn::make('is_published')->label('Publiée')->boolean(),
                TextColumn::make('published_at')->label('Date')->dateTime('d/m/Y')->sortable(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNews::route('/'),
            'create' => CreateNews::route('/create'),
            'edit' => EditNews::route('/{record}/edit'),
        ];
    }
}
