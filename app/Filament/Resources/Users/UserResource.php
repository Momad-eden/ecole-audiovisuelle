<?php

namespace App\Filament\Resources\Users;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'compte';

    protected static ?string $pluralModelLabel = 'comptes utilisateurs';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('name')->label('Nom complet')->required()->maxLength(120),
                TextInput::make('email')->label('Adresse e-mail')->email()->required()->unique(ignoreRecord: true),
                Select::make('role')->label('Rôle')->options(UserRole::class)->required()
                    ->disabled(fn (?User $record) => $record?->is(auth()->user()))
                    ->helperText('Directeur : tout. Gestionnaire : scolarité, caisse, formations. Secrétaire : candidatures, étudiants, encaissements. Communication : site et musée.'),
                Toggle::make('is_active')->label('Compte actif')->default(true)->inline(false)
                    ->disabled(fn (?User $record) => $record?->is(auth()->user())),
                TextInput::make('password')->label(fn (string $operation) => $operation === 'create' ? 'Mot de passe' : 'Nouveau mot de passe (laisser vide pour ne pas changer)')
                    ->password()->revealable()
                    ->rule(Password::min(10)->letters()->numbers())
                    ->required(fn (string $operation) => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state)),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable()->weight('bold'),
                TextColumn::make('email')->label('E-mail')->searchable(),
                TextColumn::make('role')->label('Rôle')->formatStateUsing(fn (?string $state) => UserRole::tryFrom((string) $state)?->getLabel() ?? 'Sans rôle')->badge(),
                IconColumn::make('is_active')->label('Actif')->boolean(),
                TextColumn::make('created_at')->label('Créé le')->date('d/m/Y'),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
