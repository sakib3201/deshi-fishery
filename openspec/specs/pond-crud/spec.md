## ADDED Requirements

### Requirement: Pond List
The system SHALL allow authenticated farm members to list all ponds in the current farm.

#### Scenario: List ponds for current farm
- **WHEN** an authenticated user sends a GET request to `/api/v1/ponds` with a valid `X-Farm-ID` header
- **THEN** the system returns a 200 OK response with an array of ponds
- **AND** each pond includes `id`, `pond_number`, `size`, and `created_at`

#### Scenario: List ponds when farm has no ponds
- **WHEN** an authenticated user sends a GET request to `/api/v1/ponds` for a farm with no ponds
- **THEN** the system returns a 200 OK response with an empty array

#### Scenario: List ponds without X-Farm-ID header
- **WHEN** an authenticated user sends a GET request to `/api/v1/ponds` without an `X-Farm-ID` header
- **THEN** the system returns a 400 Bad Request response with error code `MissingFarmId`

### Requirement: Pond Creation
The system SHALL allow farm owners and managers to create a new pond in the current farm.

#### Scenario: Create pond with valid data
- **WHEN** a farm owner sends a POST request to `/api/v1/ponds` with `pond_number` and `size`
- **THEN** the system creates a new pond record scoped to the current farm
- **AND** returns a 201 Created response with the pond object

#### Scenario: Create pond with duplicate number in same farm
- **WHEN** a farm owner sends a POST request to `/api/v1/ponds` with a `pond_number` that already exists in the current farm
- **THEN** the system returns a 422 Unprocessable Entity response with error code `DuplicatePondNumber`
- **AND** no pond is created

#### Scenario: Create pond with duplicate number in different farm
- **WHEN** a farm owner sends a POST request to `/api/v1/ponds` with a `pond_number` that exists in a different farm
- **THEN** the system creates the pond successfully
- **AND** returns a 201 Created response

#### Scenario: Create pond without pond_number
- **WHEN** a farm owner sends a POST request to `/api/v1/ponds` without a `pond_number`
- **THEN** the system returns a 422 Unprocessable Entity response with validation errors
- **AND** no pond is created

#### Scenario: Create pond as worker
- **WHEN** a farm worker sends a POST request to `/api/v1/ponds`
- **THEN** the system returns a 403 Forbidden response
- **AND** no pond is created

### Requirement: Pond Retrieval
The system SHALL allow authenticated farm members to view a specific pond in the current farm.

#### Scenario: Get pond by ID
- **WHEN** an authenticated user sends a GET request to `/api/v1/ponds/{id}`
- **AND** the pond belongs to the current farm
- **THEN** the system returns a 200 OK response with the pond object

#### Scenario: Get pond from different farm
- **WHEN** an authenticated user sends a GET request to `/api/v1/ponds/{id}` for a pond that belongs to a different farm
- **THEN** the system returns a 404 Not Found response

### Requirement: Pond Update
The system SHALL allow farm owners and managers to update pond details.

#### Scenario: Update pond as owner
- **WHEN** a farm owner sends a PATCH request to `/api/v1/ponds/{id}` with updated `pond_number` or `size`
- **THEN** the system updates the pond record
- **AND** returns a 200 OK response with the updated pond object

#### Scenario: Update pond as worker
- **WHEN** a farm worker sends a PATCH request to `/api/v1/ponds/{id}`
- **THEN** the system returns a 403 Forbidden response
- **AND** the pond is not updated

#### Scenario: Update pond with duplicate number
- **WHEN** a farm owner sends a PATCH request to `/api/v1/ponds/{id}` with a `pond_number` that already exists for another pond in the same farm
- **THEN** the system returns a 422 Unprocessable Entity response with error code `DuplicatePondNumber`

### Requirement: Pond Deletion
The system SHALL allow farm owners and managers to delete ponds.

#### Scenario: Delete pond as owner
- **WHEN** a farm owner sends a DELETE request to `/api/v1/ponds/{id}`
- **AND** the pond has no associated stock, sales, feed, or medicine records
- **THEN** the system deletes the pond record
- **AND** returns a 204 No Content response

#### Scenario: Delete pond as worker
- **WHEN** a farm worker sends a DELETE request to `/api/v1/ponds/{id}`
- **THEN** the system returns a 403 Forbidden response
- **AND** the pond is not deleted

#### Scenario: Delete pond with associated records
- **WHEN** a farm owner sends a DELETE request to `/api/v1/ponds/{id}` that has associated stock, sales, feed, or medicine records
- **THEN** the system returns a 422 Unprocessable Entity response with error code `PondHasRecords`
- **AND** the pond is not deleted
