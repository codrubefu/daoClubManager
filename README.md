# DAO Club Manager (Laravel API skeleton)

This repository now contains an implementation scaffold of the Laravel backend described in `docs/developer-documentation.md`.

## Implemented foundations

- Multi-tenant context resolution via `X-Tenant` header (`ResolveTenant` middleware + `TenantContext`).
- Tenant-safe data access via `BelongsToTenant` trait global scope and automatic `club_id` injection on create.
- JWT-style access token issuing and rotating refresh token workflow (`TokenService`) with reuse detection by token family revocation.
- Auth endpoints:
  - `POST /api/auth/login`
  - `POST /api/auth/refresh`
- Example RBAC policy setup for users (`UserPolicy`) and sample user management endpoints.
- Base migrations for `clubs`, `users`, and `refresh_tokens`.

## Notes

This is an implementation-focused code scaffold (application layer + migrations + routes) and expects a standard Laravel runtime/bootstrap to be present in your deployment template.
