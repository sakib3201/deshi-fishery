# AGENTS.md — Deshi Fishery

> Fisheries management platform for Bangladeshi fish farmers. Read this before every session.

## Project Context

- **Domain**: Multi-pond fisheries management (stock, sales, feed, medicine, expenses, partners).
- **Audience**: Fish farm owners, managers, and field workers in rural Bangladesh.
- **Scale**: MVP, solopreneur-built, < 100 users.
- **Language**: English default, Bengali (Bangla) toggle. Currency: BDT (৳).
- **Target devices**: Entry-level Android smartphones (primary), desktop for owners.
- **Connectivity**: Offline-first PWA; rural connectivity is spotty.

## Architecture

| Layer | Tech |
|-------|------|
| Backend | Laravel 13 + PHP 8.3 (API-first, JSON only, no Blade views except PDF templates) |
| Frontend | SvelteKit (Svelte 5 runes mode) + Tailwind CSS v4 + shadcn/ui |
| Charts | LayerChart (Svelte-native, D3-based) |
| Database | PostgreSQL 17 (shared-schema multi-tenancy via `farm_id`) |
| Cache/Queue/Session | Redis (Laravel Horizon deferred post-MVP) |
| Offline sync | ElectricSQL + PGlite (WASM PostgreSQL in browser) |
| Auth | Laravel Passport (OAuth 2.0) + Socialite (Google, Facebook) |
| PDF | Gotenberg (Dockerized HTML-to-PDF via HTTP API) |
| Container | Docker Compose (single VPS, Ubuntu 24.04) |
| Hosting | Local Bangladeshi VPS |

## Repo Layout

```
deshi-fishery/
├── backend/           # Laravel 13 API
│   ├── app/
│   ├── database/
│   ├── routes/
│   ├── tests/
│   ├── artisan
│   └── composer.json
├── frontend/          # SvelteKit (Svelte 5 runes mode) + Tailwind CSS v4
├── docker/            # Dockerfile, nginx.conf, php.ini
├── docker-compose.yml
├── docker-compose.prod.yml
├── specs/             # OpenSpec documents (spec-driven development)
├── docs/              # Design system, requirements, architecture docs
├── bruno/             # API documentation collections
├── .opencode/         # Agent instructions
├── DESIGN.md          # Single source of truth for project overview
└── AGENTS.md          # This file
```

## Developer Commands

All backend commands run from `backend/` directory.

### Setup
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

### Dev server (runs API + queue + Vite concurrently)
```bash
cd backend
composer run dev
```

### Tests
```bash
cd backend
composer run test          # Runs: php artisan config:clear + php artisan test
```

### Lint / Format / Typecheck
```bash
cd backend
vendor/bin/pint            # Laravel Pint (PHP code style)
vendor/bin/phpstan analyse --level=8   # PHPStan (install larastan first)
```

### Frontend
```bash
cd frontend
pnpm install
pnpm run dev      # Vite dev server on :5173
pnpm run build
pnpm run lint     # Prettier + ESLint
pnpm run check    # svelte-check + TypeScript strict
pnpm run test     # Playwright E2E tests
```

### Docker Compose (local)
```bash
docker compose up -d
# API: http://localhost:8080/api/v1/health
# Frontend dev: http://localhost:5173
# Gotenberg: http://localhost:3000
```

## Key Conventions

### API
- Base path: `/api/v1/`
- Resource names: plural nouns (`/ponds`, `/sales`, `/expenses`)
- Pagination: cursor-based (`?cursor=xyz&per_page=20`)
- Filtering: simple query params (`?status=paid&pond_id=1`)
- Error envelope:
  ```json
  {
    "success": false,
    "error": {
      "code": "InsufficientStock",
      "message": "...",
      "details": {}
    }
  }
  ```

### Multi-tenancy
- Every query MUST be scoped by `farm_id`.
- `farm_id` comes from `X-Farm-ID` header or user's `current_farm_id`.
- Use a global query scope on all farm-scoped models.
- Never return data from a farm the user does not belong to.

### RBAC
| Role | Permissions |
|------|-------------|
| Owner | Full CRUD, user management, farm settings |
| Manager | Create/edit data, cannot delete, edits audited |
| Worker | View limited data, input new records only |

- Enforce via middleware on ALL API routes.
- Return `403 Forbidden` for unauthorized actions.

### Frontend
- Use **Svelte 5 runes** (`$state`, `$derived`, `$effect`) — no Svelte 4 stores.
- Use **native Fetch API** — no Axios.
- Use **LayerChart** for all charts — no Chart.js wrappers.
- Use **shadcn/ui** components where available.
- All API calls go through a central client in `src/lib/api/`.
- Forms: outlined inputs, 56px min touch targets, numeric keypads where possible.
- Currency formatting: `formatCurrency(amount, locale)` — BDT symbol always.
- Numbers: Bengali numerals (০-৯) when Bangla selected; Arabic (0-9) when English.

### Code Quality
- PHP: PSR-12, `declare(strict_types=1);`, return type hints on all methods.
- PHPStan: level 8 (install `larastan/larastan` first).
- TypeScript: strict mode.
- Tests: Pest (backend), Playwright (E2E critical flows only).

## Spec-Driven Development (OpenSpec)

Every feature is specified in `specs/` before any code is written.

Specs for changes MUST be maintained in `/openspec` using the OpenSpec skills (`/opsx propose`, `/opsx explore`, `/opsx apply`, `/opsx archive`).

### Spec format
```
SPEC {ID}  {Title}
  {Description of observable behaviour}

  PRECONDITION:  {what must be true before}
  POSTCONDITION: {what is guaranteed after}
  INVARIANT:     {condition always true}
  ERROR:         {named failure mode}
```

### Keywords
- `MUST` / `MUST NOT` — non-negotiable
- `SHOULD` — strongly recommended default
- `MAY` — optional
- `INVARIANT` — always true; violation = bug
- `PRECONDITION` — must be true before operation
- `POSTCONDITION` — guaranteed after success
- `ERROR` — named failure mode surfaced explicitly

### Workflow
1. `/opsx propose` — generate spec + design + tasks for the feature
2. `/opsx explore` — think through edge cases and clarify requirements
3. Write spec in `specs/{module}.md`
4. `/opsx apply` — implement code that satisfies the spec
5. Verify with tests asserting POSTCONDITION and INVARIANT
6. `/opsx archive` — finalize and archive the completed change
7. Commit spec + code together

---

## Agent Principles

### 1. Think Before Coding
Don't assume. Don't hide confusion. Surface tradeoffs.

Before implementing:
- State your assumptions explicitly. If uncertain, ask.
- If multiple interpretations exist, present them — don't pick silently.
- If a simpler approach exists, say so. Push back when warranted.
- If something is unclear, stop. Name what's confusing. Ask.

### 2. Simplicity First
Minimum code that solves the problem. Nothing speculative.

- No features beyond what was asked.
- No abstractions for single-use code.
- No "flexibility" or "configurability" that wasn't requested.
- No error handling for impossible scenarios.
- If you write 200 lines and it could be 50, rewrite it.
- Ask yourself: "Would a senior engineer say this is overcomplicated?" If yes, simplify.

### 3. Surgical Changes
Touch only what you must. Clean up only your own mess.

When editing existing code:
- Don't "improve" adjacent code, comments, or formatting.
- Don't refactor things that aren't broken.
- Match existing style, even if you'd do it differently.
- If you notice unrelated dead code, mention it — don't delete it.

When your changes create orphans:
- Remove imports/variables/functions that YOUR changes made unused.
- Don't remove pre-existing dead code unless asked.

The test: Every changed line should trace directly to the user's request.

### 4. Goal-Driven Execution
Define success criteria. Loop until verified.

Transform tasks into verifiable goals:
- "Add validation" → "Write tests for invalid inputs, then make them pass"
- "Fix the bug" → "Write a test that reproduces it, then make it pass"
- "Refactor X" → "Ensure tests pass before and after"

For multi-step tasks, state a brief plan:
```
1. [Step] → verify: [check]
2. [Step] → verify: [check]
3. [Step] → verify: [check]
```

### 5. Add and Update Local Context
When working in a directory, add context specific to that directory in an `AGENTS.md` file so needed context gets loaded at runtime.

- If a directory has its own conventions, patterns, or module-specific rules, create or update an `AGENTS.md` inside that directory.
- Keep local `AGENTS.md` files focused: only information relevant to that subtree.
- The root `AGENTS.md` is the global source of truth; local files supplement it.
- Update local context when patterns change — stale context is worse than no context.

Example local `AGENTS.md` locations:
- `backend/AGENTS.md` — Laravel module boundaries, service patterns, repository conventions.
- `frontend/AGENTS.md` — SvelteKit routing rules, API client patterns, component hierarchy.
- `specs/AGENTS.md` — OpenSpec naming conventions, module ID prefixes, review checklist.

## Testing Patterns

### Backend (Pest)
- Base `TestCase` should set up a user + farm in `setUp()`.
- Multi-tenancy scoping test: assert only current farm's data is returned.
- Transactional integrity test: assert stock rolls back on sale failure.
- Spec-driven test: assert POSTCONDITION and INVARIANT.
- RBAC test: assert workers cannot delete records.

### Frontend (Playwright)
- Config: `tests/e2e/`, baseURL `http://localhost:8080`, Mobile Chrome + Desktop Chrome.
- Critical flows: login, offline sync, add sale, add expense.

## Never Modify

- `.env` (after initial setup)
- Migration files (after committed)
- `docker-compose.yml` / `docker-compose.prod.yml`
- Production configs
- Passport OAuth keys (`storage/oauth-*.key`)

## Allowed Commands

- `php artisan make:*`
- `php artisan migrate` (local only)
- `php artisan test`
- `vendor/bin/pest`
- `vendor/bin/phpstan analyse --level=8`
- `vendor/bin/pint`
- `composer require`
- `pnpm install/add`
- `pnpm run dev/build/test/lint/check`
- `git status`, `git diff`, `git log --oneline -10`

## Blocked Commands

- `php artisan migrate` on production/staging
- `git push`, `git reset --hard`, `rm -rf`
- `docker compose` on remote servers
- Any command that modifies `.env` or production configs

## Important Constraints

- **No WebSockets/SSE** in MVP.
- **No CDN** in MVP.
- **No feature flags**.
- **No dedicated search engine** (use DB `LIKE` + indexes).
- **Zero paid tooling cost** for MVP.
- **No Blade views** for UI — API-only backend. Blade is allowed only for Gotenberg PDF templates in `resources/views/pdf/`.
- **SQLite is used for local development and testing** (`phpunit.xml` sets `DB_CONNECTION=sqlite` `:memory:`). Local dev uses the default SQLite file (`database/database.sqlite`). Production uses PostgreSQL — this is the only remaining infrastructure piece to set up.

## Related Documents

### Obsidian Vault (master R&D source)

**Path**: `E:\Obsidian\shahon_desktop\deshi_fishery`

When the user says "obsidian vault", "the vault", or refers to the master R&D source, they mean this directory. This is the canonical location for all research, specification, and planning documents. The repo contains working copies of select documents (see below).

Key files in the vault:

| File | Purpose |
|------|---------|
| `00 - Deshi Fishery Index.md` | Master index, module map, project status |
| `01 - Functional Requirements.md` | Raw stakeholder interview transcription |
| `02 - Requirement Clarification Questionnaire.md` | 50 answered business/functional questions |
| `03 - Refined Requirements.md` | Implementation-ready specification |
| `04 - Technical Architecture Questionnaire.md` | 263 answered technical questions |
| `05 - Technical Architecture.md` | Consolidated architecture decisions |
| `06 - Technology Stack Decision Record.md` | ADRs for all 17 stack choices |
| `07 - Development & Deployment Guide.md` | Docker, CI/CD, deployment procedures |
| `08 - CI/CD Pipeline Specification.md` | Full GitHub Actions workflows (staging + production deploy) |
| `09 - Agent & Testing Quick Reference.md` | Agent guardrails, test patterns, quality checklist |
| `13 - Security Hardening Checklist.md` | 15 critical security items for launch |
| `DESIGN.md` | Single source of truth (copied to repo root) |
| `UI-UX Design Questionnaire.md` | Visual design system questionnaire (completed) |
| `docs/ui-design-system.md` | Concrete design tokens, components, Tailwind patterns |
| `docs/sprint-progress.md` | Sprint progress tracker — feature implementation status |

### In-Repo Copies

The following files are maintained as copies from the Obsidian vault for agent context during implementation. Update them when vault documents change:

- `DESIGN.md` — Project overview, stack, workflow, guardrails (read first).
- `docs/ui-design-system.md` — Visual design tokens, components, patterns.
- `docs/00-index.md` — Master document index and module map.
- `specs/` — OpenSpec documents for each module (auth, farm, pond, stock, sales, feed, medicine, expenses, partners, dashboard, sync).

| File | Purpose |
|------|---------|
| `00 - Deshi Fishery Index.md` | Master index, module map, project status |
| `01 - Functional Requirements.md` | Raw stakeholder interview transcription |
| `02 - Requirement Clarification Questionnaire.md` | 50 answered business/functional questions |
| `03 - Refined Requirements.md` | Implementation-ready spec (copied to `docs/03-refined-requirements.md`) |
| `04 - Technical Architecture Questionnaire.md` | 263 answered technical questions |
| `05 - Technical Architecture.md` | Consolidated architecture (copied to `docs/05-technical-architecture.md`) |
| `06 - Technology Stack Decision Record.md` | ADRs for all 17 stack choices (copied to `docs/06-technology-stack-adr.md`) |
| `07 - Development & Deployment Guide.md` | Docker, CI/CD, deployment procedures (copied to `docs/07-development-deployment-guide.md`) |
| `08 - CI/CD Pipeline Specification.md` | Full GitHub Actions workflows (staging + production deploy) |
| `09 - Agent & Testing Quick Reference.md` | Agent guardrails, test patterns, quality checklist (copied to `docs/09-agent-testing-quick-reference.md`) |
| `13 - Security Hardening Checklist.md` | 15 critical security items for launch (copied to `docs/13-security-hardening-checklist.md`) |
| `DESIGN.md` | Single source of truth (copied to repo root) |
| `UI-UX Design Questionnaire.md` | Visual design system questionnaire (completed) |
| `docs/ui-design-system.md` | Concrete design tokens, components, Tailwind patterns (copied to `docs/ui-design-system.md`) |
| `docs/sprint-progress.md` | Sprint progress tracker — feature implementation status |

## Setup Verification Checklist

| Item | Expected | Actual | Status |
|------|----------|--------|--------|
| Backend framework | Laravel 13 + PHP 8.3 | Laravel 13.8, PHP ^8.3 | ✅ |
| Backend dev server | `composer run dev` (concurrently: serve + queue + Vite) | `composer run dev` runs `php artisan serve`, `queue:listen`, `npm run dev` | ✅ |
| Backend tests | PHPUnit (Pest planned) | PHPUnit 12.5.12 configured, Pest not installed yet | ⚠️ |
| Backend lint | Pint + PHPStan level 8 | Pint installed, PHPStan not installed yet | ⚠️ |
| Frontend framework | SvelteKit (Svelte 5 runes mode) | SvelteKit 2.57, Svelte 5.55.2, `runes: true` in `svelte.config.js` | ✅ |
| Frontend styling | Tailwind CSS v4 | Tailwind CSS 4.2.2, `@tailwindcss/vite` plugin | ✅ |
| Frontend typecheck | TypeScript strict | `strict: true` in `tsconfig.json`, `svelte-check` installed | ✅ |
| Frontend lint | ESLint + Prettier | ESLint 10 + `eslint-plugin-svelte`, Prettier 3 + `prettier-plugin-svelte` | ✅ |
| Frontend tests | Playwright E2E | `@playwright/test` 1.59.1 installed, config at `playwright.config.ts` | ✅ |
| Docker Compose | `docker-compose.yml` + `docker-compose.prod.yml` | Not created yet | ❌ |
| PostgreSQL | 17 | Not configured yet (`.env.example` uses SQLite) | ❌ |
| Redis | 7 | Not configured yet | ❌ |
| Gotenberg | 8 | Not configured yet | ❌ |
| shadcn/ui | Svelte port | Not installed yet | ❌ |
| LayerChart | Svelte-native charts | Not installed yet | ❌ |
| ElectricSQL + PGlite | Offline sync | Not installed yet | ❌ |
| Laravel Passport | OAuth 2.0 | Not installed yet | ❌ |
| Laravel Horizon | Queue dashboard | Removed from MVP — deferred to post-MVP | ⏸️ |
| OpenSpec specs | `specs/{module}.md` | Directory exists, empty | ❌ |
| CI/CD | GitHub Actions workflows | Not created yet | ❌ |

## Current Status

- [x] Requirements Gathering — 100%
- [x] Technical Architecture — 100%
- [x] Frontend Initialization — 100% (SvelteKit + Tailwind CSS v4 installed)
- [ ] UI/UX Design — 0% (ready to start)
- [ ] Implementation — 0% (ready to start)
- [ ] Testing & Deployment — 0% (ready to start)

The backend is a fresh Laravel 13 install. The frontend is initialized with SvelteKit (Svelte 5 runes mode), Tailwind CSS v4, TypeScript strict, Prettier, ESLint, and Playwright. Docker Compose files are not yet created. The first implementation task should be to set up the Docker Compose environment, then begin implementing modules according to the OpenSpec in `specs/`.

### Sprint Progress

Implementation follows a 6-sprint plan tracked in `docs/sprint-progress.md`:

| Sprint | Focus | Key Features |
|--------|-------|--------------|
| Sprint 1 | Foundation | AUTH-001, AUTH-004, FARM-001, POND-001 |
| Sprint 2 | Core Operations | STOCK-001, SALE-001, SALE-002, EXP-001, EXP-005 |
| Sprint 3 | Inventory & Operations | FEED-001-003, MED-001-003 |
| Sprint 4 | Dashboard & Reporting | DASH-001-005 |
| Sprint 5 | Advanced Features | AUTH-002-003, SALE-003-005, FEED-004-005 |
| Sprint 6 | Partners, Sync & Polish | PARTNER-001-002, SYNC-001-002, UI-001-002, remaining features |

**Current Sprint**: Sprint 1 — Foundation (AUTH-001 in progress)
**Next Feature**: AUTH-004 Multi-Farm Tenancy
**See**: `docs/sprint-progress.md` for detailed feature status and definitions of done.

## graphify

This project has a knowledge graph at graphify-out/ with god nodes, community structure, and cross-file relationships.

When the user types `/graphify`, invoke the `skill` tool with `skill: "graphify"` before doing anything else.

Rules:
- For codebase questions, first run `graphify query "<question>"` when graphify-out/graph.json exists. Use `graphify path "<A>" "<B>"` for relationships and `graphify explain "<concept>"` for focused concepts. These return a scoped subgraph, usually much smaller than GRAPH_REPORT.md or raw grep output.
- Dirty graphify-out/ files are expected after hooks or incremental updates; dirty graph files are not a reason to skip graphify. Only skip graphify if the task is about stale or incorrect graph output, or the user explicitly says not to use it.
- If graphify-out/wiki/index.md exists, use it for broad navigation instead of raw source browsing.
- Read graphify-out/GRAPH_REPORT.md only for broad architecture review or when query/path/explain do not surface enough context.
- After modifying code, run `graphify update .` to keep the graph current (AST-only, no API cost).
