<?php

namespace App\Policies;

class ContactMessagePolicy extends RolePolicy
{
    protected array $view = ['directeur', 'gestionnaire', 'secretaire', 'communication'];

    protected ?array $create = ['directeur'];

    protected array $edit = ['directeur', 'gestionnaire', 'secretaire', 'communication'];

    protected array $delete = ['directeur'];
}
