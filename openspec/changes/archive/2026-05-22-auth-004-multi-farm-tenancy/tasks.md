## 1. Database & Migrations

- [x] 1.1 Create `farms` table migration (`id`, `name`, `location`, `created_at`, `updated_at`)
- [x] 1.2 Create `farm_user` pivot table migration (`farm_id`, `user_id`, `role`, `created_at`)
- [x] 1.3 Add `current_farm_id` nullable foreign key to `users` table
- [x] 1.4 Add foreign key constraints with `ON DELETE SET NULL` for `users.current_farm_id`
- [x] 1.5 Add foreign key constraints with `ON DELETE CASCADE` for `farm_user`
- [x] 1.6 Run migrations and verify schema

## 2. Models & Relationships

- [x] 2.1 Create `Farm` Eloquent model with `$fillable` and `users()` belongsToMany relationship
- [x] 2.2 Update `User` model with `farms()` belongsToMany relationship and `currentFarm()` belongsTo
- [x] 2.3 Create `BelongsToFarm` trait with global scope that reads active farm from request/auth
- [x] 2.4 Add `FarmScope` global scope class that applies `where('farm_id', ...)` condition
- [x] 2.5 Verify models work in tinker/console

## 3. Middleware & Request Context

- [x] 3.1 Create `EnsureFarmContext` middleware that resolves active farm from `X-Farm-ID` header or token claim
- [x] 3.2 Create `FarmContext` service class to hold active farm_id during request lifecycle
- [x] 3.3 Register middleware in `bootstrap/app.php` or route group
- [x] 3.4 Add middleware to all API routes that require farm scoping

## 4. Auth Token Enrichment

- [x] 4.1 Customize Passport token claims to include `farm_id` (current_farm_id)
- [x] 4.2 Update login response to include `requires_onboarding` flag and user's farms array
- [x] 4.3 Update `/api/v1/auth/me` response to include user's farms array
- [x] 4.4 Verify token contains farm claim via JWT decode

## 5. API Endpoints — Farm CRUD

- [x] 5.1 Create `FarmController` with `index()`, `store()`, `show()`, `update()`, `destroy()`
- [x] 5.2 Implement `index()` — list farms for authenticated user
- [x] 5.3 Implement `store()` — create farm, set owner pivot, set current_farm_id
- [x] 5.4 Implement `show()` — retrieve single farm (verify membership)
- [x] 5.5 Implement `update()` — update farm details (owner only)
- [x] 5.6 Implement `destroy()` — delete farm with cascade (owner only)
- [x] 5.7 Add validation rules (name required, unique per user, location optional)
- [x] 5.8 Add routes in `routes/api.php` under `/api/v1/farms`

## 6. API Endpoints — Farm Members

- [x] 6.1 Create `FarmMemberController` with `index()`, `store()`, `destroy()`
- [x] 6.2 Implement `index()` — list members of a farm
- [x] 6.3 Implement `store()` — add member by email (owner only)
- [x] 6.4 Implement `destroy()` — remove member (owner only, cannot remove self)
- [x] 6.5 Add validation rules (email exists, not already member)
- [x] 6.6 Add routes in `routes/api.php` under `/api/v1/farms/{farm}/members`

## 7. API Endpoints — Farm Switching

- [x] 7.1 Create `CurrentFarmController` with `update()` method
- [x] 7.2 Implement `update()` — validate farm_id, verify membership, update current_farm_id, revoke and reissue tokens
- [x] 7.3 Handle edge case: switching to same farm (no token refresh)
- [x] 7.4 Add route in `routes/api.php` for `PATCH /api/v1/users/current-farm`

## 8. Frontend — API Client Updates

- [x] 8.1 Update central API client to read `current_farm_id` from auth store
- [x] 8.2 Add `X-Farm-ID` header to all API requests
- [x] 8.3 Update auth store type definitions to include `farms` array and `requires_onboarding`
- [x] 8.4 Handle token refresh response after farm switch

## 9. Frontend — Farm Management Pages

- [x] 9.1 Create `/app/farms` page — list user's farms with cards
- [x] 9.2 Create `/app/farms/new` page — farm creation form
- [x] 9.3 Create `/app/farms/[id]/edit` page — farm edit form
- [x] 9.4 Create `/app/farms/[id]/members` page — member management
- [x] 9.5 Add farm deletion with confirmation dialog

## 10. Frontend — Farm Switcher

- [x] 10.1 Add farm switcher dropdown to global navigation
- [x] 10.2 Show current farm name in nav bar
- [x] 10.3 Call switch farm API on selection
- [x] 10.4 Update auth store with new tokens after switch
- [x] 10.5 Refresh page data after farm switch

## 11. Frontend — Onboarding Flow

- [x] 11.1 Create `/app/onboarding` page with farm creation form
- [x] 11.2 Add route guard — redirect to onboarding if `requires_onboarding` is true
- [x] 11.3 Prevent access to dashboard until at least one farm exists
- [x] 11.4 Show onboarding only once after first login

## 12. Testing — Backend

- [x] 12.1 Write test: user can create a farm
- [x] 12.2 Write test: farm name must be unique per user
- [x] 12.3 Write test: user can list their farms
- [x] 12.4 Write test: user cannot access another user's farm
- [x] 12.5 Write test: owner can update farm
- [x] 12.6 Write test: non-owner cannot update farm
- [x] 12.7 Write test: owner can delete farm
- [x] 12.8 Write test: owner can add members
- [x] 12.9 Write test: owner cannot remove themselves
- [x] 12.10 Write test: user can switch active farm
- [ ] 12.11 Write test: query scoping returns only current farm data
- [ ] 12.12 Write test: X-Farm-ID header overrides current_farm_id
- [x] 12.13 Write test: login returns requires_onboarding flag
- [x] 12.14 Update base TestCase to auto-create farm and set current_farm_id
- [x] 12.15 Run full test suite and verify all tests pass

## 13. Testing — Frontend

- [x] 13.1 Write Playwright test: farm creation flow
- [x] 13.2 Write Playwright test: farm switching updates data
- [x] 13.3 Write Playwright test: onboarding redirect for new users
- [x] 13.4 Run frontend tests and verify

## 14. Documentation & Specs

- [x] 14.1 Update `specs/auth.md` with multi-farm tenancy spec
- [x] 14.2 Update API documentation in `bruno/` collection
- [x] 14.3 Update `docs/sprint-progress.md` — mark AUTH-004 as complete
