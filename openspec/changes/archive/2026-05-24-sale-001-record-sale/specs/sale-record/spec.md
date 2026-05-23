## ADDED Requirements

### Requirement: User can record a sale
The system SHALL allow authenticated users to record a sale of fish from a pond.

#### Scenario: Successful wholesale sale creation
- **WHEN** an authenticated user submits a sale with `pond_id`, `sale_type=wholesale`, `fish_type`, `avg_fish_weight_g`, `quantity_kg`, `rate_per_kg`, `customer_name`, and `date`
- **THEN** the system creates a sale record with auto-calculated `total_amount = quantity_kg × rate_per_kg`
- **AND** assigns a unique sale code in format `SALE-{farm_id}-{YYYYMMDD}-{sequence}`
- **AND** sets `payment_status` to `pending` and `amount_paid` to 0
- **AND** decrements the pond's `current_stock_weight_kg` by `quantity_kg`
- **AND** returns the sale record with a `201 Created` status

#### Scenario: Successful retail sale creation
- **WHEN** an authenticated user submits a sale with `sale_type=retail` and all required fields
- **THEN** the system creates a sale record with `sale_type=retail`
- **AND** applies the same auto-calculation, code generation, and stock decrement as wholesale sales

#### Scenario: Sale creation rejected due to insufficient stock
- **WHEN** a user submits a sale where `quantity_kg` exceeds the pond's `current_stock_weight_kg`
- **THEN** the system rejects the request with a `422 Unprocessable Entity` status
- **AND** returns an error with code `InsufficientStock` and message indicating available stock

#### Scenario: Sale creation rejected for invalid pond
- **WHEN** a user submits a sale with a `pond_id` that does not belong to the current farm
- **THEN** the system rejects the request with a `404 Not Found` status

### Requirement: System auto-generates sale codes
The system SHALL generate a unique, human-readable sale code for every sale.

#### Scenario: Sale code format
- **WHEN** a sale is created for farm ID 3 on 2026-05-23 and it is the first sale of that day
- **THEN** the sale code SHALL be `SALE-3-20260523-001`

#### Scenario: Sale code increments per day
- **WHEN** a second sale is created for the same farm on the same day
- **THEN** the sale code SHALL be `SALE-3-20260523-002`

#### Scenario: Sale code resets daily
- **WHEN** a sale is created for the same farm on the next day
- **THEN** the sequence resets and the code SHALL be `SALE-3-20260524-001`

### Requirement: System tracks payment status
The system SHALL automatically determine and store the payment status based on `amount_paid` relative to `total_amount`.

#### Scenario: Payment status pending
- **WHEN** a sale is created with `amount_paid = 0`
- **THEN** `payment_status` SHALL be `pending`

#### Scenario: Payment status partial
- **WHEN** a sale has `amount_paid` greater than 0 but less than `total_amount`
- **THEN** `payment_status` SHALL be `partial`

#### Scenario: Payment status paid
- **WHEN** a sale has `amount_paid` equal to or greater than `total_amount`
- **THEN** `payment_status` SHALL be `paid`

### Requirement: User can list sales
The system SHALL allow authenticated users to view a paginated list of sales for the current farm.

#### Scenario: List all sales
- **WHEN** an authenticated user requests the sales list
- **THEN** the system returns only sales belonging to the current farm
- **AND** results are paginated with cursor-based pagination

#### Scenario: Filter sales by type
- **WHEN** a user requests sales with `?sale_type=wholesale`
- **THEN** only wholesale sales are returned

#### Scenario: Filter sales by payment status
- **WHEN** a user requests sales with `?payment_status=pending`
- **THEN** only pending sales are returned

#### Scenario: Filter sales by date range
- **WHEN** a user requests sales with `?from_date=2026-05-01&to_date=2026-05-31`
- **THEN** only sales within the date range are returned

### Requirement: User can view sale details
The system SHALL allow authenticated users to view the details of a specific sale.

#### Scenario: View sale details
- **WHEN** an authenticated user requests a sale by ID
- **THEN** the system returns the full sale record including pond name, fish type, average fish weight, quantities, and payment status

#### Scenario: View non-existent sale
- **WHEN** a user requests a sale that does not exist or belongs to another farm
- **THEN** the system returns a `404 Not Found` status

### Requirement: User can delete a sale
The system SHALL allow authorized users (Owner, Manager) to delete a sale record.

#### Scenario: Delete sale
- **WHEN** an authorized user deletes a sale
- **THEN** the sale record is removed
- **AND** the system returns a `204 No Content` status
- **AND** pond stock is NOT restored (user must use stock adjustment if needed)

#### Scenario: Worker cannot delete sale
- **WHEN** a user with Worker role attempts to delete a sale
- **THEN** the system returns a `403 Forbidden` status

### Requirement: System validates sale data
The system SHALL enforce validation rules on all sale creation and update requests.

#### Scenario: Missing required fields
- **WHEN** a user submits a sale without `pond_id`, `fish_type`, `quantity_kg`, `rate_per_kg`, or `avg_fish_weight_g`
- **THEN** the system returns a `422 Unprocessable Entity` with field-specific validation errors

#### Scenario: Missing average fish weight
- **WHEN** a user submits a sale without `avg_fish_weight_g`
- **THEN** the system rejects the request with a validation error indicating the field is required

#### Scenario: Negative quantity, rate, or weight
- **WHEN** a user submits a sale with negative `quantity_kg`, `rate_per_kg`, or `avg_fish_weight_g`
- **THEN** the system rejects the request with a validation error

#### Scenario: Zero quantity
- **WHEN** a user submits a sale with `quantity_kg = 0`
- **THEN** the system rejects the request with a validation error

#### Scenario: Notes field accepts free text
- **WHEN** a user submits a sale with `notes` containing up to 1000 characters
- **THEN** the system accepts and stores the notes
- **AND** the notes are returned when viewing the sale details

### Requirement: System scopes all sales by farm
The system SHALL ensure that sales data is strictly isolated per farm.

#### Scenario: Cross-farm isolation
- **WHEN** a user belonging to Farm A attempts to access a sale from Farm B
- **THEN** the system returns a `404 Not Found` status
- **AND** the sale data is not exposed

#### Scenario: List isolation
- **WHEN** a user requests the sales list
- **THEN** only sales for the user's current farm are returned
