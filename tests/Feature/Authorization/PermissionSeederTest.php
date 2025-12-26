<?php

namespace Tests\Feature\Authorization;

use App\Models\User;
use App\Support\PermissionCatalog;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_catalog_has_minimum_permissions(): void
    {
        $this->assertGreaterThanOrEqual(50, count(PermissionCatalog::permissions()));
    }

    public function test_permission_seeder_creates_roles_and_permissions(): void
    {
        $this->seed(PermissionSeeder::class);

        $guard = config('auth.defaults.guard', 'api');
        $allPermissions = PermissionCatalog::permissions();
        $this->assertSame(count($allPermissions), Permission::count());

        foreach (PermissionCatalog::rolePermissions() as $roleName => $rolePermissions) {
            $role = Role::findByName($roleName, $guard);
            $this->assertNotNull($role);
            $this->assertEqualsCanonicalizing(
                $rolePermissions,
                $role->permissions->pluck('name')->all()
            );
        }
    }

    public function test_roles_permissions_are_subset_of_catalog(): void
    {
        $allPermissions = PermissionCatalog::permissions();

        foreach (PermissionCatalog::rolePermissions() as $rolePermissions) {
            $this->assertEmpty(array_diff($rolePermissions, $allPermissions));
        }
    }

    public function test_user_inherits_permissions_from_role(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $guard = config('auth.defaults.guard', 'api');

        $permission = Permission::create([
            'name' => 'faculties.view',
            'guard_name' => $guard,
        ]);

        $role = Role::create([
            'name' => 'VIEWER',
            'guard_name' => $guard,
        ]);

        $role->givePermissionTo($permission);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->assertTrue($user->can('faculties.view'));
    }
}
