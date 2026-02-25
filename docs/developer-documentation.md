# Sports Club Multi-Tenant Platform — Developer Documentation

## Stack
- Backend: Laravel (API-only)
- Frontend: React + TypeScript + Vite
- DB: PostgreSQL (recommended; MySQL compatible)
- Auth: JWT access + refresh (rotating refresh token stored hashed in DB)
- Multi-tenant: shared schema + `club_id` isolation
- Tenant resolution: `X-Tenant` header (slug from React subdomain)

## Domains (example)
- Frontend: `https://{club}.app.example.com`
- API: `https://api.example.com`

React derives tenant from subdomain and sends:
- `X-Tenant: {club_slug}`
- `Authorization: Bearer {access_token}`

## Local development (suggested)
- Frontend: `http://club1.localhost:5173`
- API: `http://api.localhost:8000`
- Add hosts:
  - `127.0.0.1 club1.localhost`
  - `127.0.0.1 api.localhost`

## Multi-tenant rules
1. Every business table must contain `club_id` (tenant key).
2. No request is processed without a resolved tenant, except platform-only endpoints (optional).
3. `club_id` is **never** accepted from client payload for tenant resources.
4. `club_id` is injected from `TenantContext` on create and used as a mandatory filter on reads.

### Tenant resolution
- Middleware `ResolveTenant` reads `X-Tenant` and loads `clubs.slug`.
- Sets `TenantContext` (container singleton).
- All tenant models use `BelongsToTenant` trait:
  - `booted()`: add global scope `club_id = TenantContext::id()`
  - `creating()`: set `club_id` automatically

## Authentication (JWT + rotating refresh)
### Access token (JWT)
- TTL: 10–15 min
- Signed (RS256 recommended; HS256 acceptable for MVP)
- Stateless; contains minimal claims:
  - `sub`: user_id
  - `jti`: unique id
  - `tid` (optional): club_id

### Refresh token (opaque)
- Stored hashed in DB (`sha256`/`bcrypt`)
- TTL: 7–30 days
- Rotation on every refresh:
  - old refresh token marked rotated
  - new refresh token issued
- Reuse detection:
  - if an already-rotated token is used, revoke the entire `family_id`

## RBAC (roles & permissions)
Roles:
- Global: `superadmin` (platform)
- Tenant-scoped: `admin`, `coach`, `student`, `parent`

Permissions examples:
- `users.manage`
- `groups.manage`
- `trainings.manage`
- `attendance.manage`
- `finance.manage`
- `events.manage`
- `reports.view`
- `settings.manage`
- `parent.children.view`

Rules:
- Parent can access only linked children via `parent_student`.
- Coach can access only assigned groups (`group_coaches`) and their students/sessions.
- Admin can manage all tenant resources.

## Coding guidelines
- API-first, strict validation (`FormRequests`)
- Policies for authorization (per resource)
- Prefer services for domain logic:
  - `Auth/TokenService` (JWT + refresh rotation)
  - `AttendanceService`
  - `FinanceService`

## Error format (recommended)
- `401`: unauthenticated / token expired
- `403`: forbidden (policy)
- `404`: not found (tenant-safe)
- `422`: validation errors

JSON error payload:

```json
{
  "message": "Validation failed",
  "errors": {
    "field": [
      "..."
    ]
  }
}
```

## Frontend auth flow
- Store refresh token in `localStorage` (MVP)
- Store access token in memory (and optionally `localStorage`)
- API client:
  - attach `Authorization` + `X-Tenant`
  - on `401` -> call `/auth/refresh` (single flight)
  - retry original request

## Testing strategy
Backend:
- Feature tests for:
  - tenant isolation (cannot access other club data)
  - refresh rotation + reuse detection
  - parent-child access restrictions
- Policy tests

Frontend:
- API client refresh logic
- Route guards by role
