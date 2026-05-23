## Context

The multi-farm tenancy system (AUTH-004) established the database schema and relationships for farms, but there is no dedicated API surface for managing farm entities. The `FarmController` exists with basic CRUD, but it lacks proper validation, authorization enforcement, and comprehensive test coverage. This change formalizes the Farm CRUD capability as a first-class feature.

Current state:
- `farms` table exists with `name`, `location`, `created_at`, `updated_at`
- `farm_user` pivot table exists with `role` column
- `Farm` model exists with `users()` relationship
- Basic `FarmController` exists but needs hardening

## Goals / Non-Goals

**Goals:**
- Provide complete CRUD API for farm management
- Enforce ownership-based authorization (only owners can update/delete)
- Validate farm names are unique per user
- Return consistent error envelopes for all failure modes
- Achieve 100% test coverage for all CRUD operations

**Non-Goals:**
- Farm branding/logos (deferred to UI sprint)
- Geolocation coordinates (location is free-text for MVP)
- Farm archiving/soft-delete (hard delete for MVP)
- Bulk farm operations

## Decisions

1. **Reuse existing FarmController** vs create new one
   - Decision: Extend existing `FarmController` with proper validation and tests
   - Rationale: Controller skeleton exists, avoids migration complexity

2. **Validation: unique name per user** vs globally unique
   - Decision: Farm names unique per user, not globally
   - Rationale: Different users should be able to name their farms independently

3. **Authorization: owner-only updates** vs manager can update
   - Decision: Only owners can update/delete farms
   - Rationale: Managers have edit permissions on data within farms, not farm metadata

4. **Response format: include role in list** vs separate endpoint
   - Decision: Include `role` from pivot in list response
   - Rationale: Frontend needs to know user's role per farm for UI decisions

## Risks / Trade-offs

- **[Risk]** Farm deletion removes all associated data (ponds, stock, sales) via cascade
  - **Mitigation**: Document this behavior; add confirmation in frontend; consider soft-delete post-MVP
- **[Risk]** Name uniqueness per user requires careful query scoping
  - **Mitigation**: Use `whereHas` with pivot constraints in validation
- **[Trade-off]** No bulk operations → more API calls for multi-farm setups
  - **Acceptance**: MVP scale is < 100 users, negligible impact
