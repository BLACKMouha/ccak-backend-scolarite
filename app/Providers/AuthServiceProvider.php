<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Role::class => RolePolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('viewApiDocs', function (?User $user): bool {
            if (! config('scramble.require_auth')) {
                return true;
            }

            if (! $user) {
                return false;
            }

            return $user->hasRole('ADMIN')
                || $user->can('permissions.view')
                || $user->can('roles.view');
        });
    }
}
