<?php

namespace App\Policies;

use App\Models\User;
use App\Services\Authorization\PermissionService;

abstract class BasePolicy
{
    public function __construct(protected PermissionService $permissions)
    {
    }

    protected function allow(User $user, string $permission): bool
    {
        return $this->permissions->can($user, $permission);
    }
}
