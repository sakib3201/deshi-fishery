## Why

Fish farmers in Bangladesh need to record sales of fish from their ponds to track revenue and manage stock. Currently, there is no way to record wholesale or retail sales, which means farmers cannot track income, outstanding payments, or stock depletion from sales. This feature is essential for daily operations and is the next logical step after establishing pond management and stock releases.

## What Changes

- **New `sales` table** with fields: `farm_id`, `pond_id`, `sale_code`, `sale_type` (wholesale/retail), `date`, `fish_type`, `avg_fish_weight_g`, `quantity_kg`, `rate_per_kg`, `total_amount`, `customer_name`, `custom_tags`, `payment_status`, `amount_paid`, `amount_due`, `notes`.
- **New API endpoints**: `GET/POST /api/v1/sales`, `GET/PUT/DELETE /api/v1/sales/{id}`.
- **Stock decrement on sale creation**: When a sale is recorded, the associated pond's `current_stock_weight_kg` and optionally `current_stock_quantity` are reduced.
- **Auto-generated sale code**: Format `SALE-{farm_id}-{YYYYMMDD}-{sequence}`.
- **Payment status auto-calculation**: `pending` (amount_paid = 0), `partial` (0 < amount_paid < total), `paid` (amount_paid >= total).
- **Frontend pages**: Sales list (`/app/sales`), create sale (`/app/sales/new`), sale detail (`/app/sales/[id]`).
- **Real-time total calculation**: Frontend auto-computes `total_amount = quantity_kg × rate_per_kg`.
- **Validation**: Sale quantity must not exceed available pond stock. Reject with `InsufficientStock` error.
- **Multi-tenancy**: All sales scoped by `farm_id` via `X-Farm-ID` header.
- **RBAC**: Owners and Managers can create/edit/delete sales. Workers can view and create only.

## Capabilities

### New Capabilities
- `sale-record`: Recording wholesale and retail sales with auto-calculated totals and stock decrement.

### Modified Capabilities
- `pond-crud`: Pond stock fields (`current_stock_quantity`, `current_stock_weight_kg`) will be decremented when sales are recorded. This is an implementation-side change (existing stock release already modifies these fields), so no spec-level requirement changes.

## Impact

- **Backend**: New migration, model, controller, form requests, policy, and routes.
- **Frontend**: New API client methods, SvelteKit routes, and pages.
- **Database**: New `sales` table with indexes on `farm_id`, `pond_id`, `date`, `payment_status`.
- **Tests**: PHPUnit tests for API endpoints and Playwright E2E tests for frontend flows.
- **Dependencies**: Requires `pond-crud` and `stock-release` features to be in place (they are).
