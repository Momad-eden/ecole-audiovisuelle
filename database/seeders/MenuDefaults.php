<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Page;

/** Contenu par défaut du menu principal, partagé par le ContentSeeder et la migration du groupe « À propos ». */
final class MenuDefaults
{
    public const ABOUT_LABEL = 'À propos';

    /** Sous-menu « À propos » : libellé, adresse. */
    public const ABOUT_LINKS = [
        ['Mission et impact', '/mission'],
        ['Partenaires et soutiens', '/partenaires'],
        ['Nous soutenir', '/soutenir'],
        ['Actualités', '/actualites'],
        ['Presse', '/presse'],
        ['Contact', '/contact'],
    ];

    /** Phrase affichée sous chaque lien des sous-menus, par adresse. */
    public const DESCRIPTIONS = [
        '/centre-culturel' => 'Le lieu, son projet et ses activités',
        '/centre-culturel/agenda' => 'Concerts, résidences et rendez-vous à venir',
        '/centre-culturel/studio' => 'Enregistrer, mixer et produire dans un studio équipé',
        '/centre-culturel/espaces' => 'Les salles et lieux du Centre culturel',
        '/emsi' => 'L\'école des métiers du son et de l\'image',
        '/emsi/dakar' => 'Formations et vie du campus de Dakar',
        '/emsi/saint-louis' => 'Formations et vie du campus de Saint-Louis',
        '/emsi/formations' => 'Toutes les formations, campus par campus',
        '/emsi/professionnels' => 'Faire reconnaître son expérience, se perfectionner',
        '/emsi/realisations' => 'Films, sons et créations des étudiants',
        '/mission' => 'Pourquoi nous existons et ce que nous changeons',
        '/partenaires' => 'Les institutions et entreprises à nos côtés',
        '/soutenir' => 'Mécénat, dons et partenariats',
        '/actualites' => 'Les dernières nouvelles du Centre culturel et de l\'EMSI',
        '/presse' => 'Communiqués, dossier de presse et contacts médias',
        '/contact' => 'Nous écrire, nous appeler, nous trouver',
    ];

    /** Phrase d'accroche d'une rubrique du menu principal (panneau du méga-menu), par adresse. */
    public const PARENT_DESCRIPTIONS = [
        '/centre-culturel' => 'Un centre culturel à Saint-Louis : concerts, résidences, ateliers et transmission.',
        '/emsi' => 'L\'école des métiers du son, de l\'image et de la scène, à Dakar et à Saint-Louis.',
        '/mission' => 'Notre mission, celles et ceux qui nous accompagnent, et comment nous rejoindre.',
    ];

    /**
     * Rubriques du menu principal : phrase d'accroche et photo, si elles sont vides. La photo vient du panneau
     * du triptyque de l'accueil qui mène à la même adresse.
     */
    public static function fillParents(): void
    {
        $blocks = Page::where('slug', 'accueil')->value('blocks');
        $blocks = is_string($blocks) ? json_decode($blocks, true) : $blocks;
        $images = [];
        foreach ((array) $blocks as $block) {
            if (($block['type'] ?? null) === 'domains') {
                foreach ($block['data']['panels'] ?? [] as $panel) {
                    if (! empty($panel['image']) && ! empty($panel['url'])) {
                        $images[$panel['url']] ??= $panel['image'];
                    }
                }
            }
        }

        MenuItem::where('location', 'main')->whereNull('parent_id')->where('is_button', false)->has('children')->get()
            ->each(function (MenuItem $item) use ($images) {
                $item->fill([
                    'description' => $item->description ?? (self::PARENT_DESCRIPTIONS[$item->url] ?? null),
                    'image' => $item->image ?? ($images[$item->url] ?? null),
                ]);
                if ($item->isDirty()) {
                    $item->save();
                }
            });
    }

    /** Crée le groupe « À propos » du menu principal s'il n'existe pas (placé après les autres entrées). */
    public static function addAboutGroup(): void
    {
        if (MenuItem::where('location', 'main')->whereNull('parent_id')->where('label', self::ABOUT_LABEL)->exists()) {
            return;
        }

        $position = (int) MenuItem::where('location', 'main')->whereNull('parent_id')->where('is_visible', true)->max('position') + 1;
        $parent = MenuItem::create(['location' => 'main', 'label' => self::ABOUT_LABEL, 'url' => self::ABOUT_LINKS[0][1],
            'is_button' => false, 'position' => $position, 'is_visible' => true]);
        foreach (self::ABOUT_LINKS as $childPosition => [$label, $url]) {
            MenuItem::create(['location' => 'main', 'parent_id' => $parent->id, 'label' => $label, 'url' => $url,
                'is_button' => false, 'position' => $childPosition, 'is_visible' => true]);
        }
    }

    /** Remplit les descriptions vides des liens de sous-menu (celles écrites par l'équipe sont gardées). */
    public static function fillDescriptions(): void
    {
        MenuItem::where('location', 'main')->whereNotNull('parent_id')->whereNull('description')->get()
            ->each(function (MenuItem $item) {
                if (isset(self::DESCRIPTIONS[$item->url])) {
                    $item->update(['description' => self::DESCRIPTIONS[$item->url]]);
                }
            });
    }
}
