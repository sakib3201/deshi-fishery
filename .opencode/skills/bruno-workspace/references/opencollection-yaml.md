# OpenCollection YAML Reference

Complete structure reference for OpenCollection YAML request files (`.yml`) used in Bruno v3.0.0+.

## Top-Level Structure

```yaml
info:       # Request metadata (name, type, seq, tags)
http:       # HTTP request configuration
runtime:    # Scripts and assertions
settings:   # Request settings
docs:       # Request documentation
```

## info

Metadata about the request.

| Field | Type | Description |
|-------|------|-------------|
| name | string | Display name of the request |
| type | string | `http` for HTTP requests, `folder` for folders |
| seq | number | Sort position in the UI |
| tags | array of strings | Tags for filtering during collection runs |

Example:

```yaml
info:
  name: Get Users
  type: http
  seq: 1
  tags:
    - smoke
    - regression
```

## http

HTTP request configuration.

### http.method

Supported values: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`, `HEAD`, `TRACE`, `CONNECT` (uppercase).

### http.url

The request URL. Supports variable interpolation.

```yaml
http:
  method: GET
  url: "https://api.example.com/users"
```

### http.params

Parameters as array of objects with `type` field.

```yaml
http:
  params:
    - name: filter
      value: active
      type: query
    - name: limit
      value: "10"
      type: query
    - name: id
      value: "123"
      type: path
```

Fields: `name` (string), `value` (string), `type` (`query` or `path`), `disabled` (boolean, optional).

### http.headers

Request headers as array of objects.

```yaml
http:
  headers:
    - name: Content-Type
      value: application/json
    - name: Authorization
      value: Bearer {{token}}
      disabled: true
```

Fields: `name` (string), `value` (string), `disabled` (boolean, optional).

### http.body

Request body configuration.

```yaml
http:
  body:
    type: json
    data: |-
      {"name": "John Doe"}
```

| Body Type | Description |
|-----------|-------------|
| `json` | JSON body |
| `text` | Plain text |
| `xml` | XML payload |
| `form-urlencoded` | Form URL-encoded data |
| `multipart-form` | Multipart form (file uploads) |
| `graphql` | GraphQL query |

Multipart form example:

```yaml
http:
  body:
    type: multipart-form
    data:
      - name: file
        type: file
        value: /path/to/file.pdf
      - name: description
        type: text
        value: "Document"
```

### http.auth

Authentication configuration.

```yaml
# Basic auth
http:
  auth:
    type: basic
    basic:
      username: admin
      password: secret

# Bearer token
http:
  auth:
    type: bearer
    bearer:
      token: "{{authToken}}"

# API Key
http:
  auth:
    type: apikey
    apikey:
      key: X-API-Key
      value: "{{apiKey}}"
      placement: header   # or "query"

# OAuth2
http:
  auth:
    type: oauth2
    oauth2:
      grant_type: password
      access_token_url: "{{tokenUrl}}"
      username: "{{username}}"
      password: "{{password}}"
      client_id: "{{clientId}}"
      client_secret: "{{clientSecret}}"
      scope: read write

# No auth
http:
  auth:
    type: none
```

## runtime

Scripts and assertions.

### runtime.scripts

Array of script objects with `type` (`pre-request` or `post-response`) and `script` (the JS code).

```yaml
runtime:
  scripts:
    - type: pre-request
      script: |-
        bru.setVar("timestamp", Date.now());
        req.setHeader("X-Timestamp", bru.getVar("timestamp"));
    - type: post-response
      script: |-
        const body = res.getBody();
        if (body.token) {
          bru.setEnvVar("authToken", body.token);
        }
```

### runtime.tests

Array of test objects with `name` and `expr` (JS expression, truthy = pass).

```yaml
runtime:
  tests:
    - name: status is 200
      expr: res.getStatus() === 200
    - name: has data
      expr: res.getBody().data !== undefined
    - name: array not empty
      expr: res.getBody().users.length > 0
```

### runtime.assertions

Same structure as tests but auto-fail without custom messages.

```yaml
runtime:
  assertions:
    - name: status is 200
      expr: res.getStatus() === 200
    - name: content type is JSON
      expr: res.getHeader("content-type").includes("application/json")
```

## settings

Request-level settings.

```yaml
settings:
  encodeUrl: true         # URL-encode parameters
  followRedirect: true    # Follow HTTP redirects
```

## docs

Request documentation (markdown).

```yaml
docs:
  - name: Overview
    content: |
      # Get Users
      Retrieves a paginated list of users.
```

## Complete Example

```yaml
info:
  name: Create User
  type: http
  seq: 2
  tags:
    - critical
http:
  method: POST
  url: "{{baseUrl}}/api/v1/users"
  headers:
    - name: Content-Type
      value: application/json
    - name: Authorization
      value: Bearer {{authToken}}
  body:
    type: json
    data: |-
      {
        "name": "{{userName}}",
        "email": "{{userEmail}}",
        "role": "user"
      }
  auth:
    type: none
runtime:
  scripts:
    - type: pre-request
      script: |-
        // Generate correlation ID
        bru.setVar("correlationId", crypto.randomUUID());
        req.setHeader("X-Correlation-ID", bru.getVar("correlationId"));
  tests:
    - name: returns 201 Created
      expr: res.getStatus() === 201
    - name: has user ID
      expr: typeof res.getBody().id === "number"
    - name: returns correct name
      expr: res.getBody().name === "{{userName}}"
  assertions:
    - name: content type is JSON
      expr: res.getHeader("content-type").includes("application/json")
settings:
  encodeUrl: true
docs:
  - name: Overview
    content: |
      # Create User
      Creates a new user account. Requires admin privileges.
```
