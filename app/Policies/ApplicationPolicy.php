<?php

namespace App\Policies;

/** Candidatures : l'équipe administrative les traite ; seul le directeur les supprime. */
class ApplicationPolicy extends RolePolicy
{
    protected array $view = ['directeur', 'gestionnaire', 'secretaire'];

    protected ?array $create = ['directeur', 'gestionnaire', 'secretaire'];

    protected array $edit = ['directeur', 'gestionnaire', 'secretaire'];

    protected array $delete = ['directeur'];
}
