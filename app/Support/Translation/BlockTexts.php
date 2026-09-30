<?php

namespace App\Support\Translation;

/**
 * Textes des blocs de page (spec R2 §2.3) : extraction des seules feuilles texte et réapplication.
 *
 * Blocs = liste de {type, data} produite par le Builder de App\Filament\Support\PageBlocks.
 * Une feuille est retenue si sa clé figure dans la liste fermée TEXT_KEYS (ou finit par `_alt`),
 * à n'importe quelle profondeur de `data`, et si sa valeur est une chaîne non vide (hors nombres
 * purs comme « 5 »). `words` (liste de chaînes) donne une entrée par mot. Tout le reste (liens,
 * images, fichiers, couleurs, réglages, identifiants, nombres, booléens, clés inconnues) n'est
 * jamais extrait ni modifié.
 *
 * Deux formes de chemins :
 *  - extract()/apply() : chemin pointé absolu, ex. `0.data.title`, `2.data.slides.1.title` ;
 *  - keyed()/applyKeyed() : clé stable `{type}#{rang}:{chemin relatif dans data}`, ex. `hero#0:title`,
 *    où le rang est la position (0…) du bloc parmi les blocs du MÊME type de la page. Ajouter ou retirer
 *    un bloc d'un autre type ne décale rien ; ajouter/retirer/déplacer un bloc du même type avant lui
 *    décale les rangs (les textes anglais suivent alors le rang, limite acceptée).
 */
final class BlockTexts
{
    /** Liste fermée des clés texte (spec §2.3). Toute clé finissant par `_alt` est aussi un texte. */
    public const TEXT_KEYS = [
        'title', 'subtitle', 'eyebrow', 'text', 'body', 'caption', 'label', 'link_label', 'intro',
        'question', 'answer', 'description', 'credits', 'value', 'highlight', 'quote', 'author_role', 'role',
    ];

    /** Clé dont la valeur est une liste de chaînes, une entrée par élément. */
    public const LIST_KEYS = ['words'];

    /** Libellés des blocs (repris de PageBlocks). */
    public const BLOCK_LABELS = [
        'hero' => 'Grand titre (héros)', 'marquee' => 'Bandeau défilant', 'text' => 'Texte',
        'text_image' => 'Texte et image', 'venue' => 'Le lieu (Grand Théâtre)', 'equipment' => 'Le matériel',
        'gallery' => 'Galerie photos', 'video' => 'Vidéo', 'audio' => 'Écoute (sons)', 'stats' => 'Chiffres clés',
        'quote' => 'Citation / témoignage', 'cta' => 'Appel à l\'action', 'cards' => 'Cartes (piliers, avantages)',
        'timeline' => 'Chronologie / étapes', 'faq' => 'Questions fréquentes', 'programs' => 'Liste de formations',
        'artworks' => 'Réalisations des étudiants', 'rooms' => 'Univers de l\'école', 'news' => 'Dernières actualités',
        'partners' => 'Partenaires', 'professional_space' => 'Encart Espace Professionnels',
        'contact' => 'Formulaire de contact et coordonnées', 'ecosystem' => 'Écosystème (nos activités)',
        'services' => 'Services et tarifs', 'productions' => 'Productions du studio (écoute)',
        'agenda' => 'Agenda ou références', 'booking_form' => 'Formulaire de demande (devis, réservation)',
        'places' => 'Nos lieux (adresses)', 'campuses' => 'Nos campus (Dakar, Saint-Louis)',
        'domains' => 'Nos trois maisons (triptyque)', 'campus_programs' => 'Formations de ce campus',
        'downloads' => 'Documents à télécharger', 'support_form' => 'Nous soutenir (formulaire)',
    ];

    /** Libellés d'un élément de répéteur (au singulier). */
    private const ITEM_LABELS = [
        'slides' => 'Diapositive', 'facts' => 'Chiffre clé', 'hotspots' => 'Point sur la photo', 'tracks' => 'Morceau',
        'buttons' => 'Bouton', 'panels' => 'Panneau', 'items' => 'Élément', 'steps' => 'Étape', 'images' => 'Image',
        'files' => 'Document', 'groups' => 'Catégorie', 'words' => 'Mot qui défile',
    ];

    /** Libellés des champs ; `repeteur.champ` prime sur `champ`. */
    private const FIELD_LABELS = [
        'title' => 'Titre', 'subtitle' => 'Sous-titre', 'eyebrow' => 'Surtitre', 'text' => 'Texte', 'body' => 'Texte',
        'caption' => 'Légende', 'label' => 'Libellé', 'link_label' => 'Texte du lien', 'intro' => 'Phrase d\'intention',
        'question' => 'Question', 'answer' => 'Réponse', 'description' => 'Description', 'credits' => 'Crédit',
        'value' => 'Valeur', 'highlight' => 'Mot(s) en couleur', 'quote' => 'Citation', 'author_role' => 'Fonction',
        'role' => 'Fonction',
        'panels.label' => 'Texte du lien', 'buttons.label' => 'Texte', 'hotspots.label' => 'Texte',
        'quote.text' => 'Citation',
    ];

    /** @return array<string, string> chemin pointé absolu → texte */
    public static function extract(array $blocks): array
    {
        $out = [];
        foreach ($blocks as $i => $block) {
            if (is_array($block) && is_array($block['data'] ?? null)) {
                foreach (self::walk($block['data']) as $path => $text) {
                    $out["{$i}.data.{$path}"] = $text;
                }
            }
        }

        return $out;
    }

    /** Remplace les feuilles texte existantes par les valeurs données ; chemins absents ou non texte ignorés. */
    public static function apply(array $blocks, array $texts): array
    {
        if ($texts === []) {
            return $blocks;
        }
        foreach (self::extract($blocks) as $path => $_) {
            if (array_key_exists($path, $texts) && is_string($texts[$path])) {
                self::set($blocks, explode('.', $path), $texts[$path]);
            }
        }

        return $blocks;
    }

    /** @return array<string, string> clé stable `{type}#{rang}:{chemin}` → texte */
    public static function keyed(array $blocks): array
    {
        $out = [];
        foreach (self::stableIds($blocks) as $i => $id) {
            foreach (self::walk($blocks[$i]['data']) as $path => $text) {
                $out["{$id}:{$path}"] = $text;
            }
        }

        return $out;
    }

    /** Réapplique des textes indexés par clé stable ; bloc disparu ou champ inexistant → ignoré (le français reste). */
    public static function applyKeyed(array $blocks, array $keyedTexts): array
    {
        if ($keyedTexts === []) {
            return $blocks;
        }
        $absolute = [];
        foreach (self::stableIds($blocks) as $i => $id) {
            foreach (self::walk($blocks[$i]['data']) as $path => $_) {
                $key = "{$id}:{$path}";
                if (array_key_exists($key, $keyedTexts)) {
                    $absolute["{$i}.data.{$path}"] = $keyedTexts[$key];
                }
            }
        }

        return self::apply($blocks, $absolute);
    }

    /** Champ à envoyer à DeepL avec tag_handling=html : `body`, ou valeur contenant une balise. */
    public static function isHtml(string $key, string $value): bool
    {
        $segments = preg_split('/[.:]/', $key);

        return end($segments) === 'body' || preg_match('/<[a-z][\s\S]*>/i', $value) === 1;
    }

    /**
     * Libellé lisible d'une clé stable pour l'admin, ex. « Bloc 1 · Grand titre (héros) · Diapositive 2 · Titre ».
     * Libellés de blocs repris de PageBlocks (sinon le type) ; champs via FIELD_LABELS (sinon le nom du champ).
     */
    public static function describe(string $key, array $blocks): string
    {
        if (! preg_match('/^([a-z0-9_]+)#(\d+):(.+)$/', $key, $m)) {
            return $key;
        }
        [, $type, $rank, $path] = $m;

        $position = array_search("{$type}#{$rank}", self::stableIds($blocks), true);
        $parts = [$position === false ? 'Bloc supprimé' : 'Bloc '.(array_search($position, array_keys($blocks), true) + 1)];
        $parts[] = self::BLOCK_LABELS[$type] ?? $type;

        $segments = explode('.', $path);
        $parent = null;
        foreach ($segments as $n => $segment) {
            if (ctype_digit($segment)) {
                $parts[] = (self::ITEM_LABELS[$parent] ?? 'Élément').' '.((int) $segment + 1);

                continue;
            }
            $isLeaf = $n === count($segments) - 1;
            if ($isLeaf) {
                $parts[] = self::fieldLabel($segment, $parent, $type);
            }
            $parent = $segment;
        }

        return implode(' · ', $parts);
    }

    private static function fieldLabel(string $field, ?string $parent, string $type): string
    {
        if (str_ends_with($field, '_alt')) {
            return 'Description de l\'image';
        }

        return self::FIELD_LABELS["{$parent}.{$field}"] ?? self::FIELD_LABELS["{$type}.{$field}"] ?? self::FIELD_LABELS[$field] ?? $field;
    }

    /** @return array<int|string, string> index du bloc → `{type}#{rang}` */
    private static function stableIds(array $blocks): array
    {
        $ids = [];
        $ranks = [];
        foreach ($blocks as $i => $block) {
            if (! is_array($block) || ! is_string($block['type'] ?? null) || ! is_array($block['data'] ?? null)) {
                continue;
            }
            $type = $block['type'];
            $ranks[$type] = ($ranks[$type] ?? -1) + 1;
            $ids[$i] = "{$type}#{$ranks[$type]}";
        }

        return $ids;
    }

    /** @return array<string, string> chemin relatif → texte */
    private static function walk(array $data, string $prefix = ''): array
    {
        $out = [];
        foreach ($data as $key => $value) {
            $path = $prefix.$key;
            if (is_string($key) && in_array($key, self::LIST_KEYS, true) && is_array($value)) {
                foreach ($value as $n => $word) {
                    if (self::isText($word)) {
                        $out["{$path}.{$n}"] = $word;
                    }
                }
            } elseif (is_array($value)) {
                $out += self::walk($value, "{$path}.");
            } elseif (is_string($key) && self::isTextKey($key) && self::isText($value)) {
                $out[$path] = $value;
            }
        }

        return $out;
    }

    private static function isTextKey(string $key): bool
    {
        return in_array($key, self::TEXT_KEYS, true) || str_ends_with($key, '_alt');
    }

    private static function isText(mixed $value): bool
    {
        return is_string($value) && trim($value) !== '' && ! is_numeric(trim($value));
    }

    private static function set(array &$array, array $segments, string $value): void
    {
        $ref = &$array;
        foreach ($segments as $segment) {
            $ref = &$ref[$segment];
        }
        $ref = $value;
    }
}
