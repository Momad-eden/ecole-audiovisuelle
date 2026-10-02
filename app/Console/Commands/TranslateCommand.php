<?php

namespace App\Console\Commands;

use App\Jobs\TranslateRecord;
use App\Models\AgendaEvent;
use App\Models\Artwork;
use App\Models\Faq;
use App\Models\MenuItem;
use App\Models\News;
use App\Models\Page;
use App\Models\Place;
use App\Models\Program;
use App\Models\Room;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Track;
use App\Services\Translation\DeepLGlossary;
use App\Services\Translation\TranslationQuota;
use App\Services\Translation\Translator;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

/**
 * Traduction de lancement : met en file les fiches publiées dont l'anglais manque ou est périmé,
 * dans l'ordre d'importance, en comptant d'abord les caractères face au quota DeepL gratuit.
 */
class TranslateCommand extends Command
{
    protected $signature = 'emsi:translate
        {--all : Toutes les fiches publiées à traduire (comportement par défaut)}
        {--model= : Un seul type de fiche (page, program, news… ou son nom français)}
        {--dry-run : Compter seulement, sans rien mettre en file}
        {--force : Ne pas demander de confirmation}';

    protected $description = 'Met en file la traduction anglaise des fiches publiées (dans la limite gratuite de DeepL)';

    /** Dans l'ordre de traitement : alias => [libellé, requête du contenu publié]. */
    private function sources(): array
    {
        return [
            'page' => ['Pages', fn () => Page::query()->published()],
            'program' => ['Formations', fn () => Program::query()->published()],
            'track' => ['Filières', fn () => Track::query()->where('is_active', true)],
            'room' => ['Univers', fn () => Room::query()->published()],
            'menu_item' => ['Menus', fn () => MenuItem::query()->where('is_visible', true)],
            'place' => ['Lieux', fn () => Place::query()->published()],
            'service' => ['Services', fn () => Service::query()->published()],
            'artwork' => ['Réalisations', fn () => Artwork::query()->published()],
            'agenda_event' => ['Agenda', fn () => AgendaEvent::query()->published()],
            'faq' => ['FAQ', fn () => Faq::query()->where('is_visible', true)],
            'news' => ['Actualités', fn () => News::query()->published()->orderByDesc('published_at')],
            'setting' => ['Réglages', fn () => Setting::query()->whereKey(Setting::current()->getKey())],
        ];
    }

    public function handle(Translator $translator): int
    {
        $sources = $this->sources();
        if ($this->option('model') !== null) {
            $alias = $this->resolveAlias((string) $this->option('model'), $sources);
            if ($alias === null) {
                $this->error('Modèle inconnu : « '.$this->option('model').' ».');
                $this->line('Valeurs possibles : '.collect($sources)->map(fn ($s, $a) => "$a ($s[0])")->implode(', ').'.');

                return self::FAILURE;
            }
            $sources = [$alias => $sources[$alias]];
        }

        /** @var Collection<int, Model> $queue */
        $queue = collect();
        $total = 0;
        foreach ($sources as [$label, $query]) {
            $count = 0;
            $characters = 0;
            /** @var Builder $builder */
            $builder = $query();
            foreach ($builder->when($builder->getQuery()->orders === null, fn (Builder $q) => $q->orderBy($q->getModel()->getQualifiedKeyName()))->get() as $record) {
                $chars = array_sum(array_map('mb_strlen', TranslateRecord::pendingTexts($record)));
                if ($chars === 0 && $record->outdatedFields('en') === []) {
                    continue;
                }
                $queue->push($record);
                $count++;
                $characters += $chars;
            }
            $total += $characters;
            if ($count > 0) {
                $this->line("$label : $count ".($count > 1 ? 'fiches' : 'fiche').', '.$this->number($characters).' caractères');
            }
        }

        $this->line('Total : '.$queue->count().' '.($queue->count() > 1 ? 'fiches' : 'fiche').', '.$this->number($total).' caractères');

        if (! $translator->isAvailable()) {
            $this->warn('Traduction automatique non configurée : ajoutez DEEPL_API_KEY ou traduisez à la main dans l\'onglet Anglais.');

            return self::SUCCESS;
        }

        $remaining = $this->showQuota();
        if ($remaining !== null && $total > $remaining) {
            $this->line('Au-delà du quota restant, la traduction reprendra automatiquement dès qu\'il se renouvelle.');
        }

        if ($this->option('dry-run') || $queue->isEmpty()) {
            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Mettre en file {$queue->count()} fiches à traduire ?")) {
            $this->line('Rien n\'a été mis en file.');

            return self::SUCCESS;
        }

        $this->syncGlossary();
        foreach ($queue as $record) {
            TranslateRecord::dispatch($record);
        }
        $this->info("{$queue->count()} fiches mises en file de traduction.");

        return self::SUCCESS;
    }

    /** @return int|null caractères restants, null si la consommation est illisible */
    private function showQuota(): ?int
    {
        try {
            ['used' => $used, 'limit' => $limit] = app(TranslationQuota::class)->usage();
        } catch (Throwable) {
            $this->line('Quota indisponible pour le moment');

            return null;
        }
        $remaining = max(0, $limit - $used);
        $this->line("Quota DeepL du mois : utilisés {$this->number($used)} / {$this->number($limit)} (reste {$this->number($remaining)})");

        return $remaining;
    }

    /** Lexique enregistré sans clé : envoyé une fois avant la première mise en file (échec = simple avertissement). */
    private function syncGlossary(): void
    {
        $setting = Setting::current();
        if ($setting->deepl_glossary_id || empty($setting->translation_glossary)) {
            return;
        }

        try {
            app(DeepLGlossary::class)->sync($setting->translation_glossary);
        } catch (Throwable $e) {
            $this->warn('Lexique non envoyé à DeepL : '.$e->getMessage());
        }
    }

    private function resolveAlias(string $input, array $sources): ?string
    {
        $norm = fn (string $s) => Str::of(Str::ascii($s))->lower()->replaceMatches('/[^a-z]/', '')->toString();
        $wanted = $norm($input);
        foreach ($sources as $alias => [$label]) {
            if (in_array($wanted, [$norm($alias), $norm($label), $norm(Str::plural($alias))], true)) {
                return $alias;
            }
        }

        return null;
    }

    private function number(int $n): string
    {
        return number_format($n, 0, ',', ' ');
    }
}
