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
