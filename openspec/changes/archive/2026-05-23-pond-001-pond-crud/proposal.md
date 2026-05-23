## Why

Ponds are the core operational unit of a fish farm. Before users can record stock releases, sales, feed consumption, or any other operational data, they must first define the physical ponds within their farm. This feature enables farm owners and managers to create, view, update, and delete pond records scoped to a specific farm.

## What Changes

- **New API endpoints** for pond CRUD operations (`GET/POST/PATCH/DELETE /api/v1/ponds`)
- **New database table** `ponds` with fields: `id`, `farm_id`, `pond_number`, `size` (decimal acres), `created_at`, `updated_at`
- **Unique constraint** on `pond_number` per farm (not globally)
- **Multi-tenancy enforcement** via `farm_id` — all pond queries scoped to current farm
- **Frontend pages**: pond list (`/app/ponds`), create (`/app/ponds/new`), edit (`/app/ponds/[id]/edit`)
- **Farm-scoped authorization** — only farm members can view ponds; only owners/managers can create/update/delete
- **Backend tests** covering CRUD operations, duplicate pond numbers, authorization, and tenancy scoping
- **Bruno API collection** updates with pond endpoints

## Capabilities

### New Capabilities
- `pond-crud`: Pond creation, retrieval, update, and deletion within a farm scope

### Modified Capabilities
- *(none — no existing spec requirements change)*

## Impact

- **Backend**: New `Pond` model, migration, controller, FormRequest classes, factory, policy
- **Frontend**: New SvelteKit routes and components for pond management
- **Database**: New `ponds` table with `farm_id` foreign key
- **API**: New `/api/v1/ponds` resource endpoints
- **Bruno**: New requests added to existing collection
- **Depends on**: `FARM-001` (farm must exist before ponds can be created)
