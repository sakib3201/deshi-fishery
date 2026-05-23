## 1. Database & Migrations

- [x] 1.1 Add `current_stock_quantity` (integer, default 0) and `current_stock_weight_kg` (decimal 10,2, default 0.00) to `ponds` table via migration
- [x] 1.2 Create `stock_releases` migration with fields: `id`, `farm_id`, `pond_id`, `species`, `quantity`, `avg_weight_gram`, `cost_bdt`, `release_date`, `notes`, `created_by`, `created_at`, `updated_at`
- [x] 1.3 Add foreign key constraints: `farm_id` → `farms(id)` on delete cascade, `pond_id` → `ponds(id)` on delete restrict
- [x] 1.4 Run migrations and verify schema in local SQLite

## 2. Backend — Model & Relationships

- [x] 2.1 Create `StockRelease` Eloquent model with `$fillable`, casts, and `belongsTo` relationships to `Farm`, `Pond`, and `User` (created_by)
- [x] 2.2 Add `hasMany` relationship from `Pond` to `StockRelease`
- [x] 2.3 Add `hasMany` relationship from `Farm` to `StockRelease`
- [x] 2.4 Add `stockReleases()` to `User` model (via `created_by`)

## 3. Backend — Policy & Authorization

- [x] 3.1 Create `StockReleasePolicy` with `viewAny`, `view`, `create`, `update`, `delete` methods
- [x] 3.2 Enforce farm membership check in all policy methods
- [x] 3.3 Restrict `create`, `update`, `delete` to owners and managers only (workers get 403)
- [x] 3.4 Register policy in `AuthServiceProvider`

## 4. Backend — Form Requests & Validation

- [x] 4.1 Create `StoreStockReleaseRequest` with rules: species (required, string, max 100), quantity (required, integer, min 1), avg_weight_gram (required, numeric, min 0.01), cost_bdt (required, numeric, min 0), release_date (required, date, before_or_equal:today), pond_id (required, exists:ponds,id), notes (nullable, string, max 500)
- [x] 4.2 Create `UpdateStockReleaseRequest` with same rules but nullable
- [x] 4.3 Add custom validation: `pond_id` must belong to current farm (use `X-Farm-ID` header)
- [x] 4.4 Ensure validation errors return 422 with field-level messages

## 5. Backend — Controller & API Endpoints

- [x] 5.1 Create `StockReleaseController` with `index`, `store`, `show`, `update`, `destroy`
- [x] 5.2 Implement `index`: cursor pagination, filter by `pond_id` and `species`, eager load `pond`, return 200
- [x] 5.3 Implement `store`: validate, create release, increment pond stock fields, return 201 with resource
- [x] 5.4 Implement `show`: return 200 with resource (404 if not in current farm)
- [x] 5.5 Implement `update`: validate, calculate delta, adjust pond stock, update release, return 200
- [x] 5.6 Implement `destroy`: check if delete would make stock negative, if yes return 422 with `WouldMakeStockNegative`, otherwise decrement pond stock and delete, return 204
- [x] 5.7 Add routes in `routes/api.php` under `/api/v1/stock-releases`
- [x] 5.8 Apply `auth:api` and farm-scoping middleware to all routes

## 6. Backend — Tests

- [x] 6.1 Write test: list stock releases for current farm (200, correct data)
- [x] 6.2 Write test: list stock releases filtered by pond_id
- [x] 6.3 Write test: create stock release with valid data (201, pond stock incremented)
- [x] 6.4 Write test: create stock release with invalid quantity (422, no stock change)
- [x] 6.5 Write test: create stock release with future date (422)
- [x] 6.6 Write test: create stock release for pond in different farm (422)
- [x] 6.7 Write test: create stock release as worker (403)
- [x] 6.8 Write test: get stock release by ID (200)
- [x] 6.9 Write test: get stock release from different farm (404)
- [x] 6.10 Write test: update stock release quantity (200, pond stock recalculated)
- [x] 6.11 Write test: update stock release as worker (403)
- [x] 6.12 Write test: delete stock release as owner (204, pond stock decremented)
- [x] 6.13 Write test: delete stock release that would make stock negative (422)
- [x] 6.14 Write test: delete stock release as worker (403)
- [x] 6.15 Write test: pond list includes `current_stock_quantity` and `current_stock_weight_kg`
- [x] 6.16 Run all tests and ensure they pass

## 7. Frontend — API Client & Types

- [x] 7.1 Add `StockRelease` TypeScript interface in `src/lib/types/`
- [x] 7.2 Add stock release API methods to `src/lib/api/client.ts` (list, create, get, update, delete)
- [x] 7.3 Add query params support for `pond_id` and `species` filters

## 8. Frontend — Pages & Components

- [x] 8.1 Create `/stock-releases` page: list view with table (pond number, species, quantity, release date, actions)
- [x] 8.2 Create `/stock-releases/new` page: form with species dropdown, quantity, avg weight, cost, date picker, notes
- [x] 8.3 Create `/stock-releases/[id]` page: detail view with all fields and edit/delete actions
- [x] 8.4 Add stock release link to `AppNavbar` navigation
- [x] 8.5 Use design system tokens (no hardcoded colors), 56px min touch targets
- [x] 8.6 Ensure Bengali numeral support when Bangla locale is active

## 9. Frontend — Tests

- [x] 9.1 Write Playwright test: navigate to stock releases list
- [x] 9.2 Write Playwright test: create a stock release
- [x] 9.3 Write Playwright test: view stock release detail
- [x] 9.4 Write Playwright test: delete a stock release
- [x] 9.5 Run Playwright tests and ensure they pass

## 10. Documentation & Cleanup

- [x] 10.1 Update `docs/sprint-progress.md`: mark STOCK-001 as 🔄 In Progress, add change name
- [x] 10.2 Add API endpoint documentation to `bruno/` collection (if applicable)
- [x] 10.3 Run backend lint (`vendor/bin/pint`) and fix any issues
- [x] 10.4 Run frontend lint (`pnpm run lint`) and fix any issues
- [x] 10.5 Run `pnpm run check` (svelte-check + TypeScript) — errors are pre-existing in auth components, not from our changes
