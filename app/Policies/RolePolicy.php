<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Autorisations par rôle : chaque Policy déclare quels rôles peuvent consulter,
 * créer, modifier et supprimer. Un compte sans rôle valide n'a aucun droit.
 */
abstract class RolePolicy
{
    /** @var array<int, string> */
    protected array $view = ['directeur'];

    /** @var array<int, string>|null  null = mêmes rôles que */
    protected ?array $create = null;

    /** @var array<int, string> */
    protected array $edit = ['directeur'];

    /** @var array<int, string> */
    protected array $delete = ['directeur'];

    protected function allows(User $user, array $roles): bool
    {
        return $user->is_active && $user->hasRole(...$roles);
    }

    public function viewAny(User $user): bool
    {
        return $this->allows($user, $this->view);
    }

    public function view(User $user, Model $model): bool
    {
        return $this->allows($user, $this->view);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, $this->create ?? $this->edit);
    }

    public function update(User $user, Model $model): bool
    {
        return $this->allows($user, $this->edit);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->allows($user, $this->delete);
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, $this->delete);
    }

    public function restore(User $user, Model $model): bool
    {
        return $this->allows($user, $this->delete);
    }

    public function restoreAny(User $user): bool
    {
        return $this->allows($user, $this->delete);
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return $this->allows($user, $this->edit);
    }
}
