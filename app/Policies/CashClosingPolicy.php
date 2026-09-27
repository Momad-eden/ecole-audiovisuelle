<?php

namespace App\Policies;

class CashClosingPolicy extends RolePolicy
{
    protected array $view = ['directeur', 'gestionnaire'];

    protected ?array $create = ['directeur', 'gestionnaire'];

    protected array $edit = [];

    protected array $delete = [];
}
