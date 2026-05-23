## Why

The Deshi Fishery platform needs a dedicated Farm CRUD module to allow users to create, read, update, and delete farm records. While multi-farm tenancy infrastructure exists (AUTH-004), there is no standalone capability for managing farm entities themselves. This feature is foundational for Sprint 1 and blocks all pond/stock operations that depend on farm scoping.

## What Changes

- Add `FarmController` with full CRUD operations (index, store, show, update, destroy)
- Add Farm model with validation rules and relationships
- Add farm-scoped API routes under `/api/v1/farms`
- Enforce ownership-based authorization (only owners can update/delete)
- Add comprehensive Pest tests for all CRUD operations
- Add Bruno API collection requests for farm endpoints

## Capabilities

### New Capabilities
- `farm-crud`: Farm entity management — create, list, view, update, and delete farms with ownership enforcement

### Modified Capabilities
- `farm-management`: Update to include farm ownership validation rules and member management context

## Impact

- Backend: New controller, model updates, routes, tests
- Frontend: Farm management pages (list, create, edit)
- API: New endpoints under `/api/v1/farms`
- Database: Uses existing `farms` table from AUTH-004
- Authorization: Leverages existing `farm_user` pivot role system
