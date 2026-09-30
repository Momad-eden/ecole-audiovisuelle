<?php

namespace App\Http\Resources\Public\Concerns;

use App\Support\Translation\Localized;

/** Champs traduits d'une ressource publique, dans la langue de la requête (repli français). */
trait TranslatesFields
{
    protected function t(string $field): mixed
    {
        return Localized::value($this->resource, $field);
    }
}
