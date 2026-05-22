---
custom-width: 100
---
# Deshi Fishery — Feature Implementation List

> Derived from `03 - Refined Requirements.md` and `01 - Functional Requirements.md`.
> Each feature below is sized for end-to-end implementation using `/opsx` skills (OpenSpec propose → explore → apply → archive).
> For every feature, the **Manually Verifiable Behavior** section describes exactly what a human should be able to do in the running app to confirm it works.

---

## Legend

| Column | Meaning |
|--------|---------|
| **Module** | Logical grouping (matches `specs/{module}.md`) |
| **Feature** | What the user can do |
| **Backend Work** | API endpoints, DB tables, models, policies |
| **Frontend Work** | SvelteKit routes, pages, forms, charts |
| **Manually Verifiable Behavior** | Exact steps to test in browser |
| **OpenSpec ID** | Proposed spec identifier |
| **Priority** | MVP order |

---

## 1. Authentication & Authorization

### 1.1 Email / Password Registration & Login
- **Backend**: `POST /api/v1/auth/register`, `POST /api/v1/auth/login`, `POST /api/v1/auth/logout`, `POST /api/v1/auth/refresh`. Password hashing (bcrypt cost ≥12). Rate limit 5 req/15 min per IP.
- **Frontend**: `/app/login`, `/app/register` pages. Form validation, error display, JWT/cookie storage.
- **Manually Verifiable Behavior**:
  1. Go to `/app/register`, fill name, email, password, submit.
  2. See success toast → redirected to login.
  3. Log in with the same credentials → redirected to dashboard.
  4. Log out → redirected to login; accessing `/app/dashboard` redirects back to login.
- **OpenSpec ID**: `AUTH-001`
- **Priority**: P0 (blocks everything)

### 1.2 Social Login (Google, Facebook)
- **Backend**: Laravel Socialite + Passport. `GET /api/v1/auth/{provider}/redirect`, `GET /api/v1/auth/{provider}/callback`. Create/link user account.
- **Frontend**: "Continue with Google" / "Continue with Facebook" buttons on login page.
- **Manually Verifiable Behavior**:
  1. Click "Continue with Google" on login page.
  2. Complete OAuth flow in popup/redirect.
  3. Land on dashboard as authenticated user.
  4. Check DB — user exists with `provider = google` and `provider_id` set.
- **OpenSpec ID**: `AUTH-002`
- **Priority**: P1

### 1.3 Role-Based Access Control (RBAC) & Audit Logs
- **Backend**: Middleware `role:owner|manager|worker`. `audit_logs` table (immutable). Manager edits log old/new values + timestamp + editor.
- **Frontend**: UI elements conditionally rendered by role. Workers see no delete buttons, no partner ledger.
- **Manually Verifiable Behavior**:
  1. Log in as Owner → see "Users" nav item, can delete a pond.
  2. Log in as Manager → can edit a pond, but delete button hidden. Edit is logged in `audit_logs`.
  3. Log in as Worker → "Users" nav hidden, delete buttons hidden, partner ledger inaccessible (403).
- **OpenSpec ID**: `AUTH-003`
- **Priority**: P0

### 1.4 Multi-Farm Tenancy & Farm Switching
- **Backend**: `X-Farm-ID` header or `current_farm_id` on user. Global query scope on all farm-scoped models. `GET /api/v1/farms`, `POST /api/v1/farms`, `PATCH /api/v1/users/current-farm`.
- **Frontend**: Farm switcher in global nav. All API calls include `X-Farm-ID`.
- **Manually Verifiable Behavior**:
  1. Create Farm A and Farm B as Owner.
  2. Add Pond 1 to Farm A, Pond 2 to Farm B.
  3. Switch to Farm A → only Pond 1 visible.
  4. Switch to Farm B → only Pond 2 visible.
  5. Direct API call with wrong `X-Farm-ID` returns 403/404.
- **OpenSpec ID**: `AUTH-004`
- **Priority**: P0

---

## 2. Farm & Pond Management

### 2.1 Farm Configuration (Create / Edit / List)
- **Backend**: `GET/POST/PATCH/DELETE /api/v1/farms`. Fields: `name`, `location` (string or lat/lng), `total_pond_count` (auto-calculated).
- **Frontend**: `/app/farms` list, `/app/farms/new` form, `/app/farms/[id]/edit` form. Geolocation API for location.
- **Manually Verifiable Behavior**:
  1. First login → prompted to create a farm. Enter "My Farm", location "Rajshahi".
  2. See farm in list. Total pond count shows 0.
  3. Add 2 ponds → farm card updates to total pond count = 2.
  4. Edit farm name → change reflected immediately.
- **OpenSpec ID**: `FARM-001`
- **Priority**: P0

### 2.2 Pond Configuration (CRUD)
- **Backend**: `GET/POST/PATCH/DELETE /api/v1/ponds`. Fields: `pond_number` (unique per farm), `size` (decimal acres). Multi-species stocking supported (no enforced lifecycle state machine).
- **Frontend**: `/app/ponds` list, `/app/ponds/new` form, `/app/ponds/[id]/edit` form.
- **Manually Verifiable Behavior**:
  1. Go to `/app/ponds` → click "Add Pond".
  2. Enter pond number "Pond 1", size 0.5 acres. Save.
  3. See "Pond 1" in list with size 0.5.
  4. Try adding another "Pond 1" → validation error "Pond number already exists".
  5. Edit pond size to 0.75 → update reflected.
  6. Delete pond → removed from list.
- **OpenSpec ID**: `POND-001`
- **Priority**: P0

---

## 3. Fish Stock & Inventory

### 3.1 Fish Fry / Fingerling Release
- **Backend**: `GET/POST /api/v1/stock/releases`. Fields: `pond_id`, `species`, `release_date`, `quantity_released`, `initial_avg_weight`. Increments stock biomass.
- **Frontend**: `/app/stock/releases` list, `/app/stock/releases/new` form. Filterable by pond, sortable by date.
- **Manually Verifiable Behavior**:
  1. Go to `/app/stock/releases` → click "Record Release".
  2. Select "Pond 1", species "Rui", quantity 5000, avg weight 2g, date today.
  3. Save → see record in list. Pond 1 stock shows biomass = 10kg (5000 × 0.002kg).
  4. Filter list by "Pond 1" → only that pond's releases shown.
- **OpenSpec ID**: `STOCK-001`
- **Priority**: P0

### 3.2 Growth Sampling
- **Backend**: `GET/POST /api/v1/stock/samples`. Fields: `sample_date`, `species`, `sample_size`, `average_weight`, `total_biomass_estimate`. Calculates growth trends.
- **Frontend**: `/app/stock/samples` list + form. Line chart showing weight over time. Days-to-target-weight estimate.
- **Manually Verifiable Behavior**:
  1. Record a sample for Pond 1, Rui, 50 fish, avg weight 50g, date today.
  2. Record another sample 30 days later, avg weight 150g.
  3. See line chart with two points, upward trend.
  4. See "Estimated days to 1kg: ~45 days" based on growth rate.
- **OpenSpec ID**: `STOCK-002`
- **Priority**: P1

### 3.3 Mortality Tracking
- **Backend**: `GET/POST /api/v1/stock/mortality`. Fields: `date`, `pond_id`, `species`, `dead_count`, `estimated_remaining_stock`. Decrements stock. Recorded in `stock_movements`.
- **Frontend**: `/app/stock/mortality` list + form. No alerts/thresholds (MVP).
- **Manually Verifiable Behavior**:
  1. Record 50 dead Rui in Pond 1 today.
  2. Pond 1 stock count decreases by 50. Biomass recalculated.
  3. See mortality record in list with remaining stock auto-calculated.
- **OpenSpec ID**: `STOCK-003`
- **Priority**: P1

### 3.4 Partial Harvest
- **Backend**: `GET/POST /api/v1/stock/harvests`. Fields: `pond_id`, `harvest_date`, `species`, `biomass_harvested_kg`, `avg_weight_at_harvest`, `buyer_type` (wholesale/retail). Decrements stock.
- **Frontend**: `/app/stock/harvests` list + form. May suggest harvest readiness (not enforce).
- **Manually Verifiable Behavior**:
  1. Record partial harvest from Pond 1: 200kg Rui, avg weight 800g, buyer wholesale.
  2. Pond 1 biomass decreases by 200kg.
  3. Harvest appears in list with all details.
- **OpenSpec ID**: `STOCK-004`
- **Priority**: P1

### 3.5 Manual Stock Adjustment
- **Backend**: `POST /api/v1/stock/adjustments`. Fields: `pond_id`, `species`, `adjustment_kg`, `reason` (mandatory), `user_id`. Recorded in `stock_movements`.
- **Frontend**: `/app/stock/adjustments` form. Reason field required.
- **Manually Verifiable Behavior**:
  1. Go to Pond 1 stock page → click "Adjust Stock".
  2. Enter -20kg, reason "Theft". Save.
  3. Stock decreases by 20kg. Adjustment appears in stock movement ledger.
- **OpenSpec ID**: `STOCK-005`
- **Priority**: P2

---

## 4. Sales Management

### 4.1 Record a Sale (Wholesale / Retail)
- **Backend**: `GET/POST /api/v1/sales`. Unified `sales` table with `sale_type` enum. Fields: `date`, `pond_id`, `species`, `quantity_kg`, `rate_per_kg`, `total_amount` (auto), `customer_name`, `custom_tags`, `payment_status`, `amount_paid`, `amount_due` (auto). Auto-generates `sale_code`. Decrements stock.
- **Frontend**: `/app/sales` list, `/app/sales/new` form. Real-time total calculation. Payment status badge.
- **Manually Verifiable Behavior**:
  1. Go to `/app/sales/new`. Select Pond 1, species Rui, quantity 100kg, rate ৳250/kg.
  2. See total auto-calculate to ৳25,000. Enter customer "Ali Bhai".
  3. Save → sale appears in list with code `SALE-1-20260522-001` and status "Pending".
  4. Check Pond 1 stock — biomass decreased by 100kg.
- **OpenSpec ID**: `SALE-001`
- **Priority**: P0

### 4.2 Payment Tracking & Partial Payments
- **Backend**: `POST /api/v1/sales/{id}/payments`. Record partial payments with date and amount. Auto-update `payment_status` (pending → partial → paid).
- **Frontend**: Payment history on sale detail page. "Record Payment" button.
- **Manually Verifiable Behavior**:
  1. Open a ৳25,000 sale with status "Pending".
  2. Record payment of ৳10,000 → status changes to "Partial".
  3. Record payment of ৳15,000 → status changes to "Paid".
  4. See payment history with dates and amounts.
- **OpenSpec ID**: `SALE-002`
- **Priority**: P0

### 4.3 Outstanding Balances per Customer
- **Backend**: `GET /api/v1/customers/{name}/balance` or aggregated endpoint. Sum of `amount_due` across retail sales per customer.
- **Frontend**: `/app/customers` list showing name and outstanding balance.
- **Manually Verifiable Behavior**:
  1. Create 2 retail sales for "Ali Bhai": ৳10,000 due + ৳5,000 due.
  2. Go to `/app/customers` → see "Ali Bhai" with outstanding ৳15,000.
  3. Record a payment of ৳10,000 → balance updates to ৳5,000.
- **OpenSpec ID**: `SALE-003`
- **Priority**: P1

### 4.4 Sales Invoice Generation (PDF)
- **Backend**: `GET /api/v1/sales/{id}/invoice`. Generate HTML template → send to Gotenberg → return PDF. Includes farm name, location, sale details, payment status.
- **Frontend**: "Download Invoice" button on sale detail. Browser opens PDF.
- **Manually Verifiable Behavior**:
  1. Open a completed sale → click "Download Invoice".
  2. PDF opens in new tab with farm header, itemized fish, qty, rate, total, payment status, customer name.
  3. Print dialog works correctly.
- **OpenSpec ID**: `SALE-004`
- **Priority**: P1

### 4.5 Sales Filtering & Search
- **Backend**: `GET /api/v1/sales?type=wholesale&customer=Ali&from_date=...&to_date=...&species=Rui&tag=urgent`.
- **Frontend**: Filter bar on `/app/sales` with dropdowns and date pickers.
- **Manually Verifiable Behavior**:
  1. Have 5 sales: 3 wholesale, 2 retail; 2 for Ali, 3 for others.
  2. Filter by "wholesale" → only 3 shown.
  3. Filter by customer "Ali" → only Ali's sales shown.
  4. Filter by date range → only sales in range shown.
- **OpenSpec ID**: `SALE-005`
- **Priority**: P1

---

## 5. Feed Management

### 5.1 Feed Types / Catalog
- **Backend**: `GET/POST /api/v1/feed/types`. Fields: `name`, `provider`, `price_per_kg`.
- **Frontend**: `/app/feed/types` list + form.
- **Manually Verifiable Behavior**:
  1. Go to `/app/feed/types` → add "Grower Feed" by "CP Feed", price ৳45/kg.
  2. Add "Starter Feed" by "Aqua Feed", price ৳60/kg.
  3. See both in list.
- **OpenSpec ID**: `FEED-001`
- **Priority**: P0

### 5.2 Feed Stock / Warehouse Receipt
- **Backend**: `GET/POST /api/v1/feed/stock`. Fields: `date`, `feed_type_id`, `quantity_kg`, `bag_size_kg`, `number_of_bags`, `total_cost` (auto). Dual input: bag count + size OR total kg.
- **Frontend**: `/app/feed/stock` list + form. Bag count input auto-calculates kg and vice versa.
- **Manually Verifiable Behavior**:
  1. Go to `/app/feed/stock/new`. Select "Grower Feed".
  2. Enter 10 bags × 25kg → system shows 250kg, total cost ৳11,250.
  3. Save → warehouse stock for Grower Feed shows 250kg.
  4. Add another receipt: 5 bags × 25kg → stock shows 375kg.
- **OpenSpec ID**: `FEED-002`
- **Priority**: P0

### 5.3 Daily Feed Consumption per Pond
- **Backend**: `GET/POST /api/v1/feed/consumption`. Fields: `date`, `pond_id`, `feed_type_id`, `quantity_kg`. Decrements central warehouse stock.
- **Frontend**: `/app/feed/consumption` list + form. Pond selector, feed type selector, quantity.
- **Manually Verifiable Behavior**:
  1. Warehouse has 375kg Grower Feed.
  2. Record consumption: Pond 1, Grower Feed, 15kg today.
  3. Warehouse stock drops to 360kg.
  4. Record another 10kg for Pond 2 → warehouse drops to 350kg.
  5. Consumption list shows both entries with pond names.
- **OpenSpec ID**: `FEED-003`
- **Priority**: P0

### 5.4 Feed Ledger (Stock + Consumption Overview)
- **Backend**: Aggregated endpoint `GET /api/v1/feed/ledger`. Per feed type: total received, total consumed, remaining stock, breakdown by pond.
- **Frontend**: `/app/feed/ledger` page. Table or cards per feed type.
- **Manually Verifiable Behavior**:
  1. Have 500kg received, 150kg consumed across 3 ponds.
  2. Go to `/app/feed/ledger` → see Grower Feed card: Received 500kg, Consumed 150kg, Remaining 350kg.
  3. Expand card → see breakdown: Pond 1: 50kg, Pond 2: 60kg, Pond 3: 40kg.
- **OpenSpec ID**: `FEED-004`
- **Priority**: P1

### 5.5 Feed Reorder Alerts
- **Backend**: `PATCH /api/v1/feed/types/{id}/threshold`. When stock < threshold, flag in dashboard API.
- **Frontend**: Dashboard notification badge. Email notification (if configured).
- **Manually Verifiable Behavior**:
  1. Set reorder threshold for Grower Feed to 100kg.
  2. Consume feed until warehouse stock drops to 95kg.
  3. See red alert on dashboard: "Grower Feed stock low (95kg)".
  4. Receive email notification (if SMTP configured).
- **OpenSpec ID**: `FEED-005`
- **Priority**: P2

---

## 6. Medicine & Treatment Management

### 6.1 Medicine Types / Catalog
- **Backend**: `GET/POST /api/v1/medicine/types`. Fields: `name`, `category` (disinfectant, antibiotic, supplement).
- **Frontend**: `/app/medicine/types` list + form.
- **Manually Verifiable Behavior**:
  1. Add "Salt" category "disinfectant", "Oxytetracycline" category "antibiotic".
  2. See both in list with category badges.
- **OpenSpec ID**: `MED-001`
- **Priority**: P1

### 6.2 Medicine Stock / Purchase
- **Backend**: `GET/POST /api/v1/medicine/stock`. Fields: `date`, `medicine_type_id`, `quantity_purchased` (kg or L), `unit_cost`, `total_cost` (auto).
- **Frontend**: `/app/medicine/stock` list + form.
- **Manually Verifiable Behavior**:
  1. Purchase 50kg Salt at ৳20/kg → stock shows 50kg Salt, total cost ৳1,000.
  2. Purchase 10L Disinfectant X at ৳150/L → stock shows 10L.
- **OpenSpec ID**: `MED-002`
- **Priority**: P1

### 6.3 Medicine Usage / Treatment Log per Pond
- **Backend**: `GET/POST /api/v1/medicine/usage`. Fields: `date`, `pond_id`, `medicine_type_id`, `quantity_used`, `unit_cost` (auto-filled), `total_cost` (auto), `notes`. Decrements medicine inventory.
- **Frontend**: `/app/medicine/usage` list + form. Per-pond treatment history view.
- **Manually Verifiable Behavior**:
  1. Apply 5kg Salt to Pond 1 today, notes "Preventive treatment".
  2. Medicine stock drops to 45kg.
  3. Go to Pond 1 detail → see treatment log with Salt application.
  4. Go to `/app/medicine/usage` → see chronological log of all applications.
- **OpenSpec ID**: `MED-003`
- **Priority**: P1

---

## 7. Expense Tracking

### 7.1 General Expense Entry (Cash-Basis)
- **Backend**: `GET/POST/PATCH/DELETE /api/v1/expenses`. Categories: Feed, Medicine, Fuel/Diesel, Electricity, Staff Salary, Miscellaneous. Fields: `date`, `category`, `description`, `amount`, `notes`, `tags`.
- **Frontend**: `/app/expenses` list + form. Category picker, amount input, date picker.
- **Manually Verifiable Behavior**:
  1. Add expense: today, category "Miscellaneous", description "Net repair", amount ৳500.
  2. See in list with category badge and amount.
  3. Edit description → update reflected.
  4. Delete → removed from list.
- **OpenSpec ID**: `EXP-001`
- **Priority**: P0

### 7.2 Fuel Expense (with liters)
- **Backend**: Same as general expense + `purpose` (generator, machinery, aerator, transport), `quantity_liters`.
- **Frontend**: Fuel-specific form fields appear when category = "Fuel / Diesel".
- **Manually Verifiable Behavior**:
  1. Add fuel expense: 20 liters, purpose "generator", amount ৳2,000.
  2. See in list with "20L — Generator" subtitle.
- **OpenSpec ID**: `EXP-002`
- **Priority**: P1

### 7.3 Electricity Bill (with meter + billing period)
- **Backend**: Same as general expense + `meter_number`, `billing_period` (e.g., "May 2026"). Support multiple meters.
- **Frontend**: Electricity-specific form fields when category = "Electricity".
- **Manually Verifiable Behavior**:
  1. Add electricity expense: Meter 4, billing period "May 2026", amount ৳3,500.
  2. See in list with "Meter 4 — May 2026" subtitle.
  3. Add another for Meter 2 → both visible, grouped by meter optional.
- **OpenSpec ID**: `EXP-003`
- **Priority**: P1

### 7.4 Staff Salary (with breakdown)
- **Backend**: Same as general expense + `month` (e.g., "May 2026"), `breakdown` JSON array `{staff_name, amount}`.
- **Frontend**: Salary form with dynamic rows for staff name + amount. Total auto-calculates.
- **Manually Verifiable Behavior**:
  1. Add salary for May 2026: Karim ৳8,000, Rahim ৳7,000. Total auto = ৳15,000.
  2. Save → list shows "May 2026 — ৳15,000". Expand to see Karim and Rahim breakdown.
- **OpenSpec ID**: `EXP-004`
- **Priority**: P1

### 7.5 Daily Income & Expense Ledger
- **Backend**: `GET /api/v1/ledger?date=YYYY-MM-DD`. Returns all income (sales) and expenses for date, running balance, daily balance.
- **Frontend**: `/app/ledger` page. Date picker, table of entries, running balance display.
- **Manually Verifiable Behavior**:
  1. Record a sale of ৳10,000 and an expense of ৳2,000 today.
  2. Go to `/app/ledger` → select today.
  3. See income ৳10,000, expense ৳2,000, daily balance ৳8,000.
  4. Running balance shows cumulative from first record.
  5. Change date to yesterday (no entries) → shows "No entries for this date".
- **OpenSpec ID**: `EXP-005`
- **Priority**: P0

---

## 8. Partner / Shareholder Ledger

### 8.1 Partner Management (Investment / Withdrawal)
- **Backend**: `GET/POST /api/v1/partners`. Fields: `partner_name`, `date`, `transaction_type` (investment/withdrawal), `amount`, `running_balance` (auto). Affects main cash balance.
- **Frontend**: `/app/partners` list + form. Running balance per partner displayed.
- **Manually Verifiable Behavior**:
  1. Add partner "Karim" with investment ৳100,000 on Jan 1.
  2. See running balance ৳100,000. Cash balance increases by ৳100,000.
  3. Record withdrawal by Karim of ৳20,000 on Feb 1.
  4. Running balance updates to ৳80,000. Cash balance decreases by ৳20,000.
- **OpenSpec ID**: `PARTNER-001`
- **Priority**: P1

### 8.2 Profit Distribution per Harvest Cycle
- **Backend**: `GET/POST /api/v1/profit-cycles`. Calculate: total revenue, total expenses, net profit, profit share per partner (proportional to share ownership).
- **Frontend**: `/app/profit-cycles` list + detail. Shows revenue, expenses, net profit, per-partner share.
- **Manually Verifiable Behavior**:
  1. Define a cycle: "Cycle 1" (Jan 1 – Jun 30).
  2. During cycle: sales ৳500,000, expenses ৳300,000.
  3. Close cycle → net profit = ৳200,000.
  4. Partners: Karim 60%, Rahim 40%.
  5. See profit distribution: Karim ৳120,000, Rahim ৳80,000.
- **OpenSpec ID**: `PARTNER-002`
- **Priority**: P2

---

## 9. Dashboard & Reporting

### 9.1 Main Dashboard (Financial Overview)
- **Backend**: `GET /api/v1/dashboard`. Aggregated: total income, total expenses, running balance, cost per kg produced, feed stock levels with alerts, outstanding retail payments.
- **Frontend**: `/app/dashboard`. Summary cards (income, expenses, balance, stock), quick action buttons, activity feed, charts.
- **Manually Verifiable Behavior**:
  1. After adding sales and expenses, visit `/app/dashboard`.
  2. See income card showing total sales amount.
  3. See expenses card showing total expenses.
  4. See balance card = income − expenses.
  5. See feed stock card with alert indicator if low.
  6. See outstanding payments card with total due.
- **OpenSpec ID**: `DASH-001`
- **Priority**: P0

### 9.2 Expense Breakdown Pie Chart
- **Backend**: Same dashboard endpoint or `GET /api/v1/reports/expense-breakdown`.
- **Frontend**: LayerChart Pie chart on dashboard. Click slice → drill down to category expenses.
- **Manually Verifiable Behavior**:
  1. Have expenses: Feed ৳50k, Medicine ৳10k, Fuel ৳20k.
  2. See pie chart with 3 slices proportional to amounts.
  3. Click "Feed" slice → navigate to expense list filtered by Feed category.
- **OpenSpec ID**: `DASH-002`
- **Priority**: P1

### 9.3 Growth & Feeding Trends Line Chart
- **Backend**: `GET /api/v1/reports/growth-trends` or from samples/consumption data.
- **Frontend**: LayerChart Line chart on dashboard. X = date, Y = avg weight or feed consumed.
- **Manually Verifiable Behavior**:
  1. Have 3 growth samples over 3 months.
  2. See line chart with upward trend.
  3. Toggle to "Feed" view → line chart shows daily feed consumption over time.
- **OpenSpec ID**: `DASH-003`
- **Priority**: P1

### 9.4 Sales Comparison Bar Chart
- **Backend**: `GET /api/v1/reports/sales-comparison`. Monthly aggregation: total quantity, avg price per kg.
- **Frontend**: LayerChart Bar chart on dashboard. X = month, Y = quantity or price.
- **Manually Verifiable Behavior**:
  1. Have sales in May (200kg, ৳250/kg) and June (300kg, ৳260/kg).
  2. See bar chart with two bars, heights 200 and 300.
  3. Toggle to "Price per kg" → bars show 250 and 260.
- **OpenSpec ID**: `DASH-004`
- **Priority**: P1

### 9.5 Operational Reports (Daily / Weekly)
- **Backend**: `GET /api/v1/reports/daily` and `GET /api/v1/reports/weekly`. Feed per pond, medicine usage, fuel, expenses.
- **Frontend**: `/app/reports` page. Date range picker, report type selector, table output.
- **Manually Verifiable Behavior**:
  1. Select "Daily Report", date today.
  2. See table: feed given per pond, medicine used, fuel consumed, expenses incurred.
  3. Select "Weekly Report", date range → aggregated totals for the week.
- **OpenSpec ID**: `DASH-005`
- **Priority**: P1

### 9.6 Export (CSV / XLSX)
- **Backend**: `GET /api/v1/exports/{format}?type=sales|expenses|...&from_date=...&to_date=...`. Queue async generation, return download URL.
- **Frontend**: "Export" button on list/report pages. Format picker (CSV / Excel).
- **Manually Verifiable Behavior**:
  1. Go to `/app/sales`, apply date filter.
  2. Click "Export" → select "Excel".
  3. File downloads with all filtered sales data, headers in selected language.
  4. Open file → data matches what was on screen.
- **OpenSpec ID**: `DASH-006`
- **Priority**: P2

---

## 10. Offline Sync & Data Architecture

### 10.1 Offline Data Entry (PWA)
- **Backend**: `POST /api/v1/sync` (batch accept), `GET /api/v1/sync/status?since=...` (incremental fetch).
- **Frontend**: Service worker + PGlite local DB. Offline banner when disconnected. Queue changes locally.
- **Manually Verifiable Behavior**:
  1. Open app in Chrome, go to `/app/feed/consumption`.
  2. Turn off Wi-Fi → see yellow offline banner.
  3. Add a feed consumption record → saved locally, shows "Sync pending" badge.
  4. Turn Wi-Fi back on → badge changes to "Synced", record appears on server.
  5. Refresh page → record still there.
- **OpenSpec ID**: `SYNC-001`
- **Priority**: P1

### 10.2 Conflict Resolution (Last-Write-Wins)
- **Backend**: Sync endpoint compares timestamps. Rejects older changes, returns conflict details.
- **Frontend**: Show conflict toast with options to keep local or server version.
- **Manually Verifiable Behavior**:
  1. Two devices offline. Both edit the same pond's size.
  2. Device A goes online first, syncs size = 0.6.
  3. Device B goes online, tries to sync size = 0.7.
  4. Device B sees conflict: "Server has 0.6 (10:05 AM), you have 0.7 (10:03 AM). Keep yours?"
  5. Choose "Keep mine" → server updates to 0.7. Conflict logged.
- **OpenSpec ID**: `SYNC-002`
- **Priority**: P2

---

## 11. Localization & UI Polish

### 11.1 Language Toggle (English / Bangla)
- **Backend**: `PATCH /api/v1/users/locale`. Persist per user.
- **Frontend**: Language toggle in nav/header. All labels, messages, charts translated.
- **Manually Verifiable Behavior**:
  1. Click "বাংলা" toggle in header.
  2. All UI text switches to Bangla.
  3. Numbers switch to Bengali numerals (০-৯).
  4. Currency shows `৳ ১,২৫০.০০`.
  5. Log out, log back in → language preference persisted.
- **OpenSpec ID**: `UI-001`
- **Priority**: P1

### 11.2 Responsive Layout (Mobile-First)
- **Backend**: N/A.
- **Frontend**: Tailwind responsive classes. Mobile: hamburger nav, single column, full-width cards. Desktop: top nav, multi-column grids.
- **Manually Verifiable Behavior**:
  1. Open dashboard in Chrome DevTools mobile view (375px width).
  2. See hamburger menu, stacked cards, large touch targets (≥56px).
  3. Switch to desktop view (1280px).
  4. See top navigation bar, 3-4 column card grid, side-by-side layouts.
- **OpenSpec ID**: `UI-002`
- **Priority**: P1

---

## Summary Table

| # | Feature | OpenSpec ID | Priority | Backend Complexity | Frontend Complexity |
|---|---------|-------------|----------|-------------------|---------------------|
| 1 | Email/Password Auth | `AUTH-001` | P0 | Medium | Medium |
| 2 | Social Login | `AUTH-002` | P1 | Medium | Low |
| 3 | RBAC & Audit Logs | `AUTH-003` | P0 | Medium | Medium |
| 4 | Multi-Farm Tenancy | `AUTH-004` | P0 | Medium | Medium |
| 5 | Farm CRUD | `FARM-001` | P0 | Low | Low |
| 6 | Pond CRUD | `POND-001` | P0 | Low | Low |
| 7 | Fry Release | `STOCK-001` | P0 | Medium | Medium |
| 8 | Growth Sampling | `STOCK-002` | P1 | Medium | High |
| 9 | Mortality Tracking | `STOCK-003` | P1 | Low | Low |
| 10 | Partial Harvest | `STOCK-004` | P1 | Medium | Medium |
| 11 | Manual Stock Adjustment | `STOCK-005` | P2 | Low | Low |
| 12 | Record Sale | `SALE-001` | P0 | Medium | Medium |
| 13 | Payment Tracking | `SALE-002` | P0 | Medium | Medium |
| 14 | Customer Balances | `SALE-003` | P1 | Low | Low |
| 15 | Invoice PDF | `SALE-004` | P1 | Medium | Low |
| 16 | Sales Filtering | `SALE-005` | P1 | Low | Medium |
| 17 | Feed Types | `FEED-001` | P0 | Low | Low |
| 18 | Feed Stock Receipt | `FEED-002` | P0 | Medium | Medium |
| 19 | Feed Consumption | `FEED-003` | P0 | Medium | Medium |
| 20 | Feed Ledger | `FEED-004` | P1 | Medium | Medium |
| 21 | Feed Reorder Alerts | `FEED-005` | P2 | Low | Low |
| 22 | Medicine Types | `MED-001` | P1 | Low | Low |
| 23 | Medicine Stock | `MED-002` | P1 | Low | Low |
| 24 | Medicine Usage | `MED-003` | P1 | Medium | Medium |
| 25 | General Expenses | `EXP-001` | P0 | Low | Low |
| 26 | Fuel Expenses | `EXP-002` | P1 | Low | Low |
| 27 | Electricity Bills | `EXP-003` | P1 | Low | Low |
| 28 | Staff Salaries | `EXP-004` | P1 | Low | Medium |
| 29 | Daily Ledger | `EXP-005` | P0 | Medium | Medium |
| 30 | Partner Ledger | `PARTNER-001` | P1 | Medium | Medium |
| 31 | Profit Cycles | `PARTNER-002` | P2 | Medium | High |
| 32 | Main Dashboard | `DASH-001` | P0 | High | High |
| 33 | Expense Pie Chart | `DASH-002` | P1 | Low | Medium |
| 34 | Growth Line Chart | `DASH-003` | P1 | Low | Medium |
| 35 | Sales Bar Chart | `DASH-004` | P1 | Low | Medium |
| 36 | Operational Reports | `DASH-005` | P1 | Medium | Medium |
| 37 | CSV/XLSX Export | `DASH-006` | P2 | Medium | Low |
| 38 | Offline Sync | `SYNC-001` | P1 | High | High |
| 39 | Conflict Resolution | `SYNC-002` | P2 | Medium | Medium |
| 40 | Language Toggle | `UI-001` | P1 | Low | Medium |
| 41 | Responsive Layout | `UI-002` | P1 | N/A | Medium |

---

## Recommended Implementation Order (Sprints)

### Sprint 1 — Foundation
- `AUTH-001` Email/Password Auth
- `AUTH-004` Multi-Farm Tenancy
- `FARM-001` Farm CRUD
- `POND-001` Pond CRUD

### Sprint 2 — Core Operations
- `STOCK-001` Fry Release
- `SALE-001` Record Sale
- `SALE-002` Payment Tracking
- `EXP-001` General Expenses
- `EXP-005` Daily Ledger

### Sprint 3 — Inventory & Operations
- `FEED-001` Feed Types
- `FEED-002` Feed Stock
- `FEED-003` Feed Consumption
- `MED-001` Medicine Types
- `MED-002` Medicine Stock
- `MED-003` Medicine Usage

### Sprint 4 — Dashboard & Reporting
- `DASH-001` Main Dashboard
- `DASH-002` Expense Pie Chart
- `DASH-003` Growth Line Chart
- `DASH-004` Sales Bar Chart
- `DASH-005` Operational Reports

### Sprint 5 — Advanced Features
- `AUTH-002` Social Login
- `AUTH-003` RBAC & Audit Logs
- `SALE-003` Customer Balances
- `SALE-004` Invoice PDF
- `SALE-005` Sales Filtering
- `FEED-004` Feed Ledger
- `FEED-005` Feed Reorder Alerts

### Sprint 6 — Partners, Sync & Polish
- `PARTNER-001` Partner Ledger
- `PARTNER-002` Profit Cycles
- `SYNC-001` Offline Sync
- `SYNC-002` Conflict Resolution
- `UI-001` Language Toggle
- `UI-002` Responsive Layout
- `DASH-006` CSV/XLSX Export
- `STOCK-002` Growth Sampling
- `STOCK-003` Mortality Tracking
- `STOCK-004` Partial Harvest
- `STOCK-005` Manual Stock Adjustment
- `EXP-002` Fuel Expenses
- `EXP-003` Electricity Bills
- `EXP-004` Staff Salaries

---

> **Note**: Each feature above should be implemented via the OpenSpec workflow:
> 1. `/opsx propose` — generate spec + design + tasks
> 2. `/opsx explore` — think through edge cases
> 3. `/opsx apply` — implement backend + frontend + tests
> 4. `/opsx archive` — finalize and archive
