<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allow($user, 'roles.view');
    }

    public function create(User $user): bool
    {
        return $this->allow($user, 'roles.manage');
    }

    public function update(User $user, Role $role): bool
    {
        return $this->allow($user, 'roles.manage');
    }
}
