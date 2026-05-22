# Bru Markup Language Reference

Complete tag reference for `.bru` files (legacy format, still fully supported).

## Meta Block

```bru
meta {
  name: Get User
  type: http
  seq: 1
}
```

| Field | Description |
|-------|-------------|
| name | Display name |
| type | `http` or `folder` |
| seq | Sort position |

## HTTP Method Blocks

```bru
get {
  url: https://api.example.com/users
}

post {
  url: https://api.example.com/users
}

put {
  url: https://api.example.com/users/1
}

patch {
  url: https://api.example.com/users/1
}

delete {
  url: https://api.example.com/users/1
}
```

## Headers Block

```bru
headers {
  Content-Type: application/json
  Authorization: Bearer {{token}}
  ~X-Deprecated: value    // ~ prefix disables the header
}
```

## Params Blocks

```bru
params:query {
  page: 1
  limit: 10
  ~status: active         // ~ prefix disables
}

params:path {
  id: 123
}
```

## Body Block

```bru
body {
  {
    "name": "John",
    "email": "john@example.com"
  }
}
```

Body type is inferred from the `Content-Type` header or can be specified in the body block format.

## Auth Block

```bru
auth {
  mode: basic
}

auth:basic {
  username: admin
  password: secret
}

auth:bearer {
  token: {{authToken}}
}

auth:apikey {
  key: X-API-Key
  value: {{apiKey}}
  placement: header
}

auth:none {
}
```

## Vars Block

```bru
vars:pre-request {
  timestamp: ${new Date().toISOString()}
}

vars:post-response {
  authToken: res.body.token
}
```

## Assert Block

```bru
assert {
  res.status: eq 200
  res.body.id: isDefined
  res.headers.content-type: contains application/json
}
```

Assertion operators: `eq`, `neq`, `gt`, `gte`, `lt`, `lte`, `contains`, `matches`, `isDefined`, `isUndefined`, `isEmpty`, `isNotEmpty`, `isArray`, `isObject`, `isString`, `isNumber`, `isBoolean`, `isNull`.

## Tests Block

```bru
tests {
  test("status is 200", function() {
    expect(res.getStatus()).to.equal(200);
  });

  test("returns user data", function() {
    const body = res.getBody();
    expect(body).to.have.property("id");
    expect(body.name).to.equal("John");
  });
}
```

## Script Blocks

```bru
script:pre-request {
  bru.setVar("timestamp", Date.now());
  req.setHeader("X-Timestamp", bru.getVar("timestamp"));
}

script:post-response {
  const body = res.getBody();
  if (body.token) {
    bru.setEnvVar("authToken", body.token);
  }
}
```

## Docs Block

```bru
docs {
  # Get User
  Retrieves a single user by ID.
}
```

## Complete Example

```bru
meta {
  name: Create User
  type: http
  seq: 2
}

post {
  url: {{baseUrl}}/users
}

headers {
  Content-Type: application/json
  Authorization: Bearer {{authToken}}
}

body {
  {
    "name": "{{userName}}",
    "email": "{{userEmail}}"
  }
}

script:pre-request {
  bru.setVar("requestId", crypto.randomUUID());
  req.setHeader("X-Request-ID", bru.getVar("requestId"));
}

script:post-response {
  const body = res.getBody();
  if (body.token) {
    bru.setEnvVar("authToken", body.token);
  }
}

tests {
  test("returns 201", function() {
    expect(res.getStatus()).to.equal(201);
  });

  test("has user id", function() {
    expect(res.getBody()).to.have.property("id");
  });
}

docs {
  # Create User
  Creates a new user in the system.
}
```

## Disabled Items

Prefix any key in dictionary or array blocks with `~` to disable:

```bru
headers {
  ~X-Deprecated: value
}

vars:secret [
  active_key,
  ~deprecated_key
]
```
