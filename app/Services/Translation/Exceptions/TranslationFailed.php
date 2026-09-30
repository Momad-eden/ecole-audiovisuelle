<?php

namespace App\Services\Translation\Exceptions;

use RuntimeException;

/** Erreur définitive de traduction (réponse invalide, requête refusée) : inutile de réessayer. */
class TranslationFailed extends RuntimeException {}
