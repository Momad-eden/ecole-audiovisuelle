<?php

namespace App\Filament\Resources\BookingRequests;

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Filament\Resources\BookingRequests\Pages\CreateBookingRequest;
use App\Filament\Resources\BookingRequests\Pages\EditBookingRequest;
use App\Filament\Resources\BookingRequests\Pages\ListBookingRequests;
use App\Filament\Resources\BookingRequests\Pages\ViewBookingRequest;
use App\Filament\Support\FrenchLabels;
use App\Models\BookingRequest;
use App\Support\Money;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Demandes de devis et de réservation reçues du site (ou saisies après un appel). */
class BookingRequestResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = BookingRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static string|\UnitEnum|null $navigationGroup = 'Impact Live';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'demande';

    protected static ?string $pluralModelLabel = 'demandes';

    protected static ?string $recordTitleAttribute = 'reference';

    public static function getNavigationBadge(): ?string
    {
        $count = BookingRequest::where('status', BookingStatus::NEW)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Nouvelles demandes à traiter';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Demande')->columns(2)->schema([
                Select::make('type')->label('Type')->options(BookingType::class)->required(),
                TextInput::make('location')->label('Lieu de l\'événement')->maxLength(255),
                DatePicker::make('starts_on')->label('Du'),
                DatePicker::make('ends_on')->label('Au')->afterOrEqual('starts_on'),
                TextInput::make('attendees')->label('Nombre de personnes')->numeric()->minValue(1),
                TextInput::make('quoted_amount')->label('Montant du devis (FCFA)')->numeric()->minValue(0)->step(1000),
                Textarea::make('message')->label('Message du client')->rows(3)->columnSpanFull(),
            ]),
            Section::make('Client')->columns(2)->schema([
                TextInput::make('name')->label('Nom')->required()->maxLength(150),
                TextInput::make('organization')->label('Structure / artiste')->maxLength(150),
                TextInput::make('phone')->label('Téléphone')->tel()->required()->regex('/^\+?[0-9][0-9 ().-]{7,19}$/'),
                TextInput::make('email')->label('E-mail')->email(),
            ]),
            Section::make('Suivi interne')->schema([
                Textarea::make('internal_notes')->label('Notes internes (invisibles du client)')->rows(3),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Grid::make(3)->schema([
                Section::make('Demande')->columnSpan(2)->columns(2)->schema([
                    TextEntry::make('reference')->label('Référence'),
                    TextEntry::make('status')->label('Statut')->badge(),
                    TextEntry::make('type')->label('Type')->badge(),
                    TextEntry::make('created_at')->label('Reçue le')->dateTime('d/m/Y à H:i'),
                    TextEntry::make('starts_on')->label('Du')->date('d/m/Y')->placeholder('—'),
                    TextEntry::make('ends_on')->label('Au')->date('d/m/Y')->placeholder('—'),
                    TextEntry::make('location')->label('Lieu')->placeholder('—'),
                    TextEntry::make('attendees')->label('Personnes')->placeholder('—'),
                    TextEntry::make('quoted_amount')->label('Devis')->formatStateUsing(fn ($state) => $state !== null ? Money::fcfa($state) : null)->placeholder('Pas encore chiffré'),
                    TextEntry::make('message')->label('Message du client')->columnSpanFull()->placeholder('—'),
                ]),
                Section::make('Client')->columnSpan(1)->schema([
                    TextEntry::make('name')->label('Nom'),
                    TextEntry::make('organization')->label('Structure / artiste')->placeholder('—'),
                    TextEntry::make('phone')->label('Téléphone')->url(fn (BookingRequest $r) => 'tel:'.preg_replace('/[^0-9+]/', '', $r->phone)),
                    TextEntry::make('phone')->label('WhatsApp')->formatStateUsing(fn () => 'Écrire sur WhatsApp')
                        ->url(fn (BookingRequest $r) => 'https://wa.me/'.preg_replace('/[^0-9]/', '', $r->phone), true)->color('primary'),
                    TextEntry::make('email')->label('E-mail')->placeholder('—')->copyable(),
                ]),
            ]),
            Section::make('Éléments demandés')->schema([
                RepeatableEntry::make('items')->hiddenLabel()->columns(3)->placeholder('Aucun élément choisi sur le site.')->schema([
                    TextEntry::make('name')->label('Élément'),
                    TextEntry::make('kind')->label('Type')->formatStateUsing(fn (?string $state) => ['equipment' => 'Matériel', 'pack' => 'Pack', 'service' => 'Service'][$state] ?? '—'),
                    TextEntry::make('quantity')->label('Quantité'),
                ]),
            ]),
            Section::make('Notes internes')->schema([TextEntry::make('internal_notes')->hiddenLabel()->placeholder('—')]),
            Section::make('Historique')->schema([
                RepeatableEntry::make('logs')->hiddenLabel()->columns(4)->schema([
                    TextEntry::make('created_at')->label('Date')->dateTime('d/m/Y H:i'),
                    TextEntry::make('to_status')->label('Statut')->formatStateUsing(fn (?string $state) => BookingStatus::tryFrom((string) $state)?->getLabel() ?? '—'),
                    TextEntry::make('comment')->label('Commentaire')->placeholder('—'),
                    TextEntry::make('user.name')->label('Par')->placeholder('Client (en ligne)'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label('Référence')->searchable()->weight('bold'),
                TextColumn::make('name')->label('Client')->searchable()->description(fn (BookingRequest $r) => $r->organization),
                TextColumn::make('type')->label('Type')->badge(),
                TextColumn::make('starts_on')->label('Date')->date('d/m/Y')->placeholder('—'),
                TextColumn::make('status')->label('Statut')->badge(),
                TextColumn::make('created_at')->label('Reçue le')->since(),
            ])
            ->filters([SelectFilter::make('type')->label('Type')->options(BookingType::class)])
            ->recordActions([ViewAction::make()->label('Ouvrir')]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['logs' => fn ($q) => $q->latest('id')->with('user')]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookingRequests::route('/'),
            'create' => CreateBookingRequest::route('/create'),
            'view' => ViewBookingRequest::route('/{record}'),
            'edit' => EditBookingRequest::route('/{record}/edit'),
        ];
    }
}
