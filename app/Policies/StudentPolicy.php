<?php

namespace App\Policies;

class StudentPolicy extends RolePolicy
{
    protected array $view = ['directeur', 'gestionnaire', 'secretaire'];

    protected ?array $create = ['directeur', 'gestionnaire', 'secretaire'];

    protected array $edit = ['directeur', 'gestionnaire', 'secretaire'];

    protected array $delete = ['directeur'];
}
