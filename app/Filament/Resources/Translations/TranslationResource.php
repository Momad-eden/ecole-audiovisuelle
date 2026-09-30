<?php

namespace App\Filament\Resources\Translations;

use App\Enums\TranslationStatus;
use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\Translations\Pages\ListTranslations;
use App\Filament\Support\FrenchLabels;
use App\Filament\Support\TranslationTab;
use App\Models\Setting;
use App\Models\Translation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** « Traductions à relire » : liste des traductions anglaises, par défaut automatiques ou en échec (spec R2 §6). */
class TranslationResource extends Resource
{
    use FrenchLabels;

    protected static ?string $model = Translation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    // Groupe des contenus du site (pages, actualités, FAQ, menus) : l'admin n'a pas de groupe « Contenu ».
    protected static string|\UnitEnum|null $navigationGroup = 'Site';

    protected static ?int $navigationSort = 7;

    protected static ?string $modelLabel = 'traduction';

    protected static ?string $pluralModelLabel = 'traductions à relire';

    protected static ?string $navigationLabel = 'Traductions à relire';

    /** Type de fiche (alias du morph map) → libellé. */
    public const TYPES = [
        'page' => 'Page', 'program' => 'Formation', 'track' => 'Filière', 'room' => 'Univers', 'artwork' => 'Réalisation',
        'news' => 'Actualité', 'faq' => 'Question fréquente', 'agenda_event' => 'Agenda', 'service' => 'Service',
        'place' => 'Lieu', 'menu_item' => 'Lien du menu', 'setting' => 'Paramètres du site',
    ];

    private const TO_REVIEW = [TranslationStatus::AUTO, TranslationStatus::FAILED];

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('locale', 'en')->with('translatable');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Translation::where('locale', 'en')->whereIn('status', self::TO_REVIEW)->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Traductions automatiques ou en échec';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('translatable_type')->label('Type')->formatStateUsing(fn (string $state) => self::TYPES[$state] ?? $state),
                TextColumn::make('fiche')->label('Fiche')->weight('bold')->wrap()
                    ->state(fn (Translation $record) => self::recordTitle($record))
                    ->url(fn (Translation $record) => self::recordUrl($record)),
                TextColumn::make('field')->label('Champ')->wrap()
                    ->formatStateUsing(fn (Translation $record) => TranslationTab::fieldLabel($record->translatable_type, $record->field))
                    ->description(fn (Translation $record) => self::fieldDetail($record)),
                TextColumn::make('status')->label('Statut')->badge()
                    ->formatStateUsing(fn (TranslationStatus $state) => $state->label())
                    ->color(fn (Translation $record) => TranslationTab::statusColor($record)),
                TextColumn::make('updated_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('translatable_type')->label('Type')->options(self::TYPES),
                SelectFilter::make('status')->label('Statut')->multiple()
                    ->options(collect(TranslationStatus::cases())->mapWithKeys(fn (TranslationStatus $s) => [$s->value => $s->label()])->all())
                    ->default(array_map(fn (TranslationStatus $s) => $s->value, self::TO_REVIEW)),
            ])
            ->recordActions([
                Action::make('markReviewed')->label('Marquer comme relue')->icon(Heroicon::OutlinedCheck)->color('success')
                    ->visible(fn (Translation $record) => TranslationTab::canManage() && $record->value !== null && $record->status !== TranslationStatus::REVIEWED && $record->translatable)
                    ->action(function (Translation $record) {
                        abort_unless(TranslationTab::canManage(), 403);
                        $record->update([
                            'status' => TranslationStatus::REVIEWED, 'source_hash' => $record->translatable->sourceHash($record->field),
                            'reviewed_at' => now(), 'reviewed_by' => auth()->id(), 'previous_value' => null,
                        ]);
                        Notification::make()->title('Traduction marquée comme relue.')->success()->send();
                    }),
            ])
            ->emptyStateHeading('Aucune traduction à relire')
            ->emptyStateDescription('Les nouvelles traductions automatiques apparaîtront ici.');
    }

    public static function recordTitle(Translation $translation): string
    {
        $record = $translation->translatable;
        if (! $record) {
            return 'Fiche supprimée';
        }
        if ($record instanceof Setting) {
            return 'Paramètres du site';
        }

        foreach (['title', 'name', 'question', 'label'] as $attribute) {
            if (filled($value = $record->getAttribute($attribute))) {
                return (string) $value;
            }
        }

        return '#'.$record->getKey();
    }

    /** Lien vers l'onglet « Anglais » de la fiche, si la personne peut l'ouvrir. */
    public static function recordUrl(Translation $translation): ?string
    {
        $record = $translation->translatable;
        if (! $record instanceof Model) {
            return null;
        }
        if ($record instanceof Setting) {
            return SiteSettings::canAccess() ? SiteSettings::getUrl() : null;
        }

        $resource = Filament::getModelResource($record);

        return $resource && $resource::canEdit($record)
            ? $resource::getUrl('edit', ['record' => $record, 'tab' => TranslationTab::TAB_QUERY])
            : null;
    }

    private static function fieldDetail(Translation $translation): ?string
    {
        if ($translation->field !== 'blocks' || $translation->value === null) {
            return null;
        }
        $count = count(json_decode($translation->value, true) ?: []);

        return $count.' '.($count > 1 ? 'textes traduits' : 'texte traduit');
    }

    public static function getPages(): array
    {
        return ['index' => ListTranslations::route('/')];
    }
}
