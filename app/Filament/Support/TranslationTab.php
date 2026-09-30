<?php

namespace App\Filament\Support;

use App\Enums\TranslationStatus;
use App\Filament\Support\RichText\TypographyPlugin;
use App\Jobs\TranslateRecord;
use App\Models\Translation;
use App\Services\Translation\Translator;
use App\Support\Translation\BlockTexts;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Text;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Onglet « Anglais » des fiches traduites (spec R2 §6) : pour chaque champ, le français en lecture seule
 * à côté de l'anglais modifiable, l'état de la traduction et les actions « Marquer comme relue » et
 * « Retraduire » (par champ). Les pages présentent chaque texte de leurs blocs PUBLIÉS, un par un.
 *
 * Les textes anglais vivent sous `english.*` dans l'état du formulaire (jamais enregistrés dans la fiche).
 * À l'enregistrement, seules les valeurs réellement modifiées par la personne (comparées à celles chargées
 * dans le formulaire, pas à la base : une traduction automatique arrivée entre-temps n'est pas effacée)
 * deviennent « Relue ». Elles sont écrites APRÈS la fiche (flush(), appelé par l'événement RecordSaved de
 * Filament ou par la page Paramètres), pour que l'empreinte du français soit celle du texte enregistré :
 * aucune retraduction automatique du champ qui vient d'être relu. Vider un texte anglais rend le français.
 */
final class TranslationTab
{
    /** Onglet ouvert par le lien « Fiche » de la liste des traductions. */
    public const TAB_QUERY = 'anglais::data::tab';

    private const TEXT_MAX = 500;

    private const LONG_MAX = 5000;

    /**
     * Champs traduits par type de fiche (alias du morph map) : libellé et nature.
     * Natures : text, textarea, rich, rich_images (éditeur guidé), list (une case par élément),
     * seo (titre et description), blocks (textes des blocs publiés).
     */
    public const FIELDS = [
        'page' => ['title' => ['Titre de la page', 'text'], 'blocks' => ['Contenu de la page', 'blocks'], 'seo' => ['Référencement (Google)', 'seo']],
        'program' => [
            'title' => ['Intitulé', 'text'], 'summary' => ['Résumé', 'textarea'], 'description' => ['Présentation détaillée', 'rich'],
            'level_label' => ['Niveau / titre délivré', 'text'], 'duration_label' => ['Durée', 'text'],
            'skills' => ['Compétences visées', 'list'], 'outcomes' => ['Débouchés', 'list'], 'prerequisites' => ['Prérequis', 'list'],
            'equipment' => ['Équipements utilisés', 'list'], 'seo' => ['Référencement (Google)', 'seo'],
        ],
        'track' => [
            'name' => ['Nom de la filière', 'text'], 'short_name' => ['Nom court', 'text'], 'summary' => ['Résumé', 'textarea'],
            'description' => ['Description', 'rich'], 'skills' => ['Compétences', 'list'], 'outcomes' => ['Débouchés', 'list'],
        ],
        'room' => ['name' => ['Nom', 'text'], 'tagline' => ['Accroche', 'text'], 'intro' => ['Texte d\'introduction', 'textarea'], 'cover_alt' => ['Description de l\'image', 'text']],
        'artwork' => [
            'title' => ['Titre', 'text'], 'summary' => ['Présentation courte (cartel)', 'textarea'], 'creation_story' => ['Récit de création', 'rich'],
            'equipment' => ['Matériel utilisé', 'list'], 'transcript' => ['Transcription ou description du son', 'textarea'], 'cover_alt' => ['Description de l\'image', 'text'],
        ],
        'news' => ['title' => ['Titre', 'text'], 'excerpt' => ['Résumé', 'textarea'], 'content' => ['Article', 'rich_images']],
        'faq' => ['question' => ['Question', 'text'], 'answer' => ['Réponse', 'textarea']],
        'agenda_event' => [
            'title' => ['Titre', 'text'], 'summary' => ['Présentation courte', 'textarea'], 'content' => ['Texte', 'rich'],
            'venue' => ['Autre lieu', 'text'], 'image_alt' => ['Description de l\'image', 'text'],
        ],
        'service' => ['name' => ['Nom', 'text'], 'summary' => ['Présentation courte', 'textarea'], 'description' => ['Description détaillée', 'rich'], 'image_alt' => ['Description de l\'image', 'text']],
        'place' => [
            'tagline' => ['Accroche', 'text'], 'description' => ['Présentation', 'textarea'], 'highlights' => ['Points forts', 'list'],
            'opening_hours' => ['Horaires', 'text'], 'image_alt' => ['Description de l\'image', 'text'],
        ],
        'menu_item' => ['label' => ['Texte du lien', 'text']],
        'setting' => [
            'description' => ['Présentation courte (pied de page)', 'textarea'], 'opening_hours' => ['Horaires d\'accueil', 'text'],
            'seo_title' => ['Titre du site dans Google', 'text'], 'seo_description' => ['Description dans Google', 'textarea'],
        ],
    ];

    private const SEO_LABELS = ['title' => 'Titre affiché dans Google', 'description' => 'Description affichée dans Google'];

    /** @var array<string, array<string, array<string, string>>> fiche → champ → case → texte anglais saisi */
    private static array $pending = [];

    public static function make(): Tab
    {
        return Tab::make('Anglais')->icon(Heroicon::OutlinedLanguage)
            ->visible(fn (?Model $record) => (bool) $record?->exists)
            ->schema(fn (?Model $record) => $record?->exists ? self::components($record) : []);
    }

    /** Contenu de l'onglet (aussi utilisé tel quel dans une section, pour les paramètres du site). */
    public static function components(Model $record): array
    {
        $sections = [];
        foreach (self::fieldsFor($record) as $field => [$label, $kind]) {
            if ($section = self::section($record, $field, $label, $kind)) {
                $sections[] = $section;
            }
        }

        return [
            Text::make(self::canManage()
                ? 'Le site anglais affiche ces textes. Un texte anglais vide laisse le français. Une correction enregistrée passe en « Relue » et n\'est plus remplacée par la traduction automatique tant que le français ne change pas.'
                : 'Vous pouvez consulter les traductions. Seules la direction et la communication peuvent les modifier.'),
            Group::make([Hidden::make('loaded'), ...($sections ?: [Text::make('Aucun texte à traduire pour le moment.')])])
                ->key('english')
                ->statePath('english')
                ->model($record)
                ->dehydrated(false)
                ->disabled(fn () => ! self::canManage())
                ->loadStateFromRelationshipsUsing(fn (Group $component) => $component->state(self::formState($record)))
                ->saveRelationshipsUsing(fn (Group $component) => self::collect($component, $record)),
        ];
    }

    /** Clé de formulaire d'un texte de bloc (les clés stables contiennent des points et des « # »). */
    public static function slot(string $key): string
    {
        return 'k'.substr(md5($key), 0, 12);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function fieldsFor(Model $record): array
    {
        return self::FIELDS[$record->getMorphClass()] ?? [];
    }

    public static function fieldLabel(string $type, string $field): string
    {
        return self::FIELDS[$type][$field][0] ?? $field;
    }

    public static function canManage(): bool
    {
        return auth()->user()?->can('manage', Translation::class) ?? false;
    }

    public static function statusLabel(?Translation $row): string
    {
        return $row?->status?->label() ?? 'Pas encore traduit';
    }

    public static function statusColor(?Translation $row): string
    {
        return match ($row?->status) {
            TranslationStatus::REVIEWED => 'success',
            TranslationStatus::AUTO => 'warning',
            TranslationStatus::FAILED => 'danger',
            default => 'gray',
        };
    }

    // --- Présentation -------------------------------------------------------------------------------

    private static function section(Model $record, string $field, string $label, string $kind): ?Section
    {
        $slots = self::slots($record, $field, $kind);
        if ($slots === []) {
            return $kind === 'blocks'
                ? Section::make($label)->compact()->schema([Text::make('Publiez la page : les textes de ses blocs pourront ensuite être traduits.')])
                : null;
        }

        $rows = [];
        foreach ($slots as $slot) {
            $pair = [
                TextEntry::make('fr_'.md5($slot['name']))->label('Français')->state($slot['french'])->html($slot['html'])
                    ->extraAttributes(['class' => 'break-words']),
                self::input($slot)->label('Anglais'),
            ];
            $previous = fn () => self::slotValue(self::row($record, $field)?->previous_value, $kind, $slot['slot']);
            $pair[] = TextEntry::make('prev_'.md5($slot['name']))->label('Ancienne version relue')->columnSpanFull()
                ->state($previous)->html($slot['html'])->color('gray')
                ->visible(fn () => filled($previous()));

            $rows[] = $slot['label'] === null
                ? Grid::make(['default' => 1, 'lg' => 2])->schema($pair)
                : Fieldset::make($slot['label'])->columns(['default' => 1, 'lg' => 2])->schema($pair);
        }

        return Section::make($label)->key("english-{$field}")->compact()
            ->description(fn () => self::hint($record, $field))
            ->afterHeader([
                Text::make(fn () => self::statusLabel(self::row($record, $field)))->badge()->color(fn () => self::statusColor(self::row($record, $field))),
                self::markReviewedAction($record, $field),
                self::retranslateAction($record, $field, $label),
            ])
            ->schema($rows);
    }

    private static function hint(Model $record, string $field): ?string
    {
        $row = self::row($record, $field);

        return match (true) {
            $row === null => 'Le site affiche le français tant que ce texte n\'est pas traduit.',
            $row->status === TranslationStatus::FAILED => 'La traduction automatique a échoué : le site affiche le français. Saisissez le texte anglais ou cliquez sur « Retraduire ».',
            $row->source_hash !== $record->sourceHash($field) => 'Le texte français a changé depuis cette traduction : relisez-la.',
            default => null,
        };
    }

    /** @param array{name: string, input: string} $slot */
    private static function input(array $slot): Field
    {
        return match ($slot['input']) {
            'rich' => TypographyPlugin::editor($slot['name']),
            'rich_images' => TypographyPlugin::editor($slot['name'], images: true)->fileAttachmentsDisk('public')->fileAttachmentsDirectory('news/attachments'),
            'textarea' => Textarea::make($slot['name'])->rows(3)->autosize()->maxLength(self::LONG_MAX),
            default => TextInput::make($slot['name'])->maxLength(self::TEXT_MAX),
        };
    }

    private static function markReviewedAction(Model $record, string $field): Action
    {
        return Action::make('markReviewed')->label('Marquer comme relue')->icon(Heroicon::OutlinedCheck)->color('success')->size('sm')
            ->visible(function () use ($record, $field) {
                $row = self::row($record, $field);

                return self::canManage() && $row?->value !== null && $row->status !== TranslationStatus::REVIEWED;
            })
            ->action(function () use ($record, $field) {
                abort_unless(self::canManage(), 403);
                self::row($record, $field)?->update([
                    'status' => TranslationStatus::REVIEWED, 'source_hash' => $record->sourceHash($field),
                    'reviewed_at' => now(), 'reviewed_by' => auth()->id(), 'previous_value' => null,
                ]);
                $record->unsetRelation('translations');
                Notification::make()->title('Traduction marquée comme relue.')->success()->send();
            });
    }

    private static function retranslateAction(Model $record, string $field, string $label): Action
    {
        $available = fn () => app(Translator::class)->isAvailable();

        return Action::make('retranslate')->label('Retraduire')->icon(Heroicon::OutlinedArrowPath)->color('gray')->size('sm')
            ->visible(fn () => self::canManage())
            ->disabled(fn () => ! $available())
            ->tooltip(fn () => $available() ? null : 'Traduction automatique non configurée')
            ->requiresConfirmation()
            ->modalHeading("Retraduire « {$label} » ?")
            ->modalDescription('Une nouvelle traduction automatique remplacera le texte anglais dans quelques minutes. Un texte déjà relu restera visible comme « Ancienne version relue ».')
            ->modalSubmitActionLabel('Retraduire')
            ->action(function () use ($record, $field, $available) {
                abort_unless(self::canManage(), 403);
                if (! $available()) {
                    Notification::make()->title('Traduction automatique non configurée')->warning()->send();

                    return;
                }
                // Empreinte effacée : le champ redevient « à traduire » pour la tâche.
                self::row($record, $field)?->update(['source_hash' => null]);
                $record->unsetRelation('translations');
                TranslateRecord::dispatch($record);
                Notification::make()->title('Traduction demandée')->body('Le texte anglais sera mis à jour dans quelques minutes.')->success()->send();
            });
    }

    // --- Données ------------------------------------------------------------------------------------

    private static function row(Model $record, string $field): ?Translation
    {
        return $record->loadMissing('translations')->translation($field, 'en');
    }

    /**
     * Cases à traduire d'un champ (français non vide uniquement).
     *
     * @return array<int, array{field: string, slot: string, name: string, label: ?string, french: string, html: bool, input: string}>
     */
    private static function slots(Model $record, string $field, string $kind): array
    {
        $french = $record->frenchValue($field);
        $slots = [];
        $add = function (string $slot, string $name, ?string $label, string $text, string $input) use ($field, &$slots) {
            $slots[] = ['field' => $field, 'slot' => $slot, 'name' => $name, 'label' => $label, 'french' => $text,
                'html' => in_array($input, ['rich', 'rich_images'], true), 'input' => $input];
        };

        switch ($kind) {
            case 'blocks':
                $blocks = is_array($french) ? $french : [];
                foreach (BlockTexts::keyed($blocks) as $key => $text) {
                    $add($key, 'blocks.'.self::slot($key), BlockTexts::describe($key, $blocks), $text, BlockTexts::isHtml($key, $text) ? 'rich' : 'textarea');
                }
                break;
            case 'list':
                foreach (is_array($french) ? $french : [] as $i => $item) {
                    if (is_string($item) && trim($item) !== '') {
                        $add((string) $i, "{$field}.{$i}", 'Élément '.((int) $i + 1), $item, 'text');
                    }
                }
                break;
            case 'seo':
                foreach (self::SEO_LABELS as $key => $label) {
                    if (is_string($french[$key] ?? null) && trim($french[$key]) !== '') {
                        $add($key, "{$field}.{$key}", $label, $french[$key], $key === 'title' ? 'text' : 'textarea');
                    }
                }
                break;
            default:
                if (is_scalar($french) && trim((string) $french) !== '') {
                    $add('', $field, null, (string) $french, $kind);
                }
        }

        return $slots;
    }

    /** Texte anglais d'une case, lu dans une valeur enregistrée (texte, ou JSON pour blocs, listes et SEO). */
    private static function slotValue(?string $stored, string $kind, string $slot): ?string
    {
        if ($stored === null) {
            return null;
        }
        if (! in_array($kind, ['blocks', 'list', 'seo'], true)) {
            return $stored;
        }
        $decoded = json_decode($stored, true);

        return is_array($decoded) && is_string($decoded[$slot] ?? null) ? $decoded[$slot] : null;
    }

    /** @return array<string, ?string> nom de case → texte anglais (en base, puis corrections en attente) */
    private static function values(Model $record): array
    {
        $pending = self::$pending[self::key($record)] ?? [];
        $values = [];
        foreach (self::fieldsFor($record) as $field => [, $kind]) {
            $row = self::row($record, $field);
            foreach (self::slots($record, $field, $kind) as $slot) {
                $values[$slot['name']] = $pending[$field][$slot['slot']] ?? self::slotValue($row?->value, $kind, $slot['slot']);
            }
        }

        return $values;
    }

    private static function formState(Model $record): array
    {
        $values = self::values($record);
        $state = ['loaded' => json_encode($values, JSON_UNESCAPED_UNICODE)];
        foreach ($values as $name => $value) {
            data_set($state, $name, $value);
        }

        return $state;
    }

    /** Relève les textes modifiés depuis le chargement du formulaire ; écrits par flush() après la fiche. */
    private static function collect(Group $component, Model $record): void
    {
        if (! self::canManage()) {
            return;
        }

        $raw = $component->getRawState();
        $loaded = json_decode(is_array($raw) ? (string) ($raw['loaded'] ?? '') : '', true) ?: [];
        $inputs = [];
        foreach ($component->getChildSchema()->getFlatFields(withHidden: true) as $input) {
            $inputs[$input->getName()] = $input;
        }

        $changes = [];
        foreach (self::fieldsFor($record) as $field => [, $kind]) {
            foreach (self::slots($record, $field, $kind) as $slot) {
                $input = $inputs[$slot['name']] ?? null;
                if (! $input || ! array_key_exists($slot['name'], $loaded)) {
                    continue;
                }
                $new = self::clean($input->getState());
                if ($new !== self::clean(self::normalize($input, $loaded[$slot['name']]))) {
                    $changes[$field][$slot['slot']] = $new;
                }
            }
        }

        if ($changes === []) {
            unset(self::$pending[self::key($record)]);
        } else {
            self::$pending[self::key($record)] = $changes;
        }
    }

    /** Enregistre les corrections relevées pour cette fiche (après l'enregistrement du français). */
    public static function flush(Model $record): void
    {
        $changes = self::$pending[self::key($record)] ?? [];
        unset(self::$pending[self::key($record)]);
        if ($changes === [] || ! self::canManage()) {
            return;
        }

        $fields = self::fieldsFor($record);
        foreach ($changes as $field => $slots) {
            DB::transaction(function () use ($record, $field, $slots, $fields) {
                $row = $record->translations()->where('field', $field)->where('locale', 'en')->lockForUpdate()->first();
                $value = self::merge($record, $field, $fields[$field][1], $row?->value, $slots);

                if ($value === null) {
                    $row?->delete();

                    return;
                }

                $attributes = [
                    'value' => $value, 'status' => TranslationStatus::REVIEWED, 'source_hash' => $record->sourceHash($field),
                    'reviewed_at' => now(), 'reviewed_by' => auth()->id(), 'previous_value' => null,
                ];
                $row ? $row->update($attributes) : $record->translations()->create(['field' => $field, 'locale' => 'en', ...$attributes]);
            });
        }

        $record->unsetRelation('translations');
    }

    /**
     * Nouvelle valeur enregistrée d'un champ, les corrections appliquées à la valeur en base ; null = plus
     * aucun texte anglais (la traduction est supprimée, le site revient au français).
     *
     * @param  array<string, string>  $slots
     */
    private static function merge(Model $record, string $field, string $kind, ?string $stored, array $slots): ?string
    {
        $json = fn (array $value) => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $decoded = $stored !== null ? json_decode($stored, true) : null;
        $decoded = is_array($decoded) ? $decoded : [];

        switch ($kind) {
            case 'blocks':
                foreach ($slots as $key => $text) {
                    if ($text === '') {
                        unset($decoded[$key]);
                    } else {
                        $decoded[$key] = $text;
                    }
                }

                return $decoded === [] ? null : $json($decoded);

            case 'seo':
                $french = $record->frenchValue($field);
                $french = is_array($french) ? $french : [];
                $english = [];
                foreach (array_keys(self::SEO_LABELS) as $key) {
                    $text = array_key_exists($key, $slots) ? $slots[$key] : ($decoded[$key] ?? null);
                    if (is_string($text) && trim($text) !== '' && $text !== ($french[$key] ?? null)) {
                        $english[$key] = $text;
                    }
                }

                return $english === [] ? null : $json(array_merge($french, $english));

            case 'list':
                $french = $record->frenchValue($field);
                $french = is_array($french) ? $french : [];
                $result = [];
                $translated = false;
                foreach ($french as $i => $item) {
                    $text = array_key_exists((string) $i, $slots) ? $slots[(string) $i] : ($decoded[$i] ?? null);
                    $has = is_string($text) && trim($text) !== '' && $text !== $item;
                    $translated = $translated || $has;
                    $result[] = $has ? $text : $item;
                }

                return $translated ? $json($result) : null;

            default:
                $text = $slots[''] ?? '';

                return $text === '' ? null : $text;
        }
    }

    /** Valeur chargée passée par les mêmes conversions que la saisie (l'éditeur réécrit le HTML). */
    private static function normalize(Field $input, mixed $value): mixed
    {
        if ($value === null || ! $input instanceof RichEditor) {
            return $value;
        }
        foreach ($input->getStateCasts() as $cast) {
            $value = $cast->set($value);
        }
        foreach ($input->getStateCasts() as $cast) {
            $value = $cast->get($value);
        }

        return $value;
    }

    /** Texte saisi : espaces retirés, éditeur vide (« <p></p> ») = vide. */
    private static function clean(mixed $value): string
    {
        $value = is_string($value) ? trim($value) : '';
        if ($value !== '' && trim(html_entity_decode(strip_tags($value))) === '' && ! str_contains($value, '<img')) {
            return '';
        }

        return $value;
    }

    private static function key(Model $record): string
    {
        return $record->getMorphClass().':'.$record->getKey();
    }
}
