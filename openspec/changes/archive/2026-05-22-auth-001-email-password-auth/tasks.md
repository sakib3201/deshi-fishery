## 1. Backend Setup

- [x] 1.1 Install Laravel Passport: `composer require laravel/passport`
- [x] 1.2 Run Passport migrations: `php artisan migrate`
- [x] 1.3 Install Passport encryption keys: `php artisan passport:install`
- [x] 1.4 Update `User` model to use `HasApiTokens` trait and add `role` + `current_farm_id` fields
- [x] 1.5 Create migration to add `role` (enum: owner/manager/worker) and `current_farm_id` (nullable FK) to `users` table
- [x] 1.6 Configure `config/auth.php` guards to use Passport driver for API
- [x] 1.7 Add `AUTH_PASSWORD_GRANT_CLIENT_ID` and `AUTH_PASSWORD_GRANT_CLIENT_SECRET` to `.env.example`

## 2. Backend API Endpoints

- [x] 2.1 Create `RegisterRequest` Form Request with validation rules (name, email, password, password_confirmation)
- [x] 2.2 Create `LoginRequest` Form Request with validation rules (email, password)
- [x] 2.3 Create `AuthController` with `register()` method — creates user, issues tokens via Password Grant
- [x] 2.4 Create `AuthController` with `login()` method — validates credentials, issues tokens via Password Grant
- [x] 2.5 Create `AuthController` with `logout()` method — revokes current access token and refresh token
- [x] 2.6 Create `AuthController` with `refresh()` method — exchanges refresh token for new access token
- [x] 2.7 Create `AuthController` with `me()` method — returns authenticated user profile
- [x] 2.8 Add auth routes in `routes/api.php` under `/api/v1/auth/` prefix with throttle middleware (5,15)
- [x] 2.9 Implement standardized JSON error envelope for auth errors (code, message, details)

## 3. Backend Testing

- [x] 3.1 Write Pest test: successful registration returns 201 with user object and tokens
- [x] 3.2 Write Pest test: registration with duplicate email returns 422
- [x] 3.3 Write Pest test: registration with invalid password returns 422
- [x] 3.4 Write Pest test: registration with mismatched password confirmation returns 422
- [x] 3.5 Write Pest test: registration rate limiting returns 429 after 5 attempts
- [x] 3.6 Write Pest test: successful login returns 200 with tokens and user object
- [x] 3.7 Write Pest test: login with invalid password returns 401
- [x] 3.8 Write Pest test: login with non-existent email returns 401
- [x] 3.9 Write Pest test: login rate limiting returns 429 after 5 attempts
- [x] 3.10 Write Pest test: successful logout revokes tokens and returns 204
- [x] 3.11 Write Pest test: logout with invalid token returns 401
- [x] 3.12 Write Pest test: successful token refresh returns new access token
- [x] 3.13 Write Pest test: token refresh with expired token returns 401
- [x] 3.14 Write Pest test: token refresh with revoked token returns 401
- [x] 3.15 Write Pest test: password is hashed with bcrypt cost >= 12
- [x] 3.16 Write Pest test: authenticated user can retrieve own profile
- [x] 3.17 Write Pest test: unauthenticated user cannot retrieve profile
- [x] 3.18 Run full Pest suite: `vendor/bin/pest` — all tests must pass
- [x] 3.19 Run PHPStan: `vendor/bin/phpstan analyse --level=8` — zero errors
- [x] 3.20 Run Pint: `vendor/bin/pint` — zero formatting issues

## 4. Frontend Setup

- [x] 4.1 Create `src/lib/api/client.ts` — central Fetch API wrapper with base URL, credentials, and error parsing
- [x] 4.2 Create `src/lib/stores/auth.svelte.ts` — Svelte 5 runes-based auth state (user, tokens, isAuthenticated)
- [x] 4.3 Install Lucide icons if not already present

## 5. Frontend Pages & Components

- [x] 5.1 Create `/app/login` route with LoginPage.svelte
- [x] 5.2 Create `/app/register` route with RegisterPage.svelte
- [x] 5.3 Build LoginForm component with email input, password input, submit button (56px touch targets)
- [x] 5.4 Build RegisterForm component with name, email, password, password_confirmation inputs
- [x] 5.5 Implement real-time form validation with inline error messages (error color #DC2626, AlertCircle icon)
- [x] 5.6 Implement "show password" toggle on password fields
- [x] 5.7 Add loading state (skeleton or spinner) during form submission
- [x] 5.8 Add success toast notification on successful registration → redirect to login
- [x] 5.9 Add success redirect on login → navigate to `/app/dashboard`
- [x] 5.10 Add "Don't have an account? Register" / "Already have an account? Login" links

## 6. Frontend Auth Integration

- [x] 6.1 Implement `login(email, password)` function in API client — calls `POST /api/v1/auth/login`
- [x] 6.2 Implement `register(name, email, password, password_confirmation)` function in API client
- [x] 6.3 Implement `logout()` function — calls `POST /api/v1/auth/logout`, clears auth state
- [x] 6.4 Implement `refreshToken()` function — calls `POST /api/v1/auth/refresh`
- [x] 6.5 Implement `fetchProfile()` function — calls `GET /api/v1/auth/me`
- [x] 6.6 Add automatic token refresh on 401 responses (intercept fetch calls)
- [x] 6.7 Add auth guard: redirect unauthenticated users from `/app/*` to `/app/login`
- [x] 6.8 Add reverse guard: redirect authenticated users from `/app/login` to `/app/dashboard`

## 7. Frontend Testing & Quality

- [x] 7.1 Write Playwright test: user can register, see success message, redirect to login
- [x] 7.2 Write Playwright test: user can login with valid credentials, redirect to dashboard
- [x] 7.3 Write Playwright test: login with invalid credentials shows error message
- [x] 7.4 Write Playwright test: logout clears session and redirects to login
- [x] 7.5 Write Playwright test: accessing dashboard while logged out redirects to login
- [x] 7.6 Run `pnpm run check` — TypeScript strict mode passes
- [x] 7.7 Run `pnpm run lint` — ESLint + Prettier passes
- [x] 7.8 Run `pnpm run build` — production build succeeds

## 8. Manual Verification

- [x] 8.1 Start backend dev server: `cd backend && composer run dev`
- [x] 8.2 Start frontend dev server: `cd frontend && pnpm run dev`
- [x] 8.3 Open `http://localhost:5173/app/register`, fill form, submit → see success toast → redirect to login
- [x] 8.4 Log in with registered credentials → redirect to dashboard
- [x] 8.5 Open browser dev tools → Application → Cookies → verify HTTP-only auth cookie exists
- [x] 8.6 Click logout → redirect to login → verify cookie is cleared
- [x] 8.7 Try accessing `/app/dashboard` while logged out → redirect to login
- [x] 8.8 Run Bruno/API test collection against `http://localhost:8080/api/v1/auth/*` endpoints
