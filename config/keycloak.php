<?php

return [
    'realm' => env('KEYCLOAK_REALM', 'ccak'),
    'issuer' => env('KEYCLOAK_ISSUER', 'http://localhost:8080/realms/ccak'),
    'client_id' => env('KEYCLOAK_CLIENT_ID', 'backend'),
    'audiences' => array_filter(explode(',', env('KEYCLOAK_AUDIENCES', 'backend'))),

    // JWKS endpoint; default points to standard Keycloak certs URL.
    'jwks_uri' => env('KEYCLOAK_JWKS_URI', env('KEYCLOAK_ISSUER', 'http://localhost:8080/realms/ccak') . '/protocol/openid-connect/certs'),
    'jwks_cache_ttl' => (int) env('KEYCLOAK_JWKS_CACHE_TTL', 300),

    // Optionally sync Keycloak roles to Spatie Permission.
    'sync_roles' => filter_var(env('KEYCLOAK_SYNC_ROLES', false), FILTER_VALIDATE_BOOLEAN),
];
