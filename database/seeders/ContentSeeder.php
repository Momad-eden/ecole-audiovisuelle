<?php

namespace Database\Seeders;

use App\Enums\Audience;
use App\Enums\CohortStatus;
use App\Enums\FundingMode;
use App\Enums\ProgramKind;
use App\Enums\PublicationStatus;
use App\Models\Cohort;
use App\Models\Faq;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Program;
use App\Models\Redirect;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Track;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Contenu de référence (idempotent), tiré du document de projet EMSI × Grand Théâtre
 * après correction de ses incohérences. Aucun chiffre ni partenaire non sourcé.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->settings();
        $rooms = $this->rooms();
        $tracks = $this->tracks($rooms);
        $this->programs($tracks);
        $this->faqs();
        $this->partners();
        $this->pages();
        $this->menus();
        $this->redirects();
    }

    private function settings(): void
    {
        Setting::current()->update([
            'school_name' => 'EMSI — École des Métiers du Son et de l\'Image',
            'description' => 'École de formation aux métiers techniques et artistiques du son, de la lumière, de l\'image et du spectacle vivant, à Dakar.',
            'seo_title' => 'EMSI — École des Métiers du Son et de l\'Image, Dakar',
            'seo_description' => 'Formations aux métiers techniques du son, de la lumière, de l\'image et du spectacle vivant à Dakar. Musée numérique des réalisations des apprenants.',
        ]);
    }

    /** @return array<string, Room> */
    private function rooms(): array
    {
        $rooms = [
            'son' => ['Salle du Son', '#F5B83D', 'Écouter le geste technique : captations, mixages, créations sonores.'],
            'lumiere' => ['Salle de la Lumière', '#5CC8FF', 'La lumière comme matière : ambiances, scénographies, conduites de spectacle.'],
            'image' => ['Salle de l\'Image', '#FF5A4E', 'Cadrer, capter, réaliser : l\'image en direct et en mouvement.'],
            'visuel' => ['Salle du Visuel', '#C084FC', 'Motion design, habillage, création graphique pour la scène et l\'écran.'],
        ];

        $models = [];
        foreach ($rooms as $key => [$name, $color, $tagline]) {
            $models[$key] = Room::updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name, 'accent_color' => $color, 'tagline' => $tagline,
                'position' => count($models), 'status' => PublicationStatus::PUBLISHED, 'published_at' => now(),
            ]);
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
            'lumiere' => ['Technicien Lumière', 'Lumière', 'lumiere',
                'Éclairage scénique et conception lumineuse : programmation sur consoles professionnelles, réseaux DMX/Art-Net/sACN, dimensionnement d\'un parc projecteurs, conduite de spectacle en direct.',
                ['Programmation GrandMA, Chamsys, Avolites', 'Réseaux DMX, Art-Net, sACN', 'Calage et dimensionnement d\'un parc projecteurs', 'Synchronisation lumière-son-vidéo', 'Conduite d\'un spectacle en direct'],
                ['Régisseur lumière', 'Concepteur lumière (Lighting Designer)', 'Chef électricien de spectacle', 'Programmateur lumière']],
            'regie' => ['Régie Générale Spectacle', 'Régie générale', 'lumiere',
                'Management technique d\'un événement : cahier des charges, dimensionnement matériel et humain, coordination des équipes son, lumière, vidéo et sécurité, conduite du spectacle en direct.',
                ['Lecture et rédaction de cahiers des charges', 'Dimensionnement d\'un parc matériel complet', 'Coordination d\'équipes techniques', 'Planning de montage et démontage', 'Sécurité des spectacles et gestion des flux de publics'],
                ['Régisseur général spectacle', 'Régisseur adjoint', 'Chef de projet événementiel', 'Coordinateur technique de festivals']],
            'infographie' => ['Infographie et Création Numérique', 'Infographie', 'visuel',
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

    private function pages(): void
    {
        $cta = ['label' => 'Candidater', 'url' => '/candidater', 'style' => 'primary'];

        $this->page('accueil', 'Accueil', 'home', true, [
            ['hero', ['eyebrow' => 'Dakar · Son · Lumière · Image', 'title' => 'Le musée vivant des métiers du son et de l\'image', 'subtitle' => 'Entrez dans les salles de l\'EMSI : les réalisations de nos apprenants, les métiers qu\'elles révèlent et les formations qui y mènent.', 'layout' => 'full', 'buttons' => [['label' => 'Entrer dans le musée', 'url' => '/musee', 'style' => 'primary'], ['label' => 'Candidater', 'url' => '/candidater', 'style' => 'secondary']]]],
            ['rooms', ['title' => 'Entrez dans les salles', 'text' => 'Chaque salle réunit les œuvres d\'une discipline, éclairées par sa propre lumière.']],
            ['artworks', ['title' => 'Œuvres à la une', 'source' => 'featured', 'limit' => 6]],
            ['programs', ['title' => 'Nos formations', 'audience' => 'school', 'limit' => 6]],
            ['professional_space', ['title' => 'Espace Professionnels', 'text' => 'Techniciens titulaires d\'un CPS ou d\'un CS : découvrez le programme EMSI × Grand Théâtre, perfectionnement intensif et certification de niveau BTS par la VAE.', 'button_label' => 'Découvrir le programme']],
            ['news', ['title' => 'Actualités', 'limit' => 3]],
            ['partners', ['title' => 'Ils accompagnent l\'école']],
            ['cta', ['title' => 'Votre parcours commence ici', 'text' => 'Déposez votre candidature en ligne en quelques minutes.', 'buttons' => [$cta]]],
        ]);

        $this->page('ecole', 'L\'école', 'system', true, [
            ['hero', ['eyebrow' => 'L\'école', 'title' => 'École des Métiers du Son et de l\'Image', 'subtitle' => 'Former des techniciens et des créateurs par la pratique, au plus près des scènes et des plateaux.', 'layout' => 'full']],
            ['text', ['title' => 'Notre histoire', 'body' => '<p>Créée en 2016, l\'EMSI est une école de formations technico-artistiques. Elle a développé des Certificats de Spécialité (CS) et des BTS dans les métiers du spectacle vivant, et met à la disposition de ses apprenants un parc matériel professionnel, dont un studio de 154 m² et une scène live.</p>']],
            ['cards', ['title' => 'Notre pédagogie', 'items' => [
                ['icon' => 'sparkles', 'title' => 'La pratique d\'abord', 'text' => 'Les apprenants sont placés en situation réelle, sur du matériel professionnel.'],
                ['icon' => 'users', 'title' => 'Un suivi individualisé', 'text' => 'Chaque parcours est accompagné par l\'équipe pédagogique de l\'EMSI.'],
                ['icon' => 'award', 'title' => 'Des certifications', 'text' => 'CS, BTS et, pour les professionnels, certification de niveau BTS par la VAE.'],
            ]]],
            ['partners', ['title' => 'Nos partenaires']],
            ['cta', ['title' => 'Venez nous rencontrer', 'text' => 'Une question sur nos formations ? Écrivez-nous ou candidatez en ligne.', 'buttons' => [['label' => 'Nous contacter', 'url' => '/contact', 'style' => 'secondary'], $cta]]],
        ]);

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
            ['timeline', ['title' => 'Calendrier', 'steps' => [
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

    private function page(string $slug, string $title, string $type, bool $publish, array $blocks): void
    {
        $page = Page::firstOrNew(['slug' => $slug]);
        if ($page->exists) {
            return;
        }

        $page->fill([
            'title' => $title, 'type' => $type, 'is_locked' => $type !== 'free',
            'draft_blocks' => array_map(fn (array $block) => ['type' => $block[0], 'data' => $block[1]], $blocks),
        ])->save();

        if ($publish) {
            $page->publish();
        }
    }

    private function menus(): void
    {
        $items = [
            ['main', 'Le Musée', '/musee', false], ['main', 'Formations', '/formations', false], ['main', 'L\'École', '/ecole', false],
            ['main', 'Actualités', '/actualites', false], ['main', 'Espace Pro', '/professionnels', false], ['main', 'Candidater', '/candidater', true],
            ['footer', 'Contact', '/contact', false], ['footer', 'Espace Professionnels', '/professionnels', false],
            ['legal', 'Mentions légales', '/mentions-legales', false], ['legal', 'Protection des données', '/confidentialite', false],
        ];

        foreach ($items as $position => [$location, $label, $url, $button]) {
            MenuItem::updateOrCreate(['location' => $location, 'url' => $url], ['label' => $label, 'is_button' => $button, 'position' => $position]);
        }
    }

    private function redirects(): void
    {
        foreach ([
            '/projet' => '/professionnels', '/vae' => '/professionnels/bts-vae', '/galerie' => '/musee',
            '/admission' => '/candidater', '/admission/succes' => '/candidater',
        ] as $from => $to) {
            Redirect::updateOrCreate(['from_path' => $from], ['to_path' => $to, 'status_code' => 301]);
        }
    }
}
