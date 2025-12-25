# Permissions & Roles

This project uses Spatie Permission with Keycloak-authenticated users. Roles in Keycloak must match
the application roles below so they sync correctly.

## Roles

- ADMIN: Full access across all modules, including role/permission management.
- STAFF: Administrative operations (registrations, documents, finance) and read access to academics.
- FACULTY: Teaching operations (courses, assessments, grades, attendance) and academic read access.
- STUDENT: Self-service access to catalogs, enrollments, grades, and documents.

## Permission naming

Permissions follow `resource.action` (e.g., `faculties.view`, `courses.update`). Actions include
`view`, `create`, `update`, and `delete`, plus a few special cases like `users.assign_roles`.

See `app/Support/PermissionCatalog.php` for the full list and role mappings.
