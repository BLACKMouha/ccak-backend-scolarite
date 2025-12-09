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
