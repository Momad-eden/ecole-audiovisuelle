<?php

namespace App\Policies;

class NewsPolicy extends RolePolicy
{
    protected array $view = ['directeur', 'communication'];

    protected ?array $create = ['directeur', 'communication'];

    protected array $edit = ['directeur', 'communication'];

    protected array $delete = ['directeur', 'communication'];
}
