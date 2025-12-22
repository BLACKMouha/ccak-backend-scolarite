# CCAK Backend (Laravel 12)

Backend API for the CCAK scolarité platform, built with Laravel 12, PostgreSQL, Redis, Sanctum auth, Spatie Permission, DomPDF, and Laravel Excel.

## Stack
- PHP 8.2, Laravel 12
- PostgreSQL 16, Redis 7
- Sanctum (API auth), Spatie Permission (roles/permissions)
- DomPDF (PDF export), Laravel Excel (imports/exports)

## Quick start (Docker)
1) `cp .env.docker.example .env`
2) `docker compose build`
3) `docker compose up -d` (app served at http://localhost:8000)
4) Install & init app:
   - `docker compose exec app composer install`
   - `docker compose exec app php artisan key:generate`
   - `docker compose exec app php artisan migrate`
5) (Optional) workers: `docker compose exec app php artisan queue:work`

## Local dev without Docker (optional)
- Requirements: PHP 8.2, Composer, PostgreSQL, Redis, Node 18+.
- `cp .env.example .env`, adjust DB/Redis creds.
- `composer install`
- `php artisan key:generate`
- `php artisan migrate`
- `npm install && npm run dev` (if using the frontend assets)
- `php artisan serve`

## Testing
- `php artisan test`

## API docs (Scramble)
- Live docs are available at `/docs/api` (Scramble renders from your routes).
- Export a static OpenAPI file if needed: `php artisan scramble:export` (creates `api.json`).

## Auth (Keycloak)
- Configure `.env` with your Keycloak realm/issuer/client (see `KEYCLOAK_*` vars).
- Protect routes with the `keycloak` middleware; it validates JWTs via JWKS, links users by `keycloak_id`, and (optionally) syncs roles from token claims.
- Local Keycloak (example): add a service to `docker-compose.yml`:
  ```yaml
  keycloak:
    image: quay.io/keycloak/keycloak:24.0.4
    command: start-dev
    environment:
      KEYCLOAK_ADMIN: admin
      KEYCLOAK_ADMIN_PASSWORD: admin
      KC_HEALTH_ENABLED: "true"
      KC_HOSTNAME: "localhost"
      KC_HOSTNAME_ADMIN: "localhost"
      KC_HOSTNAME_STRICT: "false"
      KC_PROXY_HEADERS: "xforwarded"
    ports:
      - "8180:8080"
    networks:
      - laravel
  ```
  Realm setup (via Keycloak UI):
  1) Create realm `ccak`.
  2) Create client `backend` with Authorization Code (PKCE) and/or Client Credentials; set redirect URIs/web origins for your frontend if needed.
  3) Ensure tokens include audience `backend` and fetch JWKS from `http://localhost:8080/realms/ccak/protocol/openid-connect/certs`.
  4) Map realm/client roles into token (claims `realm_access.roles`, `resource_access`); set `KEYCLOAK_SYNC_ROLES=true` if you want them synced to Spatie Permission.
