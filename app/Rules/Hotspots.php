<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Points posés sur la photo du héros Studio : six au maximum, libellé de 40 caractères au plus. */
class Hotspots implements ValidationRule
{
    public const MAX_POINTS = 6;

    public const MAX_LABEL = 40;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $points = is_array($value) ? $value : [];

        if (count($points) > self::MAX_POINTS) {
            $fail(self::MAX_POINTS.' points au maximum.');

            return;
        }

        foreach ($points as $point) {
            if (mb_strlen(trim((string) ($point['label'] ?? ''))) > self::MAX_LABEL) {
                $fail('Chaque libellé fait '.self::MAX_LABEL.' caractères au maximum.');

                return;
            }
        }
    }
}
