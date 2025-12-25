<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends BasePolicy
{
    public function assignRoles(User $user, User $target): bool
    {
        return $this->allow($user, 'users.assign_roles');
    }
}
