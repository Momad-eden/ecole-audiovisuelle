<?php

namespace App\Policies;

/** Vitrine Impact Live : l'équipe commerciale et la communication la tiennent à jour. */
class AgendaEventPolicy extends RolePolicy
{
    protected array $view = ['directeur', 'commercial', 'communication'];

    protected array $edit = ['directeur', 'commercial', 'communication'];

    protected array $delete = ['directeur', 'commercial'];
}
