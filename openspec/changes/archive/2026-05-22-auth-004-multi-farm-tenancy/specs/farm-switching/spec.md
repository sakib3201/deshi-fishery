## ADDED Requirements

### Requirement: Active Farm Switching
The system SHALL allow authenticated users to switch their active farm at runtime, receiving updated authentication tokens.

#### Scenario: Successful farm switch with token refresh
- **WHEN** an authenticated user sends a PATCH request to `/api/v1/users/current-farm` with `farm_id` of a farm they belong to
- **THEN** the system updates the user's `current_farm_id` to the specified farm
- **AND** revokes the current access token
- **AND** issues a new access token and refresh token containing the updated `current_farm_id`
- **AND** returns a 200 OK response with the new tokens and user object

#### Scenario: Switch to same farm
- **WHEN** an authenticated user sends a PATCH request to `/api/v1/users/current-farm` with their current `farm_id`
- **THEN** the system returns a 200 OK response without revoking or reissuing tokens

#### Scenario: Switch to inaccessible farm
- **WHEN** an authenticated user sends a PATCH request to `/api/v1/users/current-farm` with a `farm_id` they do not belong to
- **THEN** the system returns a 403 Forbidden response
- **AND** the user's `current_farm_id` is not changed
- **AND** existing tokens remain valid

#### Scenario: Switch farm without authentication
- **WHEN** an unauthenticated user sends a PATCH request to `/api/v1/users/current-farm`
- **THEN** the system returns a 401 Unauthorized response

### Requirement: Token Farm Claim
The system SHALL include the active farm identifier in authentication tokens.

#### Scenario: Access token contains farm claim
- **WHEN** a user logs in or switches farms
- **THEN** the issued access token includes a custom claim `farm_id` set to the user's `current_farm_id`

#### Scenario: Token farm claim used for scoping
- **WHEN** an authenticated request does not include an `X-Farm-ID` header
- **THEN** the system extracts `farm_id` from the token claim
- **AND** uses it as the active farm for query scoping
