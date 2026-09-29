<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Points posés sur la photo du héros Studio : six au maximum, chacun avec un libellé de 40 caractères au plus. */
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

        foreach (array_values($points) as $index => $point) {
            $label = trim((string) ($point['label'] ?? ''));
            if ($label === '') {
                $fail('Écrivez ce que montre le point '.($index + 1).' (ou supprimez-le).');

                return;
            }
            if (mb_strlen($label) > self::MAX_LABEL) {
                $fail('Chaque libellé fait '.self::MAX_LABEL.' caractères au maximum.');

                return;
            }
        }
    }
}
