# Auth Module — Multi-Farm Tenancy Specification

## SPEC AUTH-004  Multi-Farm Tenancy & Farm Switching

A user can own or belong to multiple farms. All data is scoped by the active farm. The active farm is conveyed via `X-Farm-ID` header or derived from the authenticated user's `current_farm_id`.

### PRECONDITION
- User is authenticated via Laravel Passport OAuth 2.0
- User has at least one farm (created during onboarding) OR is in onboarding flow

### POSTCONDITION
- Every farm-scoped query returns only data belonging to the active farm
- Switching farms updates `current_farm_id` and refreshes the access token
- Users can only access farms they are members of

### INVARIANT
- `farm_id` is present on all farm-scoped database tables
- Cross-farm data leakage is prevented by the `BelongsToFarm` trait global scope
- The `X-Farm-ID` header takes precedence over the token's `current_farm_id` claim

### ERROR
- `InvalidFarmAccess` — User does not have access to the requested farm
- `DuplicateFarmName` — User already has a farm with this name
- `UserNotFound` — Email provided for member addition does not exist
- `AlreadyMember` — User is already a member of the farm
- `CannotRemoveOwner` — Owner cannot remove themselves from the farm

---

## API Endpoints

### Farm CRUD
- `GET /api/v1/farms` — List user's farms
- `POST /api/v1/farms` — Create a new farm
- `GET /api/v1/farms/{id}` — Get farm details
- `PATCH /api/v1/farms/{id}` — Update farm (owner only)
- `DELETE /api/v1/farms/{id}` — Delete farm (owner only)

### Farm Members
- `GET /api/v1/farms/{farm}/members` — List members
- `POST /api/v1/farms/{farm}/members` — Add member by email (owner only)
- `DELETE /api/v1/farms/{farm}/members/{user}` — Remove member (owner only)

### Farm Switching
- `PATCH /api/v1/users/current-farm` — Switch active farm

### Auth Updates
- `POST /api/v1/auth/login` — Now returns `requires_onboarding` flag and user's farms
- `GET /api/v1/auth/me` — Now returns user's farms array

---

## Database Schema

### `farms` table
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | Auto-increment |
| name | string | Required |
| location | string | Nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### `farm_user` pivot table
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | Auto-increment |
| farm_id | bigint FK | `farms.id`, cascade on delete |
| user_id | bigint FK | `users.id`, cascade on delete |
| role | string | Default: 'owner' |
| created_at | timestamp | |
| updated_at | timestamp | |
| Unique | farm_id + user_id | |

### `users` table (updated)
| Column | Type | Notes |
|--------|------|-------|
| current_farm_id | bigint FK | `farms.id`, nullable, set null on delete |

---

## Frontend Routes

- `/app/onboarding` — First-time farm creation
- `/app/farms` — List farms
- `/app/farms/new` — Create farm
- `/app/farms/{id}/edit` — Edit farm
- `/app/farms/{id}/members` — Manage members

---

## Implementation Notes

- Global query scope via `BelongsToFarm` trait on all farm-scoped models
- `FarmContext` service holds active `farm_id` during request lifecycle
- `EnsureFarmContext` middleware resolves farm from `X-Farm-ID` header or token claim
- Token refresh on farm switch for security
