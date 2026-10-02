<?php

namespace App\Filament\Resources\Tracks;

use App\Filament\Resources\Tracks\Pages\CreateTrack;
use App\Filament\Resources\Tracks\Pages\EditTrack;
use App\Filament\Resources\Tracks\Pages\ListTracks;
use App\Filament\Support\FrenchLabels;
use App\Filament\Support\RichText\TypographyPlugin;
use App\Filament\Support\TranslationTab;
use App\Models\Track;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrackResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Track::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|\UnitEnum|null $navigationGroup = 'Formations';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'filière';

    protected static ?string $pluralModelLabel = 'filières';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Français')->schema([
                    Section::make()->columns(2)->schema([
                        TextInput::make('name')->label('Nom de la filière')->required()->maxLength(120),
                        TextInput::make('short_name')->label('Nom court')->placeholder('Ex. Son'),
                        Select::make('room_id')->label('Univers')->relationship('room', 'name')->preload(),
                        Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                        Textarea::make('summary')->label('Résumé')->rows(2)->maxLength(300)->columnSpanFull(),
                        TypographyPlugin::editor('description', 'Description')
                            ->columnSpanFull(),
                        TagsInput::make('skills')->label('Compétences')->columnSpanFull(),
                        TagsInput::make('outcomes')->label('Débouchés')->columnSpanFull(),
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
            ->modifyQueryUsing(fn ($query) => $query->with('room'))
            ->columns([
                TextColumn::make('name')->label('Filière')->searchable()->weight('bold'),
                TextColumn::make('room.name')->label('Univers')->placeholder('—'),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTracks::route('/'),
            'create' => CreateTrack::route('/create'),
            'edit' => EditTrack::route('/{record}/edit'),
        ];
    }
}
