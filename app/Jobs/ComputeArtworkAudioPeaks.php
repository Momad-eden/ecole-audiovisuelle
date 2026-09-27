<?php

namespace App\Jobs;

use App\Models\Artwork;
use App\Services\AudioPeaks;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

/** Calcule la forme d'onde d'une œuvre sonore après l'envoi de son fichier. */
class ComputeArtworkAudioPeaks implements ShouldQueue
{
    use Queueable;

    public function __construct(public Artwork $artwork) {}

    public function handle(AudioPeaks $peaks): void
    {
        $artwork = $this->artwork;
        $path = $artwork->audio_file;

        $result = $path && Storage::disk('public')->exists($path)
            ? $peaks->compute(Storage::disk('public')->path($path))
            : null;

        $artwork->forceFill([
            'audio_peaks' => $result['peaks'] ?? null,
            'duration_seconds' => $result['duration'] ?? $artwork->duration_seconds,
        ])->saveQuietly();
    }
}
