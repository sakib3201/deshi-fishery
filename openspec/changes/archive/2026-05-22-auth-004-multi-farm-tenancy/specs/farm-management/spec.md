## ADDED Requirements

### Requirement: Farm Member Management
The system SHALL allow farm owners to add and remove members from their farm.

#### Scenario: Add member to farm
- **WHEN** a farm owner sends a POST request to `/api/v1/farms/{id}/members` with a user's email
- **AND** the user exists in the system
- **THEN** the system creates a `farm_user` pivot record linking the user to the farm with role "worker"
- **AND** returns a 201 Created response with the member object

#### Scenario: Add non-existent user as member
- **WHEN** a farm owner sends a POST request to `/api/v1/farms/{id}/members` with an email that does not exist
- **THEN** the system returns a 422 Unprocessable Entity response with error code "UserNotFound"
- **AND** no member is added

#### Scenario: Add duplicate member
- **WHEN** a farm owner sends a POST request to `/api/v1/farms/{id}/members` with an email of a user already on the farm
- **THEN** the system returns a 422 Unprocessable Entity response with error code "AlreadyMember"
- **AND** no new member is added

#### Scenario: Remove member from farm
- **WHEN** a farm owner sends a DELETE request to `/api/v1/farms/{id}/members/{user_id}`
- **THEN** the system removes the `farm_user` pivot record
- **AND** if the removed user's `current_farm_id` was this farm, sets it to null
- **AND** returns a 204 No Content response

#### Scenario: Remove self from farm as owner
- **WHEN** a farm owner sends a DELETE request to remove themselves from their own farm
- **THEN** the system returns a 422 Unprocessable Entity response with error code "CannotRemoveOwner"
- **AND** the owner is not removed

#### Scenario: List farm members
- **WHEN** a farm owner sends a GET request to `/api/v1/farms/{id}/members`
- **THEN** the system returns a 200 OK response with an array of members (id, name, email, role)

#### Scenario: Non-owner attempts member management
- **WHEN** a non-owner user sends any member management request
- **THEN** the system returns a 403 Forbidden response
