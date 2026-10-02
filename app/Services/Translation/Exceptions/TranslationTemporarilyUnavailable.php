<?php

namespace App\Services\Translation\Exceptions;

use RuntimeException;

/** Erreur passagère (trop de requêtes, serveur ou réseau) : à réessayer plus tard. */
class TranslationTemporarilyUnavailable extends RuntimeException {}
