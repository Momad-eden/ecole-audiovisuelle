<?php

namespace App\Exceptions;

use RuntimeException;

/** Règle métier non respectée ; le message est affiché tel quel à l'utilisateur (en français). */
class BusinessRuleException extends RuntimeException {}
