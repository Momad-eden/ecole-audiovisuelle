<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Nouvelle page d'accueil, préparée en BROUILLON : film d'ouverture, manifeste (phrase, chiffres, bandeau de photos), les trois lieux, l'agenda, la vitrine
 * photos et vidéos du centre, les actualités, l'appel au soutien et les partenaires.
 * Les blocs existants (trois lieux, actualités, partenaires, agenda) sont repris tels quels, les chiffres clés passent dans le manifeste ;
 * la version en ligne n'est jamais modifiée : l'équipe relit l'aperçu puis publie depuis l'admin.
 * Un brouillon en cours (différent de la version en ligne) n'est remplacé qu'avec --force.
 */
class HomeDraftCommand extends Command
{
    protected $signature = 'emsi:accueil-brouillon {--force : Remplacer un brouillon en cours}';

    protected $description = 'Prépare la nouvelle page d\'accueil en brouillon (la version en ligne ne change pas)';

    /** Photos proposées, dans l'ordre de préférence, si elles existent sur le disque public. */
    private const FILM_IMAGES = [
        'pages/01M3R5AKQTX1B0PBT018R88CEN.jpg', 'pages/01M3JAVXS0NXQ1AHV5KACB01GB.jpg',
        'gallery/noCgZAuj3m9nL5FlIggH578X9H9KsyBuEKgkbGPd.webp', 'pages/01M3PS2NY4YN4R4GEEDZTM5WC3.jpg',
    ];

    private const SHOWCASE = [
        ['pages/01M3R5AKQTX1B0PBT018R88CEN.jpg', 'Concert sur scène, lumières rouges', 'Sur scène', '/centre-culturel/agenda'],
        ['pages/01M3PS2NY4YN4R4GEEDZTM5WC3.jpg', 'Un ingénieur du son à la console du studio', 'Au studio', '/centre-culturel/studio'],
        ['gallery/noCgZAuj3m9nL5FlIggH578X9H9KsyBuEKgkbGPd.webp', 'Une caméra en tournage', 'En tournage', '/emsi/formations'],
        ['pages/01M3JAVXS0NXQ1AHV5KACB01GB.jpg', 'Console de mixage éclairée dans la pénombre', 'En régie', '/emsi/formations'],
        ['pages/01M3GNZZ4KEHA28Z0NA1XX9Q9X.jpg', 'Façade d\'un bâtiment aux couleurs du Sénégal', 'Nos lieux', null],
    ];

    public function handle(): int
    {
        $page = Page::where('slug', 'accueil')->first();
        if (! $page) {
            $this->error('Page d\'accueil introuvable.');

            return self::FAILURE;
        }

        if ($page->hasUnpublishedChanges() && ! $this->option('force')) {
            $this->warn('Un brouillon de l\'accueil est en cours (différent de la version en ligne) : rien n\'est remplacé. Relancez avec --force pour l\'écraser.');

            return self::FAILURE;
        }

        $current = collect($page->blocks ?? []);
        $find = fn (string $type) => $current->firstWhere('type', $type);
        $domains = $find('domains');
        $title = trim((string) ($domains['data']['intro'] ?? '')) ?: 'La culture comme héritage, l\'art comme métier';

        $blocks = array_values(array_filter([
            ['type' => 'hero', 'data' => [
                'layout' => 'film',
                'eyebrow' => 'Saint-Louis · Dakar — Sénégal',
                'title' => $title,
                'subtitle' => 'Un centre culturel, une école et un studio, réunis.',
                'images' => $this->existing(self::FILM_IMAGES),
                'video_loop' => null,
                'film_url' => null,
                'buttons' => [
                    ['label' => 'Découvrir le Centre culturel', 'url' => '/centre-culturel', 'style' => 'primary'],
                    ['label' => 'Candidater à l\'EMSI', 'url' => '/candidater', 'style' => 'secondary'],
                ],
            ]],
            ['type' => 'statement', 'data' => [
                'eyebrow' => 'Saint-Louis · Dakar',
                'text' => 'Un *centre culturel*, une *école* et un *studio* : trois lieux pour créer, apprendre et partager la culture, de Saint-Louis à Dakar.',
                'facts' => [...collect($find('stats')['data']['items'] ?? [])->map(fn ($i) => ['value' => $i['value'], 'label' => $i['label']])->take(3)->all(), ['value' => '3', 'label' => 'lieux']],
                'images' => $this->existing(self::FILM_IMAGES),
                'button_label' => 'Notre mission',
                'button_url' => '/mission',
            ]],
            $domains ? ['type' => 'domains', 'data' => [...$domains['data'], 'intro' => null]] : null,
            ['type' => 'agenda', 'data' => [...($find('agenda')['data'] ?? ['scope' => 'upcoming', 'activity' => null]), 'title' => 'À l\'affiche', 'limit' => 4]],
            ['type' => 'showcase', 'data' => [
                'eyebrow' => 'Le centre en images',
                'title' => 'Sur scène, au studio, en tournage',
                'text' => 'Concerts, résidences, enregistrements et tournages : la vie du Centre culturel et de l\'école.',
                'items' => array_values(array_filter(array_map(fn (array $item) => Storage::disk('public')->exists($item[0]) ? [
                    'image' => $item[0], 'image_alt' => $item[1], 'video' => null, 'caption' => $item[2], 'url' => $item[3],
                ] : null, self::SHOWCASE))),
                'button_label' => 'Voir la programmation',
                'button_url' => '/centre-culturel/agenda',
            ]],
            $find('news'),
            ['type' => 'cta', 'data' => [
                'title' => 'Soutenir la création, de Saint-Louis au monde',
                'text' => 'Mécènes, fondations, institutions : accompagnez la transmission des métiers de la culture au Sénégal.',
                'buttons' => [
                    ['label' => 'Nous soutenir', 'url' => '/soutenir', 'style' => 'primary'],
                    ['label' => 'Devenir partenaire', 'url' => '/partenaires', 'style' => 'secondary'],
                ],
            ]],
            $find('partners'),
        ]));

        $page->update(['draft_blocks' => $blocks]);
        $this->info('Nouvelle page d\'accueil prête en brouillon ('.count($blocks).' blocs). La version en ligne n\'a pas changé.');
        $this->line('Relisez l\'aperçu depuis l\'admin (Pages du site › Accueil), puis « Publier ».');

        return self::SUCCESS;
    }

    /** @return array<int, string> */
    private function existing(array $paths): array
    {
        return array_values(array_filter($paths, fn (string $path) => Storage::disk('public')->exists($path)));
    }
}
