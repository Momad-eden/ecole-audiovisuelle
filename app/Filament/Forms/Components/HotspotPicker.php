<?php

namespace App\Filament\Forms\Components;

use App\Rules\Hotspots;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Points sur la photo du studio : on clique sur l'aperçu pour poser un point, on écrit son
 * libellé, on le déplace en le glissant. Stocké en [{x, y, label}], x et y en % de la photo.
 */
class HotspotPicker extends Field
{
    protected string $view = 'filament.forms.components.hotspot-picker';

    protected string $imageField = 'image';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);
        $this->rule(new Hotspots);
        $this->dehydrateStateUsing(fn ($state) => self::normalize($state));
    }

    /** Nom du champ voisin qui contient la photo sur laquelle on pose les points. */
    public function imageField(string $field): static
    {
        $this->imageField = $field;

        return $this;
    }

    /** @return array<int, array{x: float, y: float, label: string}> */
    public static function normalize(mixed $state): array
    {
        return collect(is_array($state) ? $state : [])
            ->filter(fn ($p) => is_array($p) && is_numeric($p['x'] ?? null) && is_numeric($p['y'] ?? null) && trim((string) ($p['label'] ?? '')) !== '')
            ->map(fn ($p) => [
                'x' => round(min(100, max(0, (float) $p['x'])), 1),
                'y' => round(min(100, max(0, (float) $p['y'])), 1),
                'label' => trim((string) $p['label']),
            ])
            ->values()->all();
    }

    /** Adresse de la photo choisie, ou null tant qu'aucune photo n'est enregistrée. */
    public function getImageUrl(): ?string
    {
        $value = $this->evaluate(fn (Get $get) => $get($this->imageField));
        $file = is_array($value) ? collect($value)->first() : $value;

        return match (true) {
            $file instanceof TemporaryUploadedFile => rescue(fn () => $file->temporaryUrl(), null, false),
            is_string($file) && $file !== '' => Storage::disk('public')->url($file),
            default => null,
        };
    }

    public function getMaxPoints(): int
    {
        return Hotspots::MAX_POINTS;
    }

    public function getMaxLabel(): int
    {
        return Hotspots::MAX_LABEL;
    }
}
