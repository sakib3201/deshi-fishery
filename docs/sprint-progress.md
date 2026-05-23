# Deshi Fishery — Sprint Progress Tracker

> High-level progress document for tracking feature implementation across sprints.
> Each feature is implemented via OpenSpec workflow: propose → explore → apply → archive.
> Update this file after each feature is completed.

---

## Project Status Overview

| Phase | Progress | Status |
|-------|----------|--------|
| Requirements Gathering | 100% | ✅ Complete |
| Technical Architecture | 100% | ✅ Complete |
| Frontend Initialization | 100% | ✅ Complete |
| UI/UX Design | 100% | ✅ Complete |
| Implementation | 35% | 🔄 In Progress (Sprint 2) |
| Testing & Deployment | 0% | ⏳ Pending |

---

## Sprint 1 — Foundation (P0 Features)

> **Goal**: Establish authentication, multi-tenancy, and core domain primitives (farms & ponds).
> These features block all subsequent development.

| # | Feature | OpenSpec ID | Status | Change Name | Notes |
|---|---------|-------------|--------|-------------|-------|
| 1 | Email/Password Auth | `AUTH-001` | ✅ Complete | `auth-001-email-password-auth` | Archived 2026-05-22. All 73 tasks done. 14 PHPUnit tests pass. |
| 2 | Multi-Farm Tenancy | `AUTH-004` | ✅ Complete | `auth-004-multi-farm-tenancy` | Archived 2026-05-22. All 77 tasks done. 28 PHPUnit tests pass. |
| 3 | Farm CRUD | `FARM-001` | ✅ Complete | `farm-001-farm-crud` | Implemented 2026-05-23. All 24 tasks done. 17 PHPUnit tests pass. |
| 4 | Pond CRUD | `POND-001` | ✅ Complete | `pond-001-pond-crud` | Archived 2026-05-23. All 47 tasks done. 16 PHPUnit tests pass. |

**Sprint 1 Definition of Done**:
- [x] User can register with email/password
- [x] User can login and receive tokens
- [x] User can logout and tokens are revoked
- [x] User can create multiple farms
- [x] User can switch between farms
- [x] User can CRUD ponds within a farm
- [x] All data is scoped by `farm_id`
- [x] Pest tests pass for all auth and farm/pond flows
- [x] Playwright tests pass for login and farm creation
- [x] Admin panel has persistent navigation with theme/language toggles
- [x] Dark mode works across all admin pages
- [x] Design system tokens used consistently (no hardcoded colors)

---

## Sprint 2 — Core Operations (P0 Features)

> **Goal**: Enable daily operational data entry — stock releases, sales, expenses, and ledger.

| # | Feature | OpenSpec ID | Status | Change Name | Notes |
|---|---------|-------------|--------|-------------|-------|
| 5 | Fry Release | `STOCK-001` | ⏳ Pending | — | Increments stock biomass |
| 6 | Record Sale | `SALE-001` | ⏳ Pending | — | Decrements stock, payment tracking |
| 7 | Payment Tracking | `SALE-002` | ⏳ Pending | — | Partial payments, status auto-update |
| 8 | General Expenses | `EXP-001` | ⏳ Pending | — | Cash-basis expense entry |
| 9 | Daily Ledger | `EXP-005` | ⏳ Pending | — | Income/expense daily view with running balance |

**Sprint 2 Definition of Done**:
- [ ] User can record fish fry releases per pond
- [ ] User can record wholesale/retail sales
- [ ] User can track partial payments on sales
- [ ] User can add expenses by category
- [ ] User can view daily ledger with running balance
- [ ] Stock auto-adjusts on sales and releases
- [ ] All flows tested with Pest + Playwright

---

## Sprint 3 — Inventory & Operations (P0-P1 Features)

> **Goal**: Complete feed and medicine management — inventory tracking and consumption.

| # | Feature | OpenSpec ID | Status | Change Name | Notes |
|---|---------|-------------|--------|-------------|-------|
| 10 | Feed Types | `FEED-001` | ⏳ Pending | — | Catalog of feed types |
| 11 | Feed Stock Receipt | `FEED-002` | ⏳ Pending | — | Warehouse inventory in |
| 12 | Feed Consumption | `FEED-003` | ⏳ Pending | — | Per-pond daily consumption |
| 13 | Medicine Types | `MED-001` | ⏳ Pending | — | Catalog of medicines |
| 14 | Medicine Stock | `MED-002` | ⏳ Pending | — | Purchase tracking |
| 15 | Medicine Usage | `MED-003` | ⏳ Pending | — | Per-pond treatment log |

**Sprint 3 Definition of Done**:
- [ ] User can manage feed catalog
- [ ] User can record feed purchases (bag/kg dual input)
- [ ] User can record daily feed consumption per pond
- [ ] Warehouse stock auto-decrements on consumption
- [ ] User can manage medicine catalog
- [ ] User can record medicine purchases and usage
- [ ] Medicine inventory auto-decrements on usage
- [ ] All flows tested with Pest + Playwright

---

## Sprint 4 — Dashboard & Reporting (P0-P1 Features)

> **Goal**: Visualize data with charts and enable operational reporting.

| # | Feature | OpenSpec ID | Status | Change Name | Notes |
|---|---------|-------------|--------|-------------|-------|
| 16 | Main Dashboard | `DASH-001` | ⏳ Pending | — | Financial overview, summary cards |
| 17 | Expense Pie Chart | `DASH-002` | ⏳ Pending | — | LayerChart pie chart |
| 18 | Growth Line Chart | `DASH-003` | ⏳ Pending | — | LayerChart line chart |
| 19 | Sales Bar Chart | `DASH-004` | ⏳ Pending | — | LayerChart bar chart |
| 20 | Operational Reports | `DASH-005` | ⏳ Pending | — | Daily/weekly reports |

**Sprint 4 Definition of Done**:
- [ ] Dashboard shows income, expenses, balance, stock summary
- [ ] Expense breakdown pie chart is interactive
- [ ] Growth trends line chart displays sample data
- [ ] Sales comparison bar chart shows monthly data
- [ ] Daily and weekly operational reports are generated
- [ ] All charts use LayerChart with ocean-blue palette
- [ ] Dashboard tested with Playwright

---

## Sprint 5 — Advanced Features (P1-P2 Features)

> **Goal**: Add social login, RBAC, sales enhancements, and feed alerts.

| # | Feature | OpenSpec ID | Status | Change Name | Notes |
|---|---------|-------------|--------|-------------|-------|
| 21 | Social Login | `AUTH-002` | ⏳ Pending | — | Google + Facebook OAuth |
| 22 | RBAC & Audit Logs | `AUTH-003` | ⏳ Pending | — | Role middleware, edit logging |
| 23 | Customer Balances | `SALE-003` | ⏳ Pending | — | Outstanding per customer |
| 24 | Invoice PDF | `SALE-004` | ⏳ Pending | — | Gotenberg HTML-to-PDF |
| 25 | Sales Filtering | `SALE-005` | ⏳ Pending | — | Filter by type, customer, date, species |
| 26 | Feed Ledger | `FEED-004` | ⏳ Pending | — | Stock + consumption overview |
| 27 | Feed Reorder Alerts | `FEED-005` | ⏳ Pending | — | Dashboard + email notifications |

**Sprint 5 Definition of Done**:
- [ ] User can login with Google/Facebook
- [ ] RBAC enforced on all endpoints (Owner/Manager/Worker)
- [ ] Manager edits are logged in audit_logs table
- [ ] Customer balance page shows outstanding amounts
- [ ] Sales invoices generate as PDF via Gotenberg
- [ ] Sales list supports filtering and search
- [ ] Feed ledger shows received/consumed/remaining per type
- [ ] Low feed stock triggers dashboard alert

---

## Sprint 6 — Partners, Sync & Polish (P1-P2 Features)

> **Goal**: Partner accounting, offline sync, localization, and remaining stock features.

| # | Feature | OpenSpec ID | Status | Change Name | Notes |
|---|---------|-------------|--------|-------------|-------|
| 28 | Partner Ledger | `PARTNER-001` | ⏳ Pending | — | Investment/withdrawal tracking |
| 29 | Profit Cycles | `PARTNER-002` | ⏳ Pending | — | Per-harvest profit distribution |
| 30 | Offline Sync | `SYNC-001` | ⏳ Pending | — | PWA offline data entry + sync |
| 31 | Conflict Resolution | `SYNC-002` | ⏳ Pending | — | Last-write-wins with user choice |
| 32 | Language Toggle | `UI-001` | ⏳ Pending | — | English / Bangla switch |
| 33 | Responsive Layout | `UI-002` | ⏳ Pending | — | Mobile-first responsive design |
| 34 | CSV/XLSX Export | `DASH-006` | ⏳ Pending | — | Async export with filters |
| 35 | Growth Sampling | `STOCK-002` | ⏳ Pending | — | Line chart + days-to-target |
| 36 | Mortality Tracking | `STOCK-003` | ⏳ Pending | — | Dead count, stock decrement |
| 37 | Partial Harvest | `STOCK-004` | ⏳ Pending | — | Harvest steps, stock decrement |
| 38 | Manual Stock Adjustment | `STOCK-005` | ⏳ Pending | — | Reason-required adjustment |
| 39 | Fuel Expenses | `EXP-002` | ⏳ Pending | — | Liters + purpose tracking |
| 40 | Electricity Bills | `EXP-003` | ⏳ Pending | — | Meter + billing period |
| 41 | Staff Salaries | `EXP-004` | ⏳ Pending | — | Monthly breakdown by staff |

**Sprint 6 Definition of Done**:
- [ ] Partner investments/withdrawals affect cash balance
- [ ] Profit cycles calculate net profit and distribute by share
- [ ] PWA works offline for 24+ hours
- [ ] Offline data syncs automatically on reconnect
- [ ] Conflict resolution UI allows user choice
- [ ] Language toggle switches all UI text and numerals
- [ ] Layout is responsive on mobile (375px) and desktop (1280px)
- [ ] CSV/XLSX export respects filters and date ranges
- [ ] All remaining stock features implemented
- [ ] All expense sub-types (fuel, electricity, salary) implemented
- [ ] Full Playwright E2E suite passes

---

## Legend

| Symbol | Meaning |
|--------|---------|
| ✅ | Complete — feature implemented, tested, and archived |
| 🔄 | In Progress — OpenSpec change created, currently implementing |
| ⏳ | Pending — not started, waiting on dependencies |
| 🚫 | Blocked — cannot proceed due to external dependency |

---

## How to Update This Document

After completing each feature via the OpenSpec workflow:

1. **After `/opsx propose`**: Add the change name to the feature row
2. **After `/opsx apply`**: Change status from `⏳ Pending` to `🔄 In Progress`
3. **After `/opsx archive`**: Change status from `🔄 In Progress` to `✅ Complete`
4. **Update sprint checklist**: Check off completed items in the Definition of Done

---

## Current Sprint Focus

**Sprint 2 — Core Operations**

> ✅ Completed: Sprint 1 — Foundation (all 4 features done)
> 🔄 Next up: `STOCK-001` Fry Release
> Blocked by: Nothing

---

*Last updated: 2026-05-23*
*Next review: After STOCK-001 implementation complete*
