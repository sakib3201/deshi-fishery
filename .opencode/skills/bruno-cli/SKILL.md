---
name: bruno-cli
description: Run Bruno API collections from the command line, integrate with CI/CD pipelines, and generate test reports. Use when the user needs to (1) install or use the Bruno CLI (`bru` command), (2) run API collections via command line, (3) set up CI/CD pipelines with Bruno (GitHub Actions, Jenkins), (4) generate HTML/JSON/JUnit test reports from API test runs, (5) override environment variables for CLI runs, (6) configure sandbox modes (safe vs developer), (7) run collections with proxy or mTLS settings, or (8) automate API testing in headless environments. Do not use for creating or editing Bruno collections and workspace files (see bruno-workspace skill).
---

# Bruno CLI

Bruno CLI (`@usebruno/cli`) enables running API collections from the command line for automation, CI/CD integration, and headless testing.

## Installation

Requires Node.js 18+ (latest LTS recommended).

```bash
# Install globally
npm install -g @usebruno/cli
# or
pnpm install -g @usebruno/cli
# or
yarn global add @usebruno/cli
```

Verify installation:

```bash
bru --version
```

## Running Collections

### Basic Commands

```bash
# Run a collection (default: runs all requests)
bru run /path/to/collection

# Run with specific environment
bru run /path/to/collection --env local

# Run with global/workspace environment
bru run /path/to/collection --global-env shared

# Run recursively (include subfolders)
bru run /path/to/collection --recursive

# Run specific folder within collection
bru run /path/to/collection/folder-name

# Run a single request file
bru run /path/to/collection/request.yml
```

### Environment Overrides

```bash
# Override individual variables (can be used multiple times)
bru run /path/to/collection --env local --env-var BASE_URL=https://staging.example.com --env-var API_KEY=abc123

# Use an environment file
bru run /path/to/collection --env-file /path/to/env.json
bru run /path/to/collection --env-file /path/to/env.bru

# Specify workspace path when collection is not at root
bru run /path/to/collection --workspace-path /path/to/workspace
```

### Data-Driven Testing

```bash
# Run with CSV data file (iterates over rows)
bru run /path/to/collection --csv-file-path /path/to/data.csv

# Run with JSON data file
bru run /path/to/collection --json-file-path /path/to/data.json

# Run with explicit iteration count
bru run /path/to/collection --iteration-count 5
```

### Request Filtering

```bash
# Skip specific requests by name
bru run /path/to/collection --skip "Health Check,Setup"

# Run only requests matching tag
bru run /path/to/collection --reporter-tags smoke,regression

# Run requests sequentially (default)
bru run /path/to/collection
```

## Sandbox Modes

Bruno CLI v3.0.0+ defaults to **Safe Mode**. Use `--sandbox` to change behavior.

| Mode | Description | Flag |
|------|-------------|------|
| Safe | No filesystem access, no external npm packages (default) | `--sandbox=safe` or omit |
| Developer | Full filesystem access, external npm packages allowed | `--sandbox=developer` |

```bash
# Safe mode (default) - for CI/CD
bru run /path/to/collection

# Developer mode - for local runs with custom packages
bru run /path/to/collection --sandbox=developer
```

## Report Generation

Bruno CLI supports multiple output formats for test results.

```bash
# Generate HTML report
bru run /path/to/collection --output results.html --format html

# Generate JSON report
bru run /path/to/collection --output results.json --format json

# Generate JUnit XML report (for CI systems)
bru run /path/to/collection --output junit.xml --format junit

# Multiple formats at once
bru run /path/to/collection --format html --format json --output ./reports/
```

Report options:
- `--output` - Output file or directory path
- `--format` - Report format: `html`, `json`, `junit`
- `--reporter-html-title` - Custom title for HTML reports

## SSL/TLS and Security Options

```bash
# Ignore SSL certificate errors (not recommended for production)
bru run /path/to/collection --insecure

# Use client certificate (mTLS)
bru run /path/to/collection --client-cert /path/to/cert.pem --client-key /path/to/key.pem

# Use custom CA certificate
bru run /path/to/collection --cacert /path/to/ca.pem

# Use certificate and key in single file
bru run /path/to/collection --cert /path/to/cert+key.pem
```

## Proxy Configuration

```bash
# HTTP proxy
bru run /path/to/collection --proxy http://proxy.company.com:8080

# Proxy with authentication
bru run /path/to/collection --proxy http://user:pass@proxy.company.com:8080

# SOCKS5 proxy
bru run /path/to/collection --proxy socks5://localhost:1080
```

## Complete CLI Options Reference

Read `references/cli-options.md` for the full list of all available command options.

## CI/CD Integration

### GitHub Actions

```yaml
# .github/workflows/api-tests.yml
name: API Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: '20'
      - run: npm install -g @usebruno/cli
      - run: bru run ./collection --env ci --output results.html --format html
      - uses: actions/upload-artifact@v4
        if: always()
        with:
          name: test-results
          path: results.html
```

### Jenkins Pipeline

```groovy
// Jenkinsfile
pipeline {
    agent any
    tools { nodejs 'Node 20' }
    stages {
        stage('Install CLI') {
            steps {
                sh 'npm install -g @usebruno/cli'
            }
        }
        stage('Run API Tests') {
            steps {
                sh 'bru run ./collection --env staging --output junit.xml --format junit'
            }
        }
    }
    post {
        always {
            junit 'junit.xml'
        }
    }
}
```

## Environment File Formats

### JSON Environment File

```json
{
  "variables": [
    {"name": "baseUrl", "value": "https://api.example.com"},
    {"name": "apiKey", "value": "secret123"},
    {"name": "timeout", "value": "5000"}
  ]
}
```

### Bru Environment File

```bru
vars {
  baseUrl: https://api.example.com
  apiKey: secret123
  ~deprecatedVar: oldValue
}

vars:secret [
  apiKey
]
```

Use `--env-file` with either format to pass environments to CLI runs.

## Exit Codes

| Code | Meaning |
|------|---------|
| 0 | All tests passed |
| 1 | Tests failed or execution error |

Always check exit codes in CI/CD pipelines:

```bash
bru run ./collection --env ci
if [ $? -ne 0 ]; then
  echo "API tests failed"
  exit 1
fi
```

## Performance Tips

1. **Use `--recursive` sparingly** - Only include folders you need
2. **Tag your requests** - Use `--reporter-tags` to run subsets
3. **Prefer Safe Mode** - It's the default; only use Developer Mode when needed
4. **Parallel collections** - Run independent collections in parallel CI jobs
5. **Use environment files** - Faster than multiple `--env-var` flags
