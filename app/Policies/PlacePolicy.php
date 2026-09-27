<?php

namespace App\Policies;

/** Lieux (campus, studio, centre culturel) : coordonnées publiques de chaque activité. */
class PlacePolicy extends RolePolicy
{
    protected array $view = ['directeur', 'commercial', 'communication'];

    protected array $edit = ['directeur', 'communication'];

    protected array $delete = ['directeur'];
}
