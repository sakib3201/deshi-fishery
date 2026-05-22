## Context

Deshi Fishery is a fresh Laravel 13 + SvelteKit project with zero authentication infrastructure. The backend has a default `users` migration from Laravel's starter kit, but no auth controllers, no API routes, and no token system. The frontend has no auth pages or session handling.

This design must establish the authentication layer that all subsequent features (farm creation, pond management, sales, expenses, etc.) will depend on. The auth system must support three roles (Owner, Manager, Worker) and integrate with Laravel Passport for future OAuth 2.0 / social login support.

Constraints:
- API-first: JSON only, no Blade views for UI
- Multi-tenancy: `farm_id` scoping on all data queries
- RBAC: role-based middleware on all protected routes
- Mobile-first PWA: tokens must work in browser storage for offline resilience
- Security: bcrypt cost >= 12, rate limiting, HTTPS in production

## Goals / Non-Goals

**Goals:**
- Provide secure email/password registration and login
- Issue short-lived access tokens (15 min) with refresh token rotation
- Support HTTP-only cookies for web/PWA clients
- Enforce input validation and rate limiting on all auth endpoints
- Build responsive login/register pages matching the ocean-blue design system
- Achieve 100% Pest test coverage for all auth flows
- Pass PHPStan level 8 and Pint formatting

**Non-Goals:**
- Social login (Google/Facebook) — deferred to AUTH-002
- Password reset / forgot password — deferred to v2
- Two-factor authentication — out of MVP scope
- Email verification — out of MVP scope
- Admin dashboard for user management — part of AUTH-003 (RBAC)

## Decisions

### 1. Token Strategy: Laravel Passport (OAuth 2.0) over Sanctum
**Decision**: Use Laravel Passport for token issuance, not Sanctum.
**Rationale**: 
- Passport provides full OAuth 2.0 support (authorization code, client credentials, password grant, PKCE)
- Future mobile app and social login (AUTH-002) can use the same OAuth flow without changes
- Refresh token rotation is built-in
- The AGENTS.md and architecture docs already specify Passport as the chosen auth package
**Alternative considered**: Sanctum is simpler for SPA cookie auth, but lacks OAuth 2.0 server capabilities. We'd need to migrate later for mobile/social login.

### 2. Session Transport: HTTP-only Cookie + Bearer Token Hybrid
**Decision**: Support both HTTP-only secure cookies (for web/PWA) and Bearer tokens (for future mobile/external clients).
**Rationale**:
- Web/PWA clients benefit from XSS-resistant HTTP-only cookies
- Mobile clients will need Bearer tokens in the Authorization header
- Passport supports both natively
- The `Req-API-04` in refined requirements explicitly requires both mechanisms

### 3. Password Grant over Personal Access Tokens
**Decision**: Use Passport's Password Grant for the login flow, not personal access tokens.
**Rationale**:
- Password grant is the standard OAuth 2.0 flow for first-party apps where the user enters credentials directly
- It issues both access and refresh tokens automatically
- Personal access tokens are meant for API consumers, not end-user login

### 4. Frontend Token Storage: Secure Cookie (httpOnly) for Web, LocalStorage Fallback
**Decision**: Prefer httpOnly cookies set by the backend. For PWA offline scenarios, store a minimal session indicator in localStorage.
**Rationale**:
- httpOnly cookies are immune to XSS attacks
- The frontend uses native Fetch API, which automatically sends cookies with `credentials: 'include'`
- LocalStorage is only used for UI state (e.g., "remember me" preference), never for tokens

### 5. User Model: Extend Default with Role and Farm Context
**Decision**: Add `role` (enum: owner/manager/worker) and `current_farm_id` (nullable FK) to the default `users` table.
**Rationale**:
- Keeps the standard Laravel auth structure intact
- `role` enables RBAC middleware from day one
- `current_farm_id` supports the multi-farm switching feature (AUTH-004)
- Future: `farm_user` pivot table for multiple farms per user

### 6. Validation: Laravel Form Request Classes
**Decision**: Use dedicated Form Request classes for register and login endpoints.
**Rationale**:
- Centralizes validation rules and error messages
- Supports bilingual error messages (English/Bangla) via Laravel's localization
- Reusable across web and API routes
- Required by the security checklist (input validation on all endpoints)

### 7. Rate Limiting: Laravel Throttle Middleware
**Decision**: Use Laravel's built-in `throttle` middleware on auth routes.
**Rationale**:
- Simple, no additional packages needed
- Configurable per route
- Already specified in requirements (5 req/15 min per IP)

## Risks / Trade-offs

| Risk | Mitigation |
|------|------------|
| Passport adds ~10 database tables (oauth_clients, oauth_access_tokens, etc.) | Acceptable overhead; tables are lightweight and necessary for OAuth compliance |
| Passport setup is more complex than Sanctum | One-time setup cost; documented in Laravel docs; agent has Laravel expertise |
| Token refresh on mobile with spotty connectivity | Implement retry logic with exponential backoff; refresh token has longer expiry (7 days) |
| Password grant is deprecated in OAuth 2.1 | Laravel Passport still supports it; can migrate to PKCE later without breaking changes |
| Storing `current_farm_id` on user model limits future multi-farm-per-user | Migration path: add `farm_user` pivot table later; `current_farm_id` becomes a preference |

## Migration Plan

1. **Install Passport**: `composer require laravel/passport`
2. **Run Passport migrations**: `php artisan migrate` (creates oauth_* tables)
3. **Install Passport keys**: `php artisan passport:install`
4. **Run app migrations**: `php artisan migrate` (users table with role/current_farm_id)
5. **Seed test user**: Factory creates owner user for manual testing
6. **Verify**: `POST /api/v1/auth/register` → 201, `POST /api/v1/auth/login` → 200 with token

**Rollback**: Revert migrations, remove Passport package, delete keys from `storage/oauth-*.key`.

## Open Questions

1. Should we implement a `password_confirmation` field on registration, or is single-password sufficient for MVP?
   - **Decision**: Include `password_confirmation` — standard UX, minimal cost.
2. Should refresh tokens be revoked on logout, or allowed to expire naturally?
   - **Decision**: Revoke on logout — more secure, Passport supports it.
3. Should we add a `name` field validation (min length, character set) for Bengali names?
   - **Decision**: Min 2 characters, allow Unicode (Bengali script supported via regex).
