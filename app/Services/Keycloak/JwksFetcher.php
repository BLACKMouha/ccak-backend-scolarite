<?php

namespace App\Services\Keycloak;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class JwksFetcher
{
    public function getKeys(): array
    {
        $cacheKey = 'keycloak:jwks';
        $ttl = config('keycloak.jwks_cache_ttl', 300);
        return Cache::remember($cacheKey, $ttl, function () {
            $uri = config('keycloak.jwks_uri');
            $response = Http::get($uri);
            if (! $response->ok()) {
                throw new RuntimeException("Unable to fetch JWKS from {$uri}");
            }

            $keys = $response->json('keys', []);
            if (! is_array($keys) || empty($keys)) {
                throw new RuntimeException("No JWKS keys returned from {$uri}");
            }

            return $keys;
        });
    }

    public function findKeyByKid(string $kid): ?array
    {
        foreach ($this->getKeys() as $key) {
            if (($key['kid'] ?? null) === $kid) {
                return $key;
            }
        }

        return null;
    }
}
