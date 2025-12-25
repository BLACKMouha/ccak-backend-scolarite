<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Support\PermissionCatalog;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    private const GUARD = 'web';

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = PermissionCatalog::permissions();
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => self::GUARD,
            ]);
        }

        foreach (PermissionCatalog::rolePermissions() as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => self::GUARD,
            ]);

            $role->syncPermissions($rolePermissions);
        }
    }
}
