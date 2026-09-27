<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPolicy extends RolePolicy
{
    protected array $view = ['directeur'];

    protected ?array $create = ['directeur'];

    protected array $edit = ['directeur'];

    protected array $delete = ['directeur'];

    public function delete(User $user, Model $model): bool
    {
        return $user->isNot($model) && parent::delete($user, $model);
    }
}
