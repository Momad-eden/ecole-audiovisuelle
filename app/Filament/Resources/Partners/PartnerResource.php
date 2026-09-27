<?php

namespace App\Filament\Resources\Partners;

use App\Filament\Resources\Partners\Pages\CreatePartner;
use App\Filament\Resources\Partners\Pages\EditPartner;
use App\Filament\Resources\Partners\Pages\ListPartners;
use App\Models\Partner;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHandRaised;

    protected static string|\UnitEnum|null $navigationGroup = 'Site';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'partenaire';

    protected static ?string $pluralModelLabel = 'partenaires';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('name')->label('Nom')->required()->maxLength(150),
                Select::make('category')->label('Catégorie')->options(Partner::CATEGORIES)->default('institutional')->required(),
                TextInput::make('website')->label('Site web')->url(),
                FileUpload::make('logo')->label('Logo')->image()->disk('public')->directory('partners')->maxSize(4096)
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                    ->helperText('PNG transparent de préférence.'),
                Textarea::make('description')->label('Description')->rows(2)->maxLength(300)->columnSpanFull(),
                Toggle::make('is_active')->label('Affiché sur le site')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                ImageColumn::make('logo')->label('')->disk('public'),
                TextColumn::make('name')->label('Partenaire')->searchable()->weight('bold'),
                TextColumn::make('category')->label('Catégorie')->formatStateUsing(fn (string $state) => Partner::CATEGORIES[$state] ?? $state),
                IconColumn::make('is_active')->label('Affiché')->boolean(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPartners::route('/'),
            'create' => CreatePartner::route('/create'),
            'edit' => EditPartner::route('/{record}/edit'),
        ];
    }
}
