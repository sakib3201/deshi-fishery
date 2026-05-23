## Context

Sprint 1 established the foundation: users can authenticate, manage multiple farms, and CRUD ponds. Now we need to introduce the first operational data type: **stock releases** (fry releases). This is the entry point for all stock tracking — every sale, feed calculation, and harvest plan depends on knowing what was released and when.

The current `ponds` table only tracks `pond_number` and `size`. It lacks live stock state. We need to add stock fields to ponds and create a dedicated `stock_releases` table to record each release event.

## Goals / Non-Goals

**Goals:**
- Allow users to record fish fry releases into ponds with species, quantity, average weight, cost, and date.
- Automatically increment pond stock levels on each release.
- Provide CRUD API endpoints for stock releases scoped by farm.
- Build frontend pages for listing, creating, and viewing stock releases.
- Validate all inputs (positive quantities, recognized species, no future dates).
- Ensure multi-tenancy: stock data never leaks across farms.

**Non-Goals:**
- Stock decrements from sales, mortality, or harvest (those are separate changes: SALE-001, STOCK-003, STOCK-004).
- Feed or medicine tracking (Sprint 3).
- Growth sampling or projections (STOCK-002).
- Offline sync (Sprint 6).

## Decisions

**1. Stock fields on `ponds` table**
- Add `current_stock_quantity` (integer, default 0) and `current_stock_weight_kg` (decimal 10,2, default 0.00).
- Rationale: Live stock state must be queryable without summing all historical releases. This denormalization is acceptable because writes (releases) are infrequent compared to reads (dashboard, sales, reports).
- Alternative considered: Always compute from `stock_releases` table. Rejected because it would require expensive SUM queries on every dashboard load and sales transaction.

**2. `stock_releases` table structure**
- Fields: `id`, `farm_id`, `pond_id`, `species`, `quantity`, `avg_weight_gram`, `cost_bdt`, `release_date`, `notes`, `created_by`, `created_at`, `updated_at`.
- `species` is a string (not foreign key) because the species catalog is simple and may vary by region. Can be normalized later if needed.
- `quantity` is integer (whole fish count). `avg_weight_gram` is decimal to handle fry weights (e.g., 2.5g).
- `cost_bdt` tracks the purchase cost for later profit calculations.

**3. API endpoint design**
- Base: `/api/v1/stock-releases`
- Actions: `GET /` (list, cursor pagination), `POST /` (create), `GET /{id}` (show), `PATCH /{id}` (update), `DELETE /{id}` (delete).
- Query params: `pond_id` filter, `species` filter, `?cursor=xyz&per_page=20`.
- Rationale: Follows existing API conventions from pond-crud and farm-crud.

**4. Validation rules**
- `species`: required, string, max 100 chars.
- `quantity`: required, integer, min 1.
- `avg_weight_gram`: required, numeric, min 0.01.
- `cost_bdt`: required, numeric, min 0.
- `release_date`: required, date, not after today.
- `pond_id`: required, must exist in current farm.
- Rationale: Prevents nonsensical data (negative quantities, future dates) and enforces multi-tenancy via pond ownership check.

**5. Frontend approach**
- Reuse existing list/create/detail page patterns from pond-crud.
- Use Svelte 5 runes, Tailwind CSS v4 design tokens, and shadcn/ui components.
- Form: species dropdown (common Bangladeshi species: Rui, Katla, Mrigal, Tilapia, Pangas, Koi, Silver Carp, Grass Carp), quantity input, avg weight input, cost input, date picker, notes textarea.
- List page: table with pond number, species, quantity, release date, actions.

**6. Pond stock update strategy**
- On create: `pond.current_stock_quantity += release.quantity`; `pond.current_stock_weight_kg += (release.quantity * release.avg_weight_gram / 1000)`.
- On update: recalculate delta and adjust pond stock.
- On delete: subtract release quantity/weight from pond stock. If result would be negative, reject delete with `WouldMakeStockNegative`.
- Rationale: Keeps pond stock in sync without background jobs. Simple and reliable for MVP scale.

## Risks / Trade-offs

- **[Risk] Denormalized stock fields get out of sync** if future changes (sales, mortality) forget to update them.
  - **Mitigation**: All stock-modifying operations will be required to update pond fields. Document this in code comments and spec invariants.
- **[Risk] Deleting a release could make stock negative** if sales have occurred since the release.
  - **Mitigation**: Reject delete if it would make `current_stock_quantity` negative. Return `WouldMakeStockNegative` error. Users can adjust via future stock adjustment feature (STOCK-005).
- **[Risk] Species as free text leads to inconsistent entries** (e.g., "Rui" vs "rui" vs "Rohu").
  - **Mitigation**: Provide a dropdown with common species on the frontend. Backend accepts any string for flexibility, but normalization can be added later.

## Migration Plan

1. Run migration to add `current_stock_quantity` and `current_stock_weight_kg` to `ponds` table.
2. Run migration to create `stock_releases` table.
3. Deploy backend API changes.
4. Deploy frontend pages.
5. No data migration needed (new feature, no existing stock data).

## Open Questions

- Should we pre-populate a species catalog table now, or keep it as free text until Sprint 5? **Decision**: Keep as free text for MVP simplicity.
- Should stock releases support batch uploads (CSV)? **Decision**: Not in this change. Can be added later.
