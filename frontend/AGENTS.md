# Frontend AGENTS.md

> Frontend-specific conventions for Deshi Fishery. Supplement to root `AGENTS.md`.

## Tech Stack

- **Framework**: SvelteKit 2.x with Svelte 5 runes mode (`runes: true` in `svelte.config.js`)
- **Styling**: Tailwind CSS v4 with CSS-based theming (`@theme` in `app.css`)
- **Language**: TypeScript strict mode
- **Testing**: Playwright E2E
- **Icons**: Lucide Svelte
- **State**: Svelte 5 runes (`$state`, `$derived`, `$effect`) — no Svelte 4 stores

## Dark Mode Rules (CRITICAL)

### How Dark Mode Works

1. `themeStore` (from `$lib/stores/ui.svelte.ts`) toggles the `.dark` class on `<html>`
2. CSS custom properties in `app.css` switch values when `.dark` is present
3. All components using design tokens automatically adapt

### The Golden Rule

**Never use `bg-white` on cards, containers, or any surface element.**

`bg-white` is white in both light and dark mode. In dark mode, `text-on-surface` becomes white (`#F1F5F9`), so white text on white background = invisible.

### Correct Patterns

| Element | Light Mode | Dark Mode | Token to Use |
|---------|-----------|-----------|--------------|
| Page background | `#F0F9FF` | `#0F172A` | `bg-surface` |
| Cards / elevated surfaces | `#f3fbff` | `#1E293B` | `bg-surface-bright` |
| Input backgrounds | `#FFFFFF` | `#1E293B` | `bg-white dark:bg-surface-container` |
| Hover states | `#d6f2ff` | `#1E293B` | `bg-surface-container` |
| Active states | `#c7eeff` | `#334155` | `bg-surface-container-high` |
| Primary text | `#001f28` | `#F1F5F9` | `text-on-surface` |
| Secondary text | `#4a5568` | `#94A3B8` | `text-on-surface-variant` |
| Borders | `#c6c5d6` | `#334155` | `border-outline-variant` |

### Card Pattern (Always Use This)

```svelte
<!-- CORRECT: Adapts to both light and dark mode -->
<div class="bg-surface-bright border border-outline-variant/30 rounded-xl p-6">
  <h2 class="text-xl font-bold text-on-surface">Title</h2>
  <p class="text-on-surface-variant">Description</p>
</div>

<!-- WRONG: White card with invisible text in dark mode -->
<div class="bg-white border border-outline-variant/30 rounded-xl p-6">
```

### Input Pattern

```svelte
<!-- CORRECT: White in light, dark in dark mode -->
<input
  class="bg-white dark:bg-surface-container border border-outline-variant rounded-lg
         text-on-surface placeholder:text-on-surface-variant/50
         focus:border-secondary focus:ring-2 focus:ring-mist-light"
/>
```

### Footer Pattern

```svelte
<!-- CORRECT -->
<footer class="border-t border-outline-variant/30 bg-surface-bright">

<!-- WRONG -->
<footer class="border-t border-outline-variant/30 bg-white">
```

### Dropdown / Menu Pattern

```svelte
<!-- CORRECT -->
<div class="bg-surface-bright border border-outline-variant/30 rounded-xl shadow-elevated">

<!-- WRONG -->
<div class="bg-white border border-outline-variant/30 rounded-xl shadow-elevated">
```

### What NOT to Use

- `bg-white` on cards, footers, dropdowns, modals, or any container
- `text-slate-*`, `bg-slate-*`, `border-slate-*` — not part of design system
- `bg-blue-600`, `hover:bg-blue-700`, `focus:ring-blue-500` — use `bg-primary-container`, `text-primary-container`
- `dark:bg-surface-dark` on layout — the `.dark` class on `<html>` handles this automatically

### The Only Acceptable Uses of `bg-white`

1. Form inputs with `dark:bg-surface-container`:
   ```svelte
   <input class="bg-white dark:bg-surface-container ..." />
   ```
2. Skip link focus state (accessibility):
   ```svelte
   <a class="focus:bg-white focus:text-primary-container ...">Skip to content</a>
   ```

## Component Architecture

### Shared Auth Components (`$lib/components/auth/`)

All auth pages (login, register, onboarding) MUST use these shared components:

- `AuthCard` — skip link + outer container + `<main>` card + title/subtitle
- `AuthForm` — form wrapper with `aria-busy`
- `AuthInput` — label + input + password visibility toggle + error linking
- `AuthButton` — loading spinner + variant support + disabled states
- `AuthError` — AlertCircle icon + error message banner

Barrel export at `$lib/components/auth/index.ts`.

### Shared Layout Components (`$lib/components/layout/`)

All authenticated pages get these via `app/+layout.svelte`:

- `AppNavbar` — sticky header with:
  - Brand logo + name linking to dashboard
  - Desktop nav links: Dashboard, Farms, Ponds (with `aria-current="page"`)
  - Mobile hamburger menu with slide-down navigation
  - Theme toggle button (Sun/Moon icon)
  - Language toggle button (EN/বাংলা)
  - User profile dropdown with logout
  - Skip link for keyboard accessibility
- `AppFooter` — brand + copyright + quick links

Barrel export at `$lib/components/layout/index.ts`.

### UI State Stores (`$lib/stores/ui.svelte.ts`)

- `themeStore` — light/dark mode with `localStorage` persistence, system preference fallback
- `langStore` — en/bn language toggle with `localStorage` persistence

Both are SSR-safe (check `browser` before accessing `window`/`document`).

## Routing Conventions

- Auth pages: `/app/login`, `/app/register`, `/app/onboarding`, `/app/logout`
- Admin pages: `/app/dashboard`, `/app/farms`, `/app/ponds`, `/app/farms/new`, `/app/farms/[id]/edit`, etc.
- Layout `app/+layout.svelte` handles auth redirects and renders navbar/footer

## API Client Patterns

- Central client at `$lib/api/client.ts`
- Token and farm ID managed via `api.setToken()` and `api.setFarmId()`
- All requests include `Authorization: Bearer {token}` and `X-Farm-ID` headers
- 10-second timeout with `AbortController`

### Auth Store Initialization Race Condition (CRITICAL)

`authStore.init()` runs inside `onMount` in `app/+layout.svelte`. On a hard refresh, child page components execute their `<script>` **before** `init()` completes. This means `api.getFarmId()` returns `null` and the `X-Farm-ID` header is missing, causing the backend to reject the request with "X-Farm-ID header is required."

**NEVER call API methods directly in top-level `<script>` or in `$effect` that only depends on `page.params`.**

**ALWAYS wait for auth initialization before fetching data.** Use a reactive `$effect` that depends on `authStore.isAuthenticated` and `authStore.currentFarmId`:

```svelte
<script lang="ts">
  import { authStore } from '$lib/stores/auth.svelte';
  import { api } from '$lib/api/client';

  let data = $state([]);
  let loading = $state(true);

  async function loadData() {
    try {
      const response = await api.get('/some-resource');
      if (response.success) data = response.data;
    } catch (err) {
      // handle error
    } finally {
      loading = false;
    }
  }

  // CORRECT: waits for auth store to be ready
  $effect(() => {
    if (authStore.isAuthenticated && authStore.currentFarmId) {
      loadData();
    }
  });

  // WRONG: runs before authStore.init() completes on hard refresh
  // loadData();
</script>
```

For detail pages that also depend on `page.params.id`, add the auth guards to the existing `$effect`:

```svelte
$effect(() => {
  const idParam = page.params.id;
  if (idParam && authStore.isAuthenticated && authStore.currentFarmId) {
    itemId = parseInt(idParam, 10);
    loadItem();
  }
});
```

## Form Patterns

- Use `onsubmit` handler with `e.preventDefault()`
- Loading state disables submit button
- Errors displayed via `AuthError` component or inline `bg-error-container` banners
- All inputs: `min-h-[56px]`, `rounded-lg`, `border-outline-variant`
- Primary buttons: `bg-primary-container text-white h-14 min-h-[56px]`
- Secondary buttons: `border border-outline-variant text-on-surface hover:bg-surface-container`

## Build & Test

```bash
cd frontend
pnpm run build     # Must pass with zero errors
pnpm run test      # Playwright E2E tests
```

## Common Mistakes to Avoid

1. **Using `bg-white` on cards** — always use `bg-surface-bright`
2. **Using `slate-*` colors** — not part of design system, won't adapt to dark mode
3. **Using `blue-*` colors** — use `primary-container` or `secondary` tokens
4. **Forgetting `dark:` on inputs** — inputs need `bg-white dark:bg-surface-container`
5. **Adding `dark:` modifiers to layout** — the `.dark` class handles this automatically
6. **Using Svelte 4 stores** — use Svelte 5 runes (`$state`, `$derived`, `$effect`)
7. **Calling API methods in top-level script before auth is ready** — always guard data fetching with `authStore.isAuthenticated && authStore.currentFarmId` inside `$effect`
