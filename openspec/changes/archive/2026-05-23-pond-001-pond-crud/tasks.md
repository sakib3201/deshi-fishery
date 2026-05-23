## 1. Backend API Implementation

- [x] 1.1 Create `ponds` migration with `farm_id`, `pond_number`, `size` fields and unique composite index on `(farm_id, pond_number)`
- [x] 1.2 Create `Pond` model with `farm()` relationship and `$fillable` fields
- [x] 1.3 Create `PondFactory` for test data generation
- [x] 1.4 Create `StorePondRequest` FormRequest with validation rules (`pond_number` required, `size` numeric/min:0)
- [x] 1.5 Create `UpdatePondRequest` FormRequest with validation rules (same as store, plus duplicate check excluding current pond)
- [x] 1.6 Create `PondController` with `index`, `store`, `show`, `update`, `destroy` methods
- [x] 1.7 Implement `index` — list ponds scoped to current farm via `X-Farm-ID`, return 200 with array
- [x] 1.8 Implement `store` — create pond scoped to current farm, check duplicate pond number per farm, return 201
- [x] 1.9 Implement `show` — return pond if belongs to current farm, else 404
- [x] 1.10 Implement `update` — check ownership (owner/manager only), check duplicate pond number, return 200
- [x] 1.11 Implement `destroy` — check ownership (owner/manager only), check for associated records, return 204 or 422
- [x] 1.12 Add RBAC middleware to routes: workers can read, owners/managers can write
- [x] 1.13 Register routes in `api.php` under `/api/v1/ponds`
- [x] 1.14 Add `ponds` relationship to `Farm` model

## 2. Backend Tests

- [x] 2.1 Test pond list returns ponds for current farm
- [x] 2.2 Test pond list returns empty array when no ponds
- [x] 2.3 Test pond list without X-Farm-ID returns 400
- [x] 2.4 Test pond creation with valid data returns 201
- [x] 2.5 Test pond creation with duplicate number in same farm returns 422
- [x] 2.6 Test pond creation with duplicate number in different farm succeeds
- [x] 2.7 Test pond creation without pond_number returns 422
- [x] 2.8 Test pond creation as worker returns 403
- [x] 2.9 Test pond retrieval by ID returns 200
- [x] 2.10 Test pond retrieval for pond in different farm returns 404
- [x] 2.11 Test pond update as owner returns 200
- [x] 2.12 Test pond update as worker returns 403
- [x] 2.13 Test pond update with duplicate number returns 422
- [x] 2.14 Test pond deletion as owner returns 204
- [x] 2.15 Test pond deletion as worker returns 403
- [x] 2.16 Test pond deletion with associated records returns 422

## 3. Frontend Implementation

- [x] 3.1 Create `/app/ponds` page — list ponds with pond number, size, and actions
- [x] 3.2 Create `/app/ponds/new` page — form with pond number and size inputs
- [x] 3.3 Create `/app/ponds/[id]/edit` page — pre-filled form for updating pond
- [x] 3.4 Add pond navigation link to sidebar/nav
- [x] 3.5 Implement delete confirmation dialog on pond list
- [x] 3.6 Handle and display API errors (duplicate number, validation, 403)
- [x] 3.7 Show empty state when farm has no ponds

## 4. Bruno API Collection

- [x] 4.1 Add `List Ponds` request to Bruno collection
- [x] 4.2 Add `Create Pond` request with post-response script to extract pond ID
- [x] 4.3 Add `Get Pond` request
- [x] 4.4 Add `Update Pond` request
- [x] 4.5 Add `Delete Pond` request
- [x] 4.6 Add environment variable for `pondId`

## 5. Documentation & Sprint Update

- [x] 5.1 Update `docs/sprint-progress.md` — mark POND-001 as In Progress
- [x] 5.2 Verify all tests pass (`php artisan test`)
- [x] 5.3 Run Pint linting (`vendor/bin/pint`)
- [x] 5.4 Verify frontend type checks pass (`pnpm run check`)
