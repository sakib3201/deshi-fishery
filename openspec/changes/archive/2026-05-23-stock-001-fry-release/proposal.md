## Why

Fish farmers in Bangladesh need to track when fish fry (baby fish) are released into ponds. Without a systematic record, it's impossible to calculate growth rates, estimate harvest times, or track stock levels. This feature enables the foundational stock management capability that all downstream operations (sales, feed, medicine) depend on.

## What Changes

- **New `stock_releases` table**: Records every fry release event per pond with species, quantity, average weight, cost, and date.
- **New API endpoints**: CRUD endpoints for stock releases under `/api/v1/stock-releases`.
- **Pond stock auto-increment**: Each release automatically increments the pond's current stock quantity.
- **Frontend pages**: List, create, and detail views for stock releases.
- **Validation rules**: Species must be a recognized fish type, quantity must be positive, release date cannot be in the future.
- **Multi-tenancy**: All stock data scoped by `farm_id`.

## Capabilities

### New Capabilities
- `stock-release`: Recording and managing fish fry releases into ponds, including species tracking, quantity, cost, and automatic stock increment.

### Modified Capabilities
- `pond-crud`: Add `current_stock_quantity` and `current_stock_weight_kg` fields to the pond model to track live stock levels. These fields are auto-updated by stock releases (and later by sales/mortality/harvest).

## Impact

- **Backend**: New migration, model, controller, form request, policy, and routes.
- **Frontend**: New routes and Svelte components for stock release management.
- **Database**: New `stock_releases` table; adds `current_stock_quantity` and `current_stock_weight_kg` to `ponds` table.
- **API**: New resource endpoints under `/api/v1/stock-releases`.
- **Dependencies**: Requires `pond-crud` and `multi-farm-tenancy` to be complete (they are).
