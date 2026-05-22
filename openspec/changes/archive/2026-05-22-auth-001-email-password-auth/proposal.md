## Why

Deshi Fishery requires a secure authentication system before any farm data can be created or accessed. Currently, the backend is a fresh Laravel 13 install with no user management. Without authentication, there is no way to enforce multi-tenancy, RBAC, or protect sensitive financial data. Email/password auth is the primary login method and must be implemented before social login (AUTH-002) or any data-bearing features.

## What Changes

- Add email/password registration and login API endpoints (`/api/v1/auth/register`, `/api/v1/auth/login`, `/api/v1/auth/logout`, `/api/v1/auth/refresh`)
- Implement password hashing with bcrypt (cost factor >= 12)
- Add rate limiting on auth endpoints (5 requests per 15 minutes per IP)
- Create login and registration pages in the SvelteKit frontend
- Add JWT/cookie handling for session persistence
- Write Pest tests for all auth flows (registration, login, logout, token refresh, validation errors)
- Add PHPStan level 8 type coverage and Pint formatting

## Capabilities

### New Capabilities
- `user-auth`: Email/password registration, login, logout, and token refresh for fish farm owners, managers, and workers

### Modified Capabilities
- None (no existing capabilities to modify)

## Impact

- **Backend**: New AuthController, User model updates, migration for users table, auth routes in `routes/api.php`, Form Request validation classes, Pest tests
- **Frontend**: New `/app/login` and `/app/register` routes, auth form components, API client auth header handling, error display components
- **Dependencies**: Laravel Passport (for OAuth foundation), Laravel Sanctum or custom JWT (to be decided in design)
- **Database**: `users` table with `email`, `password_hash`, `name`, `role`, `current_farm_id` fields
- **Security**: Rate limiting middleware, password validation rules, secure cookie/JWT configuration
