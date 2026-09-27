<?php

namespace App\Filament\Resources\CashTransactions;

use App\Enums\CashDirection;
use App\Enums\PaymentMethod;
use App\Enums\TransactionCategory;
use App\Filament\Resources\CashTransactions\Pages\CreateCashTransaction;
use App\Filament\Resources\CashTransactions\Pages\ListCashTransactions;
use App\Filament\Resources\CashTransactions\Pages\ViewCashTransaction;
use App\Filament\Support\Fields;
use App\Filament\Support\FrenchLabels;
use App\Models\CashTransaction;
use App\Models\Enrollment;
use App\Support\Money;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CashTransactionResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = CashTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Caisse';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'opération de caisse';

    protected static ?string $pluralModelLabel = 'opérations de caisse';

    protected static ?string $navigationLabel = 'Journal de caisse';

    protected static ?string $recordTitleAttribute = 'number';

    public static function enrollmentOptions(string $search = ''): array
    {
        return Enrollment::query()
            ->with(['student', 'offering.cohort.program', 'offering.track'])
            ->whereHas('student', fn (Builder $q) => $q->where(fn (Builder $w) => $w
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('student_number', 'like', "%{$search}%")))
            ->limit(30)->get()
            ->mapWithKeys(fn (Enrollment $e) => [$e->id => "{$e->student->student_number} — {$e->student->full_name} · {$e->offering->label}"])
            ->all();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Opération')->columns(2)->schema([
                Fields::campus('place_id', 'Caisse (campus)')->columnSpanFull()
                    ->helperText('Un paiement de scolarité va automatiquement dans la caisse du campus de l\'étudiant.'),
                Radio::make('direction')->label('Nature')
                    ->options([CashDirection::IN->value => 'Encaissement (entrée d\'argent)', CashDirection::OUT->value => 'Décaissement (dépense)'])
                    ->default(CashDirection::IN->value)->required()->live()->inline()->columnSpanFull(),
                Select::make('category')->label('Catégorie')->required()->live()
                    ->options(fn (Get $get) => TransactionCategory::optionsFor(CashDirection::tryFrom((string) $get('direction')) ?? CashDirection::IN)),
                Select::make('enrollment_id')->label('Étudiant et formation')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => static::enrollmentOptions($search))
                    ->getOptionLabelUsing(fn ($value) => static::enrollmentOptions()[$value] ?? null)
                    ->visible(fn (Get $get) => TransactionCategory::tryFrom((string) $get('category'))?->requiresEnrollment() ?? false)
                    ->required(fn (Get $get) => TransactionCategory::tryFrom((string) $get('category'))?->requiresEnrollment() ?? false)
                    ->helperText('Tapez le nom ou le matricule.'),
                TextInput::make('amount')->label('Montant')->integer()->minValue(1)->maxValue(99_999_999)->required()->suffix('FCFA'),
                Select::make('method')->label('Moyen de paiement')->options(PaymentMethod::class)->default(PaymentMethod::CASH->value)->required(),
                DatePicker::make('occurred_on')->label('Date de l\'opération')->default(now())->maxDate(now())->required(),
                TextInput::make('external_reference')->label('Référence externe')->placeholder('N° Wave, chèque, facture…'),
                TextInput::make('payee')->label('Bénéficiaire / payeur')->placeholder('Ex. fournisseur, intervenant'),
                TextInput::make('label')->label('Libellé')->placeholder('Laissé vide : libellé automatique')->maxLength(255)->columnSpanFull(),
                Textarea::make('notes')->label('Observations')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()->columns(3)->schema([
                TextEntry::make('number')->label('N° de pièce'),
                TextEntry::make('direction')->label('Nature')->badge(),
                TextEntry::make('amount')->label('Montant')->formatStateUsing(fn ($state) => Money::fcfa($state))->weight('bold'),
                TextEntry::make('category')->label('Catégorie'),
                TextEntry::make('method')->label('Moyen de paiement'),
                TextEntry::make('occurred_on')->label('Date')->date('d/m/Y'),
                TextEntry::make('label')->label('Libellé')->columnSpan(2),
                TextEntry::make('enrollment.student.full_name')->label('Étudiant')->placeholder('—'),
                TextEntry::make('payee')->label('Bénéficiaire / payeur')->placeholder('—'),
                TextEntry::make('external_reference')->label('Référence externe')->placeholder('—'),
                TextEntry::make('creator.name')->label('Saisi par')->placeholder('—'),
                TextEntry::make('reverses.number')->label('Annule l\'écriture')->placeholder('—'),
                TextEntry::make('cancelled_at')->label('Annulée le')->dateTime('d/m/Y H:i')->placeholder('—')->color('danger'),
                TextEntry::make('cancel_reason')->label('Motif d\'annulation')->placeholder('—'),
                TextEntry::make('notes')->label('Observations')->placeholder('—')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['enrollment.student', 'place']))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('occurred_on')->label('Date')->date('d/m/Y')->sortable(),
                TextColumn::make('number')->label('N°')->searchable(),
                TextColumn::make('place.name')->label('Caisse')->placeholder('—')->visible(fn () => ! auth()->user()?->place_id)->toggleable(),
                TextColumn::make('label')->label('Libellé')->searchable()->wrap()
                    ->description(fn (CashTransaction $r) => $r->enrollment?->student?->student_number),
                TextColumn::make('category')->label('Catégorie')->toggleable(),
                TextColumn::make('method')->label('Moyen')->toggleable(),
                TextColumn::make('amount')->label('Montant')->alignEnd()->sortable()
                    ->formatStateUsing(fn (CashTransaction $r) => ($r->direction === CashDirection::IN ? '+ ' : '− ').Money::fcfa($r->amount))
                    ->color(fn (CashTransaction $r) => $r->direction === CashDirection::IN ? 'success' : 'danger'),
                TextColumn::make('cancelled_at')->label('')->formatStateUsing(fn ($state) => $state ? 'Annulée' : null)->badge()->color('gray'),
            ])
            ->filters([
                SelectFilter::make('place_id')->label('Caisse (campus)')->relationship('place', 'name', fn (Builder $query) => $query->where('kind', 'campus'))
                    ->visible(fn () => ! auth()->user()?->place_id),
                SelectFilter::make('direction')->label('Nature')->options(CashDirection::class),
                SelectFilter::make('category')->label('Catégorie')->options(TransactionCategory::class),
                SelectFilter::make('method')->label('Moyen')->options(PaymentMethod::class),
                Filter::make('period')->label('Période')
                    ->schema([
                        DatePicker::make('from')->label('Du'),
                        DatePicker::make('until')->label('Au'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $d) => $q->whereDate('occurred_on', '>=', $d))
                        ->when($data['until'] ?? null, fn (Builder $q, $d) => $q->whereDate('occurred_on', '<=', $d))),
                TernaryFilter::make('cancelled_at')->label('Annulées')->nullable(),
            ])
            ->recordActions([ViewAction::make()->label('Ouvrir')]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCashTransactions::route('/'),
            'create' => CreateCashTransaction::route('/create'),
            'view' => ViewCashTransaction::route('/{record}'),
        ];
    }

    /** Le personnel rattaché à un campus ne voit que les données de son campus. */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->visibleTo(auth()->user());
    }
}
