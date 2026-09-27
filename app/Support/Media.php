<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class Media
{
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : Storage::disk('public')->url($path);
    }

    /** @return array{url: string, alt: string}|null */
    public static function image(?string $path, ?string $alt = null): ?array
    {
        $url = self::url($path);

        return $url ? ['url' => $url, 'alt' => (string) $alt] : null;
    }
}
