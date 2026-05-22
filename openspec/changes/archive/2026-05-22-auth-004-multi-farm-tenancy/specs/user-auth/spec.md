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
