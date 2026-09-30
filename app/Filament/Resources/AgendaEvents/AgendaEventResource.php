<?php

namespace App\Filament\Resources\AgendaEvents;

use App\Enums\Activity;
use App\Filament\Resources\AgendaEvents\Pages\CreateAgendaEvent;
use App\Filament\Resources\AgendaEvents\Pages\EditAgendaEvent;
use App\Filament\Resources\AgendaEvents\Pages\ListAgendaEvents;
use App\Filament\Support\Fields;
use App\Filament\Support\FrenchLabels;
use App\Filament\Support\RichText\TypographyPlugin;
use App\Filament\Support\TranslationTab;
use App\Models\AgendaEvent;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AgendaEventResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = AgendaEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|\UnitEnum|null $navigationGroup = 'Impact Live';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'événement';

    protected static ?string $pluralModelLabel = 'agenda et références';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Français')->schema([
                    Section::make('Événement')->columns(2)->schema([
                        TextInput::make('title')->label('Titre')->required()->maxLength(150)->columnSpanFull(),
                        Select::make('activity')->label('Activité')->options(Activity::class)->required()->default(Activity::EVENTS->value),
                        Toggle::make('is_reference')->label('Référence (prestation réalisée)')->inline(false)
                            ->helperText('Affichée parmi « Ils nous ont fait confiance », sans date obligatoire.'),
                        DateTimePicker::make('starts_at')->label('Début')->seconds(false),
                        DateTimePicker::make('ends_at')->label('Fin')->seconds(false)->afterOrEqual('starts_at'),
                        Select::make('place_id')->label('Lieu (parmi nos lieux)')->relationship('place', 'name')->preload(),
                        TextInput::make('venue')->label('Ou autre lieu')->maxLength(150)->placeholder('Ex. Place Faidherbe'),
                        TextInput::make('city')->label('Ville')->maxLength(80),
                        TextInput::make('ticket_url')->label('Lien de billetterie')->url()->maxLength(500),
                        Textarea::make('summary')->label('Présentation courte')->rows(2)->maxLength(300)->columnSpanFull(),
                        TypographyPlugin::editor('content', 'Texte')->columnSpanFull(),
                        ...Fields::image('image', 'agenda', 'Affiche ou photo', '4:3'),
                    ]),
                    Fields::publication(),
                ]),
                TranslationTab::make(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at', 'desc')
            ->columns([
                TextColumn::make('title')->label('Événement')->weight('bold')->searchable(),
                TextColumn::make('activity')->label('Activité')->badge(),
                TextColumn::make('starts_at')->label('Date')->dateTime('d/m/Y H:i')->placeholder('—'),
                IconColumn::make('is_reference')->label('Référence')->boolean(),
                TextColumn::make('status')->label('État')->badge(),
            ])
            ->filters([
                SelectFilter::make('activity')->label('Activité')->options(Activity::class),
                TernaryFilter::make('is_reference')->label('Références'),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAgendaEvents::route('/'),
            'create' => CreateAgendaEvent::route('/create'),
            'edit' => EditAgendaEvent::route('/{record}/edit'),
        ];
    }
}
