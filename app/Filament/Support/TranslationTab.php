<?php

namespace App\Filament\Support;

use App\Enums\TranslationStatus;
use App\Filament\Support\RichText\TypographyPlugin;
use App\Jobs\TranslateRecord;
use App\Models\Translation;
use App\Services\Translation\Translator;
use App\Support\Translation\BlockTexts;
use App\Support\Translation\TranslationLeaves;
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
use WeakMap;

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
 * aucune retraduction automatique du texte qui vient d'être relu. Vider un texte anglais rend le français.
 *
 * Les corrections relevées sont rattachées à l'instance de la page Livewire (WeakMap) : elles disparaissent
 * avec la requête, et un enregistrement interrompu ne peut jamais être écrit plus tard par quelqu'un d'autre.
 *
 * Champs structurés (blocs, listes, SEO) : état par texte (TranslationLeaves) ; seuls les textes corrigés
 * passent en « Relue », les autres gardent leur état.
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
        'menu_item' => ['label' => ['Texte du lien', 'text'], 'description' => ['Description (sous-menu)', 'text']],
        'setting' => [
            'description' => ['Présentation courte (pied de page)', 'textarea'], 'opening_hours' => ['Horaires d\'accueil', 'text'],
            'seo_title' => ['Titre du site dans Google', 'text'], 'seo_description' => ['Description dans Google', 'textarea'],
        ],
    ];

    private const SEO_LABELS = ['title' => 'Titre affiché dans Google', 'description' => 'Description affichée dans Google'];

    /** @var WeakMap<object, array<string, array<string, array<string, string>>>>|null page → fiche → champ → case → texte saisi */
    private static ?WeakMap $pending = null;

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
                ->loadStateFromRelationshipsUsing(fn (Group $component) => $component->state(self::formState($record, $component->getLivewire())))
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
        return self::colorOf($row?->status);
    }

    /** « 2 textes à relire sur 7 » pour un champ structuré ; null pour un champ simple. */
    public static function reviewSummary(Model $record, string $field, ?Translation $row): ?string
    {
        if (! $record->hasStructuredTranslation($field)) {
            return null;
        }
        [$count, $total] = TranslationLeaves::toReview($record, $field, $row);

        return match (true) {
            $total === 0 => null,
            $count === 0 => $total > 1 ? "{$total} textes relus" : '1 texte relu',
            default => $count.' '.($count > 1 ? 'textes' : 'texte').' à relire sur '.$total,
        };
    }

    /**
     * « Marquer comme relue » sans modification. Champ structuré : seuls les textes qui ont un anglais sont
     * marqués ; un texte sans anglais reste à traduire (le champ reste alors « à traduire » pour la tâche).
     */
    public static function markReviewed(Model $record, string $field): void
    {
        abort_unless(self::canManage(), 403);

        DB::transaction(function () use ($record, $field) {
            $row = $record->translations()->where('field', $field)->where('locale', 'en')->lockForUpdate()->first();
            if (! $row) {
                return;
            }
            $review = ['reviewed_at' => now(), 'reviewed_by' => auth()->id()];

            if (! $record->hasStructuredTranslation($field)) {
                $row->update(['status' => TranslationStatus::REVIEWED, 'source_hash' => $record->sourceHash($field), 'previous_value' => null, ...$review]);

                return;
            }

            $states = TranslationLeaves::states($record, $field, $row);
            $english = TranslationLeaves::english($record, $field, $row);
            $previous = TranslationLeaves::previous($row);
            foreach ($record->frenchLeaves($field) as $key => $text) {
                if (isset($english[$key])) {
                    $states[$key] = ['h' => $record::leafHash($text), 's' => TranslationStatus::REVIEWED->value];
                    unset($previous[$key]);
                }
            }
            TranslationLeaves::persist($record, $field, $row, $english, $states, $previous, [
                'source_hash' => self::sourceHashAfterReview($record, $field, $states, $row), ...$review,
            ]);
        });

        $record->unsetRelation('translations');
    }

    // --- Présentation -------------------------------------------------------------------------------

    private static function colorOf(?TranslationStatus $status): string
    {
        return match ($status) {
            TranslationStatus::REVIEWED => 'success',
            TranslationStatus::AUTO => 'warning',
            TranslationStatus::FAILED => 'danger',
            default => 'gray',
        };
    }

    private static function section(Model $record, string $field, string $label, string $kind): ?Section
    {
        $slots = self::slots($record, $field, $kind);
        if ($slots === []) {
            return $kind === 'blocks'
                ? Section::make($label)->compact()->schema([Text::make('Publiez la page : les textes de ses blocs pourront ensuite être traduits.')])
                : null;
        }

        $structured = $record->hasStructuredTranslation($field);
        $rows = [];
        foreach ($slots as $slot) {
            $pair = [];
            if ($structured) {
                $state = fn () => self::slotState($record, $field, $slot);
                $pair[] = Text::make(fn () => $state()[0])->badge()->color(fn () => $state()[1])->columnSpanFull();
            }
            $pair[] = TextEntry::make('fr_'.md5($slot['name']))->label('Français')->state($slot['french'])->html($slot['html'])
                ->extraAttributes(['class' => 'break-words']);
            $pair[] = self::input($slot)->label('Anglais');
            $previous = fn () => self::previousOf($record, $field, $slot['slot']);
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

    /** @return array{0: string, 1: string} libellé et couleur de l'état d'un texte d'un champ structuré */
    private static function slotState(Model $record, string $field, array $slot): array
    {
        $state = TranslationLeaves::states($record, $field, self::row($record, $field))[$slot['slot']] ?? null;
        $status = $state ? TranslationStatus::tryFrom($state['s']) : null;
        if (! $status) {
            return ['Pas encore traduit', 'gray'];
        }
        $outdated = $status !== TranslationStatus::FAILED && $state['h'] !== $record::leafHash($slot['french']);

        return [$status->label().($outdated ? ' · le français a changé' : ''), $outdated ? 'warning' : self::colorOf($status)];
    }

    private static function hint(Model $record, string $field): ?string
    {
        $row = self::row($record, $field);
        if ($summary = self::reviewSummary($record, $field, $row)) {
            return $row?->status === TranslationStatus::FAILED
                ? $summary.'. Des textes n\'ont pas pu être traduits : le site affiche leur français.'
                : $summary.'.';
        }

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
            ->visible(fn () => self::canManage() && self::canMarkReviewed($record, $field))
            ->action(function () use ($record, $field) {
                self::markReviewed($record, $field);
                Notification::make()->title('Traduction marquée comme relue.')->success()->send();
            });
    }

    public static function canMarkReviewed(Model $record, string $field): bool
    {
        $row = self::row($record, $field);
        if ($row?->value === null) {
            return false;
        }
        if (! $record->hasStructuredTranslation($field)) {
            return $row->status !== TranslationStatus::REVIEWED;
        }

        $states = TranslationLeaves::states($record, $field, $row);
        $french = $record->frenchLeaves($field);
        foreach (array_keys(TranslationLeaves::english($record, $field, $row)) as $key) {
            if (isset($french[$key]) && ($states[$key]['s'] !== TranslationStatus::REVIEWED->value || $states[$key]['h'] !== $record::leafHash($french[$key]))) {
                return true;
            }
        }

        return false;
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
                // Empreintes effacées : le champ (et chacun de ses textes) redevient « à traduire » pour la tâche.
                $row = self::row($record, $field);
                if ($row) {
                    $leaves = is_array($row->leaves) ? array_map(fn ($state) => ['h' => null, 's' => $state['s'] ?? 'auto'], $row->leaves) : null;
                    $row->update(['source_hash' => null, 'leaves' => $leaves]);
                }
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
        $slots = [];
        $add = function (string $slot, string $name, ?string $label, string $text, string $input) use ($field, &$slots) {
            $slots[] = ['field' => $field, 'slot' => $slot, 'name' => $name, 'label' => $label, 'french' => $text,
                'html' => in_array($input, ['rich', 'rich_images'], true), 'input' => $input];
        };

        if (! $record->hasStructuredTranslation($field)) {
            $french = $record->frenchValue($field);
            if (is_scalar($french) && ! $record::isBlankText((string) $french)) {
                $add('', $field, null, (string) $french, $kind);
            }

            return $slots;
        }

        $blocks = $kind === 'blocks' ? (array) $record->frenchValue($field) : [];
        foreach ($record->frenchLeaves($field) as $key => $text) {
            match ($kind) {
                'blocks' => $add($key, 'blocks.'.self::slot($key), BlockTexts::describe($key, $blocks), $text, BlockTexts::isHtml($key, $text) ? 'rich' : 'textarea'),
                'seo' => $add($key, "{$field}.{$key}", self::SEO_LABELS[$key] ?? $key, $text, $key === 'title' ? 'text' : 'textarea'),
                default => $add($key, "{$field}.{$key}", 'Élément '.((int) $key + 1), $text, 'text'),
            };
        }

        return $slots;
    }

    private static function englishOf(Model $record, string $field, string $slot): ?string
    {
        $row = self::row($record, $field);
        if (! $record->hasStructuredTranslation($field)) {
            return $row?->value;
        }

        return TranslationLeaves::english($record, $field, $row)[$slot] ?? null;
    }

    private static function previousOf(Model $record, string $field, string $slot): ?string
    {
        $row = self::row($record, $field);
        if (! $record->hasStructuredTranslation($field)) {
            return $row?->previous_value;
        }

        return TranslationLeaves::previous($row)[$slot] ?? null;
    }

    private static function pendingMap(): WeakMap
    {
        return self::$pending ??= new WeakMap;
    }

    /** @return array<string, ?string> nom de case → texte anglais (en base, puis corrections en attente de cette page) */
    private static function values(Model $record, ?object $livewire): array
    {
        $pending = $livewire ? (self::pendingMap()[$livewire][self::key($record)] ?? []) : [];
        $values = [];
        foreach (self::fieldsFor($record) as $field => [, $kind]) {
            foreach (self::slots($record, $field, $kind) as $slot) {
                $values[$slot['name']] = $pending[$field][$slot['slot']] ?? self::englishOf($record, $field, $slot['slot']);
            }
        }

        return $values;
    }

    private static function formState(Model $record, ?object $livewire): array
    {
        $values = self::values($record, $livewire);
        $state = ['loaded' => json_encode($values, JSON_UNESCAPED_UNICODE)];
        foreach ($values as $name => $value) {
            data_set($state, $name, $value);
        }

        return $state;
    }

    /** Relève les textes modifiés depuis le chargement du formulaire ; écrits par flush() après la fiche. */
    private static function collect(Group $component, Model $record): void
    {
        $livewire = $component->getLivewire();
        $all = self::pendingMap()[$livewire] ?? [];
        unset($all[self::key($record)]);

        if (self::canManage()) {
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
            if ($changes !== []) {
                $all[self::key($record)] = $changes;
            }
        }

        self::pendingMap()[$livewire] = $all;
    }

    /** Enregistre les corrections relevées par cette page pour cette fiche (après l'enregistrement du français). */
    public static function flush(Model $record, ?object $livewire): void
    {
        if (! $livewire) {
            return;
        }
        $all = self::pendingMap()[$livewire] ?? [];
        $changes = $all[self::key($record)] ?? [];
        unset($all[self::key($record)]);
        self::pendingMap()[$livewire] = $all;
        if ($changes === [] || ! self::canManage()) {
            return;
        }

        foreach ($changes as $field => $slots) {
            DB::transaction(function () use ($record, $field, $slots) {
                $row = $record->translations()->where('field', $field)->where('locale', 'en')->lockForUpdate()->first();
                $review = ['reviewed_at' => now(), 'reviewed_by' => auth()->id()];

                if ($record->hasStructuredTranslation($field)) {
                    self::flushLeaves($record, $field, $row, $slots, $review);

                    return;
                }

                $text = $slots[''] ?? '';
                if ($text === '') {
                    $row?->delete();

                    return;
                }
                $attributes = ['value' => $text, 'status' => TranslationStatus::REVIEWED, 'source_hash' => $record->sourceHash($field), 'previous_value' => null, ...$review];
                $row ? $row->update($attributes) : $record->translations()->create(['field' => $field, 'locale' => 'en', ...$attributes]);
            });
        }

        $record->unsetRelation('translations');
    }

    /**
     * Champ structuré : seuls les textes corrigés passent en « Relue » (avec l'empreinte de leur français) ;
     * un texte vidé perd son anglais. L'empreinte du champ n'avance que si plus aucun texte n'est à traduire.
     *
     * @param  array<string, string>  $slots
     */
    private static function flushLeaves(Model $record, string $field, ?Translation $row, array $slots, array $review): void
    {
        $french = $record->frenchLeaves($field);
        $states = TranslationLeaves::states($record, $field, $row);
        $english = TranslationLeaves::english($record, $field, $row);
        $previous = TranslationLeaves::previous($row);

        foreach ($slots as $key => $text) {
            $key = (string) $key;
            unset($previous[$key]);
            if ($text === '' || ! isset($french[$key])) {
                unset($english[$key], $states[$key]);

                continue;
            }
            $english[$key] = $text;
            $states[$key] = ['h' => $record::leafHash($french[$key]), 's' => TranslationStatus::REVIEWED->value];
        }

        TranslationLeaves::persist($record, $field, $row, $english, $states, $previous, [
            'source_hash' => self::sourceHashAfterReview($record, $field, $states, $row), ...$review,
        ]);
    }

    private static function sourceHashAfterReview(Model $record, string $field, array $states, ?Translation $row): ?string
    {
        return TranslationLeaves::pending($record, $field, $states) === [] ? $record->sourceHash($field) : $row?->source_hash;
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
