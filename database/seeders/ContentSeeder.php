<?php

namespace Database\Seeders;

use App\Enums\Activity;
use App\Enums\Audience;
use App\Enums\CohortStatus;
use App\Enums\FundingMode;
use App\Enums\PlaceKind;
use App\Enums\ProgramKind;
use App\Enums\PublicationStatus;
use App\Enums\SiteDomain;
use App\Models\AgendaEvent;
use App\Models\Cohort;
use App\Models\EquipmentCategory;
use App\Models\Faq;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Place;
use App\Models\Program;
use App\Models\Redirect;
use App\Models\Room;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Track;
use App\Support\LegacyPaths;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Contenu de référence (idempotent), tiré du document de projet EMSI × Grand Théâtre
 * après correction de ses incohérences. Aucun chiffre ni partenaire non sourcé.
 *
 * refreshSite() met à niveau une base existante vers le site « Plein feux » (v2)
 * sans toucher aux paramètres ni aux autres pages (commande emsi:site-v2).
 */
class ContentSeeder extends Seeder
{
    /** @var list<string> Résumé de emsi:site-v4. */
    private array $report = [];

    public function run(): void
    {
        $this->settings();
        $rooms = $this->universes();
        $tracks = $this->tracks($rooms);
        $this->programs($tracks);
        $this->faqs();
        $this->partners();
        $this->pages();
        $this->impactLive();
        $this->siteV3();
        $this->menus();
        $this->redirects();
    }

    /** Site v3 : L'École présente les deux campus, l'accueil gagne chiffres clés, campus et agenda. */
    public function refreshSiteV3(): void
    {
        $this->siteV3();
    }

    /** Repère posé dans les paramètres quand emsi:site-v4 est passée. */
    public const SITE_V4 = 4;

    /**
     * Site v4 : un site, trois domaines (Centre culturel Habib Faye, EMSI, Impact Live Studio). Sans perte :
     * les pages sont déplacées (pas recréées), une page déjà présente n'est jamais réécrite, l'accueil n'est
     * remplacé qu'avec $home. Le premier passage est noté dans les paramètres (site_version) : les suivants
     * ne font plus que les réécritures de liens et les compléments de la page EMSI, sans recréer les pages,
     * menus ni campus des formations que l'équipe a pu retoucher ou retirer, sauf avec $force.
     * Retourne le résumé en français de ce qui a été fait.
     *
     * @return list<string>
     */
    public function refreshSiteV4(bool $home = false, bool $force = false): array
    {
        $this->report = [];
        $settings = Setting::current();
        $done = (int) $settings->site_version >= self::SITE_V4;
        if (! $done && $this->siteV4AlreadyApplied()) {
            $done = true;
            $this->report[] = 'Site déjà au format des trois domaines (mise à niveau faite avant le repère de version) : repère enregistré.';
        }
        $full = ! $done || $force;

        if ($full) {
            $this->unpublishEvents();
        }
        $this->rewriteLinks();
        if ($full) {
            $this->movePages();
            $this->siteV4Pages();
        }
        if ($home) {
            $this->homeV4();
        }
        $this->completeEmsiPage();
        if ($full) {
            $this->programCampuses();
            $this->menusV4();
        } else {
            $this->report[] = 'Mise à niveau déjà faite : les pages, menus et campus des formations ne sont plus modifiés (relancez avec --force pour les reprendre).';
        }
        $this->rewriteRedirects();

        if ((int) $settings->site_version < self::SITE_V4) {
            $settings->forceFill(['site_version' => self::SITE_V4])->save();
        }

        return $this->report;
    }

    /** Base mise à niveau par une version de la commande antérieure au repère : pages EMSI et Studio à leur nouvelle adresse. */
    private function siteV4AlreadyApplied(): bool
    {
        return Page::where('slug', 'emsi')->exists() && Page::where('slug', 'centre-culturel/studio')->exists();
    }

    /** Ajoute Impact Live (studio, événementiel, Centre culturel Habib Faye) et le campus de Saint-Louis à un site existant. */
    public function refreshImpactLive(): void
    {
        $this->impactLive();
        $this->menus(hideOthers: true);
    }

    /** Met à niveau une base existante : univers, filières, Accueil, L'École, menus et redirections. */
    public function refreshSite(): void
    {
        $rooms = $this->universes();

        foreach (self::TRACK_UNIVERSE as $slug => $universe) {
            Track::where('slug', $slug)->update(['room_id' => $rooms[$universe]->id]);
        }

        $this->pages(overwrite: true);
        $this->menus(hideOthers: true);
        $this->redirects();
    }

    /** Filière (slug) → univers. */
    private const TRACK_UNIVERSE = [
        'son' => 'son',
        'technicien-lumiere' => 'scene',
        'regie-generale-spectacle' => 'scene',
        'infographie-et-creation-numerique' => 'design',
        'cadrage-sportif-et-regie-video' => 'image',
    ];

    private function settings(): void
    {
        Setting::current()->update([
            'school_name' => 'EMSI — École des Métiers du Son et de l\'Image',
            'description' => 'École de formation aux métiers techniques et artistiques du son, de l\'image, de la lumière et du spectacle vivant, installée au Grand Théâtre National Doudou Ndiaye Coumba Rose à Dakar.',
            'seo_title' => 'EMSI — École des Métiers du Son et de l\'Image, Dakar',
            'seo_description' => 'Formations au son, à la vidéo et à la photo, à l\'infographie, à la régie et à la lumière de spectacle, au Grand Théâtre National de Dakar. Candidature en ligne.',
        ]);
    }

    /** @return array<string, Room> Les univers de l'école (anciennes « salles » du musée reprises). */
    private function universes(): array
    {
        $universes = [
            'son' => ['Son', 'salle-du-son', '#8B6CFF', 'sound', false,
                'Capter, mixer, diffuser : donner corps au son, du studio à la grande scène.',
                'Prise de son, mixage live et studio, sonorisation d\'événements : l\'univers du Son forme les techniciens qui font entendre les artistes, sur des consoles et des systèmes de diffusion professionnels.'],
            'image' => ["Image\u{00A0}: vidéo & photo", 'salle-de-limage', '#3FD0FF', 'image', false,
                'Cadrer, filmer, photographier : raconter en images, en direct comme en différé.',
                'Vidéographie, cadrage, photographie et régie vidéo : l\'univers de l\'Image apprend à composer un plan, à suivre l\'action en direct et à travailler avec une régie.'],
            'design' => ['Infographie & design', 'salle-du-visuel', '#FF4FA3', 'design', false,
                'Graphisme, motion design, 3D : inventer les images qui habillent les écrans et les scènes.',
                'Identité visuelle, habillage d\'émission, motion design et modélisation 3D : l\'univers de l\'Infographie & design forme les créateurs des images qui accompagnent les programmes et les événements.'],
            'scene' => ["Scène\u{00A0}: régie & lumière", 'salle-de-la-lumiere', '#FFB020', 'stage', false,
                'Éclairer, coordonner, conduire : faire exister le spectacle vivant.',
                'Éclairage scénique, programmation lumière et régie générale : l\'univers de la Scène prépare aux métiers qui font tenir un spectacle, de la conception à la conduite en direct.'],
            'cinema' => ['Cinéma', null, '#FF3B30', 'cinema', true,
                'Bientôt à l\'EMSI : l\'art du cinéma rejoindra nos formations.',
                'L\'EMSI prépare l\'arrivée du cinéma parmi ses formations. Laissez-nous vos coordonnées pour être informé de l\'ouverture.'],
        ];

        $models = [];
        foreach ($universes as $slug => [$name, $legacySlug, $color, $visual, $upcoming, $tagline, $intro]) {
            $room = Room::whereIn('slug', array_filter([$slug, $legacySlug]))->orderByRaw('slug = ? desc', [$slug])->first() ?? new Room;
            $room->fill([
                'name' => $name, 'slug' => $slug, 'accent_color' => $color, 'visual' => $visual, 'is_upcoming' => $upcoming,
                'tagline' => $tagline, 'intro' => $intro, 'position' => count($models),
                'status' => PublicationStatus::PUBLISHED, 'published_at' => $room->published_at ?? now(),
            ])->save();
            $models[$slug] = $room;
        }

        return $models;
    }

    /** @return array<string, Track> */
    private function tracks(array $rooms): array
    {
        $tracks = [
            'son' => ['Son', 'Son', 'son',
                'Ingénierie audio avancée : calage de systèmes Line Array, consoles numériques professionnelles, mixage live, réseaux audio Dante et MADI, mastering studio.',
                ['Calage d\'un système Line Array', 'Exploitation de consoles numériques (Yamaha, Allen & Heath, DiGiCo, Midas)', 'Mixage live en conditions réelles', 'Réseaux audio Dante et MADI', 'Architecture sonore d\'événements complexes'],
                ['Ingénieur du son live', 'Technicien système', 'Concepteur sonore']],
            'lumiere' => ['Technicien Lumière', 'Lumière', 'scene',
                'Éclairage scénique et conception lumineuse : programmation sur consoles professionnelles, réseaux DMX/Art-Net/sACN, dimensionnement d\'un parc projecteurs, conduite de spectacle en direct.',
                ['Programmation GrandMA, Chamsys, Avolites', 'Réseaux DMX, Art-Net, sACN', 'Calage et dimensionnement d\'un parc projecteurs', 'Synchronisation lumière-son-vidéo', 'Conduite d\'un spectacle en direct'],
                ['Régisseur lumière', 'Concepteur lumière (Lighting Designer)', 'Chef électricien de spectacle', 'Programmateur lumière']],
            'regie' => ['Régie Générale Spectacle', 'Régie générale', 'scene',
                'Management technique d\'un événement : cahier des charges, dimensionnement matériel et humain, coordination des équipes son, lumière, vidéo et sécurité, conduite du spectacle en direct.',
                ['Lecture et rédaction de cahiers des charges', 'Dimensionnement d\'un parc matériel complet', 'Coordination d\'équipes techniques', 'Planning de montage et démontage', 'Sécurité des spectacles et gestion des flux de publics'],
                ['Régisseur général spectacle', 'Régisseur adjoint', 'Chef de projet événementiel', 'Coordinateur technique de festivals']],
            'infographie' => ['Infographie et Création Numérique', 'Infographie', 'design',
                'Création visuelle et habillage de programmes : motion design, modélisation 3D, incrustation Chroma Key en temps réel, identité visuelle événementielle.',
                ['Motion design (After Effects, Cinema 4D)', 'Habillage d\'émission', 'Modélisation 3D', 'Incrustation Chroma Key en temps réel', 'Diffusion multi-canal'],
                ['Infographiste / motion designer', 'Directeur artistique graphique', 'Community manager événementiel']],
            'cadrage' => ['Cadrage Sportif et Régie Vidéo', 'Cadrage', 'image',
                'Prise de vue en direct : cadrage multicaméra sportif, ralenti Super Slow Motion, caméras de broadcast (fibre, HF), communication avec la régie.',
                ['Cadrage multicaméra en direct', 'Ralenti Super Slow Motion', 'Caméras de broadcast (fibre, HF)', 'Anticipation des trajectoires', 'Communication régie par intercom'],
                ['Cadreur sportif', 'Assistant réalisateur pour le direct', 'Chef opérateur']],
        ];

        $models = [];
        foreach ($tracks as $key => [$name, $short, $room, $summary, $skills, $outcomes]) {
            $models[$key] = Track::updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name, 'short_name' => $short, 'summary' => $summary, 'skills' => $skills, 'outcomes' => $outcomes,
                'room_id' => $rooms[$room]->id, 'position' => count($models), 'is_active' => true,
            ]);
        }

        return $models;
    }

    private function programs(array $tracks): void
    {
        $volet1 = Program::updateOrCreate(['slug' => 'perfectionnement'], [
            'title' => 'Perfectionnement intensif (Volet 1)',
            'audience' => Audience::PROFESSIONAL,
            'kind' => ProgramKind::INTENSIVE_UPSKILLING,
            'level_label' => 'Diplôme d\'École et Certificat de Compétences Techniques Avancées',
            'duration_label' => '12 semaines (environ 360 h)',
            'summary' => 'Camp de haute performance pour 40 techniciens titulaires d\'un CPS, perfectionnés aux standards internationaux avant les grands événements de 2026.',
            'description' => '<p>Organisé à Dakar de septembre à novembre 2026, le Volet 1 plonge les apprenants dans la haute technicité : calage d\'un système Line Array, configuration avancée des consoles numériques, programmation GrandMA en conditions de show, cadrage sportif de haut niveau.</p><p>La formation se déroule sur les plateaux et dans les salles du Grand Théâtre National Doudou Ndiaye Coumba Rose, avec 10 % de théorie ciblée et 90 % de pratique. La Régie Générale Spectacle y est intégrée comme module transversal.</p>',
            'skills' => ['Calage de systèmes Line Array', 'Consoles numériques avancées', 'Programmation GrandMA en conditions de show', 'Cadrage sportif multicaméra', 'Coordination technique (régie adjointe)'],
            'prerequisites' => ['Titulaire d\'un CPS délivré par l\'EMSI', 'Membre de l\'équipe technique des grands événements 2026'],
            'outcomes' => ['Déploiement sur les grands événements culturels et sportifs de 2026'],
            'position' => 1, 'status' => PublicationStatus::PUBLISHED, 'published_at' => now(),
        ]);

        $cohort1 = Cohort::updateOrCreate(['program_id' => $volet1->id, 'name' => 'Volet 1 — 2026'], [
            'starts_on' => '2026-09-01', 'ends_on' => '2026-11-30', 'status' => CohortStatus::RUNNING,
        ]);
        foreach (['son', 'lumiere', 'infographie', 'cadrage'] as $key) {
            $cohort1->offerings()->updateOrCreate(['track_id' => $tracks[$key]->id], [
                'capacity' => 10, 'fee_amount' => 0, 'funding_mode' => FundingMode::SPONSORED,
                'funding_note' => 'Dans le cadre du programme EMSI × Grand Théâtre', 'is_open' => false,
            ]);
        }

        $volet2 = Program::updateOrCreate(['slug' => 'bts-vae'], [
            'title' => 'Cycle de certification de niveau BTS par la VAE (Volet 2)',
            'audience' => Audience::PROFESSIONAL,
            'kind' => ProgramKind::VAE_BTS,
            'level_label' => 'Certification de niveau BTS (équivalent Bac+2)',
            'duration_label' => '9 mois (1 080 h, 30 h par semaine)',
            'summary' => 'Un cycle de 9 mois en alternance pour 60 titulaires d\'un CPS ou d\'un CS, sans Baccalauréat, conduisant à une certification de niveau BTS par la Validation des Acquis de l\'Expérience.',
            'description' => '<p>Le Volet 2 permet à des techniciens titulaires d\'un CPS ou d\'un CS, qui n\'ont pas le Baccalauréat, d\'accéder à une certification de niveau BTS (équivalent Bac+2) en valorisant leurs compétences réelles.</p><p>Le rythme est de 30 heures par semaine : 10 heures de théorie et de gestion de projet, 20 heures de travaux pratiques en situation réelle, en alternance école-entreprise. Dès le premier mois, chaque apprenant ouvre son Livret VAE, alimenté en continu et suivi par un tuteur pédagogique de l\'EMSI.</p><p>En fin de cycle, un jury composé de professionnels du secteur et de représentants de l\'État évalue le Livret VAE et soumet chaque apprenant à une soutenance : présentation du parcours, défense du Livret et mise en situation technique.</p>',
            'skills' => ['Gestion de projet et coordination technique', 'Expertise technique de niveau BTS dans sa filière', 'Direction d\'équipe', 'Gestion des aléas en direct'],
            'prerequisites' => ['Titulaire d\'un CPS ou d\'un CS', 'Âgé de 18 à 30 ans', 'Première expérience pratique appréciée', 'Sélection sur dossier et entretien de motivation'],
            'outcomes' => ['Chef de projet', 'Cadreur principal', 'Régisseur adjoint', 'Création d\'entreprise'],
            'position' => 2, 'status' => PublicationStatus::PUBLISHED, 'published_at' => now(),
        ]);

        $cohort2 = Cohort::updateOrCreate(['program_id' => $volet2->id, 'name' => 'Volet 2 — 2027'], [
            'starts_on' => '2027-02-01', 'ends_on' => '2027-10-31',
            'applications_open_at' => '2027-01-01 08:00:00', 'applications_close_at' => '2027-01-31 23:59:00',
            'status' => CohortStatus::PLANNED,
        ]);
        foreach ($tracks as $track) {
            $cohort2->offerings()->updateOrCreate(['track_id' => $track->id], [
                'capacity' => 12, 'fee_amount' => 0, 'funding_mode' => FundingMode::SPONSORED,
                'funding_note' => 'Modalités communiquées lors de la sélection', 'is_open' => true,
            ]);
        }
    }

    private function faqs(): void
    {
        $faqs = [
            ['vae', 'Faut-il avoir le Baccalauréat ?', 'Non. Le Volet 2 s\'adresse justement aux titulaires d\'un CPS ou d\'un CS qui n\'ont pas le Baccalauréat : la VAE leur permet d\'accéder à une certification de niveau BTS sur la base de leurs compétences.'],
            ['vae', 'Quel est le rythme du cycle ?', '30 heures par semaine pendant 9 mois : 10 heures de théorie et de gestion de projet, 20 heures de pratique en situation réelle, en alternance école-entreprise.'],
            ['vae', 'Qu\'est-ce que le Livret VAE ?', 'Un livret ouvert dès le premier mois, dans lequel chaque projet réalisé est consigné, photographié, filmé et rédigé pour prouver l\'acquisition des compétences. Un tuteur de l\'EMSI en valide la qualité.'],
            ['vae', 'Comment se déroule la certification ?', 'Un jury de professionnels du secteur et de représentants de l\'État évalue le Livret VAE, puis l\'apprenant présente son parcours, défend son Livret et réalise une mise en situation technique.'],
            ['vae', 'Quand ont lieu les candidatures ?', 'Le recrutement du Volet 2 est prévu en janvier 2027, pour un démarrage en février 2027. La sélection se fait sur dossier et entretien de motivation.'],
        ];

        foreach ($faqs as $position => [$group, $question, $answer]) {
            Faq::updateOrCreate(['question' => $question], ['group' => $group, 'answer' => $answer, 'position' => $position]);
        }
    }

    private function partners(): void
    {
        $partners = [
            ['Grand Théâtre National Doudou Ndiaye Coumba Rose', 'co_organizer', 'Co-porteur du programme : met à disposition ses salles, plateaux et équipements techniques.'],
            ['Direction des Concours du Sénégal', 'institutional', 'Partenaire institutionnel garant de la certification du Volet 1.'],
            ['Ministère de la Culture', 'institutional', 'Associé à la gouvernance stratégique du programme.'],
            ['Ministère de la Formation professionnelle', 'institutional', 'Associé à la gouvernance stratégique du programme.'],
        ];

        foreach ($partners as $position => [$name, $category, $description]) {
            Partner::updateOrCreate(['name' => $name], ['category' => $category, 'description' => $description, 'is_active' => true, 'position' => $position]);
        }
    }

    private function pages(bool $overwrite = false): void
    {
        $cta = ['label' => 'Candidater', 'url' => '/candidater', 'style' => 'primary'];
        $venue = ['venue', [
            'eyebrow' => 'Notre adresse',
            'title' => 'Au cœur du Grand Théâtre National Doudou Ndiaye Coumba Rose',
            'text' => 'L\'EMSI est installée dans les locaux du Grand Théâtre National, à Dakar. Nos apprenants se forment là où le spectacle se fabrique : sur les plateaux, dans les salles et en régie.',
            'facts' => [
                ['value' => '2016', 'label' => 'année de création de l\'école'],
                ['value' => '154 m²', 'label' => 'de studio'],
                ['value' => 'Live', 'label' => 'une scène pour s\'exercer en conditions réelles'],
            ],
            'buttons' => [['label' => 'Découvrir l\'école', 'url' => '/ecole', 'style' => 'secondary']],
        ]];
        $equipment = ['equipment', [
            'title' => 'Sur quoi vous vous formez',
            'text' => 'Le matériel des grandes scènes et des plateaux de télévision, en conditions réelles.',
            'groups' => [
                ['category' => 'Consoles son', 'items' => ['Yamaha', 'Allen & Heath', 'DiGiCo', 'Midas']],
                ['category' => 'Diffusion et réseaux audio', 'items' => ['Systèmes Line Array', 'Dante', 'MADI']],
                ['category' => 'Lumière de spectacle', 'items' => ['GrandMA', 'Chamsys', 'Avolites', 'DMX, Art-Net, sACN']],
                ['category' => 'Image et broadcast', 'items' => ['Caméras broadcast (fibre, HF)', 'Super Slow Motion', 'Intercom de régie']],
                ['category' => 'Création numérique', 'items' => ['After Effects', 'Cinema 4D', 'Chroma Key en temps réel']],
            ],
        ]];

        $this->page('accueil', 'Accueil', 'home', true, [
            ['hero', [
                'eyebrow' => 'Dakar · Grand Théâtre National',
                'title' => 'Faites de votre passion un métier',
                'subtitle' => 'L\'EMSI forme les techniciens et les créateurs du son, de l\'image et du spectacle vivant, sur du matériel professionnel, au cœur du Grand Théâtre National Doudou Ndiaye Coumba Rose.',
                'layout' => 'stage',
                'buttons' => [['label' => 'Choisir mon univers', 'url' => '/univers', 'style' => 'primary'], ['label' => 'Candidater', 'url' => '/candidater', 'style' => 'secondary']],
            ]],
            ['marquee', ['words' => ['Son', 'Image', 'Lumière', 'Design', 'Scène', 'Cinéma']]],
            ['rooms', ['eyebrow' => 'Cinq univers, une école', 'title' => 'Choisissez votre univers', 'text' => 'Chaque univers a sa lumière, ses outils et ses métiers. Explorez-les, puis trouvez la formation qui vous ressemble.']],
            $venue,
            $equipment,
            ['timeline', ['title' => 'Rejoindre l\'EMSI', 'layout' => 'steps', 'steps' => [
                ['period' => '01', 'title' => 'Choisir son univers', 'text' => 'Explorez les univers et les filières pour trouver votre voie.'],
                ['period' => '02', 'title' => 'Candidater en ligne', 'text' => 'Un formulaire en quelques minutes, sans vous déplacer.'],
                ['period' => '03', 'title' => 'Entretien de motivation', 'text' => 'L\'équipe pédagogique vous rencontre pour parler de votre projet.'],
                ['period' => '04', 'title' => 'Entrer en scène', 'text' => 'Vous rejoignez les plateaux et les régies de l\'école.'],
            ]]],
            ['artworks', ['title' => 'Réalisations des étudiants', 'source' => 'latest', 'limit' => 6]],
            ['programs', ['title' => 'Nos formations', 'audience' => 'school', 'limit' => 6]],
            ['professional_space', ['title' => 'Vous êtes déjà technicien ?', 'text' => 'Titulaires d\'un CPS ou d\'un CS : perfectionnement intensif et certification de niveau BTS par la VAE, avec le Grand Théâtre National.', 'button_label' => 'Découvrir l\'Espace Pro']],
            ['news', ['title' => 'Actualités', 'limit' => 3]],
            ['partners', ['title' => 'Ils accompagnent l\'école']],
            ['cta', ['title' => 'Votre place est sur scène', 'text' => 'Candidatez en ligne en quelques minutes : l\'équipe de l\'EMSI vous répond.', 'buttons' => [$cta, ['label' => 'Nous contacter', 'url' => '/contact', 'style' => 'secondary']]]],
        ], $overwrite);

        $this->page('ecole', 'L\'école', 'system', true, [
            ['hero', ['eyebrow' => 'L\'école', 'title' => 'École des Métiers du Son et de l\'Image', 'subtitle' => 'Former des techniciens et des créateurs par la pratique, au plus près des scènes et des plateaux.', 'layout' => 'full']],
            ['text', ['title' => 'Notre histoire', 'body' => '<p>Créée en 2016, l\'EMSI est une école de formations technico-artistiques. Elle a développé des Certificats de Spécialité (CS) et des BTS dans les métiers du spectacle vivant, et met à la disposition de ses apprenants un parc matériel professionnel, dont un studio de 154 m² et une scène live.</p>']],
            $venue,
            ['cards', ['title' => 'Notre pédagogie', 'items' => [
                ['icon' => 'sparkles', 'title' => 'La pratique d\'abord', 'text' => 'Les apprenants sont placés en situation réelle, sur du matériel professionnel.'],
                ['icon' => 'users', 'title' => 'Un suivi individualisé', 'text' => 'Chaque parcours est accompagné par l\'équipe pédagogique de l\'EMSI.'],
                ['icon' => 'award', 'title' => 'Des certifications', 'text' => 'CS, BTS et, pour les professionnels, certification de niveau BTS par la VAE.'],
            ]]],
            $equipment,
            ['partners', ['title' => 'Nos partenaires']],
            ['cta', ['title' => 'Venez nous rencontrer', 'text' => 'Une question sur nos formations ? Écrivez-nous ou candidatez en ligne.', 'buttons' => [['label' => 'Nous contacter', 'url' => '/contact', 'style' => 'secondary'], $cta]]],
        ], $overwrite);

        $this->page('contact', 'Contact', 'system', true, [
            ['contact', ['title' => 'Nous contacter', 'text' => 'Information, partenariat, presse ou visite de l\'école : écrivez-nous, nous vous répondrons.']],
        ]);

        $this->page('professionnels', 'Espace Professionnels', 'professional', true, [
            ['hero', ['eyebrow' => 'Programme EMSI × Grand Théâtre', 'title' => 'Former et certifier les techniciens de l\'audiovisuel et de l\'événementiel', 'subtitle' => 'Un programme porté par l\'EMSI et le Grand Théâtre National Doudou Ndiaye Coumba Rose, pour les techniciens titulaires d\'un CPS ou d\'un CS.', 'layout' => 'full']],
            ['cards', ['title' => 'Deux volets complémentaires', 'items' => [
                ['icon' => 'sparkles', 'title' => 'Volet 1 — Perfectionnement intensif', 'text' => '12 semaines (environ 360 h), 40 techniciens, septembre à novembre 2026. En cours.', 'url' => '/professionnels/perfectionnement'],
                ['icon' => 'award', 'title' => 'Volet 2 — Certification de niveau BTS par la VAE', 'text' => '9 mois (1 080 h), 60 places, démarrage en février 2027. Recrutement en janvier 2027.', 'url' => '/professionnels/bts-vae'],
            ]]],
            ['stats', ['title' => 'Le programme en chiffres', 'items' => [
                ['value' => '100', 'label' => 'techniciens', 'detail' => '40 au Volet 1, 60 au Volet 2'],
                ['value' => '5', 'label' => 'filières', 'detail' => 'Son, Lumière, Régie générale, Infographie, Cadrage'],
                ['value' => '30 %', 'label' => 'de femmes visées', 'detail' => 'par cohorte'],
            ]]],
            ['timeline', ['title' => 'Calendrier', 'layout' => 'list', 'steps' => [
                ['period' => 'Septembre – novembre 2026', 'title' => 'Formation intensive', 'tag' => 'Volet 1', 'text' => '12 semaines sur les plateaux du Grand Théâtre.'],
                ['period' => 'Novembre 2026', 'title' => 'Évaluation et certification', 'tag' => 'Volet 1'],
                ['period' => 'Janvier 2027', 'title' => 'Recrutement des 60 apprenants', 'tag' => 'Volet 2', 'text' => 'Sélection sur dossier et entretien de motivation.'],
                ['period' => 'Février – octobre 2027', 'title' => 'Cycle en alternance', 'tag' => 'Volet 2'],
                ['period' => 'Mai 2027', 'title' => 'Point d\'étape du Livret VAE', 'tag' => 'Volet 2'],
                ['period' => 'Octobre 2027', 'title' => 'Soutenances devant le jury', 'tag' => 'Volet 2'],
                ['period' => 'Novembre 2027', 'title' => 'Certification de niveau BTS', 'tag' => 'Volet 2'],
            ]]],
            ['programs', ['title' => 'Les programmes', 'audience' => 'professional', 'limit' => 6]],
            ['partners', ['title' => 'Porteurs et partenaires']],
            ['faq', ['title' => 'Questions fréquentes', 'group' => 'vae']],
            ['cta', ['title' => 'Être prévenu de l\'ouverture des candidatures', 'text' => 'Le recrutement du Volet 2 ouvre en janvier 2027.', 'buttons' => [['label' => 'Candidater', 'url' => '/professionnels/candidater', 'style' => 'primary']]]],
        ]);

        $this->page('mentions-legales', 'Mentions légales', 'system', false, [
            ['text', ['title' => 'Mentions légales', 'body' => '<p>À compléter par l\'école : éditeur du site, responsable de la publication, hébergeur.</p>']],
        ]);

        $this->page('confidentialite', 'Protection des données', 'system', false, [
            ['text', ['title' => 'Protection des données personnelles', 'body' => '<p>À valider par l\'école : finalités du traitement des candidatures, durée de conservation, droits des personnes (loi n° 2008-12 sur la protection des données à caractère personnel) et contact auprès de la Commission de protection des données personnelles (CDP).</p>']],
        ]);
    }

    /**
     * Crée la page, ou avec $overwrite la republie avec les nouveaux blocs : la version
     * précédente reste dans l'historique et l'image (ou la vidéo) du premier bloc héros est reprise.
     */
    private function page(string $slug, string $title, string $type, bool $publish, array $blocks, bool $overwrite = false): void
    {
        $page = Page::firstOrNew(['slug' => $slug]);
        if ($page->exists && ! $overwrite) {
            return;
        }

        $blocks = array_map(fn (array $block) => ['type' => $block[0], 'data' => $block[1]], $blocks);

        if ($page->exists) {
            $previousHero = collect($page->blocks ?? $page->draft_blocks ?? [])->firstWhere('type', 'hero')['data'] ?? [];
            $heroIndex = collect($blocks)->search(fn (array $block) => $block['type'] === 'hero');
            if ($heroIndex !== false) {
                $blocks[$heroIndex]['data'] += array_filter(array_intersect_key($previousHero, array_flip(['image', 'image_alt', 'video_loop'])));
            }
        }

        $page->fill(['title' => $title, 'type' => $type, 'is_locked' => $type !== 'free', 'draft_blocks' => $blocks])->save();

        if ($publish) {
            $page->publish();
        }
    }

    /**
     * Impact Live : lieux, services (sans prix inventés : « Sur devis »), catégories de matériel,
     * référence Festival de Saint-Louis, pages Studio, Events et Centre culturel Habib Faye, et blocs
     * ajoutés à l'accueil (écosystème) et aux pages L'École et Contact (adresses). Idempotent.
     */
    private function impactLive(): void
    {
        $places = [
            ['EMSI Dakar', 'emsi-dakar', PlaceKind::CAMPUS, 'Dakar', 'Grand Théâtre National Doudou Ndiaye Coumba Rose'],
            ['EMSI Saint-Louis', 'emsi-saint-louis', PlaceKind::CAMPUS, 'Saint-Louis', null],
            ['Impact Live Studio', 'impact-live-studio', PlaceKind::STUDIO, 'Saint-Louis', null],
            ['Centre culturel Habib Faye', 'espace-habib-faye', PlaceKind::CULTURAL_CENTER, 'Saint-Louis', null],
        ];
        foreach ($places as $position => [$name, $slug, $kind, $city, $address]) {
            $place = Place::firstOrNew(['slug' => $slug]);
            if (! $place->exists) {
                $place->fill(['name' => $name, 'kind' => $kind, 'city' => $city, 'address' => $address, 'position' => $position,
                    'status' => PublicationStatus::PUBLISHED, 'published_at' => now()])->save();
            }
        }

        $services = [
            'studio' => [
                ['Enregistrement', 'Voix, instruments, groupes : captez votre musique dans des conditions professionnelles.', 'microphone'],
                ['Mixage', 'L\'équilibre, la profondeur et l\'énergie de chaque titre, par un ingénieur du son.', 'adjustments'],
                ['Mastering', 'La touche finale pour que vos titres sonnent fort et juste, partout.', 'sparkles'],
            ],
            'events' => [
                ['Sonorisation', 'Des systèmes de sonorisation de dernière génération, installés et exploités par nos techniciens.', 'speaker'],
                ['Éclairage scénique', 'Lumières de spectacle, conception et conduite pendant l\'événement.', 'light'],
                ['Podiums et structures de scène', 'Scènes et podiums de spectacle, montés en toute sécurité.', 'stage'],
            ],
            'space' => [
                ['Location de la salle', 'Concerts, spectacles, résidences, conférences : accueillez votre public au Centre culturel Habib Faye.', 'building'],
            ],
        ];
        foreach ($services as $activity => $list) {
            foreach ($list as $position => [$name, $summary, $icon]) {
                Service::firstOrCreate(['activity' => $activity, 'name' => $name], [
                    'summary' => $summary, 'icon' => $icon, 'position' => $position,
                    'status' => PublicationStatus::PUBLISHED, 'published_at' => now(),
                ]);
            }
        }

        foreach (['Sonorisation', 'Lumière', 'Scène et podiums'] as $position => $name) {
            EquipmentCategory::firstOrCreate(['name' => $name], ['position' => $position]);
        }

        AgendaEvent::firstOrCreate(['title' => 'Festival de Saint-Louis', 'is_reference' => true], [
            'activity' => Activity::EVENTS, 'city' => 'Saint-Louis', 'summary' => 'Sonorisation, lumières et podiums fournis par Impact Live Events.',
            'status' => PublicationStatus::PUBLISHED, 'published_at' => now(),
        ]);

        $dream = 'Impact Live Studio, l\'EMSI et le Centre culturel Habib Faye sont la preuve qu\'un rêve peut devenir réalité, même en Afrique.';

        $this->page('studio', 'Impact Live Studio', 'system', true, [
            ['hero', ['eyebrow' => 'Saint-Louis · Studio d\'enregistrement', 'title' => 'Ici, votre son', 'words' => ['prend vie', 'se raconte', 'se mixe', 'traverse les frontières'],
                'subtitle' => 'Impact Live Studio, le studio fondé par Boubacar Tall, ingénieur du son sénégalais, à Saint-Louis. Un lieu pensé pour les artistes.',
                'layout' => 'studio', 'buttons' => [['label' => 'Réserver une session', 'url' => '#reserver', 'style' => 'primary'], ['label' => 'Écouter nos productions', 'url' => '#productions', 'style' => 'secondary']]]],
            ['services', ['title' => 'Du premier enregistrement au master', 'text' => 'Tarifs indicatifs : le prix final dépend de votre projet et vous est confirmé sur devis.', 'activity' => 'studio']],
            ['productions', ['title' => 'Sorti de nos consoles', 'limit' => 6]],
            ['equipment_list', ['title' => 'Le matériel du studio', 'usage' => 'studio', 'limit' => 24]],
            ['quote', ['text' => $dream, 'author' => 'Boubacar Tall', 'role' => 'Ingénieur du son, fondateur']],
            ['booking_form', ['title' => 'Réserver une session', 'text' => 'Dites-nous ce que vous voulez enregistrer : nous vous proposons une date et un devis.', 'booking_type' => 'studio_session']],
        ]);

        $this->page('events', 'Impact Live Events', 'system', true, [
            ['hero', ['eyebrow' => 'Impact Live Events · Location et prestations', 'title' => 'Le son et la lumière', 'words' => ['des grandes scènes', 'des festivals', 'de vos concerts'],
                'subtitle' => 'Sonorisation de dernière génération, lumières et podiums de spectacle : nous équipons les artistes et les événements, du Festival de Saint-Louis aux concerts privés.',
                'layout' => 'events', 'buttons' => [['label' => 'Demander un devis', 'url' => '/demande', 'style' => 'primary'], ['label' => 'Voir le matériel', 'url' => '/events/materiel', 'style' => 'secondary']]]],
            ['services', ['title' => 'Nos prestations', 'activity' => 'events']],
            ['packs', ['title' => 'Des packs prêts à jouer', 'text' => 'Tout le nécessaire pour votre événement, installé et exploité par nos techniciens.']],
            ['equipment_list', ['title' => 'Le matériel à louer', 'usage' => 'rental', 'limit' => 12]],
            ['agenda', ['title' => 'Ils nous ont fait confiance', 'scope' => 'references', 'limit' => 12]],
            ['agenda', ['title' => 'Prochains événements', 'scope' => 'upcoming', 'activity' => 'events', 'limit' => 6]],
            ['booking_form', ['title' => 'Parlez-nous de votre événement', 'text' => 'Date, lieu, public attendu : nous vous répondons avec une proposition sur mesure.', 'booking_type' => 'event_service']],
        ]);

        $this->page('espace-habib-faye', 'Centre culturel Habib Faye', 'system', true, [
            ['hero', ['eyebrow' => 'Centre culturel · Saint-Louis', 'title' => 'Centre culturel Habib Faye', 'subtitle' => 'Un centre culturel privé à Saint-Louis, dédié à la musique et aux arts vivants.', 'layout' => 'full',
                'buttons' => [['label' => 'Voir la programmation', 'url' => '#programmation', 'style' => 'primary'], ['label' => 'Louer la salle', 'url' => '#louer', 'style' => 'secondary']]]],
            ['text', ['title' => 'Un lieu pour la création', 'body' => '<p>'.$dream.'</p><p>Présentation détaillée du lieu à compléter par l\'équipe.</p>']],
            ['agenda', ['title' => 'Programmation', 'scope' => 'upcoming', 'activity' => 'space', 'limit' => 12]],
            ['services', ['title' => 'Accueillir votre événement', 'activity' => 'space']],
            ['booking_form', ['title' => 'Louer le Centre culturel Habib Faye', 'booking_type' => 'space_rental']],
            ['places', ['title' => 'Nous trouver', 'kind' => 'cultural_center']],
        ]);

        $this->insertBlock('accueil', ['ecosystem', [
            'eyebrow' => 'Un rêve devenu réalité',
            'title' => 'Plus qu\'une école : un écosystème',
            'text' => 'Fondés par Boubacar Tall, ingénieur du son sénégalais basé à Saint-Louis. '.$dream,
            'items' => [
                ['name' => 'EMSI', 'activity' => 'school', 'text' => 'Former aux métiers du son, de l\'image et de la scène, à Dakar et à Saint-Louis.', 'url' => '/formations'],
                ['name' => 'Impact Live Studio', 'activity' => 'studio', 'text' => 'Enregistrer, mixer et masteriser dans un studio pensé pour les artistes.', 'url' => '/studio'],
                ['name' => 'Impact Live Events', 'activity' => 'events', 'text' => 'Sonorisation, lumières et podiums pour les artistes et les festivals.', 'url' => '/events'],
                ['name' => 'Centre culturel Habib Faye', 'activity' => 'space', 'text' => 'Un centre culturel privé à Saint-Louis.', 'url' => '/espace-habib-faye'],
            ],
        ]], after: 'venue');
        $this->insertBlock('ecole', ['places', ['title' => 'Deux campus, les mêmes formations', 'kind' => 'campus']], after: 'venue');
        $this->insertBlock('contact', ['places', ['title' => 'Nos lieux']]);
    }

    private function siteV3(): void
    {
        // Accroches et points forts des campus, seulement s'ils sont encore vides (l'école a pu les saisir).
        $campuses = [
            'emsi-dakar' => ['Au cœur du Grand Théâtre National Doudou Ndiaye Coumba Rose', [
                'Des formations sur les plateaux et dans les salles du Grand Théâtre',
                'Les univers Son, Image, Infographie & design et Scène',
                'Le programme professionnel EMSI × Grand Théâtre',
            ]],
            'emsi-saint-louis' => ['À Saint-Louis, aux côtés d\'Impact Live Studio et du Centre culturel Habib Faye', [
                'Les mêmes formations qu\'à Dakar',
                'Un studio d\'enregistrement et un centre culturel à proximité',
                'Au contact des artistes et des événements de Saint-Louis',
            ]],
        ];
        foreach ($campuses as $slug => [$tagline, $highlights]) {
            $place = Place::where('slug', $slug)->first();
            if ($place) {
                $place->fill(array_filter([
                    'tagline' => $place->tagline ? null : $tagline,
                    'highlights' => $place->highlights ? null : $highlights,
                ]))->save();
            }
        }

        $school = Page::where('slug', 'ecole')->first();
        if (! $school || ! collect($school->draft_blocks ?? [])->contains('type', 'campuses')) {
            $cta = ['label' => 'Candidater', 'url' => '/candidater', 'style' => 'primary'];
            $this->page('ecole', 'L\'école', 'system', true, [
                ['hero', ['eyebrow' => 'L\'école', 'title' => 'Deux écoles, une même passion',
                    'subtitle' => 'L\'EMSI forme aux métiers du son, de l\'image et du spectacle vivant à Dakar, au Grand Théâtre National, et à Saint-Louis. Les mêmes formations, la même exigence, le même matériel professionnel.',
                    'layout' => 'editorial', 'caption' => 'EMSI · Dakar · Saint-Louis',
                    'buttons' => [$cta, ['label' => 'Voir les formations', 'url' => '/formations', 'style' => 'secondary']]]],
                ['campuses', ['eyebrow' => 'Nos campus', 'title' => 'Choisissez votre campus', 'text' => 'À Dakar comme à Saint-Louis, vous suivez le même programme et passez les mêmes certifications.']],
                ['stats', ['title' => 'L\'EMSI en quelques repères', 'items' => [
                    ['value' => '2016', 'label' => 'année de création'],
                    ['value' => '2', 'label' => 'campus', 'detail' => 'Dakar et Saint-Louis'],
                    ['value' => '5', 'label' => 'filières techniques', 'detail' => 'Son, lumière, régie, infographie, cadrage'],
                ]]],
                ['text', ['title' => 'Notre histoire', 'body' => '<p>Créée en 2016, l\'EMSI est une école de formations technico-artistiques. Elle a développé des Certificats de Spécialité (CS) et des BTS dans les métiers du spectacle vivant, et met à la disposition de ses apprenants un parc matériel professionnel, dont un studio de 154 m² et une scène live.</p><p>Fondée par Boubacar Tall, ingénieur du son sénégalais basé à Saint-Louis, l\'EMSI grandit aux côtés d\'Impact Live Studio, d\'Impact Live Events et du Centre culturel Habib Faye.</p>']],
                ['cards', ['title' => 'Notre pédagogie', 'items' => [
                    ['icon' => 'sparkles', 'title' => 'La pratique d\'abord', 'text' => 'Les apprenants sont placés en situation réelle, sur du matériel professionnel.'],
                    ['icon' => 'users', 'title' => 'Un suivi individualisé', 'text' => 'Chaque parcours est accompagné par l\'équipe pédagogique de l\'EMSI.'],
                    ['icon' => 'award', 'title' => 'Des certifications', 'text' => 'CS, BTS et, pour les professionnels, certification de niveau BTS par la VAE.'],
                ]]],
                ['venue', [
                    'eyebrow' => 'Campus de Dakar',
                    'title' => 'Au cœur du Grand Théâtre National Doudou Ndiaye Coumba Rose',
                    'text' => 'À Dakar, nos apprenants se forment là où le spectacle se fabrique : sur les plateaux, dans les salles et en régie.',
                    'facts' => [['value' => '154 m²', 'label' => 'de studio'], ['value' => 'Live', 'label' => 'une scène pour s\'exercer en conditions réelles']],
                ]],
                ['equipment', [
                    'title' => 'Sur quoi vous vous formez',
                    'text' => 'Le matériel des grandes scènes et des plateaux de télévision, en conditions réelles.',
                    'groups' => [
                        ['category' => 'Consoles son', 'items' => ['Yamaha', 'Allen & Heath', 'DiGiCo', 'Midas']],
                        ['category' => 'Diffusion et réseaux audio', 'items' => ['Systèmes Line Array', 'Dante', 'MADI']],
                        ['category' => 'Lumière de spectacle', 'items' => ['GrandMA', 'Chamsys', 'Avolites', 'DMX, Art-Net, sACN']],
                        ['category' => 'Image et broadcast', 'items' => ['Caméras broadcast (fibre, HF)', 'Super Slow Motion', 'Intercom de régie']],
                        ['category' => 'Création numérique', 'items' => ['After Effects', 'Cinema 4D', 'Chroma Key en temps réel']],
                    ],
                ]],
                ['partners', ['title' => 'Nos partenaires']],
                ['cta', ['title' => 'Venez nous rencontrer', 'text' => 'À Dakar ou à Saint-Louis : une question sur nos formations ? Écrivez-nous ou candidatez en ligne.', 'buttons' => [$cta, ['label' => 'Nous contacter', 'url' => '/contact', 'style' => 'secondary']]]],
            ], overwrite: true);
        }

        $this->insertBlock('accueil', ['stats', ['title' => 'L\'EMSI en quelques repères', 'items' => [
            ['value' => '2', 'label' => 'campus', 'detail' => 'Dakar et Saint-Louis'],
            ['value' => '5', 'label' => 'univers', 'detail' => 'Son, Image, Design, Scène, et bientôt le Cinéma'],
            ['value' => '2016', 'label' => 'année de création'],
        ]]], after: 'marquee');
        $this->insertBlock('accueil', ['campuses', ['eyebrow' => 'Deux écoles', 'title' => 'Dakar ou Saint-Louis ?', 'text' => 'Les mêmes formations dans nos deux campus : choisissez le plus proche de chez vous.']], after: 'rooms');
        $this->insertBlock('accueil', ['agenda', ['title' => 'Prochains rendez-vous', 'scope' => 'upcoming', 'limit' => 4]], after: 'professional_space');
    }

    /**
     * Ajoute un bloc à une page s'il n'y est pas déjà. Si la page a des modifications non publiées,
     * le bloc est ajouté au brouillon sans rien publier à la place de l'équipe.
     */
    private function insertBlock(string $slug, array $block, ?string $after = null): void
    {
        $page = Page::where('slug', $slug)->first();
        if (! $page || collect($page->draft_blocks ?? [])->contains('type', $block[0])) {
            return;
        }

        $publish = $page->isPublished() && ! $page->hasUnpublishedChanges();
        $blocks = $page->draft_blocks ?? [];
        $index = $after ? collect($blocks)->search(fn ($b) => ($b['type'] ?? null) === $after) : false;
        array_splice($blocks, $index === false ? count($blocks) : $index + 1, 0, [['type' => $block[0], 'data' => $block[1]]]);

        $page->update(['draft_blocks' => $blocks]);
        if ($publish) {
            $page->publish();
        }
    }

    private function menus(bool $hideOthers = false): void
    {
        $items = [
            'main' => [['Univers', '/univers', false], ['Formations', '/formations', false], ['Studio', '/studio', false], ['Events', '/events', false],
                ['L\'École', '/ecole', false], ['Espace Pro', '/professionnels', false], ['Candidater', '/candidater', true]],
            'footer' => [['Réalisations', '/realisations', false], ['Agenda', '/agenda', false], ['Centre culturel Habib Faye', '/espace-habib-faye', false],
                ['Actualités', '/actualites', false], ['Contact', '/contact', false]],
            'legal' => [['Mentions légales', '/mentions-legales', false], ['Protection des données', '/confidentialite', false]],
        ];

        foreach ($items as $location => $links) {
            foreach ($links as $position => [$label, $url, $button]) {
                MenuItem::updateOrCreate(['location' => $location, 'url' => $url], ['label' => $label, 'is_button' => $button, 'position' => $position, 'is_visible' => true]);
            }

            // Les anciennes entrées sont masquées, jamais supprimées : on peut les réafficher dans l'admin.
            if ($hideOthers) {
                MenuItem::where('location', $location)->whereNotIn('url', array_column($links, 1))->update(['is_visible' => false]);
            }
        }
    }

    private function redirects(): void
    {
        foreach ([
            '/projet' => '/professionnels', '/vae' => '/professionnels/bts-vae', '/galerie' => '/realisations',
            '/admission' => '/candidater', '/admission/succes' => '/candidater',
        ] as $from => $to) {
            Redirect::updateOrCreate(['from_path' => $from], ['to_path' => $to, 'status_code' => 301]);
        }
    }

    /** Impact Live Events est retiré du site : sa page est gardée, en brouillon (plus servie). */
    private function unpublishEvents(): void
    {
        $events = Page::where('slug', 'events')->first();
        if ($events && $events->status !== PublicationStatus::DRAFT) {
            $events->forceFill(['status' => PublicationStatus::DRAFT])->save();
            $this->report[] = 'Page « /events » (Impact Live Events) repassée en brouillon : gardée, mais plus affichée.';
        }
    }

    /**
     * Liens des blocs (version en ligne et brouillon) et des menus vers les nouvelles adresses ;
     * surtitres de héros « Dakar · Grand Théâtre National » → « Dakar · Saint-Louis ».
     */
    private function rewriteLinks(): void
    {
        $pages = [];
        foreach (Page::orderBy('id')->get() as $page) {
            $draft = $this->rewriteBlocks($page->draft_blocks);
            $live = $this->rewriteBlocks($page->blocks);
            if ($draft !== $page->draft_blocks || $live !== $page->blocks) {
                $this->saveBlocks($page, $draft, $live);
                $pages[] = '/'.$page->slug;
            }
        }
        if ($pages) {
            $this->report[] = 'Liens et surtitres mis à jour sur '.count($pages).' page(s) : '.implode(', ', $pages).'.';
        }

        $menus = 0;
        foreach (MenuItem::orderBy('id')->get() as $item) {
            $url = LegacyPaths::rewrite((string) $item->url);
            if ($url !== $item->url) {
                $item->update(['url' => $url]);
                $menus++;
            }
        }
        if ($menus) {
            $this->report[] = "Liens de menu mis à jour : {$menus}.";
        }
    }

    private function rewriteBlocks(?array $blocks): ?array
    {
        if ($blocks === null) {
            return null;
        }

        return array_map(function ($block) {
            if (! is_array($block)) {
                return $block;
            }
            $block = $this->rewriteValue($block);
            if (($block['type'] ?? null) === 'hero' && is_array($block['data'] ?? null)) {
                $block['data'] = $this->siteV4Eyebrow($block['data']);
                foreach ($block['data']['slides'] ?? [] as $i => $slide) {
                    if (is_array($slide)) {
                        $block['data']['slides'][$i] = $this->siteV4Eyebrow($slide);
                    }
                }
            }

            return $block;
        }, $blocks);
    }

    /** Adresses (champs « url », « …_url ») et liens des textes enrichis, à toute profondeur. */
    private function rewriteValue(mixed $value, int|string|null $key = null): mixed
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->rewriteValue($v, is_string($k) ? $k : null);
            }

            return $value;
        }
        if (! is_string($value) || $key === null) {
            return $value;
        }
        if ($key === 'url' || str_ends_with($key, '_url')) {
            return LegacyPaths::rewrite($value);
        }

        return str_contains($value, 'href') ? LegacyPaths::rewriteHtml($value) : $value;
    }

    private function siteV4Eyebrow(array $data): array
    {
        if (is_string($data['eyebrow'] ?? null)) {
            $data['eyebrow'] = str_replace('Dakar · Grand Théâtre National', 'Dakar · Saint-Louis', $data['eyebrow']);
        }

        return $data;
    }

    /**
     * Enregistre des blocs corrigés sans rien publier à la place de l'équipe : une page en ligne sans
     * modification en attente est republiée (nouvelle révision) ; sinon la version en ligne et le brouillon
     * sont corrigés chacun de leur côté, et la nouvelle version en ligne est gardée dans l'historique.
     */
    private function saveBlocks(Page $page, ?array $draft, ?array $live): void
    {
        if ($page->isPublished() && ! $page->hasUnpublishedChanges()) {
            $page->update(['draft_blocks' => $draft]);
            $page->publish();

            return;
        }

        $liveChanged = $live !== $page->blocks;
        $page->forceFill(['draft_blocks' => $draft, 'blocks' => $live])->save();
        if ($page->isPublished() && $liveChanged) {
            $page->revisions()->create(['title' => $page->title, 'blocks' => $live]);
        }
    }

    /** Déplace une page (même enregistrement : blocs, historique et statut gardés), jamais sur une page existante. */
    private function movePages(): void
    {
        $moves = [
            ['studio', 'centre-culturel/studio', SiteDomain::STUDIO],
            ['ecole', 'emsi', SiteDomain::EMSI],
            ['espace-habib-faye', 'centre-culturel', SiteDomain::MAISON],
            ['professionnels', 'emsi/professionnels', SiteDomain::EMSI],
        ];
        foreach ($moves as [$from, $to, $domain]) {
            $page = Page::where('slug', $from)->first();
            if (! $page) {
                continue;
            }
            if (Page::where('slug', $to)->exists()) {
                $this->report[] = "Page « /{$from} » non déplacée : l'adresse « /{$to} » est déjà prise (rien n'a été écrasé). À régler dans l'admin.";

                continue;
            }
            $page->forceFill(['slug' => $to, 'domain' => $domain])->save();
            $this->report[] = "Page déplacée : /{$from} → /{$to}.";
        }
    }

    /** Pages des trois domaines, créées seulement si leur adresse est libre, publiées, textes « À compléter ». */
    private function siteV4Pages(): void
    {
        $todo = fn (string $what) => "À compléter : {$what}";
        $hero = fn (string $eyebrow, string $title, string $subtitle, array $buttons = []) => ['hero', array_filter([
            'eyebrow' => $eyebrow, 'title' => $title, 'subtitle' => $subtitle, 'layout' => 'compact', 'buttons' => $buttons,
        ])];
        $text = fn (string $title, string $body) => ['text', ['title' => $title, 'body' => "<p>{$body}</p>"]];

        // Seulement si aucune page n'a pu y être déplacée (nouvelle installation).
        $this->createPage('centre-culturel', 'Centre culturel Habib Faye', SiteDomain::MAISON, [
            $hero('Centre culturel · Saint-Louis', 'Centre culturel Habib Faye', $todo('présentez ici le Centre culturel, Habib Faye et Boubacar Tall.')),
            $text('Notre mission', $todo('la mission du Centre culturel.')),
            ['agenda', ['title' => 'Programmation', 'scope' => 'upcoming', 'limit' => 6]],
            ['places', ['title' => 'Nous trouver', 'kind' => 'cultural_center']],
        ]);
        $this->createPage('emsi', 'L\'école', SiteDomain::EMSI, [
            $hero('EMSI · Dakar · Saint-Louis', 'École des Métiers du Son et de l\'Image', $todo('présentez ici l\'école.'),
                [['label' => 'Candidater', 'url' => '/candidater', 'style' => 'primary']]),
            ['campuses', ['eyebrow' => 'Nos campus', 'title' => 'Choisissez votre campus']],
            ['programs', ['title' => 'Nos formations', 'audience' => 'school', 'limit' => 6]],
        ]);

        $this->createPage('centre-culturel/agenda', 'Programmation', SiteDomain::MAISON, [
            $hero('Centre culturel Habib Faye', 'Programmation', $todo('présentez ici la programmation du Centre culturel (concerts, résidences, rencontres).')),
            ['agenda', ['title' => 'Prochains rendez-vous', 'scope' => 'upcoming', 'limit' => 24]],
        ]);
        $this->createPage('centre-culturel/espaces', 'Les espaces', SiteDomain::MAISON, [
            $hero('Centre culturel Habib Faye · Saint-Louis', 'Les espaces', $todo('présentez ici les salles et espaces à louer (capacité, équipement).')),
            ['places', ['title' => 'Nos espaces', 'kind' => 'cultural_center']],
            ['booking_form', ['title' => 'Louer un espace', 'text' => 'Date, type d\'événement, public attendu : nous vous répondons avec une proposition.', 'booking_type' => 'space_rental']],
        ]);

        // Campus : repérés par leur ville (Dakar, Saint-Louis), sinon par leur ordre dans Administration › Lieux.
        $campuses = Place::campuses()->orderBy('position')->orderBy('id')->get();
        $dakar = $campuses->first(fn (Place $p) => Str::slug((string) $p->city) === 'dakar') ?? $campuses->get(0);
        $saintLouis = $campuses->first(fn (Place $p) => Str::slug((string) $p->city) === 'saint-louis')
            ?? $campuses->first(fn (Place $p) => ! $p->is($dakar));

        $venue = collect([Page::where('type', 'home')->orderBy('id')->first(), Page::where('slug', 'accueil')->first(), Page::where('slug', 'emsi')->first()])
            ->filter()->map(fn (Page $p) => collect($p->blocks ?? $p->draft_blocks ?? [])->firstWhere('type', 'venue')['data'] ?? null)->filter()->first()
            ?? ['eyebrow' => 'Notre adresse', 'title' => 'Au cœur du Grand Théâtre National Doudou Ndiaye Coumba Rose',
                'text' => 'À Dakar, nos apprenants se forment là où le spectacle se fabrique : sur les plateaux, dans les salles et en régie.'];
        $venue['buttons'] = [];
        $theatre = Partner::where('name', 'like', '%Grand Théâtre%')->first();

        foreach ([['emsi/dakar', 'Dakar', $dakar], ['emsi/saint-louis', 'Saint-Louis', $saintLouis]] as [$slug, $city, $campus]) {
            $blocks = [$hero("EMSI · Campus de {$city}", "Campus de {$city}", $todo("présentez ici le campus de {$city} (lieu, équipe, équipements)."),
                $campus ? [['label' => "Candidater à {$city}", 'url' => "/candidater?campus={$campus->slug}", 'style' => 'primary']] : [])];
            if ($campus) {
                $blocks[] = ['campus_programs', ['title' => "Les formations à {$city}", 'campus_id' => $campus->id]];
            } elseif (! Page::where('slug', $slug)->exists()) {
                $this->report[] = "Aucun campus de {$city} dans Administration › Lieux : bloc « Formations de ce campus » à ajouter sur /{$slug}.";
            }
            if ($city === 'Dakar') {
                $blocks[] = ['venue', $venue];
                $blocks[] = ['partners', ['title' => 'Notre partenaire, le Grand Théâtre National', 'categories' => [$theatre->category ?? 'co_organizer']]];
            }
            $this->createPage($slug, "Campus de {$city}", SiteDomain::EMSI, $blocks);
        }

        $this->createPage('mission', 'Mission et impact', SiteDomain::GENERAL, [
            $hero('Centre culturel Habib Faye · EMSI · Impact Live Studio', 'Mission et impact', $todo('résumez ici la mission commune des trois lieux.')),
            $text('Notre mission', $todo('la mission, les publics touchés, les résultats (chiffres fournis par l\'équipe).')),
        ]);
        $this->createPage('partenaires', 'Partenaires et soutiens', SiteDomain::GENERAL, [
            $hero('Ils nous accompagnent', 'Partenaires et soutiens', $todo('présentez ici ce que les partenaires rendent possible.')),
            ['partners', ['title' => 'Nos partenaires']],
        ]);
        $this->createPage('soutenir', 'Nous soutenir', SiteDomain::GENERAL, [
            $hero('Partenariat, mécénat, don', 'Nous soutenir', $todo('expliquez ici comment soutenir le Centre culturel, l\'école et le studio.')),
            ['support_form', ['title' => 'Nous écrire', 'text' => 'Partenaires, mécènes et donateurs : écrivez-nous, nous vous répondrons rapidement.']],
        ]);
        $this->createPage('presse', 'Presse', SiteDomain::GENERAL, [
            $hero('Espace presse', 'Presse', $todo('présentez ici le contact presse et les documents à disposition.')),
            // Pas de bloc « Documents » vide : l'admin exige au moins un document pour enregistrer la page.
            $text('Dossier de presse', $todo('ajoutez un bloc « Documents à télécharger » avec le dossier de presse et les logos, puis retirez ce texte.')),
        ]);
    }

    private function createPage(string $slug, string $title, SiteDomain $domain, array $blocks): void
    {
        if (Page::where('slug', $slug)->exists()) {
            return;
        }
        $page = Page::create(['slug' => $slug, 'title' => $title, 'type' => 'system', 'domain' => $domain, 'is_locked' => true,
            'draft_blocks' => array_map(fn (array $block) => ['type' => $block[0], 'data' => $block[1]], $blocks)]);
        $page->publish();
        $this->report[] = "Page créée : /{$slug} ({$title}), textes « À compléter ».";
    }

    /**
     * Accueil des trois domaines : héros Cinéma (une diapositive par domaine, photos des pages existantes),
     * triptyque, puis chiffres, agenda, actualités et partenaires de l'accueil actuel. L'ancienne version
     * (et un brouillon non publié) restent dans l'historique. Un accueil qui a déjà le triptyque n'est pas touché.
     */
    private function homeV4(): void
    {
        $home = Page::where('type', 'home')->orderBy('id')->first() ?? Page::where('slug', 'accueil')->first();
        if (! $home) {
            $this->report[] = 'Accueil introuvable : non modifié.';

            return;
        }
        // Construit depuis la version en ligne : un brouillon non relu n'est jamais publié (il est gardé dans l'historique).
        $current = $home->blocks ?? [];
        if (collect($current)->contains('type', 'domains') || collect($home->draft_blocks ?? [])->contains('type', 'domains')) {
            $this->report[] = 'Accueil : déjà au format des trois domaines, non modifié.';

            return;
        }

        $images = [
            'maison' => $this->heroImage(['centre-culturel', 'espace-habib-faye']),
            'emsi' => $this->heroImage([$home->slug, 'emsi', 'ecole']),
            'studio' => $this->heroImage(['centre-culturel/studio', 'studio']),
        ];
        $domains = [
            'maison' => ['Centre culturel · Saint-Louis', 'Centre culturel Habib Faye', 'Concerts, résidences et transmission, à Saint-Louis.', '/centre-culturel', 'Découvrir le Centre culturel'],
            'emsi' => ['École · Dakar · Saint-Louis', 'EMSI', 'Les métiers du son, de l\'image et de la scène, sur du matériel professionnel.', '/emsi', 'Se former'],
            'studio' => ['Studio d\'enregistrement · Saint-Louis', 'Impact Live Studio', 'Enregistrement, mixage et mastering, dans un studio pensé pour les artistes.', '/centre-culturel/studio', 'Réserver une séance'],
        ];

        $panels = [];
        $slides = [];
        foreach ($domains as $domain => [$eyebrow, $title, $text, $url, $label]) {
            $image = $images[$domain] ?? ['image' => null, 'image_alt' => null];
            $panels[] = ['domain' => $domain, 'eyebrow' => $eyebrow, 'title' => $title, 'text' => $text] + $image + ['url' => $url, 'label' => $label];
            $slides[] = $image + ['eyebrow' => $eyebrow, 'title' => $title, 'link_label' => $label, 'link_url' => $url];
        }

        $blocks = [];
        $missing = array_keys(array_filter($images, fn ($image) => $image === null));
        if ($missing === []) {
            $blocks[] = ['type' => 'hero', 'data' => ['eyebrow' => 'Dakar · Saint-Louis', 'title' => 'La culture comme héritage, l\'art comme métier', 'layout' => 'cinema',
                'slides' => $slides, 'facts' => [['value' => '3', 'label' => 'maisons'], ['value' => '2', 'label' => 'campus de l\'EMSI']]]];
        } else {
            $names = array_map(fn (string $domain) => SiteDomain::from($domain)->label(), $missing);
            $this->report[] = 'Accueil : photo manquante pour '.implode(', ', $names).' (image du héros de sa page) : le triptyque ouvre la page à la place du héros Cinéma. '
                .'Ajoutez les photos puis un héros « Cinéma » dans l\'admin si vous le souhaitez.';
        }
        $blocks[] = ['type' => 'domains', 'data' => ['intro' => 'La culture comme héritage, l\'art comme métier', 'panels' => $panels]];
        foreach (['stats', 'agenda', 'news', 'partners'] as $type) {
            if ($block = collect($current)->firstWhere('type', $type)) {
                $blocks[] = $block;
            }
        }

        if ($home->hasUnpublishedChanges() && $home->draft_blocks !== null) {
            $home->revisions()->create(['title' => $home->title.' (brouillon non publié)', 'blocks' => $home->draft_blocks]);
        }
        $home->update(['draft_blocks' => $blocks]);
        $home->publish();
        $this->report[] = 'Accueil republié avec '.($missing === [] ? 'le héros Cinéma et ' : '').'le triptyque des trois lieux ; l\'ancienne version reste dans l\'historique.';
    }

    /**
     * Page EMSI : les univers et les réalisations (repris de l'ancien accueil, sinon textes de départ),
     * juste après les cartes des campus ; le lieu du Grand Théâtre est retiré s'il est déjà sur /emsi/dakar.
     * Un bloc que la page a déjà eu (dans son historique) et que l'équipe a retiré n'est pas remis.
     * Brouillon en attente respecté ; nouvelle révision seulement si quelque chose change. Relançable.
     */
    private function completeEmsiPage(): void
    {
        $page = Page::where('slug', 'emsi')->first();
        if (! $page) {
            return;
        }
        $history = $page->revisions()->pluck('blocks')->flatten(1)->pluck('type')->filter()->unique();
        $dakar = Page::where('slug', 'emsi/dakar')->first();
        $venueAtDakar = collect([...($dakar?->blocks ?? []), ...($dakar?->draft_blocks ?? [])])->contains('type', 'venue');

        $complete = function (?array $blocks) use ($history, $venueAtDakar): ?array {
            if ($blocks === null) {
                return null;
            }
            $blocks = array_values($blocks);
            if ($venueAtDakar) {
                $blocks = array_values(array_filter($blocks, fn ($block) => ($block['type'] ?? null) !== 'venue'));
            }
            $types = array_column($blocks, 'type');
            $missing = array_values(array_filter(['rooms', 'artworks'], fn (string $type) => ! in_array($type, $types, true) && ! $history->contains($type)));
            if ($missing === []) {
                return $blocks;
            }
            $at = array_search('campuses', $types, true);
            $at = $at === false ? min(1, count($blocks)) : $at + 1;
            array_splice($blocks, $at, 0, array_map(fn (string $type) => ['type' => $type, 'data' => $this->formerHomeBlock($type)], $missing));

            return $blocks;
        };

        $live = $complete($page->blocks);
        $draft = $complete($page->draft_blocks);
        if ($live === $page->blocks && $draft === $page->draft_blocks) {
            return;
        }
        $this->saveBlocks($page, $draft, $live);
        $this->report[] = 'Page « /emsi » complétée : univers et réalisations des étudiants'.($venueAtDakar ? ', lieu du Grand Théâtre laissé à /emsi/dakar' : '').'.';
    }

    /** Bloc de l'accueil actuel ou de son historique (le plus récent), sinon les textes de départ. */
    private function formerHomeBlock(string $type): array
    {
        $home = Page::where('type', 'home')->orderBy('id')->first() ?? Page::where('slug', 'accueil')->first();
        $versions = collect([$home?->blocks, $home?->draft_blocks])->merge($home?->revisions()->pluck('blocks') ?? []);
        foreach ($versions as $blocks) {
            $block = collect($blocks ?? [])->firstWhere('type', $type);
            if (is_array($block['data'] ?? null)) {
                return $block['data'];
            }
        }

        return match ($type) {
            'rooms' => ['eyebrow' => 'Cinq univers, une école', 'title' => 'Choisissez votre univers',
                'text' => 'Chaque univers a sa lumière, ses outils et ses métiers. Explorez-les, puis trouvez la formation qui vous ressemble.'],
            'artworks' => ['title' => 'Réalisations des étudiants', 'source' => 'latest', 'limit' => 6],
        };
    }

    /** @return array{image: string, image_alt: ?string}|null Photo du premier héros (version en ligne) de la première de ces pages qui en a une. */
    private function heroImage(array $slugs): ?array
    {
        foreach ($slugs as $slug) {
            $page = Page::where('slug', $slug)->first();
            $hero = collect($page?->blocks ?? $page?->draft_blocks ?? [])->firstWhere('type', 'hero')['data'] ?? [];
            if (is_string($hero['image'] ?? null) && $hero['image'] !== '') {
                return ['image' => $hero['image'], 'image_alt' => $hero['image_alt'] ?? null];
            }
        }

        return null;
    }

    /** Formations sans campus coché : proposées dans tous les campus. Les sessions restent « les deux campus ». */
    private function programCampuses(): void
    {
        $campuses = Place::campuses()->orderBy('position')->orderBy('id')->pluck('id');
        if ($campuses->isEmpty()) {
            return;
        }
        $programs = Program::whereDoesntHave('campuses')->get();
        $programs->each(fn (Program $program) => $program->campuses()->sync($campuses));
        if ($programs->isNotEmpty()) {
            $this->report[] = "Formations proposées dans les {$campuses->count()} campus : {$programs->count()}.";
        }
    }

    /**
     * Menu principal (Centre culturel et EMSI avec leurs sous-menus) et pied de page, construits une seule fois :
     * s'ils existent déjà, l'équipe a pu les retoucher et on n'y touche plus. Les entrées existantes à la
     * même adresse et au même libellé (Contact, Candidater…) sont réutilisées, les autres sont masquées (jamais supprimées).
     */
    private function menusV4(): void
    {
        if (MenuItem::where('location', 'main')->whereNull('parent_id')->where('url', '/centre-culturel')->whereHas('children')->exists()) {
            $this->report[] = 'Menu principal : déjà en place, non modifié.';
        } else {
            $claimed = [];
            $tree = [
                ['Accueil', '/', false, []],
                ['Centre culturel Habib Faye', '/centre-culturel', false, [['Le Centre culturel', '/centre-culturel'], ['Programmation', '/centre-culturel/agenda'],
                    ['Impact Live Studio', '/centre-culturel/studio'], ['Les espaces', '/centre-culturel/espaces']]],
                ['EMSI', '/emsi', false, [['L\'école', '/emsi'], ['Campus de Dakar', '/emsi/dakar'], ['Campus de Saint-Louis', '/emsi/saint-louis'],
                    ['Formations', '/emsi/formations'], ['VAE et professionnels', '/emsi/professionnels'], ['Réalisations', '/emsi/realisations']]],
                [MenuDefaults::ABOUT_LABEL, MenuDefaults::ABOUT_LINKS[0][1], false, MenuDefaults::ABOUT_LINKS],
                ['Candidater', '/candidater', true, []],
            ];
            foreach ($tree as $position => [$label, $url, $button, $children]) {
                $parent = $this->menuItem($claimed, 'main', $url, ['label' => $label, 'parent_id' => null, 'is_button' => $button, 'position' => $position]);
                foreach ($children as $childPosition => [$childLabel, $childUrl]) {
                    $this->menuItem($claimed, 'main', $childUrl, ['label' => $childLabel, 'parent_id' => $parent->id, 'is_button' => false, 'position' => $childPosition]);
                }
            }
            MenuItem::where('location', 'main')->whereNotIn('id', $claimed)->update(['is_visible' => false]);
            MenuDefaults::fillDescriptions();
            MenuDefaults::fillParents();
            $this->report[] = 'Menu principal reconstruit (Accueil, Centre culturel Habib Faye, EMSI, À propos, Candidater) ; les anciennes entrées sont masquées.';
        }

        if (MenuItem::where('location', 'footer')->where('url', '/mission')->exists()) {
            $this->report[] = 'Pied de page : déjà en place, non modifié.';
        } else {
            $claimed = [];
            $links = [['Mission et impact', '/mission'], ['Partenaires et soutiens', '/partenaires'], ['Nous soutenir', '/soutenir'],
                ['Actualités', '/actualites'], ['Presse', '/presse'], ['Contact', '/contact']];
            foreach ($links as $position => [$label, $url]) {
                $this->menuItem($claimed, 'footer', $url, ['label' => $label, 'parent_id' => null, 'is_button' => false, 'position' => $position]);
            }
            MenuItem::where('location', 'footer')->whereNotIn('id', $claimed)->update(['is_visible' => false]);
            $this->report[] = 'Pied de page reconstruit ; les anciennes entrées sont masquées.';
        }
    }

    /** Réutilise la même entrée (même adresse, même libellé, pas encore prise), sinon la crée ; toujours visible. */
    private function menuItem(array &$claimed, string $location, string $url, array $attributes): MenuItem
    {
        $item = MenuItem::where('location', $location)->where('url', $url)->where('label', $attributes['label'])->whereNotIn('id', $claimed)
            ->orderByDesc('is_visible')->orderBy('id')->first() ?? new MenuItem(['location' => $location, 'url' => $url]);
        $item->fill($attributes + ['is_visible' => true])->save();
        $claimed[] = $item->id;

        return $item;
    }

    /** Redirections de l'admin : cible directe vers la nouvelle adresse (pas de chaîne de redirections). */
    private function rewriteRedirects(): void
    {
        $count = 0;
        foreach (Redirect::orderBy('id')->get() as $redirect) {
            $to = LegacyPaths::rewrite((string) $redirect->to_path);
            if ($to !== $redirect->to_path) {
                $redirect->update(['to_path' => $to]);
                $count++;
            }
        }
        if ($count) {
            $this->report[] = "Redirections de l'admin mises à jour : {$count}.";
        }
    }
}
