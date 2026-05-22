## Context

The backend is a fresh Laravel 13 install with Passport OAuth 2.0 already configured (AUTH-001). There is currently no concept of a "farm" in the domain model. Every table that will be created from this point forward (ponds, stock, sales, expenses, feed, medicine) must belong to a farm and be query-scoped. The frontend is a SvelteKit SPA using native Fetch API; it currently stores an access token but does not send any tenancy header.

## Goals / Non-Goals

**Goals:**
- A user can own or belong to multiple farms.
- Every farm-scoped database query is automatically filtered by the active farm.
- The active farm is conveyed via `X-Farm-ID` header or derived from the authenticated user's `current_farm_id`.
- Switching farms is a single API call that updates `current_farm_id` and returns a refreshed token pair.
- First-time registration prompts the user to create a farm before accessing the dashboard.
- All existing auth tests continue to pass; new tenancy tests are added.

**Non-Goals:**
- Farm-level RBAC (roles like Owner/Manager/Worker per farm) — that is AUTH-003.
- Farm invitations via email — out of MVP scope; manual member addition only.
- Data migration from single-farm to multi-farm — there is no production data yet.
- Sub-domain or path-based tenancy — shared-schema with `farm_id` only.

## Decisions

### 1. Shared-schema multi-tenancy via `farm_id`
- **Choice**: One database, one schema, every farm-scoped table has a `farm_id` column.
- **Rationale**: Simplest to operate on a single VPS. No schema-per-farm complexity. Easy backups.
- **Alternative**: Schema-per-farm — rejected due to operational overhead for <100 users.

### 2. Global query scope applied by a `BelongsToFarm` trait
- **Choice**: A trait that boots a global scope reading `farm_id` from a request-bound service or auth user.
- **Rationale**: Zero chance of forgetting to scope a query. Developers cannot accidentally leak cross-farm data.
- **Trade-off**: Slightly magical; requires careful testing of unscoped queries in admin contexts.

### 3. `current_farm_id` stored on the `users` table
- **Choice**: A `current_farm_id` nullable foreign key on `users`.
- **Rationale**: Fast lookup on every request without extra joins. Simple to update on farm switch.
- **Alternative**: Store in Redis/session — rejected because tokens must be stateless for PWA offline sync later.

### 4. Token enrichment with `current_farm_id`
- **Choice**: Passport access tokens include a custom claim `farm_id` (the user's current farm).
- **Rationale**: The API can validate tenancy from the token alone when the header is missing. Frontend can decode the JWT to know the active farm without an extra round-trip.
- **Trade-off**: Token size grows slightly; token refresh required on farm switch.

### 5. `X-Farm-ID` header takes precedence over token claim
- **Choice**: If the header is present and valid for the user, use it; otherwise fall back to `current_farm_id` from the token.
- **Rationale**: Allows frontend to optimistically switch farms before token refresh completes. Supports API testing with tools like Bruno.

### 6. Farm creation during onboarding
- **Choice**: After email verification, if the user has no farms, redirect to an onboarding page that forces farm creation.
- **Rationale**: Prevents null-farm states. Every authenticated request thereafter has a valid farm context.

## Risks / Trade-offs

| Risk | Mitigation |
|------|------------|
| Forgetting to apply `BelongsToFarm` on a new model | Code-review checklist + PHPStan custom rule (future) |
| `current_farm_id` points to a deleted farm | Foreign-key constraint with `ON DELETE SET NULL` + middleware check that rejects requests with invalid farm |
| Token refresh on farm switch feels slow | Optimistic UI update on frontend; refresh token in background |
| Cross-farm data leakage in raw SQL or unscoped queries | Audit all `DB::raw` usage; ban unscoped queries in non-admin code |
| Null `farm_id` in test setup | Base `TestCase` automatically creates a farm and sets it as current in `setUp()` |

## Migration Plan

No production migration needed (MVP, no live data). For local/dev:
1. Run new migrations (`farms`, `farm_user`).
2. Seed a default farm for existing test users (if any).
3. Verify all existing AUTH-001 tests still pass.

## Open Questions

- Should we enforce a maximum number of farms per user in MVP? (Decision: no limit for now.)
- Should farm names be unique per user or globally? (Decision: unique per user.)
