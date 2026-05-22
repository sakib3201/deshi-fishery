## ADDED Requirements

### Requirement: Farm Creation
The system SHALL allow authenticated users to create a farm with a name and optional location.

#### Scenario: Successful farm creation
- **WHEN** an authenticated user sends a POST request to `/api/v1/farms` with name "My Farm" and location "Rajshahi"
- **THEN** the system creates a new farm with the provided details
- **AND** associates the user as the farm owner via the `farm_user` pivot table
- **AND** sets the user's `current_farm_id` to the new farm's id
- **AND** returns a 201 Created response with the farm object

#### Scenario: Farm creation with duplicate name for same user
- **WHEN** an authenticated user who already owns a farm named "My Farm" sends a POST request to `/api/v1/farms` with name "My Farm"
- **THEN** the system returns a 422 Unprocessable Entity response with error code "DuplicateFarmName"
- **AND** no new farm is created

#### Scenario: Farm creation without authentication
- **WHEN** an unauthenticated user sends a POST request to `/api/v1/farms`
- **THEN** the system returns a 401 Unauthorized response
- **AND** no farm is created

### Requirement: Farm Listing
The system SHALL allow authenticated users to list all farms they own or belong to.

#### Scenario: List user's farms
- **WHEN** an authenticated user sends a GET request to `/api/v1/farms`
- **THEN** the system returns a 200 OK response with an array of farms the user is associated with
- **AND** each farm includes its id, name, location, and the user's role on that farm

#### Scenario: List farms without authentication
- **WHEN** an unauthenticated user sends a GET request to `/api/v1/farms`
- **THEN** the system returns a 401 Unauthorized response

### Requirement: Farm Retrieval
The system SHALL allow authenticated users to retrieve a specific farm they have access to.

#### Scenario: Retrieve accessible farm
- **WHEN** an authenticated user sends a GET request to `/api/v1/farms/{id}` for a farm they belong to
- **THEN** the system returns a 200 OK response with the farm object

#### Scenario: Retrieve inaccessible farm
- **WHEN** an authenticated user sends a GET request to `/api/v1/farms/{id}` for a farm they do not belong to
- **THEN** the system returns a 404 Not Found response

### Requirement: Farm Update
The system SHALL allow farm owners to update farm details.

#### Scenario: Successful farm update by owner
- **WHEN** a farm owner sends a PATCH request to `/api/v1/farms/{id}` with name "Updated Farm Name"
- **THEN** the system updates the farm's name
- **AND** returns a 200 OK response with the updated farm object

#### Scenario: Farm update by non-owner
- **WHEN** a non-owner user sends a PATCH request to `/api/v1/farms/{id}`
- **THEN** the system returns a 403 Forbidden response
- **AND** the farm is not updated

### Requirement: Farm Deletion
The system SHALL allow farm owners to delete a farm and all its associated data.

#### Scenario: Successful farm deletion by owner
- **WHEN** a farm owner sends a DELETE request to `/api/v1/farms/{id}`
- **THEN** the system deletes the farm and cascades deletion to all farm-scoped data (ponds, stock, sales, expenses, feed, medicine)
- **AND** removes all `farm_user` pivot records for that farm
- **AND** sets affected users' `current_farm_id` to null if it was this farm
- **AND** returns a 204 No Content response

#### Scenario: Farm deletion by non-owner
- **WHEN** a non-owner user sends a DELETE request to `/api/v1/farms/{id}`
- **THEN** the system returns a 403 Forbidden response
- **AND** the farm is not deleted

### Requirement: Farm Switching
The system SHALL allow authenticated users to switch their active farm.

#### Scenario: Successful farm switch
- **WHEN** an authenticated user sends a PATCH request to `/api/v1/users/current-farm` with `farm_id` of a farm they belong to
- **THEN** the system updates the user's `current_farm_id` to the specified farm
- **AND** revokes the current access token
- **AND** issues a new access token and refresh token containing the updated `current_farm_id`
- **AND** returns a 200 OK response with the new tokens and user object

#### Scenario: Switch to inaccessible farm
- **WHEN** an authenticated user sends a PATCH request to `/api/v1/users/current-farm` with a `farm_id` they do not belong to
- **THEN** the system returns a 403 Forbidden response
- **AND** the user's `current_farm_id` is not changed

#### Scenario: Switch farm without authentication
- **WHEN** an unauthenticated user sends a PATCH request to `/api/v1/users/current-farm`
- **THEN** the system returns a 401 Unauthorized response

### Requirement: Multi-Tenancy Query Scoping
The system SHALL automatically scope all farm-scoped database queries to the active farm.

#### Scenario: Query scoped to active farm
- **WHEN** an authenticated user with `current_farm_id = 1` sends any request to a farm-scoped endpoint
- **THEN** the system only returns data where `farm_id = 1`
- **AND** data belonging to other farms is never returned

#### Scenario: Query with X-Farm-ID header
- **WHEN** an authenticated user sends a request with `X-Farm-ID: 2` header to a farm-scoped endpoint
- **AND** the user belongs to farm 2
- **THEN** the system scopes the query to `farm_id = 2` regardless of the user's `current_farm_id`

#### Scenario: Query with invalid X-Farm-ID header
- **WHEN** an authenticated user sends a request with `X-Farm-ID: 99` header
- **AND** the user does not belong to farm 99
- **THEN** the system returns a 403 Forbidden response

### Requirement: Onboarding Flow
The system SHALL guide first-time users to create a farm before accessing the dashboard.

#### Scenario: First login without farms
- **WHEN** a newly registered user logs in and has no associated farms
- **THEN** the login response includes `requires_onboarding: true`
- **AND** the frontend redirects to the onboarding page instead of the dashboard

#### Scenario: Login with existing farms
- **WHEN** a user with existing farms logs in
- **THEN** the login response includes `requires_onboarding: false`
- **AND** the frontend redirects to the dashboard

## MODIFIED Requirements

### Requirement: User Login
The system SHALL authenticate registered users with their email and password, issuing access and refresh tokens upon success.

#### Scenario: Successful login
- **WHEN** a registered user submits email "karim@example.com" and correct password "SecurePass123!"
- **THEN** the system returns a 200 OK response with an access token (15-minute expiry) and a refresh token (7-day expiry)
- **AND** sets an HTTP-only secure cookie containing the access token
- **AND** returns the user object with role, `current_farm_id`, and an array of the user's farms
- **AND** includes `requires_onboarding` flag (true if user has no farms)

#### Scenario: Login with invalid password
- **WHEN** a registered user submits email "karim@example.com" and incorrect password "WrongPass123!"
- **THEN** the system returns a 401 Unauthorized response with error code "InvalidCredentials"
- **AND** no tokens are issued

#### Scenario: Login with non-existent email
- **WHEN** a user submits email "nonexistent@example.com" and any password
- **THEN** the system returns a 401 Unauthorized response with error code "InvalidCredentials"
- **AND** no tokens are issued

#### Scenario: Login rate limiting
- **WHEN** more than 5 login requests are submitted from the same IP within 15 minutes
- **THEN** the system returns a 429 Too Many Requests response
- **AND** no tokens are issued for subsequent requests until the window resets

### Requirement: Authenticated User Profile
The system SHALL allow authenticated users to retrieve their own profile information.

#### Scenario: Retrieve own profile
- **WHEN** an authenticated user sends a GET request to `/api/v1/auth/me`
- **THEN** the system returns a 200 OK response with the user's profile (id, name, email, role, `current_farm_id`)
- **AND** includes an array of the user's farms with id, name, and role on each farm
- **AND** the password hash is excluded from the response

#### Scenario: Retrieve profile without authentication
- **WHEN** an unauthenticated user sends a GET request to `/api/v1/auth/me`
- **THEN** the system returns a 401 Unauthorized response
