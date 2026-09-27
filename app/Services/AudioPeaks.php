<?php

namespace App\Services;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Calcule la forme d'onde (pics normalisés entre 0 et 1) et la durée d'un fichier son.
 * WAV PCM 16 bits : lecture directe en PHP. Autres formats : ffmpeg, s'il est installé.
 */
class AudioPeaks
{
    private const RATE = 8000;

    /** @return array{peaks: array<int, float>, duration: int}|null */
    public function compute(string $path, int $count = 200): ?array
    {
        $pcm = $this->readWav($path) ?? $this->decodeWithFfmpeg($path);
        if ($pcm === null || strlen($pcm['samples']) < 2) {
            return null;
        }

        $values = unpack('s*', $pcm['samples']);
        $total = count($values);
        $bucket = max(1, intdiv($total, $count));
        $peaks = [];

        for ($i = 0; $i < $count; $i++) {
            $max = 0;
            $start = $i * $bucket + 1;
            $end = min($total, $start + $bucket - 1);
            for ($j = $start; $j <= $end; $j++) {
                $max = max($max, abs($values[$j]));
            }
            $peaks[] = round($max / 32768, 3);
        }

        return ['peaks' => $peaks, 'duration' => (int) round($total / $pcm['rate'])];
    }

    /** @return array{samples: string, rate: int}|null */
    private function readWav(string $path): ?array
    {
        $data = @file_get_contents($path);
        if ($data === false || substr($data, 0, 4) !== 'RIFF' || substr($data, 8, 4) !== 'WAVE') {
            return null;
        }

        $offset = 12;
        $format = null;
        while ($offset + 8 <= strlen($data)) {
            $id = substr($data, $offset, 4);
            $size = unpack('V', substr($data, $offset + 4, 4))[1];
            if ($id === 'fmt ') {
                $format = unpack('vcodec/vchannels/Vrate/Vbyterate/vblock/vbits', substr($data, $offset + 8, 16));
            }
            if ($id === 'data' && $format && $format['codec'] === 1 && $format['bits'] === 16) {
                $samples = substr($data, $offset + 8, $size);
                if ($format['channels'] > 1) {
                    // On ne garde que le premier canal.
                    $samples = implode('', array_map(fn ($frame) => substr($frame, 0, 2), str_split($samples, 2 * $format['channels'])));
                }

                return ['samples' => $samples, 'rate' => $format['rate']];
            }
            $offset += 8 + $size + ($size % 2);
        }

        return null;
    }

    /** @return array{samples: string, rate: int}|null */
    private function decodeWithFfmpeg(string $path): ?array
    {
        $ffmpeg = (new ExecutableFinder)->find('ffmpeg');
        if (! $ffmpeg || ! is_file($path)) {
            return null;
        }

        $process = new Process([$ffmpeg, '-v', 'error', '-i', $path, '-ac', '1', '-ar', (string) self::RATE, '-f', 's16le', '-']);
        $process->setTimeout(120);
        $process->run();

        return $process->isSuccessful() && $process->getOutput() !== '' ? ['samples' => $process->getOutput(), 'rate' => self::RATE] : null;
    }
}
