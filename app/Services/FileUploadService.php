<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Enregistre un fichier sur le disque public.
     */
    public function upload(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    /**
     * Remplace un ancien fichier par un nouveau sur le disque public.
     */
    public function replace(?UploadedFile $newFile, ?string $oldPath, string $directory): ?string
    {
        if (!$newFile) {
            return $oldPath;
        }

        $this->delete($oldPath);

        return $this->upload($newFile, $directory);
    }

    /**
     * Supprime un fichier du disque public si existant.
     */
    public function delete(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
