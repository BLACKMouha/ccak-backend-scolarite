<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Keycloak\JwtValidator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class KeycloakAuthenticate
{
    public function __construct(private readonly JwtValidator $validator)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @throws AuthenticationException
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $token = $this->extractBearerToken($request);
        $claims = $this->validator->validate($token);

        $user = $this->resolveUser($claims);
        Auth::setUser($user);

        return $next($request);
    }

    private function extractBearerToken(Request $request): string
    {
        $token = $request->bearerToken();
        if (! $token) {
            throw new AuthenticationException('Missing bearer token.');
        }

        return $token;
    }

    private function resolveUser(array $claims): User
    {
        $sub = $claims['sub'] ?? null;
        if (! $sub) {
            throw new AuthenticationException('Missing subject in token.');
        }

        $email = $claims['email'] ?? null;
        $defaultEmail = $email ?: sprintf('%s@keycloak.local', Str::slug($sub));

        $user = User::query()->firstOrCreate(
            ['keycloak_id' => $sub],
            [
                'email' => $defaultEmail,
                'user_type' => 'STAFF',
                'is_active' => true,
            ]
        );

        $user->last_login_at = now();

        if ($email && $user->email !== $email) {
            $user->email = $email;
        }

        $user->save();

        if (config('keycloak.sync_roles')) {
            $this->syncRoles($user, $claims);
        }

        return $user;
    }

    private function syncRoles(User $user, array $claims): void
    {
        if (! method_exists($user, 'syncRoles')) {
            return;
        }

        $roles = Arr::wrap(data_get($claims, 'realm_access.roles', []));

        // Include client roles if present.
        $resourceRoles = collect(data_get($claims, 'resource_access', []))
            ->map(fn ($access) => Arr::wrap($access['roles'] ?? []))
            ->flatten()
            ->all();

        $roles = array_values(array_unique(array_merge($roles, $resourceRoles)));

        if (! empty($roles)) {
            $user->syncRoles($roles);
        }
    }
}
