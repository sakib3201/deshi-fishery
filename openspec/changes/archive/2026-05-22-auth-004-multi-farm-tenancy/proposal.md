## Why

Deshi Fishery serves fish farm owners who often operate multiple farms. Currently, the system only supports a single farm per user, which limits scalability and does not reflect real-world usage where an owner manages several ponds across different locations. Multi-farm tenancy is required so that all downstream features (ponds, stock, sales, expenses, feed, medicine) can be properly scoped and isolated per farm. This change is a P0 blocker for Sprint 1.

## What Changes

- **New database tables**: `farms`, `farm_user` (pivot), and `farm_invitations`.
- **New API endpoints**: farm CRUD, farm switching, member listing, and invitation flow.
- **Global query scope**: every farm-scoped model automatically filters by the active `farm_id`.
- **Auth token enrichment**: access tokens include `current_farm_id`; switching farms issues a new token pair.
- **Frontend updates**: farm switcher in global navigation, farm management pages, and `X-Farm-ID` header on all API calls.
- **Registration flow update**: first-time users are prompted to create their first farm before reaching the dashboard.

## Capabilities

### New Capabilities
- `multi-farm-tenancy`: Core tenancy model — farms, membership roles, active farm tracking, and global query scoping.
- `farm-management`: CRUD operations for farms and farm members.
- `farm-switching`: Runtime switching of the active farm with token refresh.

### Modified Capabilities
- `user-auth`: Token payload now includes `current_farm_id`; login response shape changes to include the user's farm list.

## Impact

- **Backend**: All existing and future Eloquent models that belong to a farm must use the new `BelongsToFarm` trait / global scope.
- **Frontend**: Central API client must read `current_farm_id` from auth state and attach `X-Farm-ID` to every request.
- **Database**: New migrations; existing `users` table remains unchanged.
- **Tests**: Every test must set an active farm context before asserting farm-scoped data.
- **Dependencies**: None new; relies on existing Laravel 13 + Passport stack.
