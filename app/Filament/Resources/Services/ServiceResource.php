<?php

namespace App\Filament\Resources\Services;

use App\Enums\Activity;
use App\Enums\PriceUnit;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Support\Fields;
use App\Filament\Support\FrenchLabels;
use App\Filament\Support\RichText\TypographyPlugin;
use App\Filament\Support\TranslationTab;
use App\Models\Service;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|\UnitEnum|null $navigationGroup = 'Impact Live';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'service';

    protected static ?string $pluralModelLabel = 'services';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('Français')->schema([
                    Section::make('Service')->columns(2)->schema([
                        Select::make('activity')->label('Activité')->options([
                            Activity::STUDIO->value => Activity::STUDIO->getLabel(),
                            Activity::EVENTS->value => Activity::EVENTS->getLabel(),
                            Activity::SPACE->value => Activity::SPACE->getLabel(),
                        ])->required()->default(Activity::STUDIO->value),
                        TextInput::make('name')->label('Nom')->required()->maxLength(120)->placeholder('Ex. Mixage'),
                        Textarea::make('summary')->label('Présentation courte')->rows(2)->maxLength(300)->columnSpanFull(),
                        TypographyPlugin::editor('description', 'Description détaillée')->columnSpanFull(),
                        TextInput::make('price_from')->label('Prix « à partir de » (FCFA)')->numeric()->minValue(0)->step(1000)
                            ->helperText('Laisser vide pour afficher « Sur devis ».'),
                        Select::make('price_unit')->label('Unité du prix')->options(PriceUnit::class)->default('hour'),
                        ...Fields::image('image', 'services', 'Photo (facultatif)', '4:3'),
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
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('name')->label('Service')->weight('bold')->searchable(),
                TextColumn::make('activity')->label('Activité')->badge(),
                TextColumn::make('price_from')->label('Prix')->formatStateUsing(fn (Service $r) => $r->priceLabel()),
                TextColumn::make('status')->label('État')->badge(),
            ])
            ->filters([SelectFilter::make('activity')->label('Activité')->options(Activity::class)])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}
