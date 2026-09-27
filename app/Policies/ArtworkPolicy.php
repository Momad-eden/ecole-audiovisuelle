<?php

namespace App\Policies;

class ArtworkPolicy extends RolePolicy
{
    protected array $view = ['directeur', 'communication', 'commercial'];

    protected ?array $create = ['directeur', 'communication', 'commercial'];

    protected array $edit = ['directeur', 'communication', 'commercial'];

    protected array $delete = ['directeur', 'communication', 'commercial'];
}
