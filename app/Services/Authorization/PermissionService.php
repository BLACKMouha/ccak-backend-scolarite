<?php

namespace App\Services\Authorization;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PermissionService
{
    public function can(User $user, string $permission): bool
    {
        return $user->can($permission);
    }

    public function owns(User $user, Model $model, string $ownerKey = 'user_id'): bool
    {
        return (string) data_get($model, $ownerKey) === (string) $user->getKey();
    }

    public function canOrOwn(User $user, string $permission, Model $model, string $ownerKey = 'user_id'): bool
    {
        return $this->can($user, $permission) || $this->owns($user, $model, $ownerKey);
    }
}
