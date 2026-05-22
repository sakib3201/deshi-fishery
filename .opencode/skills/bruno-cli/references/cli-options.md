# Bruno CLI Options Reference

Complete reference for `bru run` command options.

## Basic Options

| Option | Alias | Description |
|--------|-------|-------------|
| `-h`, `--help` | | Output usage information |
| `--version` | | Output version number |

## Setup Options

| Option | Description |
|--------|-------------|
| `--env [string]` | Specify environment to run with |
| `--global-env [string]` | Specify global/workspace-level environment |
| `--workspace-path [string]` | Workspace path when collection is not at root |
| `--env-var [string]` | Overwrite a single environment variable (repeatable) |
| `--env-file [string]` | Path to environment file (.bru or .json) |
| `--sandbox [string]` | JavaScript sandbox: `safe` (default) or `developer` |
| `--csv-file-path [string]` | CSV file for data-driven testing |
| `--json-file-path [string]` | JSON data file for iterations |
| `--iteration-count [number]` | Number of times to run the collection |
| `-r`, `--recursive` | Run recursively through subfolders |

## Request Options

| Option | Description |
|--------|-------------|
| `--skip [string]` | Comma-separated list of request names to skip |
| `--reporter-tags [string]` | Comma-separated tags to filter requests |

## SSL & Security Options

| Option | Description |
|--------|-------------|
| `--insecure` | Allow insecure SSL connections (skip cert validation) |
| `--cacert [string]` | Path to CA certificate file |
| `--cert [string]` | Path to client certificate file |
| `--client-cert [string]` | Path to client certificate (mTLS) |
| `--client-key [string]` | Path to client private key (mTLS) |

## Output & Reporting Options

| Option | Description |
|--------|-------------|
| `--output [string]` | Output file or directory path |
| `--format [string]` | Report format: `html`, `json`, `junit` (repeatable) |
| `--reporter-html-title [string]` | Custom title for HTML reports |

## Proxy Options

| Option | Description |
|--------|-------------|
| `--proxy [string]` | Proxy URL (http://host:port or socks5://host:port) |

## Usage Examples

```bash
# Basic run
bru run ./my-collection

# With environment
bru run ./my-collection --env production

# With variable overrides
bru run ./my-collection --env staging --env-var API_KEY=abc123

# Recursive with HTML report
bru run ./my-collection --recursive --output report.html --format html

# CI run with JUnit output
bru run ./my-collection --env ci --output junit.xml --format junit

# Data-driven with CSV
bru run ./my-collection --csv-file-path ./test-data.csv --format json

# Skip health checks
bru run ./my-collection --skip "Health Check"

# Run only smoke tests
bru run ./my-collection --reporter-tags smoke

# Developer mode with proxy
bru run ./my-collection --sandbox=developer --proxy http://localhost:8080

# mTLS
bru run ./my-collection --client-cert ./cert.pem --client-key ./key.pem
```

## Command Syntax

```
bru run [options] <collection-path|request-file>
```

The positional argument can be:
- A collection directory (runs all requests in the collection)
- A folder within a collection (runs all requests in that folder)
- A single request file (runs that specific request)
