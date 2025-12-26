<?php

namespace Tests\Support;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

trait InteractsWithPermissions
{
    protected function seedPermissions(array $permissions): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $guard = config('auth.defaults.guard', 'api');

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guard,
            ]);
        }
    }

    protected function actingAsUserWithPermissions(array $permissions): User
    {
        $this->seedPermissions($permissions);

        $user = User::factory()->create();
        $user->givePermissionTo($permissions);

        $this->actingAs($user);

        return $user;
    }
}
