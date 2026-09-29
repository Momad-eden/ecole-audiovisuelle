<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Pages d'essai des héros Projection, Studio et Cinéma, pour les parcours Playwright.
 * Lancée par la préparation des tests e2e et supprimée à la fin (--remove) : ces pages ne
 * doivent jamais rester dans une base qui partira en production.
 */
class HeroShowcaseCommand extends Command
{
    protected $signature = 'emsi:hero-showcase {--remove : Supprimer les pages et fichiers d\'essai}';

    protected $description = 'Crée (ou supprime avec --remove) les pages d\'essai des héros Projection, Studio et Cinéma';

    private const DIRECTORY = 'pages/essai-heros';

    public const SLUGS = ['essai-heros-projection', 'essai-heros-studio', 'essai-heros-cinema'];

    public function handle(): int
    {
        if ($this->option('remove')) {
            Page::whereIn('slug', self::SLUGS)->get()->each->delete();
            Storage::disk('public')->deleteDirectory(self::DIRECTORY);
            $this->info('Pages d\'essai des héros supprimées.');

            return self::SUCCESS;
        }

        $images = $this->copyImages();
        $sounds = [$this->tone('demo-voix.wav', 330), $this->tone('demo-mix.wav', 220)];

        $this->page('essai-heros-projection', 'Essai — Projection', [
            'layout' => 'projection', 'eyebrow' => 'Dakar · Grand Théâtre National', 'title' => 'Faites de votre passion un métier',
            'highlight' => 'un métier', 'subtitle' => 'Son, lumière et image, au cœur du Grand Théâtre.',
            'image' => $images['grande_salle'], 'image_alt' => 'La grande salle', 'caption' => 'Ondes lumineuses',
            'buttons' => [['label' => 'Candidater', 'url' => '/candidater', 'style' => 'primary']],
        ]);

        $this->page('essai-heros-studio', 'Essai — Studio', [
            'layout' => 'studio', 'eyebrow' => 'Impact Live Studio', 'title' => 'Le studio qui fait sonner Dakar',
            'highlight' => 'sonner', 'subtitle' => 'Enregistrement, mixage et mastering, avec ingénieur du son.',
            'image' => $images['studio_son'], 'image_alt' => 'La régie du studio',
            'hotspots' => [
                ['x' => 49, 'y' => 55, 'label' => 'Console 48 pistes'],
                ['x' => 52, 'y' => 34, 'label' => 'Cabine de prise et piano à queue'],
                ['x' => 85, 'y' => 68, 'label' => 'Préamplis et effets'],
            ],
            'tracks' => [
                ['file' => $sounds[0], 'title' => 'Voix témoin', 'credits' => 'Enregistré ici'],
                ['file' => $sounds[1], 'title' => 'Mix témoin', 'credits' => 'Mixé ici'],
            ],
            'buttons' => [['label' => 'Réserver une séance', 'url' => '/studio#reserver', 'style' => 'primary']],
        ]);

        $this->page('essai-heros-cinema', 'Essai — Cinéma', [
            'layout' => 'cinema', 'title' => 'Apprenez sur la plus grande scène du Sénégal',
            'slides' => [
                ['image' => $images['grande_salle'], 'image_alt' => 'La grande salle', 'eyebrow' => 'Univers Scène', 'title' => 'Apprenez sur la plus grande scène du Sénégal', 'link_label' => 'Découvrir', 'link_url' => '/univers'],
                ['image' => $images['studio_son'], 'image_alt' => 'Le studio', 'eyebrow' => 'Univers Son', 'title' => 'Enregistrez dans un vrai studio'],
                ['image' => $images['regie_broadcast'], 'image_alt' => 'La régie vidéo', 'eyebrow' => 'Univers Image', 'title' => 'Réalisez en régie broadcast'],
            ],
            'facts' => [
                ['value' => '5', 'label' => 'filières'], ['value' => '2', 'label' => 'campus'],
                ['value' => '100 %', 'label' => 'pratique'], ['value' => 'GTN', 'label' => 'Grand Théâtre National'],
            ],
            'buttons' => [['label' => 'Candidater', 'url' => '/candidater', 'style' => 'primary']],
        ]);

        $this->info('Pages d\'essai créées : /'.implode(', /', self::SLUGS));

        return self::SUCCESS;
    }

    private function page(string $slug, string $title, array $hero): void
    {
        $page = Page::firstOrNew(['slug' => $slug]);
        $page->fill(['title' => $title, 'type' => 'free', 'is_locked' => false, 'draft_blocks' => [['type' => 'hero', 'data' => $hero]]])->save();
        $page->publish();
    }

    /** @return array<string, string> chemins des photos copiées sur le disque public */
    private function copyImages(): array
    {
        return collect(['grande_salle', 'studio_son', 'regie_broadcast'])->mapWithKeys(function (string $name) {
            $path = self::DIRECTORY."/{$name}.jpg";
            Storage::disk('public')->put($path, file_get_contents(public_path("images/lieux/{$name}.jpg")));

            return [$name => $path];
        })->all();
    }

    /** Court son de démonstration (3 s, note tenue qui s'éteint), en WAV 16 bits mono. */
    private function tone(string $name, float $frequency): string
    {
        $rate = 22050;
        $samples = '';
        for ($i = 0; $i < $rate * 3; $i++) {
            $fade = 1 - $i / ($rate * 3);
            $samples .= pack('v', (int) (sin(2 * M_PI * $frequency * $i / $rate) * 9000 * $fade) & 0xFFFF);
        }
        $header = 'RIFF'.pack('V', 36 + strlen($samples)).'WAVEfmt '.pack('VvvVVvv', 16, 1, 1, $rate, $rate * 2, 2, 16).'data'.pack('V', strlen($samples));
        $path = self::DIRECTORY."/{$name}";
        Storage::disk('public')->put($path, $header.$samples);

        return $path;
    }
}
