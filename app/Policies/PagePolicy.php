<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PagePolicy extends RolePolicy
{
    protected array $view = ['directeur', 'communication'];

    protected ?array $create = ['directeur', 'communication'];

    protected array $edit = ['directeur', 'communication'];

    protected array $delete = ['directeur', 'communication'];

    public function delete(User $user, Model $model): bool
    {
        return ! $model->is_locked && parent::delete($user, $model);
    }
}
