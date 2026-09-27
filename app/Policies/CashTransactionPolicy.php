<?php

namespace App\Policies;

/** Caisse : écritures inaltérables (ni modification ni suppression). */
class CashTransactionPolicy extends RolePolicy
{
    protected array $view = ['directeur', 'gestionnaire', 'secretaire'];

    protected ?array $create = ['directeur', 'gestionnaire', 'secretaire'];

    protected array $edit = [];

    protected array $delete = [];
}
