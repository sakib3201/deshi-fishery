## ADDED Requirements

### Requirement: Stock Release List
The system SHALL allow authenticated farm members to list all stock releases in the current farm.

#### Scenario: List stock releases for current farm
- **WHEN** an authenticated user sends a GET request to `/api/v1/stock-releases` with a valid `X-Farm-ID` header
- **THEN** the system returns a 200 OK response with an array of stock releases
- **AND** each release includes `id`, `pond_id`, `species`, `quantity`, `avg_weight_gram`, `cost_bdt`, `release_date`, and `created_at`

#### Scenario: List stock releases filtered by pond
- **WHEN** an authenticated user sends a GET request to `/api/v1/stock-releases?pond_id=1`
- **THEN** the system returns only stock releases for that pond

#### Scenario: List stock releases when farm has none
- **WHEN** an authenticated user sends a GET request to `/api/v1/stock-releases` for a farm with no stock releases
- **THEN** the system returns a 200 OK response with an empty array

### Requirement: Stock Release Creation
The system SHALL allow farm owners and managers to record a new fish fry release into a pond.

#### Scenario: Create stock release with valid data
- **WHEN** a farm owner sends a POST request to `/api/v1/stock-releases` with `pond_id`, `species`, `quantity`, `avg_weight_gram`, `cost_bdt`, and `release_date`
- **THEN** the system creates a new stock release record scoped to the current farm
- **AND** increments the pond's `current_stock_quantity` by `quantity`
- **AND** increments the pond's `current_stock_weight_kg` by `(quantity * avg_weight_gram / 1000)`
- **AND** returns a 201 Created response with the stock release object

#### Scenario: Create stock release with invalid quantity
- **WHEN** a farm owner sends a POST request to `/api/v1/stock-releases` with `quantity` of 0
- **THEN** the system returns a 422 Unprocessable Entity response with validation errors
- **AND** no stock release is created
- **AND** the pond stock is unchanged

#### Scenario: Create stock release with future date
- **WHEN** a farm owner sends a POST request to `/api/v1/stock-releases` with `release_date` in the future
- **THEN** the system returns a 422 Unprocessable Entity response with validation errors
- **AND** no stock release is created

#### Scenario: Create stock release for pond in different farm
- **WHEN** a farm owner sends a POST request to `/api/v1/stock-releases` with a `pond_id` that belongs to a different farm
- **THEN** the system returns a 404 Not Found response
- **AND** no stock release is created

#### Scenario: Create stock release as worker
- **WHEN** a farm worker sends a POST request to `/api/v1/stock-releases`
- **THEN** the system returns a 403 Forbidden response
- **AND** no stock release is created

### Requirement: Stock Release Retrieval
The system SHALL allow authenticated farm members to view a specific stock release in the current farm.

#### Scenario: Get stock release by ID
- **WHEN** an authenticated user sends a GET request to `/api/v1/stock-releases/{id}`
- **AND** the stock release belongs to the current farm
- **THEN** the system returns a 200 OK response with the stock release object

#### Scenario: Get stock release from different farm
- **WHEN** an authenticated user sends a GET request to `/api/v1/stock-releases/{id}` for a release that belongs to a different farm
- **THEN** the system returns a 404 Not Found response

### Requirement: Stock Release Update
The system SHALL allow farm owners and managers to update stock release details.

#### Scenario: Update stock release quantity
- **WHEN** a farm owner sends a PATCH request to `/api/v1/stock-releases/{id}` with a new `quantity`
- **THEN** the system updates the stock release record
- **AND** recalculates the pond's `current_stock_quantity` by subtracting the old quantity and adding the new quantity
- **AND** recalculates the pond's `current_stock_weight_kg` accordingly
- **AND** returns a 200 OK response with the updated stock release object

#### Scenario: Update stock release as worker
- **WHEN** a farm worker sends a PATCH request to `/api/v1/stock-releases/{id}`
- **THEN** the system returns a 403 Forbidden response
- **AND** the stock release is not updated

### Requirement: Stock Release Deletion
The system SHALL allow farm owners and managers to delete stock releases.

#### Scenario: Delete stock release as owner
- **WHEN** a farm owner sends a DELETE request to `/api/v1/stock-releases/{id}`
- **AND** deleting it would not make the pond's `current_stock_quantity` negative
- **THEN** the system deletes the stock release record
- **AND** decrements the pond's `current_stock_quantity` and `current_stock_weight_kg` by the release's values
- **AND** returns a 204 No Content response

#### Scenario: Delete stock release that would make stock negative
- **WHEN** a farm owner sends a DELETE request to `/api/v1/stock-releases/{id}` that would make the pond's `current_stock_quantity` negative
- **THEN** the system returns a 422 Unprocessable Entity response with error code `WouldMakeStockNegative`
- **AND** the stock release is not deleted

#### Scenario: Delete stock release as worker
- **WHEN** a farm worker sends a DELETE request to `/api/v1/stock-releases/{id}`
- **THEN** the system returns a 403 Forbidden response
- **AND** the stock release is not deleted

## MODIFIED Requirements

### Requirement: Pond List
The system SHALL allow authenticated farm members to list all ponds in the current farm.

#### Scenario: List ponds for current farm
- **WHEN** an authenticated user sends a GET request to `/api/v1/ponds` with a valid `X-Farm-ID` header
- **THEN** the system returns a 200 OK response with an array of ponds
- **AND** each pond includes `id`, `pond_number`, `size`, `current_stock_quantity`, `current_stock_weight_kg`, and `created_at`

### Requirement: Pond Retrieval
The system SHALL allow authenticated farm members to view a specific pond in the current farm.

#### Scenario: Get pond by ID
- **WHEN** an authenticated user sends a GET request to `/api/v1/ponds/{id}`
- **AND** the pond belongs to the current farm
- **THEN** the system returns a 200 OK response with the pond object
- **AND** the pond includes `current_stock_quantity` and `current_stock_weight_kg`
