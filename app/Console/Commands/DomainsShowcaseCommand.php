<?php

namespace App\Console\Commands;

use App\Enums\Audience;
use App\Enums\CohortStatus;
use App\Enums\FundingMode;
use App\Enums\ProgramKind;
use App\Enums\PublicationStatus;
use App\Models\Application;
use App\Models\CashTransaction;
use App\Models\Cohort;
use App\Models\Enrollment;
use App\Models\Offering;
use App\Models\Page;
use App\Models\Place;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Pages d'essai des blocs « Nos trois maisons », « Formations de ce campus », « Nous soutenir » et
 * « Documents à télécharger », et une formation d'essai proposée seulement dans le premier campus,
 * pour les parcours Playwright. Lancée par la préparation des tests e2e et supprimée à la fin (--remove) :
 * rien de tout cela ne doit rester dans une base qui partira en production.
 */
class DomainsShowcaseCommand extends Command
{
    protected $signature = 'emsi:domains-showcase {--remove : Supprimer les pages, la formation et les fichiers d\'essai}';

    protected $description = 'Crée (ou supprime avec --remove) les pages d\'essai des blocs triptyque, formations du campus, documents et « Nous soutenir »';

    private const DIRECTORY = 'pages/essai-domaines';

    public const SLUGS = ['essai-domaines-accueil', 'essai-domaines-campus', 'essai-domaines-soutenir', 'essai-domaines-documents'];

    public const PROGRAM_SLUG = 'essai-dakar-seulement';

    public function handle(): int
    {
        if ($this->option('remove')) {
            if (! $this->remove()) {
                return self::FAILURE;
            }
            $this->info('Pages et formation d\'essai des nouveaux blocs supprimées.');

            return self::SUCCESS;
        }

        $campuses = Place::published()->campuses()->orderBy('position')->orderBy('id')->get();
        if ($campuses->isEmpty()) {
            $this->error('Aucun campus publié : créez d\'abord un campus dans Administration › Lieux.');

            return self::FAILURE;
        }

        $images = $this->copyImages();
        $this->program($campuses->first());

        $panel = fn (string $domain, string $eyebrow, string $title, string $text, string $image, string $url, string $label) => [
            'domain' => $domain, 'eyebrow' => $eyebrow, 'title' => $title, 'text' => $text,
            'image' => $images[$image], 'image_alt' => '', 'url' => $url, 'label' => $label,
        ];
        $this->page('essai-domaines-accueil', 'Essai — Nos trois maisons', 'general', [['type' => 'domains', 'data' => [
            'intro' => 'La culture comme héritage, l\'art comme métier',
            'panels' => [
                $panel('maison', 'Centre culturel · Saint-Louis', 'Maison Habib Faye', 'Concerts, résidences, transmission. Et Impact Live Studio.', 'facade_monumentale', '/maison-habib-faye', 'Découvrir la Maison'),
                $panel('emsi', 'École · Dakar & Saint-Louis', 'EMSI', 'Les métiers du son, de l\'image et de la scène.', 'grande_salle', '/emsi', 'Se former'),
                $panel('studio', '● REC · Studio', 'Impact Live Studio', 'Enregistrement, mixage, mastering.', 'studio_son', '/maison-habib-faye/studio', 'Réserver'),
            ],
        ]]]);

        // Un bloc par campus (le premier d'abord) : la formation d'essai ne doit apparaître que dans le premier.
        $this->page('essai-domaines-campus', 'Essai — Formations du campus', 'emsi', $campuses->take(2)->map(fn (Place $campus) => [
            'type' => 'campus_programs', 'data' => ['title' => "Les formations à {$campus->city}", 'campus_id' => $campus->id],
        ])->values()->all());

        $this->page('essai-domaines-soutenir', 'Essai — Nous soutenir', 'general', [['type' => 'support_form', 'data' => [
            'title' => 'Nous soutenir', 'text' => 'Partenaires, mécènes et donateurs : écrivez-nous, nous vous répondrons rapidement.',
        ]]]);

        $this->page('essai-domaines-documents', 'Essai — Documents', 'emsi', [['type' => 'downloads', 'data' => [
            'title' => 'Documents à télécharger',
            'files' => [['file' => $this->pdf('brochure-essai.pdf'), 'title' => 'Brochure d\'essai', 'description' => 'Un document de démonstration.']],
        ]]]);

        $this->info('Pages d\'essai créées : /'.implode(', /', self::SLUGS));

        return self::SUCCESS;
    }

    private function page(string $slug, string $title, string $domain, array $blocks): void
    {
        $page = Page::firstOrNew(['slug' => $slug]);
        $page->fill(['title' => $title, 'type' => 'free', 'domain' => $domain, 'is_locked' => false, 'draft_blocks' => $blocks])->save();
        $page->publish();
    }

    /** Formation d'essai, publiée, proposée seulement dans ce campus, avec une session ouverte et une offre. */
    private function program(Place $campus): void
    {
        $program = Program::withTrashed()->firstOrNew(['slug' => self::PROGRAM_SLUG]);
        $program->fill([
            'title' => 'Essai — Dakar seulement', 'audience' => Audience::SCHOOL, 'kind' => ProgramKind::CERTIFICATE,
            'summary' => 'Formation de démonstration des tests automatiques.', 'position' => 999,
            'status' => PublicationStatus::PUBLISHED, 'published_at' => now()->subDay(),
        ])->save();
        $program->restore();
        $program->campuses()->sync([$campus->id]);

        $cohort = Cohort::firstOrNew(['program_id' => $program->id, 'name' => 'Session d\'essai']);
        $cohort->fill([
            'place_id' => $campus->id, 'starts_on' => now()->addMonth()->toDateString(), 'ends_on' => now()->addMonths(10)->toDateString(),
            'applications_open_at' => null, 'applications_close_at' => null, 'status' => CohortStatus::OPEN,
        ])->save();

        Offering::firstOrNew(['cohort_id' => $cohort->id, 'track_id' => null])->fill([
            'capacity' => 10, 'fee_amount' => 0, 'registration_fee_amount' => 0, 'funding_mode' => FundingMode::PAID, 'is_open' => true,
        ])->save();
    }

    /**
     * Supprime uniquement ce que la commande a créé (pages et formation retrouvées par leur adresse), ainsi que
     * les candidatures et inscriptions déposées par les parcours e2e sur la formation d'essai (et leurs pièces).
     * Refuse si une inscription d'essai a une écriture de caisse : la caisse ne s'efface jamais.
     */
    private function remove(): bool
    {
        $program = Program::withTrashed()->where('slug', self::PROGRAM_SLUG)->first();
        $cohortIds = $program ? Cohort::where('program_id', $program->id)->pluck('id') : collect();
        $offeringIds = Offering::whereIn('cohort_id', $cohortIds)->pluck('id');
        $enrollments = Enrollment::withTrashed()->whereIn('offering_id', $offeringIds)->get();
        $applications = Application::withTrashed()->whereIn('offering_id', $offeringIds)->get();

        if (CashTransaction::whereIn('enrollment_id', $enrollments->modelKeys())->exists()) {
            $this->error('Suppression annulée : une inscription à la formation d\'essai « '.self::PROGRAM_SLUG.' » a une écriture de caisse, '
                .'qui ne peut pas être effacée. Annulez-la dans la caisse puis supprimez la formation d\'essai à la main. Rien n\'a été supprimé.');

            return false;
        }

        $studentIds = $enrollments->pluck('student_id')->merge($applications->pluck('student_id'))->filter()->unique();

        DB::transaction(function () use ($program, $cohortIds, $offeringIds, $enrollments, $applications, $studentIds) {
            Page::whereIn('slug', self::SLUGS)->get()->each->delete();
            if (! $program) {
                return;
            }

            $enrollments->each->forceDelete();
            $applications->each->forceDelete(); // l'historique (application_events) suit en cascade
            Offering::whereIn('id', $offeringIds)->get()->each->delete();
            Cohort::whereIn('id', $cohortIds)->get()->each->delete();
            $program->campuses()->detach();
            $program->forceDelete();

            // Élèves créés seulement pour ces candidatures ou inscriptions d'essai.
            Student::withTrashed()->whereIn('id', $studentIds)->get()
                ->reject(fn (Student $student) => Application::withTrashed()->where('student_id', $student->id)->exists()
                    || Enrollment::withTrashed()->where('student_id', $student->id)->exists())
                ->each->forceDelete();
        });

        // Pièces jointes des candidatures (disque privé), une fois la base nettoyée.
        foreach ($applications as $application) {
            Storage::disk('local')->deleteDirectory("applications/{$application->uuid}");
            $paths = collect($application->documents ?? [])->pluck('path')->filter()->all();
            Storage::disk('local')->delete($paths);
            foreach ($paths as $path) {
                if (Storage::disk('local')->allFiles(dirname($path)) === []) {
                    Storage::disk('local')->deleteDirectory(dirname($path));
                }
            }
        }

        Storage::disk('public')->deleteDirectory(self::DIRECTORY);

        return true;
    }

    /** @return array<string, string> chemins des photos copiées sur le disque public */
    private function copyImages(): array
    {
        return collect(['facade_monumentale', 'grande_salle', 'studio_son'])->mapWithKeys(function (string $name) {
            $path = self::DIRECTORY."/{$name}.jpg";
            Storage::disk('public')->put($path, file_get_contents(public_path("images/lieux/{$name}.jpg")));

            return [$name => $path];
        })->all();
    }

    /** Petit PDF d'une page (« Document d'essai »), valide et lisible par un navigateur. */
    private function pdf(string $name): string
    {
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
        ];
        $stream = "BT /F1 24 Tf 72 760 Td (Document d'essai) Tj ET";
        $objects[] = '<< /Length '.strlen($stream)." >>\nstream\n{$stream}\nendstream";
        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n{$object}\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref'."\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF\n";

        $path = self::DIRECTORY."/{$name}";
        Storage::disk('public')->put($path, $pdf);

        return $path;
    }
}
