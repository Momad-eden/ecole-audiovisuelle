<?php

namespace App\Policies;

class TrackPolicy extends RolePolicy
{
    protected array $view = ['directeur', 'gestionnaire', 'secretaire', 'communication'];

    protected ?array $create = ['directeur', 'gestionnaire'];

    protected array $edit = ['directeur', 'gestionnaire'];

    protected array $delete = ['directeur'];
}
