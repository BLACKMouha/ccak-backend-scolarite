<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Document;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use App\Policies\DocumentPolicy;
use App\Policies\GuardianPolicy;
use App\Policies\RolePolicy;
use App\Policies\StudentPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Document::class => DocumentPolicy::class,
        Guardian::class => GuardianPolicy::class,
        Role::class => RolePolicy::class,
        Student::class => StudentPolicy::class,
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
