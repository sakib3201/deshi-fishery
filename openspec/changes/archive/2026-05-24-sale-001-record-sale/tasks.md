## 1. Database & Migrations

- [x] 1.1 Create migration for `sales` table with all fields (`farm_id`, `pond_id`, `sale_code`, `sale_type`, `date`, `fish_type`, `avg_fish_weight_g`, `quantity_kg`, `rate_per_kg`, `total_amount`, `customer_name`, `custom_tags`, `payment_status`, `amount_paid`, `amount_due`, `notes`, `created_by`, `timestamps`)
- [x] 1.2 Add indexes on `farm_id`, `pond_id`, `date`, `payment_status`, `sale_type`
- [x] 1.3 Run migration and verify table structure

## 2. Backend Models & Relationships

- [x] 2.1 Create `Sale` model with `$fillable`, `$casts`, and relationships (`Farm`, `Pond`, `User`)
- [x] 2.2 Add `sales()` relationship to `Farm` model
- [x] 2.3 Add `sales()` relationship to `Pond` model
- [x] 2.4 Add `payment_status` accessor/mutator logic in `Sale` model
- [x] 2.5 Implement `booted()` method in `Sale` model to auto-update `payment_status` on save

## 3. Backend Validation & Policies

- [x] 3.1 Create `StoreSaleRequest` form request with validation rules (required fields, numeric positive, pond belongs to farm)
- [x] 3.2 Create `UpdateSaleRequest` form request with validation rules
- [x] 3.3 Create `SalePolicy` with `viewAny`, `view`, `create`, `update`, `delete` methods (farm membership + role checks)
- [x] 3.4 Register `SalePolicy` in `AuthServiceProvider`

## 4. Backend Controller & Routes

- [x] 4.1 Create `SaleController` with `index`, `store`, `show`, `update`, `destroy` methods
- [x] 4.2 Implement `index` with filtering by `sale_type`, `payment_status`, `from_date`, `to_date`
- [x] 4.3 Implement `store` with auto-calculation of `total_amount`, `amount_due`, sale code generation, and pond stock decrement
- [x] 4.4 Implement `show` with farm-scoped lookup
- [x] 4.5 Implement `update` with validation and payment status recalculation
- [x] 4.6 Implement `destroy` with authorization check
- [x] 4.7 Add routes in `routes/api.php` under `/api/v1/sales` with auth middleware
- [x] 4.8 Add role middleware (`role:owner,manager` for delete; `role:owner,manager,worker` for create/view)

## 5. Backend Tests

- [x] 5.1 Create `SaleControllerTest` with `setUp` creating user, farm, pond
- [x] 5.2 Test successful wholesale sale creation with stock decrement
- [x] 5.3 Test successful retail sale creation
- [x] 5.4 Test sale code generation format and sequence
- [x] 5.5 Test payment status auto-calculation (pending, partial, paid)
- [x] 5.6 Test `InsufficientStock` error when quantity exceeds pond stock
- [x] 5.7 Test validation errors for missing/invalid fields (including `avg_fish_weight_g`)
- [x] 5.8 test cross-farm isolation (404 for other farm's sale)
- [x] 5.9 Test filtering by `sale_type`, `payment_status`, date range
- [x] 5.10 Test Worker cannot delete sale (403)
- [x] 5.11 Test sale deletion does not restore stock
- [x] 5.12 Run full PHPUnit suite and ensure all tests pass

## 6. Frontend API Client

- [x] 6.1 Add `sales` object to API client with `list`, `create`, `get`, `update`, `delete` methods
- [x] 6.2 Support query params for filtering (`sale_type`, `payment_status`, `from_date`, `to_date`)
- [x] 6.3 Handle `InsufficientStock` error code explicitly

## 7. Frontend Pages

- [x] 7.1 Create `/app/sales` list page with table showing sale code, date, customer, fish type, avg weight, quantity, total, payment status badge
- [x] 7.2 Add filter controls for sale type and payment status
- [x] 7.3 Create `/app/sales/new` form page with pond selector, fish type input, avg fish weight (g), quantity, rate, customer name, notes textarea, sale type toggle, date picker
- [x] 7.4 Implement real-time total calculation (`quantity_kg × rate_per_kg`)
- [x] 7.5 Create `/app/sales/[id]` detail page showing all sale fields and payment status
- [x] 7.6 Add delete button with confirmation dialog (detail page)
- [x] 7.7 Add Bengali numeral formatting for quantities and amounts

## 8. Frontend Navigation & Integration

- [x] 8.1 Add "Sales" link to `AppNavbar` navigation
- [x] 8.2 Ensure active state highlighting for sales routes
- [x] 8.3 Add "Record Sale" button on sales list page

## 9. Frontend E2E Tests

- [x] 9.1 Create `sale.e2e.ts` with test for navigating to sales page
- [x] 9.2 Test creating a wholesale sale and verifying it appears in list
- [x] 9.3 Test real-time total calculation in create form
- [x] 9.4 Test viewing sale detail page
- [x] 9.5 Test deleting a sale
- [x] 9.6 Run all Playwright tests and ensure they pass

## 10. Documentation & Cleanup

- [x] 10.1 Add Bruno API collection for sales endpoints (`List`, `Create`, `Get`, `Update`, `Delete`)
- [x] 10.2 Run `vendor/bin/pint` on backend files
- [x] 10.3 Run Prettier on frontend files
- [x] 10.4 Update `docs/sprint-progress.md` — mark SALE-001 as ✅ Complete
