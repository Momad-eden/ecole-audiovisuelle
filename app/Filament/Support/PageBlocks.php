<?php

namespace App\Filament\Support;

use App\Enums\Audience;
use App\Models\Faq;
use App\Models\Partner;
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

/**
 * Catalogue des blocs de page. Chaque bloc a des champs limités (aucun HTML libre) ;
 * le site Next.js possède un composant par type de bloc (même nom).
 */
final class PageBlocks
{
    /** @return array<int, Block> */
    public static function all(): array
    {
        return [
            self::hero(), self::marquee(), self::text(), self::textImage(), self::venue(), self::equipment(),
            self::gallery(), self::video(), self::audio(), self::stats(), self::quote(), self::cta(), self::cards(),
            self::timeline(), self::faq(), self::programs(), self::artworks(), self::rooms(), self::news(),
            self::partners(), self::professionalSpace(), self::contact(),
        ];
    }

    private static function richText(string $name = 'body', string $label = 'Texte'): RichEditor
    {
        return RichEditor::make($name)->label($label)
            ->toolbarButtons([['bold', 'italic', 'link'], ['h2', 'h3'], ['bulletList', 'orderedList', 'blockquote'], ['undo', 'redo']]);
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

    private static function buttons(int $max = 2): Repeater
    {
        return Repeater::make('buttons')->label('Boutons')->maxItems($max)->defaultItems(0)->columns(3)
            ->addActionLabel('Ajouter un bouton')
            ->schema([
                TextInput::make('label')->label('Texte')->required()->maxLength(30),
                TextInput::make('url')->label('Lien')->required()->placeholder('/candidater ou https://…')
                    ->regex('#^(/|https?://)#')->maxLength(255),
                Select::make('style')->label('Style')->options(['primary' => 'Principal (lumineux)', 'secondary' => 'Discret'])->default('primary')->required(),
            ]);
    }

    private static function hero(): Block
    {
        return Block::make('hero')->label('Grand titre (héros)')->icon('heroicon-o-sparkles')->schema([
            TextInput::make('eyebrow')->label('Surtitre')->maxLength(60),
            TextInput::make('title')->label('Titre')->required()->maxLength(80),
            Textarea::make('subtitle')->label('Sous-titre')->rows(2)->maxLength(200),
            ...self::image('image', 'Image de fond'),
            FileUpload::make('video_loop')->label('Boucle vidéo muette (facultatif)')->disk('public')->directory('pages/video')
                ->acceptedFileTypes(['video/mp4', 'video/webm'])->maxSize(20480)
                ->helperText('MP4 court et léger (moins de 20 Mo). Remplacé par l\'image si l\'internaute limite les animations.'),
            Radio::make('layout')->label('Mise en page')->options([
                'stage' => 'Scène animée (faisceaux de lumière)',
                'full' => 'Plein écran',
                'split' => 'Texte et image côte à côte',
            ])->default('stage')->inline()->live(),
            TagsInput::make('words')->label('Mots qui défilent à la fin du titre')->placeholder('Ex. le son')
                ->helperText('Scène animée uniquement : le titre se termine par ces mots, l\'un après l\'autre. Laissez vide pour un titre fixe.')
                ->visible(fn ($get) => $get('layout') === 'stage'),
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
            TextInput::make('title')->label('Titre')->required()->maxLength(90),
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
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Sur quoi vous vous formez'),
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
            TextInput::make('title')->label('Titre')->maxLength(80),
            self::richText()->required(),
        ]);
    }

    private static function textImage(): Block
    {
        return Block::make('text_image')->label('Texte et image')->icon('heroicon-o-photo')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80),
            self::richText()->required(),
            ...self::image('image', 'Image', true),
            Radio::make('image_position')->label('Position de l\'image')->options(['left' => 'À gauche', 'right' => 'À droite'])->default('right')->inline(),
        ]);
    }

    private static function gallery(): Block
    {
        return Block::make('gallery')->label('Galerie photos')->icon('heroicon-o-squares-2x2')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80),
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
            TextInput::make('title')->label('Titre')->maxLength(80),
            TextInput::make('url')->label('Lien YouTube ou Vimeo')->required()->url()->regex('#^https?://(www\.)?(youtube\.com|youtu\.be|vimeo\.com)/#i'),
            ...self::image('poster', 'Image d\'aperçu (facultatif)'),
            TextInput::make('caption')->label('Légende')->maxLength(200),
            Textarea::make('transcript')->label('Transcription (accessibilité)')->rows(3),
        ]);
    }

    private static function audio(): Block
    {
        return Block::make('audio')->label('Écoute (sons)')->icon('heroicon-o-musical-note')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80),
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
            TextInput::make('title')->label('Titre')->maxLength(80),
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
            TextInput::make('title')->label('Titre')->required()->maxLength(80),
            Textarea::make('text')->label('Texte')->rows(2)->maxLength(200),
            self::buttons()->minItems(1),
        ]);
    }

    private static function cards(): Block
    {
        return Block::make('cards')->label('Cartes (piliers, avantages)')->icon('heroicon-o-rectangle-group')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80),
            Repeater::make('items')->label('Cartes')->minItems(2)->maxItems(6)->columns(2)->addActionLabel('Ajouter une carte')
                ->schema([
                    Select::make('icon')->label('Icône')->options(self::icons()),
                    TextInput::make('title')->label('Titre')->required()->maxLength(60),
                    Textarea::make('text')->label('Texte')->rows(2)->maxLength(200)->columnSpanFull(),
                    TextInput::make('url')->label('Lien (facultatif)')->regex('#^(/|https?://)#')->columnSpanFull(),
                ]),
        ]);
    }

    private static function timeline(): Block
    {
        return Block::make('timeline')->label('Chronologie / étapes')->icon('heroicon-o-calendar')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80),
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
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Questions fréquentes'),
            Select::make('group')->label('Groupe de questions')->options(Faq::GROUPS)->required()
                ->helperText('Les questions se gèrent dans Site › FAQ.'),
        ]);
    }

    private static function programs(): Block
    {
        return Block::make('programs')->label('Liste de formations')->icon('heroicon-o-academic-cap')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80),
            Select::make('audience')->label('Public')->options(Audience::class)->required(),
            TextInput::make('limit')->label('Nombre maximum')->integer()->minValue(1)->maxValue(24)->default(6),
        ]);
    }

    private static function artworks(): Block
    {
        return Block::make('artworks')->label('Réalisations des étudiants')->icon('heroicon-o-photo')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80),
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
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Choisissez votre univers'),
            Textarea::make('text')->label('Texte')->rows(2)->maxLength(200),
        ]);
    }

    private static function news(): Block
    {
        return Block::make('news')->label('Dernières actualités')->icon('heroicon-o-newspaper')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Actualités'),
            TextInput::make('limit')->label('Nombre')->integer()->minValue(1)->maxValue(12)->default(3),
        ]);
    }

    private static function partners(): Block
    {
        return Block::make('partners')->label('Partenaires')->icon('heroicon-o-hand-raised')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Nos partenaires'),
            Select::make('categories')->label('Catégories affichées')->options(Partner::CATEGORIES)->multiple()
                ->helperText('Laisser vide pour afficher tous les partenaires actifs.'),
        ]);
    }

    private static function professionalSpace(): Block
    {
        return Block::make('professional_space')->label('Encart Espace Professionnels')->icon('heroicon-o-briefcase')->schema([
            TextInput::make('title')->label('Titre')->required()->maxLength(80)->default('Espace Professionnels'),
            Textarea::make('text')->label('Texte')->rows(3)->maxLength(300),
            ...self::image('image', 'Image'),
            TextInput::make('button_label')->label('Texte du bouton')->default('Découvrir le programme')->maxLength(30),
        ]);
    }

    private static function contact(): Block
    {
        return Block::make('contact')->label('Formulaire de contact et coordonnées')->icon('heroicon-o-envelope')->schema([
            TextInput::make('title')->label('Titre')->maxLength(80)->default('Nous contacter'),
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
}
