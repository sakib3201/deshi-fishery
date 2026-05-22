---
title: DESIGN.md — Deshi Fishery
tags:
  - design
  - agentic-engineering
  - deshi-fishery
  - spec-driven
status: active
created: 2026-05-22
related:
  - 00 - Deshi Fishery Index
  - 05 - Technical Architecture
  - 09 - Agent & Testing Quick Reference
  - docs/ui-design-system
custom-width: 100
---

# DESIGN.md — Deshi Fishery

> [!info] Purpose
> This is the **single source of truth** for the Deshi Fishery project. The agent MUST read this file before every session. It contains everything needed to understand the project, navigate the codebase, and implement features correctly.

---

## 1. Project Identity

| | |
|---|---|
| **Name** | Deshi Fishery |
| **Tagline** | মাছ চাষের সেরা assistant ("The best assistant for fish farming") |
| **Domain** | Fisheries management platform for Bangladeshi fish farmers |
| **Audience** | Fish farm owners, managers, and field workers in rural Bangladesh |
| **Scale** | MVP, solopreneur-built, < 100 users |
| **Primary Language** | English (default), Bengali (Bangla) toggle |
| **Currency** | Bangladeshi Taka (BDT) — ৳ |

### Brand Personality
- **Friendly / approachable** — not corporate or intimidating
- **Traditional / rooted in rural Bangladesh** — respects the user's context
- **Clear and trustworthy** — financial data must feel reliable

---

## 2. Stack Overview

### Backend
| Layer | Technology |
|-------|------------|
| Framework | Laravel 13 + PHP 8.3 |
| Database | PostgreSQL 17 |
| Cache/Queue/Session | Redis + Laravel Horizon |
| Search | Database `LIKE` + indexes |
| PDF Generation | Gotenberg (HTML-to-PDF via HTTP API) |
| Auth | Laravel Passport (OAuth 2.0) + Socialite |
| API Style | JSON-only, `/api/v1/`, cursor pagination |

### Frontend
| Layer | Technology |
|-------|------------|
| Framework | Svelte 5 (runes mode) |
| UI Components | shadcn/ui + Tailwind CSS |
| Charts | LayerChart (Svelte-native, D3-based) |
| State | Svelte 5 runes + ElectricSQL + PGlite |
| HTTP | Native Fetch API |
| Build | Vite |
| Icons | Lucide (outlined style) |

### Infrastructure
| Layer | Technology |
|-------|------------|
| Hosting | Local Bangladeshi VPS |
| OS | Ubuntu 24.04 LTS |
| Container | Docker Compose (app + db + redis + nginx + gotenberg) |
| SSL | Let's Encrypt (Certbot) |

---

## 3. Directory Structure

```
deshi-fishery/
├── backend/                    # Laravel 13 API
│   ├── app/
│   │   ├── Http/Controllers/Api/V1/   # API controllers only
│   │   ├── Models/                    # Eloquent models
│   │   ├── Services/                  # Business logic
│   │   └── Policies/                  # Authorization
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   │   └── api.php             # API routes only — no web routes
│   ├── tests/
│   │   └── Pest.php
│   ├── resources/
│   │   └── views/pdf/          # Blade templates for Gotenberg PDF generation
│   ├── storage/
│   │   ├── app/invoices/       # Generated PDFs
│   │   └── oauth/*.key         # NEVER commit these
│   └── composer.json
├── frontend/                   # Svelte 5 SPA
│   ├── src/
│   │   ├── lib/
│   │   │   ├── components/     # Reusable Svelte components
│   │   │   │   └── ui/         # shadcn/ui components
│   │   │   ├── stores/         # Svelte 5 runes-based state
│   │   │   ├── api/            # API client functions
│   │   │   └── utils/          # Helpers, formatters
│   │   ├── routes/             # SvelteKit-style routes (or page-based)
│   │   ├── app.html            # HTML entry point
│   │   └── app.css             # Global styles + Tailwind
│   ├── static/                 # PWA manifest, icons, service worker
│   ├── tests/e2e/              # Playwright tests
│   └── package.json
├── specs/                      # OpenSpec documents
│   ├── auth.md
│   ├── farm.md
│   ├── pond.md
│   ├── stock.md
│   ├── sales.md
│   ├── feed.md
│   ├── medicine.md
│   ├── expenses.md
│   ├── partners.md
│   ├── dashboard.md
│   └── sync.md
├── docker/
│   ├── Dockerfile
│   ├── nginx.conf
│   └── php.ini
├── docker-compose.yml
├── docker-compose.prod.yml
├── .env.example
├── .opencode/
│   └── agent-instructions.md   # Agent guardrails (sync with this file)
├── docs/
│   └── ui-design-system.md     # Visual design tokens and components
├── bruno/                      # API documentation collections
└── README.md
```

---

## 4. Development Methodology: OpenSpec / Spec-Driven

Every feature is specified in `specs/` before any code is written.

### Spec Format

```
SPEC {ID}  {Title}
  {Description of observable behaviour in present-tense declarative form}

  PRECONDITION:  {what must be true before}
  POSTCONDITION: {what is guaranteed after success}
  INVARIANT:     {condition always true}
  ERROR:         {named failure mode}
```

### Keywords

| Keyword | Meaning |
|---------|---------|
| `MUST` | Non-negotiable requirement |
| `MUST NOT` | Prohibited behaviour |
| `SHOULD` | Strongly recommended default |
| `MAY` | Optional behaviour |
| `INVARIANT` | Always true; any violation is a bug |
| `PRECONDITION` | Must be true before operation |
| `POSTCONDITION` | Guaranteed after operation succeeds |
| `ERROR` | Named failure mode surfaced explicitly |

### Workflow

1. **Write spec** in `specs/{module}.md`
2. **Review** spec for completeness
3. **Feed spec** to agent as context
4. **Implement** code that satisfies the spec
5. **Verify** with tests asserting POSTCONDITION and INVARIANT
6. **Commit** spec + code together

---

## 5. Critical Patterns

### 5.1 Multi-Tenancy

- Every query MUST be scoped by `farm_id`.
- The `farm_id` is set from `X-Farm-ID` header or user's `current_farm_id`.
- Use a global query scope on all farm-scoped models.
- Never return data from a farm the user does not belong to.

### 5.2 RBAC

| Role | Permissions |
|------|-------------|
| **Owner** | Full CRUD, user management, farm settings |
| **Manager** | Create/edit data, cannot delete, edits are audited |
| **Worker** | View limited data, input new records only |

- Enforce via middleware on ALL API routes.
- Return `403 Forbidden` for unauthorized actions.

### 5.3 API Conventions

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
      "message": "Not enough fish in stock for this sale.",
      "details": { "available": 50, "requested": 100 }
    }
  }
  ```

### 5.4 Frontend Conventions

- Use **Svelte 5 runes** (`$state`, `$derived`, `$effect`) — no Svelte 4 stores.
- Use **native Fetch API** — no Axios.
- Use **LayerChart** for all charts — no Chart.js wrappers.
- Use **shadcn/ui components** where available; build custom only when necessary.
- All API calls go through a central client in `src/lib/api/` that handles auth headers and error parsing.
- Forms use outlined input style (Material Design).
- Primary actions use elevated buttons with shadow.

### 5.5 PDF Generation (Gotenberg)

- Design invoice templates as Blade views in `resources/views/pdf/`.
- Render HTML with Tailwind CSS (or inline styles for reliability).
- Send HTML to Gotenberg via HTTP POST; receive PDF bytes.
- Store generated PDFs in `storage/app/invoices/`.

---

## 6. Code Quality Rules

### PHP
- PSR-12 coding standard
- `declare(strict_types=1);` in every file
- Return type hints on all methods
- PHPStan level 8
- Pest tests for all critical paths
- PHPDoc blocks for public methods

### TypeScript / Svelte
- Strict mode enabled
- ESLint + Prettier
- Component props typed with interfaces

### Never Modify
- `.env` (after initial setup)
- Migration files (after committed)
- `docker-compose.yml` / `docker-compose.prod.yml`
- Production configs
- Passport OAuth keys

---

## 7. Agent Guardrails

### Allowed Commands
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

### Blocked Commands
- `php artisan migrate` on production/staging
- `git push`, `git reset --hard`, `rm -rf`
- `docker compose` on remote servers
- Any command that modifies `.env` or production configs

---

## 8. Localization

- Default language: **English**
- Toggle: visible in app bar (EN / বাংলা)
- Bengali font: **Noto Sans Bengali**
- English font: **Roboto**
- When Bengali is selected: all numbers display as Bengali numerals (০-৯)
- Currency: ৳ symbol always, numerals follow language setting
- Respect system font size for accessibility

---

## 9. UI/UX Principles (Summary)

See `docs/ui-design-system.md` for full design tokens and component specs.

- **Ocean blue color palette**: Primary `#050C9C`, secondary `#3572EF`, accent `#3ABEF9`, light `#A7E6FF`
- **Light + Dark mode** supported
- **Spacious layout**: large touch targets (56x56dp min), breathing room
- **Mobile**: hamburger sidebar navigation
- **Desktop**: top navigation bar
- **Dashboard**: summary cards + quick actions + charts + today's activity
- **Farm switching**: selector on dashboard
- **Tables** for lists (compact, scannable)
- **Skeleton screens** for loading
- **Offline banner** at top when disconnected
- **High contrast mode** for sunlight readability
- **Friendly illustrations** for empty states
- **Photo upload** supported in MVP

---

## 10. Next Steps for Agent

When starting a new session:

1. Read this `DESIGN.md`.
2. Read the relevant OpenSpec from `specs/`.
3. Read `docs/ui-design-system.md` for visual guidance.
4. Implement the feature.
5. Write Pest tests verifying POSTCONDITION and INVARIANT.
6. Run quality checklist before suggesting commit.
