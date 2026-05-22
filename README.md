# 🐟 Deshi Fishery

> **মাছ চাষের সেরা assistant** — "The best assistant for fish farming"

A modern, offline-first fisheries management platform built for Bangladeshi fish farmers. Track stock, sales, feed, medicine, expenses, and partners across multiple ponds and farms — all from your smartphone.

[![Laravel 13](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel)](https://laravel.com)
[![Svelte 5](https://img.shields.io/badge/Svelte-5-FF3E00?logo=svelte)](https://svelte.dev)
[![Tailwind CSS v4](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?logo=tailwindcss)](https://tailwindcss.com)
[![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)](https://php.net)
[![PostgreSQL 17](https://img.shields.io/badge/PostgreSQL-17-4169E1?logo=postgresql)](https://postgresql.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 🌊 What is Deshi Fishery?

**Deshi Fishery** is a multi-pond fisheries management platform designed specifically for fish farm owners, managers, and field workers in rural Bangladesh.

### Key Features

- 🏡 **Multi-Farm Management** — Own and switch between multiple farms
- 🌊 **Pond Tracking** — Manage unlimited ponds per farm
- 📦 **Stock Management** — Track fry releases, growth, mortality, and harvests
- 💰 **Sales & Payments** — Record wholesale/retail sales with partial payment tracking
- 🥘 **Feed & Medicine** — Inventory tracking with consumption logs
- 📊 **Dashboard & Reports** — Visualize data with charts and operational reports
- 💵 **Expense Tracking** — General expenses, fuel, electricity, and staff salaries
- 🤝 **Partner Accounting** — Investment tracking and profit distribution
- 🌐 **Bilingual** — English and Bengali (Bangla) with Bengali numerals
- 📱 **Offline-First PWA** — Works without internet; syncs when connected
- 🎨 **Beautiful UI** — Ocean-blue palette, mobile-first, accessible design

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      CLIENT LAYER                           │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐  │
│  │   Web App   │  │  Mobile PWA │  │   Offline Cache     │  │
│  │  (Svelte 5) │  │  (Svelte 5) │  │  (ElectricSQL+      │  │
│  │             │  │             │  │   PGlite WASM)      │  │
│  └──────┬──────┘  └──────┬──────┘  └─────────────────────┘  │
└─────────┼────────────────┼───────────────────────────────────┘
          │                │
          ▼                ▼
┌─────────────────────────────────────────────────────────────┐
│                      API LAYER                              │
│  ┌─────────────────────────────────────────────────────────┐│
│  │              Laravel 13 API (JSON-only)                 ││
│  │  • REST API  /api/v1/                                   ││
│  │  • OAuth 2.0 (Laravel Passport)                         ││
│  │  • Social Login (Google, Facebook)                      ││
│  │  • RBAC (Owner / Manager / Worker)                      ││
│  │  • Multi-tenancy (farm_id scoped)                       ││
│  └─────────────────────────────────────────────────────────┘│
└──────────────────────────┬──────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────┐
│                   INFRASTRUCTURE LAYER                      │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐  │
│  │  PostgreSQL │  │    Redis    │  │    Gotenberg        │  │
│  │     17      │  │  (Queue/    │  │  (PDF Generation)   │  │
│  │             │  │   Cache/    │  │                     │  │
│  │             │  │   Session)  │  │                     │  │
│  └─────────────┘  └─────────────┘  └─────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | Laravel 13 + PHP 8.3 |
| **Frontend** | SvelteKit (Svelte 5 runes mode) + Tailwind CSS v4 |
| **UI Components** | shadcn/ui |
| **Charts** | LayerChart (Svelte-native, D3-based) |
| **Database** | PostgreSQL 17 (production), SQLite (local dev) |
| **Cache/Queue** | Redis |
| **Auth** | Laravel Passport (OAuth 2.0) + Socialite |
| **PDF** | Gotenberg (Dockerized HTML-to-PDF) |
| **Offline Sync** | ElectricSQL + PGlite (WASM PostgreSQL in browser) |
| **Container** | Docker Compose |
| **Hosting** | Local Bangladeshi VPS (Ubuntu 24.04) |

---

## 📁 Project Structure

```
deshi-fishery/
├── backend/                    # Laravel 13 API
│   ├── app/
│   │   ├── Http/Controllers/Api/V1/   # API controllers
│   │   ├── Models/                    # Eloquent models
│   │   ├── Services/                  # Business logic
│   │   └── Policies/                  # Authorization
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   │   └── api.php             # API routes only
│   ├── tests/
│   │   └── Pest.php            # Pest tests
│   ├── resources/
│   │   └── views/pdf/          # Blade templates for PDF generation
│   └── composer.json
├── frontend/                   # SvelteKit SPA
│   ├── src/
│   │   ├── lib/
│   │   │   ├── components/     # Reusable Svelte components
│   │   │   │   └── ui/         # shadcn/ui components
│   │   │   ├── stores/         # Svelte 5 runes-based state
│   │   │   ├── api/            # API client functions
│   │   │   └── utils/          # Helpers, formatters
│   │   ├── routes/             # SvelteKit routes
│   │   ├── app.html            # HTML entry point
│   │   └── app.css             # Global styles + Tailwind
│   ├── static/                 # PWA manifest, icons, service worker
│   ├── tests/e2e/              # Playwright E2E tests
│   └── package.json
├── specs/                      # OpenSpec documents (spec-driven dev)
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
├── docker/                     # Docker configuration
│   ├── Dockerfile
│   ├── nginx.conf
│   └── php.ini
├── docker-compose.yml          # Local development
├── docker-compose.prod.yml     # Production deployment
├── bruno/                      # API documentation collections
├── docs/                       # Design system & architecture docs
│   ├── ui-design-system.md
│   └── sprint-progress.md
├── .opencode/                  # Agent instructions & harness config
├── DESIGN.md                   # Single source of truth
├── AGENTS.md                   # Agent-focused project context
└── README.md                   # This file
```

---

## 🚀 Quick Start

### Prerequisites

- PHP 8.3+
- Composer
- Node.js 20+ & pnpm
- Docker & Docker Compose (optional, for full stack)
- PostgreSQL 17 (or use SQLite for local dev)

### Backend Setup

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations and seeders
php artisan migrate --seed

# Start development server (API + queue + Vite concurrently)
composer run dev
```

The API will be available at `http://localhost:8000/api/v1/`

### Frontend Setup

```bash
cd frontend

# Install dependencies
pnpm install

# Start development server
pnpm run dev
```

The frontend will be available at `http://localhost:5173`

### Docker Compose (Full Stack)

```bash
# Start all services
docker compose up -d

# Services:
# - API:       http://localhost:8080/api/v1/health
# - Frontend:  http://localhost:5173
# - Gotenberg: http://localhost:3000
```

---

## 🧪 Testing

### Backend Tests (Pest)

```bash
cd backend
composer run test          # Runs: php artisan config:clear + php artisan test
```

### Frontend Tests (Playwright)

```bash
cd frontend
pnpm run test              # E2E critical flows: login, offline sync, add sale, add expense
```

### Lint & Format

```bash
# Backend
cd backend
vendor/bin/pint            # Laravel Pint (PHP code style)

# Frontend
cd frontend
pnpm run lint              # Prettier + ESLint
pnpm run check             # svelte-check + TypeScript strict
```

---

## 📝 Development Conventions

### API Conventions

- **Base path**: `/api/v1/`
- **Resources**: plural nouns (`/ponds`, `/sales`, `/expenses`)
- **Pagination**: cursor-based (`?cursor=xyz&per_page=20`)
- **Filtering**: simple query params (`?status=paid&pond_id=1`)
- **Error envelope**:
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

### Multi-Tenancy

- Every query **MUST** be scoped by `farm_id`
- `farm_id` comes from `X-Farm-ID` header or user's `current_farm_id`
- Never return data from a farm the user does not belong to

### RBAC

| Role | Permissions |
|------|-------------|
| **Owner** | Full CRUD, user management, farm settings |
| **Manager** | Create/edit data, cannot delete, edits audited |
| **Worker** | View limited data, input new records only |

### Frontend Conventions

- Use **Svelte 5 runes** (`$state`, `$derived`, `$effect`) — no Svelte 4 stores
- Use **native Fetch API** — no Axios
- Use **LayerChart** for all charts
- Use **shadcn/ui** components where available
- All API calls go through `src/lib/api/`
- Currency: BDT (৳) with Bengali numerals when Bangla is selected

### Code Quality

- **PHP**: PSR-12, `declare(strict_types=1);`, return type hints, PHPStan level 8
- **TypeScript**: Strict mode, ESLint + Prettier
- **Tests**: Pest (backend), Playwright (E2E)

---

## 🔄 Spec-Driven Development (OpenSpec)

Every feature is specified in `specs/` before any code is written.

### Spec Format

```
SPEC {ID}  {Title}
  {Description of observable behaviour}

  PRECONDITION:  {what must be true before}
  POSTCONDITION: {what is guaranteed after}
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

### Workflow

1. **`/opsx propose`** — Generate spec + design + tasks for the feature
2. **`/opsx explore`** — Think through edge cases and clarify requirements
3. **Write spec** in `specs/{module}.md`
4. **`/opsx apply`** — Implement code that satisfies the spec
5. **Verify** with tests asserting POSTCONDITION and INVARIANT
6. **`/opsx archive`** — Finalize and archive the completed change
7. **Commit** spec + code together

---

## 🤖 OpenCode Agent Harness Setup

This project uses [OpenCode](https://github.com/your-org/opencode) for AI-assisted development.

### Setup Harness

1. **Install OpenCode** (if not already installed):
   ```bash
   # Follow the OpenCode installation guide for your platform
   ```

2. **Configure the agent**:
   The project includes agent instructions in `.opencode/` and context files (`AGENTS.md`, `DESIGN.md`).

3. **Start a session**:
   ```bash
   # OpenCode will automatically load AGENTS.md and DESIGN.md
   # for context on every session
   ```

### Available OpenSpec Commands

| Command | Purpose |
|---------|---------|
| `/opsx propose` | Propose a new change with spec + design + tasks |
| `/opsx explore` | Explore ideas, edge cases, and requirements |
| `/opsx apply` | Implement tasks from an OpenSpec change |
| `/opsx archive` | Archive a completed change |

### Agent Principles

1. **Think Before Coding** — State assumptions explicitly. Ask when uncertain.
2. **Simplicity First** — Minimum code that solves the problem. Nothing speculative.
3. **Surgical Changes** — Touch only what you must. Match existing style.
4. **Goal-Driven Execution** — Define success criteria. Loop until verified.
5. **Add Local Context** — Update `AGENTS.md` files when patterns change.

---

## 🎨 UI/UX Design

- **Ocean Blue Palette**: Primary `#050C9C`, Secondary `#3572EF`, Accent `#3ABEF9`, Light `#A7E6FF`
- **Light + Dark mode** supported
- **Mobile-first**: 56x56dp minimum touch targets
- **Bengali font**: Noto Sans Bengali
- **English font**: Roboto
- **High contrast mode** for sunlight readability

See [`docs/ui-design-system.md`](docs/ui-design-system.md) for full design tokens and component specs.

---

## 📊 Project Status

| Phase | Progress | Status |
|-------|----------|--------|
| Requirements Gathering | 100% | ✅ Complete |
| Technical Architecture | 100% | ✅ Complete |
| Frontend Initialization | 100% | ✅ Complete |
| UI/UX Design | 0% | 🔄 Ready to start |
| Implementation | 15% | 🔄 In Progress (Sprint 1) |
| Testing & Deployment | 0% | ⏳ Pending |

**Current Sprint**: Sprint 1 — Foundation
- ✅ `AUTH-001` Email/Password Auth (archived)
- 🔄 `AUTH-004` Multi-Farm Tenancy (next up)
- ⏳ `FARM-001` Farm CRUD (blocked)
- ⏳ `POND-001` Pond CRUD (blocked)

See [`docs/sprint-progress.md`](docs/sprint-progress.md) for detailed feature status.

---

## 📄 License

This project is open-sourced under the [MIT License](LICENSE).

---

## 🙏 Acknowledgements

Built with love for Bangladeshi fish farmers. 🐟🇧🇩

---

<p align="center">
  <strong>Deshi Fishery</strong> — মাছ চাষের সেরা assistant
</p>
