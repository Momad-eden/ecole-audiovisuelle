<?php

namespace App\Services\Translation\Exceptions;

use RuntimeException;

/** Quota de caractères DeepL atteint (ou sur le point de l'être). */
class QuotaExceeded extends RuntimeException {}
