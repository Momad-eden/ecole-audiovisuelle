<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Partner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OfficialProjectSeeder extends Seeder
{
    /**
     * Seed the official 5 filières and key project partners.
     */
    public function run(): void
    {
        $coursesData = [
            [
                'title'          => 'Techniques du Son avancées',
                'slug'           => 'techniques-du-son-avancees',
                'category'       => 'Ingénierie Son & Live',
                'description'    => 'Formation de haut niveau dédiée à l\'ingénierie sonore pour les méga-événements, festivals et spectacles vivants. Le programme forme des techniciens et régisseurs capables de maîtriser le calage de systèmes Line Array, les consoles numériques de dernière génération (Yamaha, DiGiCo, Wing), les réseaux audionumériques Dante/MADI et le mixage Live façade et retours selon les standards internationaux.',
                'duration'       => '3 à 9 mois (Volet 1 & Volet 2 VAE)',
                'level'          => 'Perfectionnement intensif & BTS Bac+2 (VAE)',
                'students_count' => 20,
                'price'          => 0, // Sur demande / Pris en charge selon dispositif
                'is_active'      => true,
            ],
            [
                'title'          => 'Technicien Lumière',
                'slug'           => 'technicien-lumiere',
                'category'       => 'Éclairage & Scénographie',
                'description'    => 'Parcours d\'excellence dédié à la conception scénographique, programmation et exploitation d\'éclairages pour spectacles vivants, festivals et plateaux broadcast. Maîtrise approfondie des pupitres GrandMA (MA2/MA3), ChamSys MagicQ et Avolites, des protocoles de communication lumière (DMX512, Art-Net, sACN), des projecteurs asservis et de la synchronisation de shows timecodés.',
                'duration'       => '3 à 9 mois (Volet 1 & Volet 2 VAE)',
                'level'          => 'Perfectionnement intensif & BTS Bac+2 (VAE)',
                'students_count' => 20,
                'price'          => 0,
                'is_active'      => true,
            ],
            [
                'title'          => 'Régie Générale Spectacle',
                'slug'           => 'regie-generale-spectacle',
                'category'       => 'Régie & Management Technique',
                'description'    => 'Formation professionnalisante axée sur le management technique global d\'événements majeurs et de festivals. Les régisseurs généraux coordonnent les équipes son, lumière et vidéo, rédigent et analysent les fiches techniques, veillent au respect strict des normes de sécurité ERP du spectacle vivant, et assurent le pilotage opérationnel et la gestion des aléas en direct.',
                'duration'       => '3 à 9 mois (Volet 1 & Volet 2 VAE)',
                'level'          => 'Perfectionnement intensif & BTS Bac+2 (VAE)',
                'students_count' => 20,
                'price'          => 0,
                'is_active'      => true,
            ],
            [
                'title'          => 'Infographie et Création Numérique',
                'slug'           => 'infographie-et-creation-numerique',
                'category'       => 'Motion Design & Habillage Live',
                'description'    => 'Cursus dédié à la création visuelle pour le direct, au motion design 2D/3D et à la diffusion multi-écrans. Les apprenants apprennent à concevoir les identités visuelles, habillages d\'écrans géants LED, flux 3D en temps réel, incrustations studio Chroma Key et synthétiseurs graphiques pour les retransmissions télévisées et les scénographies événementielles immersives.',
                'duration'       => '3 à 9 mois (Volet 1 & Volet 2 VAE)',
                'level'          => 'Perfectionnement intensif & BTS Bac+2 (VAE)',
                'students_count' => 20,
                'price'          => 0,
                'is_active'      => true,
            ],
            [
                'title'          => 'Cadrage Sportif et Régie Vidéo',
                'slug'           => 'cadrage-sportif-et-regie-video',
                'category'       => 'Broadcast & Captation Sportive',
                'description'    => 'Formation d\'élite préparant aux exigences du direct sportif et des retransmissions multicaméras internationales. Les techniciens maîtrisent le cadrage broadcast longue portée (optiques 50x-100x), les liaisons fibre optique et HF/RF, l\'exploitation des mélangeurs de production et la génération instantanée de ralentis Super Slow Motion.',
                'duration'       => '3 à 9 mois (Volet 1 & Volet 2 VAE)',
                'level'          => 'Perfectionnement intensif & BTS Bac+2 (VAE)',
                'students_count' => 20,
                'price'          => 0,
                'is_active'      => true,
            ],
        ];

        foreach ($coursesData as $courseInfo) {
            Course::updateOrCreate(
                ['slug' => $courseInfo['slug']],
                $courseInfo
            );
        }

        // Partenaires officiels
        $partnersData = [
            [
                'name'        => 'Grand Théâtre National Doudou Ndiaye Coumba Rose',
                'website'     => 'https://grandtheatre.sn',
                'description' => 'Co-porteur officiel du projet. Infrastructure culturelle d\'exception au Sénégal offrant les plateaux scéniques, régies et conditions réelles d\'immersion.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Direction des Concours & de la Formation Professionnelle',
                'website'     => null,
                'description' => 'Partenaire institutionnel garantissant le cadre légal de la Validation des Acquis de l\'Expérience (VAE) et la délivrance des certifications d\'État.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Festival Bienal de Dakar & Événements Majeurs',
                'website'     => null,
                'description' => 'Cadres d\'application pratique et d\'immersion professionnelle pour les techniciens formés sur les grands festivals et manifestations nationales.',
                'is_active'   => true,
            ],
        ];

        foreach ($partnersData as $partnerInfo) {
            Partner::updateOrCreate(
                ['name' => $partnerInfo['name']],
                $partnerInfo
            );
        }
    }
}
