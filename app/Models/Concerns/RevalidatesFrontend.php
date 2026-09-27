<?php

namespace App\Models\Concerns;

use App\Services\FrontendRevalidator;

/** Toute modification d'un contenu public déclenche la mise à jour du site. */
trait RevalidatesFrontend
{
    public static function bootRevalidatesFrontend(): void
    {
        $refresh = fn () => app(FrontendRevalidator::class)->queue(['content']);

        static::saved($refresh);
        static::deleted($refresh);
    }
}
