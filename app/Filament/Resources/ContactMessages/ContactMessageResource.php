<?php

namespace App\Filament\Resources\ContactMessages;

use App\Enums\ContactMessageStatus;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|\UnitEnum|null $navigationGroup = 'Site';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'message';

    protected static ?string $pluralModelLabel = 'messages reçus';

    public static function getNavigationBadge(): ?string
    {
        $count = ContactMessage::where('status', ContactMessageStatus::NEW)->count();

        return $count ? (string) $count : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextEntry::make('subject')->label('Objet')->formatStateUsing(fn (string $state) => ContactMessage::SUBJECTS[$state] ?? $state),
            TextEntry::make('created_at')->label('Reçu le')->dateTime('d/m/Y à H:i'),
            TextEntry::make('name')->label('Nom'),
            TextEntry::make('status')->label('État')->badge(),
            TextEntry::make('email')->label('E-mail')->placeholder('—')->copyable(),
            TextEntry::make('phone')->label('Téléphone')->placeholder('—'),
            TextEntry::make('message')->label('Message')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $mark = fn (ContactMessageStatus $status, string $label, string $icon) => Action::make("mark_{$status->value}")
            ->label($label)->icon($icon)
            ->visible(fn (ContactMessage $record) => $record->status !== $status && auth()->user()->can('update', $record))
            ->action(fn (ContactMessage $record) => $record->update(['status' => $status, 'handled_by' => auth()->id()]));

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Reçu le')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('name')->label('Nom')->searchable()->weight('bold')->description(fn (ContactMessage $r) => $r->email ?? $r->phone),
                TextColumn::make('subject')->label('Objet')->formatStateUsing(fn (string $state) => ContactMessage::SUBJECTS[$state] ?? $state),
                TextColumn::make('message')->label('Message')->limit(60)->wrap(),
                TextColumn::make('status')->label('État')->badge(),
            ])
            ->filters([SelectFilter::make('status')->label('État')->options(ContactMessageStatus::class)->default(ContactMessageStatus::NEW->value)])
            ->recordActions([
                ViewAction::make()->label('Lire'),
                $mark(ContactMessageStatus::HANDLED, 'Marquer comme traité', 'heroicon-o-check'),
                $mark(ContactMessageStatus::SPAM, 'Indésirable', 'heroicon-o-no-symbol'),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListContactMessages::route('/')];
    }
}
