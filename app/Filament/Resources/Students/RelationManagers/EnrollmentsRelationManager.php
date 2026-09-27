<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Enums\EnrollmentStatus;
use App\Enums\FundingMode;
use App\Enums\PaymentMethod;
use App\Enums\TransactionCategory;
use App\Enums\UserRole;
use App\Exceptions\BusinessRuleException;
use App\Filament\Resources\Applications\ApplicationResource;
use App\Models\CashTransaction;
use App\Models\Enrollment;
use App\Models\Offering;
use App\Services\CashRegister;
use App\Support\Money;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    protected static ?string $title = 'Inscriptions et paiements';

    protected static ?string $modelLabel = 'inscription';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('offering_id')->label('Offre')
                ->options(fn () => ApplicationResource::offeringOptions())
                ->searchable()->required()->live()
                ->afterStateUpdated(function (Set $set, ?string $state) {
                    $offering = $state ? Offering::find($state) : null;
                    $set('fee_amount_due', $offering?->fee_amount);
                    $set('funding_mode', $offering?->funding_mode?->value);
                })
                ->disabledOn('edit')
                ->columnSpanFull(),
            DatePicker::make('enrolled_on')->label('Date d\'inscription')->default(now())->required(),
            Select::make('status')->label('Statut')->options(EnrollmentStatus::class)->default(EnrollmentStatus::ENROLLED)->required(),
            TextInput::make('fee_amount_due')->label('Frais de formation (FCFA)')->integer()->minValue(0)->required()->suffix('FCFA'),
            TextInput::make('discount_amount')->label('Remise (FCFA)')->integer()->minValue(0)->default(0)->suffix('FCFA'),
            Select::make('funding_mode')->label('Financement')->options(FundingMode::class)->default(FundingMode::PAID)->required(),
            Textarea::make('notes')->label('Observations')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['offering.cohort.program', 'offering.track'])
                ->withSum(['transactions as paid_in' => fn (Builder $q) => $q->where('direction', 'in')], 'amount')
                ->withSum(['transactions as paid_out' => fn (Builder $q) => $q->where('direction', 'out')], 'amount'))
            ->columns([
                TextColumn::make('offering.label')->label('Formation')->wrap(),
                TextColumn::make('status')->label('Statut')->badge(),
                TextColumn::make('fee_amount_due')->label('Frais')->formatStateUsing(fn ($state) => Money::fcfa($state)),
                TextColumn::make('paid')->label('Payé')
                    ->state(fn (Enrollment $record) => (int) $record->paid_in - (int) $record->paid_out)
                    ->formatStateUsing(fn ($state) => Money::fcfa($state)),
                TextColumn::make('balance')->label('Reste à payer')
                    ->state(fn (Enrollment $record) => $record->amountDue() - ((int) $record->paid_in - (int) $record->paid_out))
                    ->formatStateUsing(fn ($state) => Money::fcfa($state))
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->weight('bold'),
                TextColumn::make('enrolled_on')->label('Inscrit le')->date('d/m/Y'),
            ])
            ->headerActions([
                CreateAction::make()->label('Nouvelle inscription')
                    ->visible(fn () => auth()->user()->hasRole(UserRole::DIRECTEUR, UserRole::GESTIONNAIRE)),
            ])
            ->recordActions([
                Action::make('pay')
                    ->label('Encaisser')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn () => auth()->user()->can('create', CashTransaction::class))
                    ->schema([
                        Select::make('category')->label('Nature')->required()->default(TransactionCategory::SCOLARITE->value)
                            ->options([
                                TransactionCategory::SCOLARITE->value => TransactionCategory::SCOLARITE->getLabel(),
                                TransactionCategory::INSCRIPTION->value => TransactionCategory::INSCRIPTION->getLabel(),
                            ]),
                        TextInput::make('amount')->label('Montant (FCFA)')->integer()->minValue(1)->maxValue(99_999_999)->required()->suffix('FCFA'),
                        Select::make('method')->label('Moyen de paiement')->options(PaymentMethod::class)->default(PaymentMethod::CASH->value)->required(),
                        TextInput::make('external_reference')->label('Référence (Wave, chèque…)'),
                        DatePicker::make('occurred_on')->label('Date')->default(now())->maxDate(now())->required(),
                    ])
                    ->action(function (array $data, Enrollment $record) {
                        try {
                            $transaction = app(CashRegister::class)->record($data + ['direction' => 'in', 'enrollment_id' => $record->id], auth()->user());
                            Notification::make()->title("Encaissement {$transaction->number} enregistré.")->success()
                                ->actions([Action::make('receipt')->label('Imprimer le reçu')->url(route('admin.cash.receipt', $transaction), true)])
                                ->send();
                        } catch (BusinessRuleException $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
                EditAction::make()->visible(fn () => auth()->user()->hasRole(UserRole::DIRECTEUR, UserRole::GESTIONNAIRE)),
            ]);
    }
}
