---
name: bruno-workspace
description: Create and manage Bruno API client workspaces, collections, requests, environments, variables, tests, and scripts. Use when the user needs to (1) create or modify Bruno API collections, (2) write .bru or .yml request files, (3) set up Bruno workspaces with folders and environments, (4) write API tests and assertions, (5) configure variables at any scope (environment, collection, folder, request, runtime), (6) use OpenCollection YAML format for API requests, or (7) organize API collections for Git collaboration. Do not use for Bruno CLI execution or CI/CD pipeline setup (see bruno-cli skill).
---

# Bruno Workspace and Collection Management

Bruno is an open-source API client that stores collections as plain text files (`.bru` or `.yml`) in a folder structure, enabling Git-based collaboration. Bruno v3.0.0+ uses OpenCollection YAML (`.yml`) as the recommended format for new collections, while maintaining full backward compatibility with `.bru` files.

## Workspace Structure

A Bruno workspace is a root directory containing:

```
workspace/
├── bruno.json                 # Workspace configuration
├── collection.yml             # Collection metadata (OpenCollection YAML)
├── environments/              # Environment files
│   ├── local.yml
│   └── production.yml
├── docs/                      # Collection-level documentation
├── scripts/                   # Collection-level scripts
│   ├── pre-request.js
│   └── post-response.js
├── tests/                     # Collection-level tests
│   └── assert.js
└── [folders]/                 # Request folders (arbitrary nesting)
    ├── folder.yml             # Folder metadata
    ├── docs/
    ├── scripts/
    ├── tests/
    ├── list-users.yml         # Request file (OpenCollection YAML)
    ├── create-user.bru        # Request file (legacy Bru format)
    └── [subfolders]/          # Nested folders supported
        └── ...
```

**Key files:**
- `bruno.json` - Workspace config. Minimum: `{"version": "1", "name": "workspace-name", "type": "collection"}`
- `collection.yml` - Collection metadata with `name`, `description`, `variables`, `headers`, `auth`, `docs`
- `folder.yml` - Folder metadata with `name`, `variables`, `headers`, `auth`, `docs`
- Individual request files - Named after the request, one file per request

When creating a workspace, always create the `bruno.json` file at the root. Set `type` to `collection` for API collections.

## Request File Formats

Bruno supports two formats for request files. **Default to OpenCollection YAML (`.yml`) for all new collections** unless the user explicitly requests `.bru` format.

### OpenCollection YAML Format (Recommended)

Read `references/opencollection-yaml.md` for the complete structure reference and all supported fields.

Quick example:

```yaml
# GET request with query params
info:
  name: List Users
  type: http
  seq: 1
http:
  method: GET
  url: "{{baseUrl}}/users"
  params:
    - name: page
      value: "1"
      type: query
    - name: limit
      value: "10"
      type: query
  headers:
    - name: Accept
      value: application/json
settings:
  encodeUrl: true
```

```yaml
# POST request with JSON body and auth
info:
  name: Create User
  type: http
  seq: 2
http:
  method: POST
  url: "{{baseUrl}}/users"
  headers:
    - name: Content-Type
      value: application/json
  body:
    type: json
    data: |-
      {
        "name": "{{userName}}",
        "email": "{{userEmail}}"
      }
  auth:
    type: bearer
    bearer:
      token: "{{authToken}}"
runtime:
  tests:
    - name: returns 201
      expr: res.getStatus() === 201
    - name: has id
      expr: res.getBody().id > 0
```

### Bru Markup Language Format (Legacy)

Read `references/bru-lang.md` for the complete tag reference.

Quick example:

```bru
meta {
  name: List Users
  type: http
  seq: 1
}

get {
  url: {{baseUrl}}/users?page=1&limit=10
}

params:query {
  page: 1
  limit: 10
}

headers {
  Accept: application/json
}
```

## Variables

Bruno has a 7-layer variable precedence system (highest to lowest):

1. **Runtime variables** - Set via `bru.setVar()` in scripts, highest precedence
2. **Request variables** - Defined in the request file's `vars` section
3. **Folder variables** - Defined in `folder.yml` or folder-level `.bru` metadata
4. **Environment variables** - Defined in environment files (e.g., `environments/local.yml`)
5. **Collection variables** - Defined in `collection.yml`
6. **Global variables** - Workspace-level environment variables shared across all collections
7. **Process env** - System environment variables accessed via `{{process.env.VAR_NAME}}`

**Variable interpolation syntax:** `{{variableName}}`

**Prompt variables** (special, not in precedence chain): `{{?Enter your name}}` - prompts user at runtime

### Environment Files

Environment files live in the `environments/` directory:

```yaml
# environments/local.yml
variables:
  - name: baseUrl
    value: http://localhost:3000
  - name: apiKey
    value: dev-key-123
    secret: true        # Masked in UI and logs
  - name: timeout
    value: "5000"
    type: number
```

Supported variable types: `string` (default), `number`, `boolean`, `secret`.

### Creating Variables Programmatically

In pre-request or post-response scripts:

```javascript
// Set a runtime variable (highest precedence, request scope)
bru.setVar("authToken", response.json().token);

// Set an environment variable (persists across requests)
bru.setEnvVar("authToken", response.json().token);

// Set a collection variable
bru.setCollectionVar("version", "1.0.0");

// Set a global variable
bru.setGlobalVar("sharedKey", "abc123");
```

## Tests and Assertions

Bruno uses a Chai-based assertion library. Tests are defined in the `runtime.tests` array (YAML) or `tests` block (Bru).

### Basic Test Patterns

```yaml
# In OpenCollection YAML
runtime:
  tests:
    - name: status is 200
      expr: res.getStatus() === 200
    - name: has users array
      expr: Array.isArray(res.getBody())
    - name: returns user data
      expr: res.getBody().name === "John Doe"
```

```yaml
# Assertions (auto-fail without custom messages)
runtime:
  assertions:
    - name: status is 200
      expr: res.getStatus() === 200
    - name: content type is JSON
      expr: res.getHeader("content-type").includes("application/json")
```

### Advanced Testing

```yaml
runtime:
  tests:
    # JSON path validation
    - name: nested field exists
      expr: res.getBody().user.profile.email !== undefined
    # Array validation
    - name: returns 3 items
      expr: res.getBody().length === 3
    # Response time
    - name: responds under 500ms
      expr: res.getResponseTime() < 500
    # Header check
    - name: has auth header
      expr: res.getHeader("authorization") !== undefined
```

### jsonBody and jsonSchema Assertions

Use `jsonBody` for readable JSON path checks:

```javascript
test("body deep-equals object", function() {
  expect(res.getBody()).to.have.jsonBody({id: 1, name: "Alice"});
});
test("nested path exists", function() {
  expect(res.getBody()).to.have.jsonBody("user.id");
});
test("nested path equals value", function() {
  expect(res.getBody()).to.have.jsonBody("user.id", 123);
});
```

Use `jsonSchema` for contract testing (Ajv under the hood):

```javascript
test("matches user schema", function() {
  const schema = {
    type: "object",
    required: ["id", "email"],
    properties: {
      id: {type: "integer"},
      email: {type: "string", format: "email"}
    }
  };
  expect(res.getBody()).to.have.jsonSchema(schema);
});
```

## Scripts

Bruno supports JavaScript scripts at collection, folder, and request levels.

### Script Execution Order

1. Collection pre-request script
2. Folder pre-request script (if in a folder)
3. Request pre-request script
4. **HTTP Request executes**
5. Collection post-response script
6. Folder post-response script (if in a folder)
7. Request post-response script

### Script Types

```yaml
# OpenCollection YAML - scripts in runtime section
runtime:
  scripts:
    - type: pre-request
      script: |-
        // Set dynamic headers
        bru.setVar("timestamp", new Date().toISOString());
        req.setHeader("X-Timestamp", bru.getVar("timestamp"));
    - type: post-response
      script: |-
        // Extract token from response
        const body = res.getBody();
        if (body.token) {
          bru.setEnvVar("authToken", body.token);
        }
```

### Common Script Operations

```javascript
// Access request
req.getUrl();
req.getMethod();
req.getHeader("content-type");
req.setHeader("X-Custom", "value");
req.removeHeader("X-Old");

// Access response
res.getStatus();
res.getBody();
res.getHeader("content-type");
res.getResponseTime();

// Variable operations
bru.getVar("varName");
bru.setVar("varName", "value");         // Runtime variable
bru.setEnvVar("varName", "value");      // Environment variable
bru.setCollectionVar("varName", "value");
bru.setGlobalVar("varName", "value");

// Sleep/delay
await bru.sleep(1000);  // milliseconds
```

## Authentication

OpenCollection YAML supports these auth types in the `http.auth` section:

```yaml
# No auth (default)
auth:
  type: none

# Basic auth
auth:
  type: basic
  basic:
    username: "{{username}}"
    password: "{{password}}"

# Bearer token
auth:
  type: bearer
  bearer:
    token: "{{authToken}}"

# API Key
auth:
  type: apikey
  apikey:
    key: X-API-Key
    value: "{{apiKey}}"
    placement: header    # or "query"

# OAuth2
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
```

## Collection-Level Configuration

The `collection.yml` file defines defaults for all requests in the collection:

```yaml
name: My API
variables:
  - name: baseUrl
    value: https://api.example.com
    type: string
headers:
  - name: Accept
    value: application/json
  - name: X-API-Version
    value: "2"
auth:
  type: bearer
  bearer:
    token: "{{authToken}}"
docs:
  - name: Overview
    content: |
      # My API Collection
      Base URL: {{baseUrl}}
```

## Body Types

OpenCollection YAML supports these body types in `http.body`:

| Type | Usage |
|------|-------|
| `json` | `{"key": "value"}` |
| `text` | Plain text content |
| `xml` | XML payload |
| `form-urlencoded` | `key=value&key2=value2` |
| `multipart-form` | File uploads and form data |
| `graphql` | GraphQL queries |

Example multipart form with file upload:

```yaml
http:
  method: POST
  url: "{{baseUrl}}/upload"
  body:
    type: multipart-form
    data:
      - name: file
        type: file
        value: /path/to/file.pdf
      - name: description
        type: text
        value: "My document"
```

## File Organization Best Practices

1. **Use descriptive file names** - `get-user-by-id.yml` instead of `request-1.yml`
2. **Group related requests in folders** - `users/`, `orders/`, `auth/`
3. **Name folders after API resources** - Match REST resource names
4. **Use sequence numbers sparingly** - The `seq` field controls UI order; increment by 10 to allow insertions
5. **Tag requests for filtering** - Add `tags: [smoke, regression]` to `info` for test run filtering
6. **Store secrets in environment variables** - Never hardcode credentials; mark as `secret: true`
7. **Use `docs/` folders** - Add markdown documentation at collection, folder, and request levels
8. **Keep environments minimal** - Only define variables that differ between environments

## Decision Guide

When creating Bruno collections, follow this decision tree:

1. **New collection?** Use OpenCollection YAML (`.yml`) format
2. **Adding to existing `.bru` collection?** Match the existing format unless migrating
3. **Need Git collaboration?** Both formats work; YAML has better tooling integration
4. **Simple requests?** YAML for readability
5. **Complex scripts?** Both support JavaScript equally
