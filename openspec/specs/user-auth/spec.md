# user-auth Specification

## Purpose
TBD - created by archiving change auth-001-email-password-auth. Update Purpose after archive.
## Requirements
### Requirement: User Registration
The system SHALL allow new users to register with a unique email address, name, and password.

#### Scenario: Successful registration
- **WHEN** a user submits a registration request with name "Karim", email "karim@example.com", password "SecurePass123!", and password_confirmation "SecurePass123!"
- **THEN** the system creates a new user account with role "owner"
- **AND** returns a 201 Created response with the user object (excluding password)
- **AND** issues an access token and refresh token

#### Scenario: Registration with duplicate email
- **WHEN** a user submits a registration request with an email that already exists
- **THEN** the system returns a 422 Unprocessable Entity response with error code "DuplicateEmail"
- **AND** no new user is created

#### Scenario: Registration with invalid password
- **WHEN** a user submits a registration request with password "123" (less than 6 characters)
- **THEN** the system returns a 422 Unprocessable Entity response with validation error "Password must be at least 6 characters"
- **AND** no new user is created

#### Scenario: Registration with mismatched password confirmation
- **WHEN** a user submits a registration request with password "SecurePass123!" and password_confirmation "DifferentPass123!"
- **THEN** the system returns a 422 Unprocessable Entity response with validation error "Password confirmation does not match"
- **AND** no new user is created

#### Scenario: Registration rate limiting
- **WHEN** more than 5 registration requests are submitted from the same IP within 15 minutes
- **THEN** the system returns a 429 Too Many Requests response
- **AND** no new user is created for subsequent requests until the window resets

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

### Requirement: User Logout
The system SHALL allow authenticated users to log out, revoking their current access token and refresh token.

#### Scenario: Successful logout
- **WHEN** an authenticated user sends a logout request with a valid access token
- **THEN** the system revokes the access token and associated refresh token
- **AND** clears the HTTP-only auth cookie
- **AND** returns a 204 No Content response

#### Scenario: Logout with invalid token
- **WHEN** a user sends a logout request with an invalid or expired access token
- **THEN** the system returns a 401 Unauthorized response
- **AND** no tokens are revoked

### Requirement: Token Refresh
The system SHALL allow users to obtain a new access token using a valid refresh token.

#### Scenario: Successful token refresh
- **WHEN** a user sends a refresh request with a valid refresh token
- **THEN** the system returns a 200 OK response with a new access token (15-minute expiry)
- **AND** the old access token is revoked
- **AND** a new refresh token is issued (7-day expiry)

#### Scenario: Token refresh with expired refresh token
- **WHEN** a user sends a refresh request with an expired refresh token
- **THEN** the system returns a 401 Unauthorized response with error code "TokenExpired"
- **AND** no new tokens are issued

#### Scenario: Token refresh with revoked refresh token
- **WHEN** a user sends a refresh request with a refresh token that was revoked (e.g., after logout)
- **THEN** the system returns a 401 Unauthorized response with error code "TokenRevoked"
- **AND** no new tokens are issued

### Requirement: Password Security
The system SHALL enforce strong password hashing and validation rules.

#### Scenario: Password hashing
- **WHEN** a user registers with password "SecurePass123!"
- **THEN** the system stores the password using bcrypt with cost factor >= 12
- **AND** the plain-text password is never stored or logged

#### Scenario: Password minimum requirements
- **WHEN** a user registers with password "weak"
- **THEN** the system rejects the password with validation error "Password must be at least 6 characters"

### Requirement: Authenticated User Profile
The system SHALL allow authenticated users to retrieve their own profile information.

#### Scenario: Retrieve own profile
- **WHEN** an authenticated user sends a GET request to /api/v1/auth/me
- **THEN** the system returns a 200 OK response with the user's profile (id, name, email, role, current_farm_id)
- **AND** includes an array of the user's farms with id, name, and role on each farm
- **AND** the password hash is excluded from the response

#### Scenario: Retrieve profile without authentication
- **WHEN** an unauthenticated user sends a GET request to /api/v1/auth/me
- **THEN** the system returns a 401 Unauthorized response

