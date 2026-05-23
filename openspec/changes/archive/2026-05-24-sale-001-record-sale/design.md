## Context

The Deshi Fishery platform currently supports farm creation, pond management, and stock releases. The next critical feature is recording sales of fish from ponds. This is a daily operational activity for fish farmers — they sell fish to wholesale buyers (beparis) or retail customers. Each sale must decrement pond stock, track payment status, and generate a unique sale code for reference.

This feature builds on the existing `ponds` table (which has `current_stock_quantity` and `current_stock_weight_kg` fields) and the multi-tenancy system (`farm_id` scoping via `X-Farm-ID`).

## Goals / Non-Goals

**Goals:**
- Allow users to record wholesale and retail sales with fish type, average fish weight, quantity, rate, and customer details.
- Auto-calculate total amount (`quantity_kg × rate_per_kg`).
- Auto-generate unique sale codes (`SALE-{farm_id}-{YYYYMMDD}-{sequence}`).
- Track payment status (`pending`, `partial`, `paid`) with `amount_paid` and `amount_due`.
- Decrement pond stock (`current_stock_weight_kg`) when a sale is recorded.
- Prevent sales that would result in negative stock (`InsufficientStock` error).
- Provide list, create, view, and delete operations for sales.
- Support filtering sales by date range, sale type, and payment status.

**Non-Goals:**
- Partial payments (covered by `SALE-002`).
- Customer balance tracking (covered by `SALE-003`).
- Invoice PDF generation (covered by `SALE-004`).
- Sales filtering by customer name or species (covered by `SALE-005`).
- Complex pricing rules (discounts, tiered pricing).
- Integration with accounting software.

## Decisions

1. **Unified sales table with `sale_type` enum**
   - *Decision*: Use a single `sales` table with `sale_type` enum (`wholesale`, `retail`) instead of separate tables.
   - *Rationale*: Both sale types share the same fields. A single table simplifies queries, indexing, and reporting. The `sale_type` field allows filtering and differentiation.
   - *Alternative considered*: Separate `wholesale_sales` and `retail_sales` tables — rejected due to duplication and harder aggregation.

2. **Auto-generated sale code**
   - *Decision*: Generate sale codes in the format `SALE-{farm_id}-{YYYYMMDD}-{sequence}` where sequence is a 3-digit zero-padded number per farm per day.
   - *Rationale*: Farmers need human-readable reference numbers. Including farm ID and date makes codes unique and sortable. Per-day sequence keeps codes short.
   - *Alternative considered*: UUIDs — rejected as not human-friendly for rural users.

3. **Stock decrement on sale creation only**
   - *Decision*: Decrement pond stock when a sale is created. Do not restore stock on sale deletion (manual adjustment or `STOCK-005` can handle corrections).
   - *Rationale*: Sales are financial transactions. Deleting a sale should not silently restore stock — that could mask errors. A separate stock adjustment feature provides auditability.
   - *Alternative considered*: Restore stock on delete — rejected because it breaks financial traceability.

4. **Payment status as computed field**
   - *Decision*: Store `amount_paid` and compute `payment_status` on-the-fly (or via model accessor) rather than storing the status as a column.
   - *Rationale*: Prevents data inconsistency where `amount_paid` and `payment_status` could disagree. However, for query performance (filtering by status), we will store `payment_status` as a generated/stored column updated by model events.
   - *Alternative considered*: Virtual column only — rejected because filtering by payment status is a common operation.

5. **Decimal precision for monetary values**
   - *Decision*: Use `decimal(12, 2)` for `rate_per_kg`, `total_amount`, `amount_paid`, `amount_due`.
   - *Rationale*: BDT uses 2 decimal places. `decimal` avoids floating-point errors in financial calculations.

6. **Fish type as string (not foreign key)**
   - *Decision*: Store fish type as a string column named `fish_type` (same pattern as `stock_releases` which uses `species`).
   - *Rationale*: MVP simplicity. No separate fish type catalog needed yet. The column name `fish_type` is clearer than `species` for the Bangladeshi context. Can normalize later if required.

## Risks / Trade-offs

- **[Risk]** Sale deletion does not restore stock → farmers may forget to manually adjust.
  - *Mitigation*: Show a warning on delete: "This will not restore pond stock. Use Stock Adjustment if needed."
- **[Risk]** Concurrent sales from the same pond could oversell stock (race condition).
  - *Mitigation*: Use database-level pessimistic locking (`SELECT FOR UPDATE`) on the pond row during sale creation, or validate stock again inside a transaction.
- **[Risk]** Sale code sequence gaps if a transaction rolls back.
  - *Mitigation*: Acceptable for MVP. Sequence numbers may have gaps but remain unique.
- **[Risk]** Average fish weight is self-reported and may be inaccurate.
  - *Mitigation*: Store as `avg_fish_weight_g` (grams) for precision. Make it a required field so users must provide an estimate.
- **[Trade-off]** Storing `payment_status` as a column requires keeping it in sync with `amount_paid`.
  - *Mitigation*: Update `payment_status` in model `saving` event. Write tests to verify sync.

## Migration Plan

1. Run migration to create `sales` table.
2. Seed test data if in local environment.
3. No rollback needed for MVP — new table, no data migration.

## Open Questions

- Should we allow sales with `quantity_kg` exceeding stock if the user explicitly confirms? (Decision: No for MVP — reject with error.)
- Should `amount_paid` default to 0 or `total_amount`? (Decision: Default to 0 — pending payment.)
- Should `avg_fish_weight_g` default to a calculated value from pond stock? (Decision: No — user must input. Pond stock may have mixed sizes.)
