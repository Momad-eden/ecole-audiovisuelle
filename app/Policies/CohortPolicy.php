<?php

namespace App\Policies;

class CohortPolicy extends RolePolicy
{
    protected array $view = ['directeur', 'gestionnaire', 'secretaire'];

    protected ?array $create = ['directeur', 'gestionnaire'];

    protected array $edit = ['directeur', 'gestionnaire'];

    protected array $delete = ['directeur'];
}
