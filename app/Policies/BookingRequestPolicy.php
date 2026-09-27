<?php

namespace App\Policies;

/** Demandes de devis et de réservation : données clients, réservées à l'équipe commerciale. */
class BookingRequestPolicy extends RolePolicy
{
    protected array $view = ['directeur', 'commercial'];

    protected array $edit = ['directeur', 'commercial'];

    protected array $delete = ['directeur'];
}
