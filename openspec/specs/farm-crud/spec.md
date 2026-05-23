## ADDED Requirements

### Requirement: Farm List
The system SHALL allow authenticated users to list all farms they belong to.

#### Scenario: List farms for authenticated user
- **WHEN** an authenticated user sends a GET request to `/api/v1/farms`
- **THEN** the system returns a 200 OK response with an array of farms
- **AND** each farm includes `id`, `name`, `location`, and the user's `role`

#### Scenario: List farms when user belongs to no farms
- **WHEN** an authenticated user with no farms sends a GET request to `/api/v1/farms`
- **THEN** the system returns a 200 OK response with an empty array

### Requirement: Farm Creation
The system SHALL allow authenticated users to create a new farm.

#### Scenario: Create farm with valid data
- **WHEN** an authenticated user sends a POST request to `/api/v1/farms` with `name` and optional `location`
- **THEN** the system creates a new farm record
- **AND** associates the user as owner via `farm_user` pivot
- **AND** sets the user's `current_farm_id` to the new farm
- **AND** returns a 201 Created response with the farm object

#### Scenario: Create farm with duplicate name
- **WHEN** an authenticated user sends a POST request to `/api/v1/farms` with a name they already use
- **THEN** the system returns a 422 Unprocessable Entity response with error code `DuplicateFarmName`
- **AND** no farm is created

#### Scenario: Create farm without name
- **WHEN** an authenticated user sends a POST request to `/api/v1/farms` without a `name`
- **THEN** the system returns a 422 Unprocessable Entity response with validation errors
- **AND** no farm is created

### Requirement: Farm Retrieval
The system SHALL allow authenticated users to view a specific farm they belong to.

#### Scenario: Get farm by ID
- **WHEN** an authenticated user sends a GET request to `/api/v1/farms/{id}`
- **AND** the user belongs to the farm
- **THEN** the system returns a 200 OK response with the farm object

#### Scenario: Get farm user does not belong to
- **WHEN** an authenticated user sends a GET request to `/api/v1/farms/{id}` for a farm they do not belong to
- **THEN** the system returns a 404 Not Found response

### Requirement: Farm Update
The system SHALL allow farm owners to update farm details.

#### Scenario: Update farm as owner
- **WHEN** a farm owner sends a PATCH request to `/api/v1/farms/{id}` with updated `name` or `location`
- **THEN** the system updates the farm record
- **AND** returns a 200 OK response with the updated farm object

#### Scenario: Update farm as non-owner
- **WHEN** a non-owner user sends a PATCH request to `/api/v1/farms/{id}`
- **THEN** the system returns a 403 Forbidden response
- **AND** the farm is not updated

#### Scenario: Update farm with duplicate name
- **WHEN** a farm owner sends a PATCH request to `/api/v1/farms/{id}` with a name they already use for another farm
- **THEN** the system returns a 422 Unprocessable Entity response with error code `DuplicateFarmName`

### Requirement: Farm Deletion
The system SHALL allow farm owners to delete farms.

#### Scenario: Delete farm as owner
- **WHEN** a farm owner sends a DELETE request to `/api/v1/farms/{id}`
- **THEN** the system deletes the farm record
- **AND** removes all `farm_user` pivot records for the farm
- **AND** returns a 204 No Content response

#### Scenario: Delete farm as non-owner
- **WHEN** a non-owner user sends a DELETE request to `/api/v1/farms/{id}`
- **THEN** the system returns a 403 Forbidden response
- **AND** the farm is not deleted
