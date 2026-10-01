<?php

namespace App\Filament\Support;

use App\Enums\Audience;
use App\Enums\BookingType;
use App\Enums\PlaceKind;
use App\Enums\SiteDomain;
use App\Filament\Forms\Components\HotspotPicker;
use App\Filament\Support\RichText\TypographyPlugin;
use App\Models\Faq;
use App\Models\Partner;
use App\Models\Place;
use App\Models\Room;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

/**
 * Catalogue des blocs de page. Chaque bloc a des champs limités (aucun HTML libre) ;
 * le site Next.js possède un composant par type de bloc (même nom).
 */
final class PageBlocks
{
    /** Aide sous les titres : un mot entre astérisques est mis en valeur (italique, couleur de la rubrique). */
    private const EMPHASIS_HELP = 'Pour mettre un mot en valeur (italique, en couleur), entourez-le d\'astérisques : l\'art comme *métier*.';

    /** @return array<int, Block> */
    public static function all(): array
    {
        return [
            self::hero(), self::marquee(), self::text(), self::textImage(), self::venue(), self::equipment(),
            self::gallery(), self::video(), self::audio(), self::stats(), self::quote(), self::cta(), self::cards(),
            self::timeline(), self::faq(), self::programs(), self::artworks(), self::rooms(), self::news(),
            self::partners(), self::professionalSpace(), self::contact(),
            self::ecosystem(), self::services(), self::productions(),
            self::agenda(), self::bookingForm(), self::places(), self::campuses(),
            self::domains(), self::campusPrograms(), self::downloads(), self::supportForm(), self::showcase(), self::statement(), self::institution(),
        ];
    }

    private static function ecosystem(): Block
    {
        return Block::make('ecosystem')->label('Écosystème (nos activités)')->icon('heroicon-o-globe-europe-africa')->schema([
            TextInput::make('eyebrow')->label('Surtitre')->maxLength(60),
            TextInput::make('title')->label('Titre')->required()->maxLength(90)->helperText(self::EMPHASIS_HELP),
            Textarea::make('text')->label('Texte')->rows(3)->maxLength(500),
            Repeater::make('items')->label('Activités')->minItems(1)->maxItems(6)->addActionLabel('Ajouter une activité')
                ->schema([
                    TextInput::make('name')->label('Nom')->required()->maxLength(60),
                    Select::make('activity')->label('Couleur / animation')->options(['school' => 'EMSI (école)', ...['studio' => 'Impact Live Studio', 'events' => 'Impact Live Events', 'space' => 'Centre culturel Habib Faye']])->required(),
                    Textarea::make('text')->label('Présentation courte')->rows(2)->maxLength(200),
                    LinkTargets::field('url', 'Lien'),
                    ...self::image('image', 'Photo (facultatif)'),
                ]),
        ]);
    }

    private static function services(): Block
    {
        return Block::make('services')->label('Services et tarifs')->icon('heroicon-o-wrench-screwdriver')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Nos services')->helperText(self::EMPHASIS_HELP),
            Textarea::make('text')->label('Texte')->rows(2)->maxLength(300),
            Select::make('activity')->label('Services de')->options(['studio' => 'Impact Live Studio', 'events' => 'Impact Live Events', 'space' => 'Centre culturel Habib Faye'])->required()->default('studio')
                ->helperText('Les services se gèrent dans Impact Live › Services.'),
        ]);
    }

    private static function productions(): Block
    {
        return Block::make('productions')->label('Productions du studio (écoute)')->icon('heroicon-o-musical-note')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Sorti de nos consoles')->helperText(self::EMPHASIS_HELP),
            TextInput::make('limit')->label('Nombre')->numeric()->minValue(1)->maxValue(24)->default(6)
                ->helperText('Réalisations marquées « Impact Live Studio » dans Univers & réalisations › Réalisations.'),
        ]);
    }

    private static function agenda(): Block
    {
        return Block::make('agenda')->label('Agenda ou références')->icon('heroicon-o-calendar-days')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->helperText(self::EMPHASIS_HELP),
            Radio::make('scope')->label('Afficher')->options(['upcoming' => 'Les prochains événements', 'references' => 'Nos références (prestations réalisées)'])->default('upcoming')->inline(),
            Select::make('activity')->label('Activité (facultatif)')->options(['school' => 'EMSI', ...['studio' => 'Impact Live Studio', 'events' => 'Impact Live Events', 'space' => 'Centre culturel Habib Faye']]),
            TextInput::make('limit')->label('Nombre')->numeric()->minValue(1)->maxValue(24)->default(6),
        ]);
    }

    private static function bookingForm(): Block
    {
        return Block::make('booking_form')->label('Formulaire de demande (devis, réservation)')->icon('heroicon-o-inbox-arrow-down')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Demander un devis')->helperText(self::EMPHASIS_HELP),
            Textarea::make('text')->label('Texte')->rows(2)->maxLength(300),
            Select::make('booking_type')->label('Type de demande')->options([
                BookingType::STUDIO_SESSION->value => BookingType::STUDIO_SESSION->getLabel(),
                BookingType::SPACE_RENTAL->value => BookingType::SPACE_RENTAL->getLabel(),
            ])->required()->default('studio_session')
                ->helperText('Les demandes arrivent dans Impact Live › Demandes.'),
        ]);
    }

    private static function campuses(): Block
    {
        return Block::make('campuses')->label('Nos campus (Dakar, Saint-Louis)')->icon('heroicon-o-academic-cap')->schema([
            TextInput::make('eyebrow')->label('Surtitre')->maxLength(60),
            TextInput::make('title')->label('Titre')->maxLength(90)->default('Choisissez votre campus')->helperText(self::EMPHASIS_HELP),
            Textarea::make('text')->label('Texte')->rows(2)->maxLength(300)
                ->helperText('Photos, accroches, points forts et adresses se gèrent dans Administration › Lieux.'),
        ]);
    }

    private static function places(): Block
    {
        return Block::make('places')->label('Nos lieux (adresses)')->icon('heroicon-o-map-pin')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Nous trouver')->helperText(self::EMPHASIS_HELP),
            Select::make('kind')->label('Lieux affichés')->options(PlaceKind::class)->placeholder('Tous les lieux'),
        ]);
    }

    private static function domains(): Block
    {
        return Block::make('domains')->label('Nos trois lieux (triptyque)')->icon('heroicon-o-view-columns')->schema([
            Repeater::make('panels')->label('Panneaux')->minItems(3)->maxItems(3)->defaultItems(3)
                ->addActionLabel('Ajouter un panneau')->reorderable()
                ->helperText('Trois grands panneaux côte à côte (empilés sur téléphone), un par domaine. Tout le panneau est cliquable.')
                ->schema([
                    Select::make('domain')->label('Domaine (donne la couleur)')->options(SiteDomain::class)->required(),
                    TextInput::make('eyebrow')->label('Surtitre')->maxLength(60)->placeholder('Ex. Centre culturel · Saint-Louis'),
                    TextInput::make('title')->label('Titre')->required()->maxLength(40),
                    Textarea::make('text')->label('Texte court')->rows(2)->maxLength(160),
                    ...self::image('image', 'Photo'),
                    LinkTargets::field('url', 'Lien')->required(),
                    TextInput::make('label')->label('Texte du lien')->maxLength(30)->default('Découvrir'),
                ]),
            Textarea::make('intro')->label('Phrase d\'intention (sous les panneaux)')->rows(2)->maxLength(160)
                ->placeholder('Ex. La culture comme héritage, l\'art comme métier'),
        ]);
    }

    private static function campusPrograms(): Block
    {
        return Block::make('campus_programs')->label('Formations de ce campus')->icon('heroicon-o-academic-cap')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Les formations de ce campus')->helperText(self::EMPHASIS_HELP),
            Select::make('campus_id')->label('Campus')->required()
                ->options(fn () => Place::published()->campuses()->orderBy('position')->pluck('name', 'id')->all())
                ->helperText('Affiche les formations ouvertes à la candidature dans ce campus, avec la prochaine rentrée. Formations et sessions se gèrent dans le menu Formations.'),
        ]);
    }

    private static function downloads(): Block
    {
        return Block::make('downloads')->label('Documents à télécharger')->icon('heroicon-o-arrow-down-tray')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Documents à télécharger')->helperText(self::EMPHASIS_HELP),
            Repeater::make('files')->label('Documents')->minItems(1)->maxItems(20)->addActionLabel('Ajouter un document')->reorderable()
                ->schema([
                    FileUpload::make('file')->label('Fichier PDF')->required()->disk('public')->directory('pages/documents')->visibility('public')
                        ->acceptedFileTypes(['application/pdf'])->maxSize(20480)->preserveFilenames()
                        ->helperText('PDF uniquement, 20 Mo maximum.'),
                    TextInput::make('title')->label('Titre')->required()->maxLength(90)->placeholder('Ex. Brochure des formations 2026'),
                    Textarea::make('description')->label('Description (facultatif)')->rows(2)->maxLength(200),
                ]),
        ]);
    }

    private static function supportForm(): Block
    {
        return Block::make('support_form')->label('Nous soutenir (formulaire)')->icon('heroicon-o-heart')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Nous soutenir')->helperText(self::EMPHASIS_HELP),
            Textarea::make('text')->label('Texte')->rows(3)->maxLength(400)
                ->helperText('Les messages arrivent dans Site › Messages reçus, avec le type de soutien choisi.'),
        ]);
    }

    private static function richText(string $name = 'body', string $label = 'Texte'): RichEditor
    {
        return TypographyPlugin::editor($name, $label);
    }

    private static function image(string $name = 'image', string $label = 'Image', bool $required = false): array
    {
        return [
            FileUpload::make($name)->label($label)->image()->disk('public')->directory('pages')
                ->imageEditor()->maxSize(8192)->required($required),
            TextInput::make("{$name}_alt")->label('Description de l\'image (accessibilité)')->maxLength(200)
                ->required(fn ($get) => filled($get($name))),
        ];
    }

    private static function audioUpload(string $name, string $label): FileUpload
    {
        return FileUpload::make($name)->label($label)
            ->disk('public')->directory('pages/audio')->visibility('public')
            ->acceptedFileTypes(['audio/mpeg', 'audio/mp4', 'audio/x-m4a', 'audio/wav', 'audio/x-wav', 'audio/ogg'])->maxSize(20480);
    }

    private static function buttons(int $max = 2): Repeater
    {
        return Repeater::make('buttons')->label('Boutons')->maxItems($max)->defaultItems(0)->columns(3)
            ->addActionLabel('Ajouter un bouton')
            ->schema([
                TextInput::make('label')->label('Texte')->required()->maxLength(30),
                LinkTargets::field('url', 'Lien')->required(),
                Select::make('style')->label('Style')->options(['primary' => 'Principal (lumineux)', 'secondary' => 'Discret'])->default('primary')->required(),
            ]);
    }

    private static function hero(): Block
    {
        return Block::make('hero')->label('Grand titre (héros)')->icon('heroicon-o-sparkles')->schema([
            TextInput::make('eyebrow')->label('Surtitre')->maxLength(60),
            TextInput::make('title')->label('Titre')->required()->maxLength(80)->helperText(self::EMPHASIS_HELP),
            Textarea::make('subtitle')->label('Sous-titre')->rows(2)->maxLength(200),
            ...self::image('image', 'Image de fond'),
            FileUpload::make('video_loop')->label('Boucle vidéo muette (facultatif)')->disk('public')->directory('pages/video')
                ->acceptedFileTypes(['video/mp4', 'video/webm'])->maxSize(20480)
                ->helperText('MP4 court et léger (moins de 20 Mo). Remplacé par l\'image si l\'internaute limite les animations.'),
            Radio::make('layout')->label('Mise en page')->options([
                'film' => 'Film (vidéo plein écran, grande phrase, idéal pour l\'accueil)',
                'masterpiece' => 'Œuvre d\'art (rubans de lumière interactifs, titre-image, cartel)',
                'projection' => 'Projection (photo de fond, rubans de lumière, cartel)',
                'cinema' => 'Cinéma (diaporama plein écran et chiffres clés)',
                'stage' => 'Scène animée (faisceaux de lumière)',
                'studio' => 'Studio (photo, matériel commenté et écoute)',
                'events' => 'Événementiel animé (sonorisation et lumières)',
                'spotlight' => 'Projecteur (titre centré sous une poursuite)',
                'editorial' => 'Éditorial (grand titre et portrait, façon magazine)',
                'poster' => 'Affiche de concert (titre géant sur aplat de couleur)',
                'mosaic' => 'Mosaïque (titre et collage de photos)',
                'compact' => 'Sobre (en-tête court, pages secondaires)',
                'full' => 'Plein écran',
                'split' => 'Texte et image côte à côte',
            ])->default('stage')->inline()->live(),
            TextInput::make('film_url')->label('Film complet (lien YouTube ou Vimeo, facultatif)')->url()
                ->regex('#^https?://(www\.)?(youtube\.com|youtu\.be|vimeo\.com)/#i')
                ->helperText('Ajoute un bouton « Voir le film » qui ouvre la vidéo avec le son. La boucle muette ci-dessus tourne en fond.')
                ->visible(fn ($get) => $get('layout') === 'film'),
            TagsInput::make('words')->label('Mots qui défilent à la fin du titre')->placeholder('Ex. le son')
                ->helperText('Scène animée uniquement : le titre se termine par ces mots, l\'un après l\'autre. Laissez vide pour un titre fixe.')
                ->visible(fn ($get) => in_array($get('layout'), ['stage', 'events'], true)),
            TextInput::make('highlight')->label('Mot(s) du titre à mettre en couleur')->maxLength(40)
                ->helperText('Doit figurer tel quel dans le titre.')
                ->visible(fn ($get) => in_array($get('layout'), ['projection', 'studio'], true)),
            self::audioUpload('sound', 'Son de l\'œuvre (facultatif)')
                ->helperText('Joué en boucle quand le visiteur clique « Écouter l\'œuvre » ; sa main le déplace entre les enceintes et le rend plus ou moins brillant. Idéal : une nappe ou un extrait de 20 à 60 secondes, qui boucle sans coupure (MP3, 20 Mo maximum). Sans fichier, un son synthétique suit la main.')
                ->visible(fn ($get) => in_array($get('layout'), ['masterpiece', 'projection'], true)),
            FileUpload::make('images')->label(fn ($get) => $get('layout') === 'film' ? 'Photos qui défilent (sans vidéo, 2 à 4)' : 'Photos de la mosaïque (3 ou 4)')
                ->image()->multiple()->reorderable()->maxFiles(4)
                ->disk('public')->directory('pages')->maxSize(8192)
                ->helperText(fn ($get) => $get('layout') === 'film' ? 'Utilisées tant qu\'aucune boucle vidéo n\'est déposée : elles se succèdent en fondu, avec un lent zoom. Grandes photos de préférence (au moins 1920 px de large).' : null)
                ->visible(fn ($get) => in_array($get('layout'), ['mosaic', 'film'], true)),
            TextInput::make('caption')->label('Légende de la photo')->maxLength(120)
                ->visible(fn ($get) => in_array($get('layout'), ['masterpiece', 'projection', 'editorial', 'mosaic', 'poster'], true))
                ->helperText('Œuvre d\'art et Projection : le titre du cartel de musée. Éditorial, mosaïque, affiche : la légende de la photo.'),
            HotspotPicker::make('hotspots')->label('Points sur la photo')->imageField('image')
                ->visible(fn ($get) => $get('layout') === 'studio'),
            Repeater::make('tracks')->label('Morceaux à écouter')->maxItems(3)->defaultItems(0)
                ->addActionLabel('Ajouter un morceau')
                ->helperText('Jusqu\'à trois productions du studio. Rien ne joue tout seul : le visiteur appuie sur lecture.')
                ->schema([
                    self::audioUpload('file', 'Fichier')->required(),
                    TextInput::make('title')->label('Titre')->required()->maxLength(60),
                    TextInput::make('credits')->label('Crédit (ex. « Mixé et masterisé ici »)')->maxLength(80),
                ])
                ->visible(fn ($get) => $get('layout') === 'studio'),
            Repeater::make('slides')->label('Diapositives')->minItems(2)->maxItems(5)->defaultItems(0)
                ->addActionLabel('Ajouter une diapositive')
                ->helperText('De 2 à 5 photos qui se succèdent toutes les 6 secondes, chacune avec son titre.')
                ->schema([
                    ...self::image('image', 'Photo', required: true),
                    TextInput::make('eyebrow')->label('Surtitre')->maxLength(60),
                    TextInput::make('title')->label('Titre')->required()->maxLength(80),
                    TextInput::make('link_label')->label('Texte du lien (facultatif)')->maxLength(30),
                    LinkTargets::field('link_url', 'Lien')->required(fn ($get) => filled($get('link_label'))),
                ])
                ->visible(fn ($get) => $get('layout') === 'cinema'),
            Repeater::make('facts')->label('Chiffres clés')->maxItems(4)->defaultItems(0)->columns(2)
                ->addActionLabel('Ajouter un chiffre')
                ->schema([
                    TextInput::make('value')->label('Valeur (ex. 5)')->required()->maxLength(12),
                    TextInput::make('label')->label('Libellé (ex. filières)')->required()->maxLength(40),
                ])
                ->visible(fn ($get) => $get('layout') === 'cinema'),
            ColorPicker::make('accent')->label('Couleur de lumière (facultatif)'),
            self::buttons(),
        ]);
    }

    private static function marquee(): Block
    {
        return Block::make('marquee')->label('Bandeau défilant')->icon('heroicon-o-arrows-right-left')->schema([
            TagsInput::make('words')->label('Mots')->required()->placeholder('Ex. Son')
                ->helperText('Quelques mots courts qui défilent en grand (ils restent immobiles si l\'internaute limite les animations).'),
        ]);
    }

    private static function venue(): Block
    {
        return Block::make('venue')->label('Le lieu (Grand Théâtre)')->icon('heroicon-o-building-office-2')->schema([
            TextInput::make('eyebrow')->label('Surtitre')->maxLength(60),
            TextInput::make('title')->label('Titre')->required()->maxLength(90)->helperText(self::EMPHASIS_HELP),
            Textarea::make('text')->label('Texte')->rows(3)->maxLength(500),
            ...self::image('image', 'Photo du lieu (facultatif)'),
            Repeater::make('facts')->label('Repères')->maxItems(4)->defaultItems(0)->columns(2)->addActionLabel('Ajouter un repère')
                ->schema([
                    TextInput::make('value')->label('Valeur')->required()->maxLength(14)->placeholder('Ex. 154 m²'),
                    TextInput::make('label')->label('Libellé')->required()->maxLength(60)->placeholder('Ex. de studio'),
                ]),
            self::buttons(1),
        ]);
    }

    private static function equipment(): Block
    {
        return Block::make('equipment')->label('Le matériel')->icon('heroicon-o-cpu-chip')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Sur quoi vous vous formez')->helperText(self::EMPHASIS_HELP),
            Textarea::make('text')->label('Texte')->rows(2)->maxLength(300),
            Repeater::make('groups')->label('Catégories')->minItems(1)->maxItems(8)->addActionLabel('Ajouter une catégorie')
                ->schema([
                    TextInput::make('category')->label('Catégorie')->required()->maxLength(40)->placeholder('Ex. Consoles son'),
                    TagsInput::make('items')->label('Matériel et logiciels')->required()->placeholder('Ex. DiGiCo'),
                    ...self::image('image', 'Photo (facultatif)'),
                ]),
        ]);
    }

    private static function text(): Block
    {
        return Block::make('text')->label('Texte')->icon('heroicon-o-document-text')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->helperText(self::EMPHASIS_HELP),
            self::richText()->required(),
        ]);
    }

    private static function textImage(): Block
    {
        return Block::make('text_image')->label('Texte et image')->icon('heroicon-o-photo')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->helperText(self::EMPHASIS_HELP),
            self::richText()->required(),
            ...self::image('image', 'Image', true),
            Radio::make('image_position')->label('Position de l\'image')->options(['left' => 'À gauche', 'right' => 'À droite'])->default('right')->inline(),
        ]);
    }

    private static function gallery(): Block
    {
        return Block::make('gallery')->label('Galerie photos')->icon('heroicon-o-squares-2x2')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->helperText(self::EMPHASIS_HELP),
            Repeater::make('images')->label('Images')->minItems(2)->maxItems(40)->grid(2)->addActionLabel('Ajouter une image')
                ->schema([
                    ...self::image('image', 'Image', true),
                    TextInput::make('caption')->label('Légende')->maxLength(140),
                ]),
            Select::make('layout')->label('Présentation')->options(['grid' => 'Grille', 'mosaic' => 'Mosaïque', 'carousel' => 'Carrousel'])->default('grid'),
        ]);
    }

    private static function video(): Block
    {
        return Block::make('video')->label('Vidéo')->icon('heroicon-o-film')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->helperText(self::EMPHASIS_HELP),
            TextInput::make('url')->label('Lien YouTube ou Vimeo')->required()->url()->regex('#^https?://(www\.)?(youtube\.com|youtu\.be|vimeo\.com)/#i'),
            ...self::image('poster', 'Image d\'aperçu (facultatif)'),
            TextInput::make('caption')->label('Légende')->maxLength(200),
            Textarea::make('transcript')->label('Transcription (accessibilité)')->rows(3),
        ]);
    }

    private static function audio(): Block
    {
        return Block::make('audio')->label('Écoute (sons)')->icon('heroicon-o-musical-note')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->helperText(self::EMPHASIS_HELP),
            Textarea::make('description')->label('Description')->rows(2)->maxLength(300),
            Repeater::make('tracks')->label('Pistes')->minItems(1)->maxItems(20)->addActionLabel('Ajouter une piste')->columns(2)
                ->schema([
                    TextInput::make('title')->label('Titre de la piste')->required()->maxLength(120),
                    TextInput::make('credits')->label('Crédits')->maxLength(160),
                    FileUpload::make('file')->label('Fichier son')->required()->disk('public')->directory('pages/audio')
                        ->acceptedFileTypes(['audio/mpeg', 'audio/mp4', 'audio/x-m4a', 'audio/wav', 'audio/x-wav', 'audio/ogg'])->maxSize(51200)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    private static function stats(): Block
    {
        return Block::make('stats')->label('Chiffres clés')->icon('heroicon-o-chart-bar')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->helperText(self::EMPHASIS_HELP),
            Repeater::make('items')->label('Chiffres')->minItems(2)->maxItems(6)->columns(3)->addActionLabel('Ajouter un chiffre')
                ->schema([
                    TextInput::make('value')->label('Valeur')->required()->maxLength(10)->placeholder('Ex. 154 m²'),
                    TextInput::make('label')->label('Libellé')->required()->maxLength(40),
                    TextInput::make('detail')->label('Précision')->maxLength(80),
                ]),
        ]);
    }

    private static function quote(): Block
    {
        return Block::make('quote')->label('Citation / témoignage')->icon('heroicon-o-chat-bubble-bottom-center-text')->schema([
            Textarea::make('text')->label('Citation')->required()->rows(3)->maxLength(400),
            TextInput::make('author')->label('Auteur')->maxLength(80),
            TextInput::make('role')->label('Fonction')->maxLength(100),
            ...self::image('photo', 'Photo (facultatif)'),
        ]);
    }

    private static function cta(): Block
    {
        return Block::make('cta')->label('Appel à l\'action')->icon('heroicon-o-cursor-arrow-rays')->schema([
            TextInput::make('title')->label('Titre')->required()->maxLength(80)->helperText(self::EMPHASIS_HELP),
            Textarea::make('text')->label('Texte')->rows(2)->maxLength(200),
            self::buttons()->minItems(1),
        ]);
    }

    private static function cards(): Block
    {
        return Block::make('cards')->label('Cartes (piliers, avantages)')->icon('heroicon-o-rectangle-group')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->helperText(self::EMPHASIS_HELP),
            Repeater::make('items')->label('Cartes')->minItems(2)->maxItems(6)->columns(2)->addActionLabel('Ajouter une carte')
                ->schema([
                    Select::make('icon')->label('Icône')->options(self::icons()),
                    TextInput::make('title')->label('Titre')->required()->maxLength(60),
                    Textarea::make('text')->label('Texte')->rows(2)->maxLength(200)->columnSpanFull(),
                    LinkTargets::field('url', 'Lien (facultatif)')->columnSpanFull(),
                ]),
        ]);
    }

    private static function timeline(): Block
    {
        return Block::make('timeline')->label('Chronologie / étapes')->icon('heroicon-o-calendar')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->helperText(self::EMPHASIS_HELP),
            Radio::make('layout')->label('Présentation')->options(['list' => 'Chronologie verticale', 'steps' => 'Étapes numérotées côte à côte'])->default('list')->inline(),
            Repeater::make('steps')->label('Étapes')->minItems(2)->maxItems(20)->columns(3)->addActionLabel('Ajouter une étape')
                ->schema([
                    TextInput::make('period')->label('Période')->required()->maxLength(40)->placeholder('Ex. Février 2027'),
                    TextInput::make('title')->label('Titre')->required()->maxLength(80),
                    TextInput::make('tag')->label('Étiquette')->maxLength(30)->placeholder('Ex. Volet 2'),
                    Textarea::make('text')->label('Texte')->rows(2)->maxLength(200)->columnSpanFull(),
                ]),
        ]);
    }

    private static function faq(): Block
    {
        return Block::make('faq')->label('Questions fréquentes')->icon('heroicon-o-question-mark-circle')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Questions fréquentes')->helperText(self::EMPHASIS_HELP),
            Select::make('group')->label('Groupe de questions')->options(Faq::GROUPS)->required()
                ->helperText('Les questions se gèrent dans Site › FAQ.'),
        ]);
    }

    private static function programs(): Block
    {
        return Block::make('programs')->label('Liste de formations')->icon('heroicon-o-academic-cap')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->helperText(self::EMPHASIS_HELP),
            Select::make('audience')->label('Public')->options(Audience::class)->required(),
            TextInput::make('limit')->label('Nombre maximum')->integer()->minValue(1)->maxValue(24)->default(6),
        ]);
    }

    private static function artworks(): Block
    {
        return Block::make('artworks')->label('Réalisations des étudiants')->icon('heroicon-o-photo')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->helperText(self::EMPHASIS_HELP),
            Radio::make('source')->label('Réalisations affichées')->options(['featured' => 'À la une', 'room' => 'D\'un univers', 'latest' => 'Les plus récentes'])->default('featured')->required()->live(),
            Select::make('room_id')->label('Univers')->options(fn () => Room::orderBy('position')->pluck('name', 'id')->all())
                ->visible(fn ($get) => $get('source') === 'room')->required(fn ($get) => $get('source') === 'room'),
            TextInput::make('limit')->label('Nombre maximum')->integer()->minValue(1)->maxValue(24)->default(6),
        ]);
    }

    private static function rooms(): Block
    {
        return Block::make('rooms')->label('Univers de l\'école')->icon('heroicon-o-building-library')->schema([
            TextInput::make('eyebrow')->label('Surtitre')->maxLength(60),
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Choisissez votre univers')->helperText(self::EMPHASIS_HELP),
            Textarea::make('text')->label('Texte')->rows(2)->maxLength(200),
        ]);
    }

    private static function news(): Block
    {
        return Block::make('news')->label('Dernières actualités')->icon('heroicon-o-newspaper')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Actualités')->helperText(self::EMPHASIS_HELP),
            TextInput::make('limit')->label('Nombre')->integer()->minValue(1)->maxValue(12)->default(3),
        ]);
    }

    private static function partners(): Block
    {
        return Block::make('partners')->label('Partenaires')->icon('heroicon-o-hand-raised')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Nos partenaires')->helperText(self::EMPHASIS_HELP),
            Select::make('categories')->label('Catégories affichées')->options(Partner::CATEGORIES)->multiple()
                ->helperText('Laisser vide pour afficher tous les partenaires actifs.'),
            Radio::make('layout')->label('Présentation')->options([
                'wall' => 'Mur de logos (regroupés par catégorie)',
                'strip' => 'Bandeau discret « Avec le soutien de » (haut de page)',
            ])->default('wall')->inline(),
        ]);
    }

    private static function professionalSpace(): Block
    {
        return Block::make('professional_space')->label('Encart Espace Professionnels')->icon('heroicon-o-briefcase')->schema([
            TextInput::make('title')->label('Titre')->required()->maxLength(80)->default('Espace Professionnels')->helperText(self::EMPHASIS_HELP),
            Textarea::make('text')->label('Texte')->rows(3)->maxLength(300),
            ...self::image('image', 'Image'),
            TextInput::make('button_label')->label('Texte du bouton')->default('Découvrir le programme')->maxLength(30),
        ]);
    }

    private static function contact(): Block
    {
        return Block::make('contact')->label('Formulaire de contact et coordonnées')->icon('heroicon-o-envelope')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Nous contacter')->helperText(self::EMPHASIS_HELP),
            Textarea::make('text')->label('Texte')->rows(2)->maxLength(200),
        ]);
    }

    /** @return array<string, string> */
    public static function icons(): array
    {
        return [
            'audio-lines' => 'Son', 'lightbulb' => 'Lumière', 'video' => 'Vidéo / cadrage', 'palette' => 'Graphisme',
            'clapperboard' => 'Régie / spectacle', 'graduation-cap' => 'Diplôme', 'users' => 'Équipe', 'award' => 'Certification',
            'calendar' => 'Calendrier', 'map-pin' => 'Lieu', 'briefcase' => 'Métier', 'sparkles' => 'Excellence',
        ];
    }

    private static function showcase(): Block
    {
        return Block::make('showcase')->label('Vitrine photos et vidéos')->icon('heroicon-o-play-circle')->schema([
            TextInput::make('eyebrow')->label('Surtitre')->maxLength(60),
            TextInput::make('title')->label('Titre')->required()->maxLength(80)->helperText(self::EMPHASIS_HELP),
            Textarea::make('text')->label('Texte')->rows(2)->maxLength(300),
            Repeater::make('items')->label('Photos et vidéos')->minItems(1)->maxItems(12)->defaultItems(0)->grid(2)
                ->addActionLabel('Ajouter une photo ou une vidéo')
                ->helperText('La première occupe la grande case. Une vidéo joue en boucle, sans le son, quand elle apparaît à l\'écran ; le visiteur l\'agrandit pour l\'écouter.')
                ->schema([
                    ...self::image('image', 'Photo (ou image d\'aperçu de la vidéo)', true),
                    FileUpload::make('video')->label('Vidéo (facultatif)')->disk('public')->directory('pages/video')
                        ->acceptedFileTypes(['video/mp4', 'video/webm'])->maxSize(51200)
                        ->helperText('MP4 de moins de 50 Mo ; quelques dizaines de secondes suffisent.'),
                    TextInput::make('caption')->label('Légende')->maxLength(120),
                    LinkTargets::field('url', 'Lien (facultatif)'),
                ]),
            TextInput::make('button_label')->label('Texte du bouton (facultatif)')->maxLength(40),
            LinkTargets::field('button_url', 'Lien du bouton')->required(fn ($get) => filled($get('button_label'))),
        ]);
    }

    private static function statement(): Block
    {
        return Block::make('statement')->label('Manifeste (phrase, chiffres, bandeau de photos)')->icon('heroicon-o-megaphone')->schema([
            TextInput::make('eyebrow')->label('Surtitre')->maxLength(60),
            Textarea::make('text')->label('Grande phrase')->required()->rows(3)->maxLength(240)->helperText(self::EMPHASIS_HELP),
            Repeater::make('facts')->label('Chiffres clés')->maxItems(4)->defaultItems(0)->columns(2)->addActionLabel('Ajouter un chiffre')
                ->schema([
                    TextInput::make('value')->label('Valeur (ex. 2016, +300, 5)')->required()->maxLength(12),
                    TextInput::make('label')->label('Libellé (ex. année de création)')->required()->maxLength(40),
                ]),
            FileUpload::make('images')->label('Bandeau de photos (facultatif)')->image()->multiple()->reorderable()->maxFiles(12)
                ->disk('public')->directory('pages')->maxSize(8192)
                ->helperText('De 4 à 12 photos qui glissent lentement en continu (immobiles si le visiteur limite les animations).'),
            TextInput::make('button_label')->label('Texte du bouton (facultatif)')->maxLength(40),
            LinkTargets::field('button_url', 'Lien du bouton')->required(fn ($get) => filled($get('button_label'))),
        ]);
    }

    private static function institution(): Block
    {
        return Block::make('institution')->label('Présentation institutionnelle (mission, valeurs, mot du fondateur)')->icon('heroicon-o-building-library')->schema([
            TextInput::make('eyebrow')->label('Surtitre')->maxLength(60)->default('Notre engagement'),
            TextInput::make('title')->label('Titre')->required()->maxLength(90)->helperText(self::EMPHASIS_HELP),
            Textarea::make('text')->label('Présentation')->rows(3)->maxLength(400),
            Repeater::make('pillars')->label('Piliers (ex. Mission, Vision, Valeurs)')->maxItems(4)->defaultItems(0)->columns(2)
                ->addActionLabel('Ajouter un pilier')
                ->schema([
                    TextInput::make('title')->label('Titre')->required()->maxLength(40),
                    Textarea::make('text')->label('Texte')->required()->rows(3)->maxLength(300),
                ]),
            Section::make('Le mot du fondateur (facultatif)')->collapsible()->schema([
                Textarea::make('quote')->label('Citation')->rows(3)->maxLength(400),
                TextInput::make('author')->label('Nom')->maxLength(80)->required(fn ($get) => filled($get('quote'))),
                TextInput::make('role')->label('Fonction (ex. Fondateur)')->maxLength(80),
                ...self::image('photo', 'Portrait (facultatif)'),
            ]),
            TextInput::make('button_label')->label('Texte du bouton (facultatif)')->maxLength(40),
            LinkTargets::field('button_url', 'Lien du bouton')->required(fn ($get) => filled($get('button_label'))),
        ]);
    }
}
