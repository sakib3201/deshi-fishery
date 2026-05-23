## Context

Ponds are the fundamental operational unit in Deshi Fishery. Every subsequent feature — stock releases, sales, feed consumption, medicine application — operates on a per-pond basis. This design builds on the existing multi-farm tenancy system (`AUTH-004`) and farm CRUD (`FARM-001`) to add pond management within a farm scope.

Current state:
- Farms exist with `id`, `name`, `location`
- Users belong to farms via `farm_user` pivot with `role` (owner/manager/worker)
- `X-Farm-ID` header scopes all requests to a specific farm
- Global query scope enforces `farm_id` filtering on all farm-scoped models

## Goals / Non-Goals

**Goals:**
- Allow farm members to create ponds with a unique pond number and size
- Enforce pond number uniqueness per farm (duplicate allowed across different farms)
- Scope all pond operations to the current farm via `X-Farm-ID`
- Allow owners and managers to update/delete ponds; workers have read-only access
- Auto-update farm's `total_pond_count` when ponds are created or deleted
- Provide frontend pages for pond list, creation, and editing

**Non-Goals:**
- Pond geolocation or GPS coordinates (deferred to post-MVP)
- Pond lifecycle state machine (active/inactive/drained)
- Photo uploads for ponds
- Stock tracking within ponds (handled by `STOCK-001`)
- Batch pond creation

## Decisions

### 1. Pond number uniqueness: per-farm, not global
**Decision:** `pond_number` must be unique within a farm, but different farms can have the same pond number.
**Rationale:** Farm A's "Pond 1" and Farm B's "Pond 1" are independent. Global uniqueness would be unnecessarily restrictive.
**Alternative considered:** Global uniqueness with farm prefix (e.g., "FARM-A-Pond-1") — rejected as it complicates the simple numbering scheme users expect.

### 2. Size stored as decimal acres
**Decision:** `size` is a `DECIMAL(8,2)` representing acres.
**Rationale:** Bangladeshi fish farmers commonly measure pond size in acres. Decimal precision allows for fractional acres (e.g., 0.5 acres).
**Alternative considered:** Store in square meters — rejected as it requires mental conversion for users.

### 3. Soft deletes vs hard deletes
**Decision:** Hard deletes for ponds.
**Rationale:** Ponds are simple configuration data with no financial or audit implications. If a pond was created by mistake, it should be fully removable. Stock and sales records that reference a deleted pond will use foreign key constraints with `ON DELETE RESTRICT` to prevent deletion of ponds with associated records (enforced at the application level in MVP).
**Alternative considered:** Soft deletes with `deleted_at` — rejected as unnecessary complexity for MVP; can be added later if audit requirements emerge.

### 4. Auto-update farm pond count
**Decision:** Farm's `total_pond_count` is auto-calculated from the `ponds` table, not a cached counter.
**Rationale:** Avoids cache invalidation complexity. The count is a simple `COUNT(*)` query that performs well at MVP scale (< 100 ponds per farm).
**Alternative considered:** Increment/decrement counter on pond create/delete — rejected as premature optimization.

### 5. RBAC: Workers read-only
**Decision:** Workers can list and view ponds but cannot create, update, or delete.
**Rationale:** Workers are field staff who input operational data (feed, medicine) but should not modify farm structure.
**Alternative considered:** Allow workers to create ponds — rejected as it violates principle of least privilege.

## Risks / Trade-offs

| Risk | Mitigation |
|------|------------|
| Deleting a pond with stock/sales records would orphan data | Application-level check: reject delete if pond has associated records; return 422 with `PondHasRecords` error code |
| Farm switching mid-operation could create pond in wrong farm | `X-Farm-ID` is validated on every request; frontend always includes header from current farm context |
| Duplicate pond numbers within a farm due to race condition | Database unique composite index on `(farm_id, pond_number)` |
| Large number of ponds slows list endpoint | Pagination with cursor (already standard in API); unlikely to be an issue at MVP scale |

## Migration Plan

1. Run migration to create `ponds` table
2. Seed test data via factory for local development
3. No rollback needed — new table, no data migration

## Open Questions

- *(none at this time)*
