<?php

namespace Tests\Feature\Domain;

use App\Models\Artwork;
use App\Services\AudioPeaks;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AudioPeaksTest extends TestCase
{
    use RefreshDatabase;

    /** WAV PCM 16 bits mono : une seconde de silence puis une seconde de sinusoïde pleine amplitude. */
    private function wav(): string
    {
        $rate = 8000;
        $samples = '';
        for ($i = 0; $i < $rate * 2; $i++) {
            $value = $i < $rate ? 0 : (int) (32767 * sin(2 * M_PI * 440 * $i / $rate));
            $samples .= pack('v', $value & 0xFFFF);
        }

        return 'RIFF'.pack('V', 36 + strlen($samples)).'WAVE'
            .'fmt '.pack('VvvVVvv', 16, 1, 1, $rate, $rate * 2, 2, 16)
            .'data'.pack('V', strlen($samples)).$samples;
    }

    public function test_peaks_and_duration_are_computed_from_a_wav_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('artworks/audio/test.wav', $this->wav());

        $result = app(AudioPeaks::class)->compute(Storage::disk('public')->path('artworks/audio/test.wav'), 100);

        $this->assertCount(100, $result['peaks']);
        $this->assertSame(2, $result['duration']);
        $this->assertLessThan(0.05, max(array_slice($result['peaks'], 0, 45)));
        $this->assertGreaterThan(0.9, max(array_slice($result['peaks'], 55)));
    }

    public function test_saving_an_artwork_with_audio_stores_its_waveform(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('artworks/audio/test.wav', $this->wav());

        $artwork = Artwork::create(['title' => 'Son', 'kind' => 'audio', 'audio_file' => 'artworks/audio/test.wav']);

        $artwork->refresh();
        $this->assertCount(200, $artwork->audio_peaks);
        $this->assertSame(2, $artwork->duration_seconds);
    }

    public function test_unreadable_audio_leaves_the_waveform_empty(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('artworks/audio/broken.mp3', 'pas du son');

        $artwork = Artwork::create(['title' => 'Cassé', 'kind' => 'audio', 'audio_file' => 'artworks/audio/broken.mp3']);

        $this->assertNull($artwork->refresh()->audio_peaks);
    }
}
