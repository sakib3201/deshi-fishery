---
title: UI Design System — Deshi Fishery
tags:
  - design
  - ui-system
  - tokens
  - deshi-fishery
status: active
created: 2026-05-22
related:
  - DESIGN.md
  - UI-UX Design Questionnaire
custom-width: 100
---

# UI Design System — Deshi Fishery

> [!info] Purpose
> Concrete visual design tokens, component patterns, and layout rules. The agent MUST follow these when generating UI code. All values are copy-paste ready for Tailwind CSS.

---

## 1. Color Palette

### Brand Colors (Ocean Blue)

| Token | Hex | Tailwind Class | Usage |
|-------|-----|----------------|-------|
| Token | Hex | Tailwind Class | Usage |
|-------|-----|----------------|-------|
| `--color-primary` | `#000265` | `bg-primary` | Deep primary, hero overlays, footer backgrounds |
| `--color-primary-container` | `#050C9C` | `bg-primary-container` | Primary buttons, active nav, brand accent |
| `--color-secondary` | `#0054cc` | `bg-secondary` | Secondary buttons, links, highlights |
| `--color-secondary-container` | `#2f6dea` | `bg-secondary-container` | Hover states, secondary fills |
| `--color-accent` | `#3ABEF9` | `bg-[#3ABEF9]` | Icons, badges, chart accents, hover states |
| `--color-light` | `#A7E6FF` | `bg-[#A7E6FF]` | Background tints, skeleton shimmer, disabled states |
| `--color-chart-primary` | `#3572EF` | `bg-chart-primary` | Chart primary series |
| `--color-chart-secondary` | `#3ABEF9` | `bg-chart-secondary` | Chart secondary series |
| `--color-success-green` | `#10B981` | `bg-success-green` | Success badges, profit indicators |
| `--color-background` | `#f3fbff` | `bg-background` | Page background |
| `--color-surface` | `#F0F9FF` | `bg-surface` | Card/page surface |
| `--color-surface-container` | `#d6f2ff` | `bg-surface-container` | Elevated surfaces, feature cards |
| `--color-surface-container-high` | `#c7eeff` | `bg-surface-container-high` | Higher elevation surfaces |
| `--color-surface-container-low` | `#e5f6ff` | `bg-surface-container-low` | Lower elevation surfaces |
| `--color-surface-bright` | `#f3fbff` | `bg-surface-bright` | Bright section backgrounds |
| `--color-on-surface` | `#001f28` | `text-on-surface` | Primary text on surfaces |
| `--color-on-surface-variant` | `#4a5568` | `text-on-surface-variant` | Secondary/muted text |
| `--color-outline-variant` | `#c6c5d6` | `border-outline-variant` | Subtle borders and dividers |

### Semantic Colors

| Token | Hex | Tailwind Class | Usage |
|-------|-----|----------------|-------|
| `--color-success` | `#16A34A` | `bg-green-600` | Success messages, paid status, growth up |
| `--color-danger` | `#DC2626` | `bg-red-600` | Errors, unpaid status, mortality alerts, deletions |
| `--color-warning` | `#EAB308` | `bg-yellow-500` | Low stock alerts, pending payments, warnings |
| `--color-info` | `#3572EF` | `bg-[#3572EF]` | Info banners, tips, neutral highlights |

### Neutral Colors

| Token | Hex | Tailwind Class | Usage |
|-------|-----|----------------|-------|
| `--color-white` | `#FFFFFF` | `bg-white` | Card backgrounds, input backgrounds |
| `--color-surface` | `#F0F9FF` | `bg-[#F0F9FF]` | Page background (very light blue tint) |
| `--color-surface-dark` | `#0F172A` | `bg-slate-900` | Dark mode page background |
| `--color-border` | `#BFDBFE` | `border-blue-200` | Borders, dividers |
| `--color-text-primary` | `#0F172A` | `text-slate-900` | Headings, primary text |
| `--color-text-secondary` | `#475569` | `text-slate-600` | Body text, descriptions |
| `--color-text-muted` | `#94A3B8` | `text-slate-400` | Placeholders, disabled text, timestamps |
| `--color-text-on-primary` | `#FFFFFF` | `text-white` | Text on primary/secondary buttons |

### Dark Mode Colors

| Token | Light | Dark |
|-------|-------|------|
| Page background | `#F0F9FF` | `#0F172A` |
| Card background | `#FFFFFF` | `#1E293B` |
| Text primary | `#0F172A` | `#F1F5F9` |
| Text secondary | `#475569` | `#CBD5E1` |
| Border | `#BFDBFE` | `#334155` |
| Surface tint | `#E0F2FE` | `#1E3A5F` |

---

## 2. Typography

### Font Families

```css
/* tailwind.config.ts */
fontFamily: {
  sans: ['Roboto', 'Noto Sans Bengali', 'system-ui', 'sans-serif'],
  bengali: ['Noto Sans Bengali', 'Roboto', 'system-ui', 'sans-serif'],
}
```

- **English**: Roboto (neutral, widely supported)
- **Bengali**: Noto Sans Bengali (Google Fonts, free)
- **Fallback**: system-ui sans-serif

### Type Scale

| Token | Size | Line Height | Weight | Usage |
|-------|------|-------------|--------|-------|
| `text-xs` | 12px / 0.75rem | 1.25rem | 400 | Captions, timestamps, badges |
| `text-sm` | 14px / 0.875rem | 1.375rem | 400 | Body small, helper text |
| `text-base` | 16px / 1rem | 1.5rem | 400 | Body default, inputs |
| `text-lg` | 18px / 1.125rem | 1.75rem | 500 | Subheadings, card titles |
| `text-xl` | 20px / 1.25rem | 1.75rem | 600 | Section headings |
| `text-2xl` | 24px / 1.5rem | 2rem | 700 | Page titles |
| `text-3xl` | 30px / 1.875rem | 2.25rem | 700 | Brand header, dashboard numbers |

### Numerals

- **English mode**: Arabic numerals (0-9)
- **Bengali mode**: Bengali numerals (০-৯) throughout ALL numbers
- Use a formatter utility: `formatNumber(value, locale)`

---

## 3. Spacing

### Base Unit

Base unit: **4px** (Tailwind default)

### Common Spacing Tokens

| Token | Value | Usage |
|-------|-------|-------|
| `space-1` | 4px | Tight gaps, icon padding |
| `space-2` | 8px | Inline element gaps |
| `space-3` | 12px | Small component padding |
| `space-4` | 16px | Standard padding, card internal |
| `space-5` | 20px | Section gaps |
| `space-6` | 24px | Card padding, form section gaps |
| `space-8` | 32px | Page section gaps |
| `space-10` | 40px | Large section separation |
| `space-12` | 48px | Dashboard widget gaps |

### Layout Grid

- **Mobile**: Single column, full-width cards, 16px page padding
- **Tablet (md)**: 2-column grid for cards, 24px page padding
- **Desktop (lg)**: 3-column grid for summary cards, 32px page padding
- **Max content width**: 1280px (`max-w-7xl`)

---

## 4. Components

### 4.1 Buttons

#### Primary Button

```svelte
<button
  class="bg-[#050C9C] text-white px-6 py-3 rounded-lg shadow-md
         hover:bg-[#040a7a] active:scale-[0.98]
         disabled:bg-[#A7E6FF] disabled:text-[#050C9C] disabled:opacity-60
         transition-all duration-150
         min-h-[56px] text-base font-medium"
>
  {label}
</button>
```

- Background: `--color-primary` (`#050C9C`)
- Text: white
- Padding: `px-6 py-3` (24px horizontal, 12px vertical)
- Border radius: `rounded-lg` (8px)
- Shadow: `shadow-md` (elevated)
- Min height: **56px** (touch target)
- Hover: darken 10%
- Active: scale down slightly
- Disabled: light tint with primary text

#### Secondary Button

```svelte
<button
  class="bg-[#3572EF] text-white px-6 py-3 rounded-lg shadow-sm
         hover:bg-[#2a5cd1] active:scale-[0.98]
         transition-all duration-150
         min-h-[56px] text-base font-medium"
>
  {label}
</button>
```

- Background: `--color-secondary` (`#3572EF`)
- Same sizing as primary
- Use for secondary actions (Cancel, Back, Edit)

#### Outline Button

```svelte
<button
  class="border-2 border-[#050C9C] text-[#050C9C] px-6 py-3 rounded-lg
         hover:bg-[#050C9C] hover:text-white
         transition-all duration-150
         min-h-[56px] text-base font-medium"
>
  {label}
</button>
```

- Use for tertiary actions, destructive confirmations

#### Icon Button

```svelte
<button
  class="w-14 h-14 flex items-center justify-center rounded-full
         bg-[#3ABEF9] text-white shadow-sm
         hover:bg-[#2aa8e0] active:scale-[0.95]
         transition-all duration-150"
>
  <Icon name={icon} size={24} />
</button>
```

- Size: 56x56px (touch target)
- Shape: circle for FAB, rounded-lg for toolbar icons

### 4.2 Inputs

#### Text Input (Outlined)

```svelte
<div class="flex flex-col gap-1">
  <label class="text-sm font-medium text-slate-700">{label}</label>
  <input
    type="text"
    class="w-full px-4 py-3 rounded-lg border-2 border-blue-200
           bg-white text-slate-900 text-base
           focus:border-[#3572EF] focus:ring-2 focus:ring-[#A7E6FF]
           focus:outline-none
           placeholder:text-slate-400
           transition-colors duration-150
           min-h-[56px]"
    placeholder={placeholder}
  />
  {#if helper}
    <p class="text-xs text-slate-500">{helper}</p>
  {/if}
</div>
```

- Border: 2px `--color-border`
- Background: white
- Focus: `--color-secondary` border + `--color-light` ring
- Min height: **56px**
- Padding: `px-4 py-3`
- Border radius: `rounded-lg` (8px)

#### Number Input

Same as text input but with:
- `inputmode="numeric"` for mobile numeric keypad
- `pattern="[0-9]*"` for validation
- Right-aligned text for currency/quantity

#### Select / Dropdown

```svelte
<div class="relative">
  <select
    class="w-full px-4 py-3 pr-10 rounded-lg border-2 border-blue-200
           bg-white text-slate-900 text-base
           focus:border-[#3572EF] focus:ring-2 focus:ring-[#A7E6FF]
           focus:outline-none appearance-none
           min-h-[56px]"
  >
    {#each options as option}
      <option value={option.value}>{option.label}</option>
    {/each}
  </select>
  <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
    <ChevronDown size={20} class="text-slate-500" />
  </div>
</div>
```

### 4.3 Cards

#### Summary Card (Dashboard)

```svelte
<div class="bg-white rounded-xl p-6 shadow-sm border border-blue-100
            hover:shadow-md transition-shadow duration-200">
  <div class="flex items-center justify-between mb-3">
    <span class="text-sm font-medium text-slate-500">{title}</span>
    <div class="w-10 h-10 rounded-lg bg-[#A7E6FF] flex items-center justify-center">
      <Icon name={icon} size={20} class="text-[#050C9C]" />
    </div>
  </div>
  <div class="text-3xl font-bold text-slate-900 mb-1">{value}</div>
  {#if trend}
    <div class="flex items-center gap-1 text-sm {trend > 0 ? 'text-green-600' : 'text-red-600'}">
      <TrendingUp size={16} />
      <span>{Math.abs(trend)}%</span>
    </div>
  {/if}
</div>
```

- Background: white
- Border radius: `rounded-xl` (12px)
- Padding: `p-6` (24px)
- Border: subtle `border-blue-100`
- Shadow: `shadow-sm`
- Icon container: 40x40px, `--color-light` background, `--color-primary` icon

#### Data Card (List Item)

```svelte
<div class="bg-white rounded-lg p-4 border border-blue-100
            hover:border-[#3ABEF9] transition-colors duration-150"
>
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-12 h-12 rounded-full bg-[#A7E6FF] flex items-center justify-center">
        <Icon name={icon} size={24} class="text-[#050C9C]" />
      </div>
      <div>
        <div class="font-medium text-slate-900">{title}</div>
        <div class="text-sm text-slate-500">{subtitle}</div>
      </div>
    </div>
    <div class="text-right">
      <div class="font-semibold text-slate-900">{amount}</div>
      <div class="text-xs {statusColor}">{status}</div>
    </div>
  </div>
</div>
```

### 4.4 Tables

```svelte
<div class="bg-white rounded-xl border border-blue-100 overflow-hidden">
  <table class="w-full">
    <thead class="bg-[#F0F9FF]">
      <tr>
        {#each headers as header}
          <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">
            {header}
          </th>
        {/each}
      </tr>
    </thead>
    <tbody class="divide-y divide-blue-100">
      {#each rows as row}
        <tr class="hover:bg-[#F0F9FF] transition-colors">
          <td class="px-4 py-3 text-sm text-slate-900">{row.date}</td>
          <td class="px-4 py-3 text-sm text-slate-900">{row.description}</td>
          <td class="px-4 py-3 text-sm font-medium {row.amount >= 0 ? 'text-green-600' : 'text-red-600'}">
            ৳ {formatNumber(Math.abs(row.amount), locale)}
          </td>
        </tr>
      {/each}
    </tbody>
  </table>
</div>
```

- Container: white, rounded-xl, border
- Header: `--color-surface` background, semibold text
- Rows: divider lines, hover highlight
- Amounts: green for positive, red for negative
- Padding: `px-4 py-3`

### 4.5 Badges

```svelte
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
             {variant === 'paid' ? 'bg-green-100 text-green-800' :
              variant === 'pending' ? 'bg-yellow-100 text-yellow-800' :
              variant === 'unpaid' ? 'bg-red-100 text-red-800' :
              'bg-blue-100 text-blue-800'}"
>
  {label}
</span>
```

| Variant | Background | Text |
|---------|------------|------|
| `paid` | `bg-green-100` | `text-green-800` |
| `pending` | `bg-yellow-100` | `text-yellow-800` |
| `unpaid` | `bg-red-100` | `text-red-800` |
| `info` | `bg-blue-100` | `text-blue-800` |

### 4.6 Charts (LayerChart)

```svelte
<script>
  import { Chart, Svg, Axis, Bars, Line, Area, Pie, Tooltip } from 'layerchart';
</script>

<!-- Bar Chart -->
<Chart data={chartData} x="month" y="sales"
       class="h-64 w-full">
  <Svg>
    <Axis placement="bottom" />
    <Axis placement="left" />
    <Bars radius={4} fill="#3572EF" />
  </Svg>
  <Tooltip />
</Chart>

<!-- Line Chart -->
<Chart data={chartData} x="date" y="weight"
       class="h-64 w-full">
  <Svg>
    <Axis placement="bottom" />
    <Axis placement="left" />
    <Area fill="#A7E6FF" opacity={0.3} />
    <Line stroke="#3572EF" strokeWidth={2} />
  </Svg>
  <Tooltip />
</Chart>

<!-- Pie Chart -->
<Chart data={pieData} x="category" y="amount"
       class="h-64 w-full">
  <Svg>
    <Pie innerRadius={60} />
  </Svg>
  <Tooltip />
</Chart>
```

**Chart Color Palette:**
- Primary series: `#3572EF`
- Secondary series: `#3ABEF9`
- Tertiary series: `#050C9C`
- Accent series: `#A7E6FF`
- Positive trend: `#16A34A`
- Negative trend: `#DC2626`

### 4.7 Empty State

```svelte
<div class="flex flex-col items-center justify-center py-12 px-4 text-center">
  <div class="w-24 h-24 mb-4">
    <!-- Colorful illustration SVG -->
    <EmptyStateIllustration />
  </div>
  <h3 class="text-lg font-semibold text-slate-900 mb-2">{title}</h3>
  <p class="text-sm text-slate-500 mb-6 max-w-xs">{description}</p>
  <button class="bg-[#050C9C] text-white px-6 py-3 rounded-lg shadow-md"
          on:click={onAction}>
    {actionLabel}
  </button>
</div>
```

- Centered layout
- Colorful illustration (not line art)
- Friendly headline + description
- Primary CTA button to add first record

### 4.8 Offline Banner

```svelte
{#if isOffline}
  <div class="bg-yellow-500 text-white px-4 py-2 text-sm font-medium
              flex items-center justify-center gap-2"
  >
    <WifiOff size={16} />
    <span>You are offline. Changes will sync when you reconnect.</span>
  </div>
{/if}
```

- Fixed at top of screen
- Yellow background, white text
- Icon + message
- Dismisses automatically when online

### 4.9 Skeleton Screen

```svelte
<div class="animate-pulse">
  <div class="h-8 bg-[#A7E6FF] rounded-lg w-3/4 mb-4"></div>
  <div class="h-4 bg-[#A7E6FF] rounded w-full mb-2"></div>
  <div class="h-4 bg-[#A7E6FF] rounded w-5/6 mb-2"></div>
  <div class="h-4 bg-[#A7E6FF] rounded w-4/6"></div>
</div>
```

- Use `--color-light` (`#A7E6FF`) for shimmer
- `animate-pulse` for subtle motion
- Rounded rectangles matching content shape

---

## 5. Layout Patterns

### 5.1 Mobile Layout (Hamburger Sidebar)

```
┌─────────────────────────────┐
│ ≡  Deshi Fishery    EN বাংলা │  ← Top app bar
├─────────────────────────────┤
│                             │
│  ┌─────────────────────┐    │
│  │ Summary Cards       │    │  ← 2-column grid
│  │ [Income] [Expenses] │    │
│  └─────────────────────┘    │
│                             │
│  ┌─────────────────────┐    │
│  │ Quick Actions       │    │
│  │ [+Sale] [+Expense]  │    │
│  └─────────────────────┘    │
│                             │
│  ┌─────────────────────┐    │
│  │ Today's Activity    │    │
│  │ • Feed given...     │    │
│  │ • Sale recorded...  │    │
│  └─────────────────────┘    │
│                             │
│  ┌─────────────────────┐    │
│  │ Charts              │    │
│  │ [Line chart]        │    │
│  └─────────────────────┘    │
│                             │
└─────────────────────────────┘
```

- Top app bar: hamburger menu, brand name, language toggle
- Content: single column, 16px padding
- Cards: full width, stacked vertically
- Bottom: safe area padding for gesture navigation

### 5.2 Desktop Layout (Top Navigation)

```
┌─────────────────────────────────────────────────────────────┐
│ Deshi Fishery    Dashboard  Sales  Expenses  Reports  [EN]  │  ← Top nav
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐       │
│  │ Income   │ │ Expenses │ │ Balance  │ │ Stock    │       │  ← 4-col cards
│  │ ৳ 45,000 │ │ ৳ 12,500 │ │ ৳ 32,500│ │ 850 kg   │       │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘       │
│                                                             │
│  ┌────────────────────────┐ ┌────────────────────────┐     │
│  │ Quick Actions          │ │ Today's Activity       │     │
│  │ [+ Sale] [+ Expense]   │ │ • 8:00 AM - Feed...    │     │
│  │ [+ Stock] [+ Medicine] │ │ • 10:30 AM - Sale...   │     │
│  └────────────────────────┘ └────────────────────────┘     │
│                                                             │
│  ┌────────────────────────────────────────────────────┐    │
│  │ Charts                                             │    │
│  │ [Line chart] [Pie chart] [Bar chart]               │    │
│  └────────────────────────────────────────────────────┘    │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

- Top navigation bar with links
- 3-4 column grid for summary cards
- 2-column layout for quick actions + activity
- Full-width charts section
- Max width: 1280px, centered

### 5.3 Sidebar Navigation (Mobile Hamburger)

```
┌─────────────────────────────────────┐
│  🐟 Deshi Fishery                   │
│  মাছ চাষের সেরা assistant          │
├─────────────────────────────────────┤
│  🏠 Dashboard                       │
│  💰 Sales                           │
│  📦 Stock                           │
│  🌾 Feed                            │
│  💊 Medicine                        │
│  💸 Expenses                        │
│  👥 Partners                        │
│  📊 Reports                         │
├─────────────────────────────────────┤
│  ⚙️ Settings                        │
│  🌐 Language: English               │
│  👤 Profile                         │
│  🚪 Logout                          │
└─────────────────────────────────────┘
```

- Slide-in from left
- Brand header with icon + tagline
- Navigation items with icons
- Separator before settings/logout
- Current page highlighted with `--color-primary` background tint

---

## 6. Form Patterns

### 6.1 Add Sale Form

```
┌─────────────────────────────┐
│ ← New Sale                  │
├─────────────────────────────┤
│                             │
│  Pond *                     │
│  ┌─────────────────────┐    │
│  │ Select pond...    ▼ │    │
│  └─────────────────────┘    │
│                             │
│  Species *                  │
│  ┌─────────────────────┐    │
│  │ Enter species...    │    │
│  └─────────────────────┘    │
│                             │
│  Quantity (kg) *            │
│  ┌─────────────────────┐    │
│  │ 0.00                │    │
│  └─────────────────────┘    │
│                             │
│  Rate per kg (৳) *          │
│  ┌─────────────────────┐    │
│  │ 0.00                │    │
│  └─────────────────────┘    │
│                             │
│  Total: ৳ 0.00              │
│                             │
│  Customer Name              │
│  ┌─────────────────────┐    │
│  │ Optional            │    │
│  └─────────────────────┘    │
│                             │
│  [💾 Save Sale]             │
│                             │
└─────────────────────────────┘
```

- Required fields marked with `*`
- Real-time total calculation
- Large tap targets
- Sticky save button at bottom (optional)

### 6.2 Form Validation

- Inline error messages below fields
- Error color: `--color-danger` (`#DC2626`)
- Error icon: `AlertCircle` from Lucide
- Shake animation on submit with errors

```svelte
{#if error}
  <div class="flex items-center gap-1 mt-1">
    <AlertCircle size={14} class="text-red-600" />
    <span class="text-xs text-red-600">{error}</span>
  </div>
{/if}
```

---

## 7. Currency Formatting

```typescript
// src/lib/utils/formatters.ts

export function formatCurrency(amount: number, locale: 'en' | 'bn'): string {
  const formatter = new Intl.NumberFormat(locale === 'bn' ? 'bn-BD' : 'en-BD', {
    style: 'currency',
    currency: 'BDT',
    minimumFractionDigits: 2,
  });
  return formatter.format(amount);
}

export function formatNumber(value: number, locale: 'en' | 'bn'): string {
  return new Intl.NumberFormat(locale === 'bn' ? 'bn-BD' : 'en-BD').format(value);
}

export function formatDate(date: Date, locale: 'en' | 'bn'): string {
  return new Intl.DateTimeFormat(locale === 'bn' ? 'bn-BD' : 'en-GB', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  }).format(date);
}
```

- Bengali mode: `৳ ১,২৫০.০০`
- English mode: `৳ 1,250.00`
- Always use BDT symbol (৳)

---

## 8. Responsive Breakpoints

```typescript
// tailwind.config.ts
screens: {
  'sm': '640px',   // Large phones
  'md': '768px',   // Tablets
  'lg': '1024px',  // Small laptops
  'xl': '1280px',  // Desktops
}
```

| Breakpoint | Layout Changes |
|------------|----------------|
| < 640px (mobile) | Single column, hamburger nav, full-width cards |
| 640-768px (large phone) | 2-column card grid |
| 768-1024px (tablet) | 2-column layout, sidebar nav optional |
| > 1024px (desktop) | 3-4 column cards, top nav, max-width container |

---

## 9. Animation Tokens

```css
/* tailwind.config.ts */
transitionDuration: {
  'fast': '100ms',
  'normal': '150ms',
  'slow': '200ms',
}
transitionTimingFunction: {
  'default': 'cubic-bezier(0.4, 0, 0.2, 1)',
  'bounce': 'cubic-bezier(0.34, 1.56, 0.64, 1)',
}
```

| Animation | Duration | Usage |
|-----------|----------|-------|
| Button press | 100ms | Active state scale |
| Hover | 150ms | Color/shadow changes |
| Page transition | 200ms | Route changes |
| Skeleton pulse | 2000ms | Loading shimmer |
| Chart render | 500ms | LayerChart animation |

---

## 10. Dark Mode Implementation

```svelte
<!-- src/app.html or layout -->
<html class="dark">
  <body class="bg-[#F0F9FF] dark:bg-slate-900 text-slate-900 dark:text-slate-100">
    <slot />
  </body>
</html>
```

Use Tailwind's `dark:` prefix everywhere:

```svelte
<div class="bg-white dark:bg-slate-800
            border-blue-100 dark:border-slate-700
            text-slate-900 dark:text-slate-100"
>
```

Toggle via class on `html` element:

```typescript
function toggleDarkMode() {
  document.documentElement.classList.toggle('dark');
  localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
}
```

---

## 11. Icon Mapping

| Feature | Lucide Icon | Size |
|---------|-------------|------|
| Dashboard | `LayoutDashboard` | 20px |
| Sales | `Banknote` | 20px |
| Stock | `Fish` | 20px |
| Feed | `Wheat` | 20px |
| Medicine | `Pill` | 20px |
| Expenses | `Receipt` | 20px |
| Partners | `Users` | 20px |
| Reports | `BarChart3` | 20px |
| Settings | `Settings` | 20px |
| Add | `Plus` | 24px |
| Edit | `Pencil` | 16px |
| Delete | `Trash2` | 16px |
| Search | `Search` | 20px |
| Filter | `Filter` | 20px |
| Calendar | `Calendar` | 20px |
| Download | `Download` | 20px |
| Offline | `WifiOff` | 16px |
| Success | `CheckCircle2` | 20px |
| Error | `AlertCircle` | 20px |
| Warning | `AlertTriangle` | 20px |
| Info | `Info` | 20px |
| Menu | `Menu` | 24px |
| Close | `X` | 20px |
| Chevron | `ChevronRight` / `ChevronDown` | 16px |
| Trend Up | `TrendingUp` | 16px |
| Trend Down | `TrendingDown` | 16px |
| Language | `Globe` | 20px |

---

## 12. Photo Upload Pattern

```svelte
<div class="border-2 border-dashed border-blue-200 rounded-xl p-8
            flex flex-col items-center justify-center gap-3
            hover:border-[#3572EF] hover:bg-[#F0F9FF]
            transition-colors cursor-pointer"
     on:click={triggerFileInput}
>
  <Camera size={32} class="text-slate-400" />
  <span class="text-sm text-slate-500">Tap to upload photo</span>
  <span class="text-xs text-slate-400">JPG, PNG up to 5MB</span>
</div>
```

- Dashed border, rounded-xl
- Camera icon + instructions
- Hover: solid border + tinted background
- Accept: JPG, PNG, max 5MB

---

## 13. Onboarding Screens

### Screen 1: Welcome

```
┌─────────────────────────────┐
│                             │
│      [Fish illustration]    │
│                             │
│   Welcome to Deshi Fishery  │
│                             │
│   মাছ চাষের সেরা assistant │
│                             │
│   Manage your ponds, track  │
│   sales, and grow your      │
│   business — all in one     │
│   place.                    │
│                             │
│        [Get Started →]      │
│                             │
└─────────────────────────────┘
```

### Screen 2: Features

```
┌─────────────────────────────┐
│                             │
│   [Chart illustration]      │
│                             │
│   Track Everything          │
│                             │
│   Monitor stock, feed,      │
│   medicine, and expenses    │
│   with easy-to-read charts  │
│   and reports.              │
│                             │
│        [Next →]             │
│                             │
└─────────────────────────────┘
```

### Screen 3: Offline First

```
┌─────────────────────────────┐
│                             │
│   [Offline illustration]    │
│                             │
│   Works Offline             │
│                             │
│   Record data even without  │
│   internet. Everything      │
│   syncs automatically       │
│   when you're back online.  │
│                             │
│        [Start Using App]    │
│                             │
└─────────────────────────────┘
```

- Full-screen colorful illustrations
- Bold headline + description
- Single CTA button at bottom
- Swipe or tap to advance
- Skip option available

---

## 14. Error Message Pattern

```svelte
<div class="bg-red-50 border border-red-200 rounded-lg p-4">
  <div class="flex items-start gap-3">
    <AlertCircle size={20} class="text-red-600 mt-0.5" />
    <div class="flex-1">
      <h4 class="font-medium text-red-800">{error.title}</h4>
      <p class="text-sm text-red-700 mt-1">{error.message}</p>
      {#if error.details}
        <details class="mt-2">
          <summary class="text-xs text-red-600 cursor-pointer">Technical details</summary>
          <pre class="mt-1 text-xs text-red-600 bg-red-100 p-2 rounded">{error.details}</pre>
        </details>
      {/if}
    </div>
  </div>
</div>
```

- Friendly headline (user-facing)
- Specific message (actionable)
- Expandable technical details (for debugging)
- Red color scheme

---

## 15. Tailwind Config Summary

```typescript
// tailwind.config.ts
import type { Config } from 'tailwindcss';

export default {
  content: ['./src/**/*.{html,js,svelte,ts}'],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        brand: {
          primary: '#050C9C',
          secondary: '#3572EF',
          accent: '#3ABEF9',
          light: '#A7E6FF',
        },
        surface: {
          DEFAULT: '#F0F9FF',
          dark: '#0F172A',
          container: '#d6f2ff',
          'container-high': '#c7eeff',
          'container-low': '#e5f6ff',
          bright: '#f3fbff',
        },
        primary: {
          DEFAULT: '#000265',
          container: '#050c9c',
        },
        secondary: {
          DEFAULT: '#0054cc',
          container: '#2f6dea',
        },
        'on-surface': {
          DEFAULT: '#001f28',
          variant: '#4a5568',
        },
        background: '#f3fbff',
        'on-background': '#001f28',
        'outline-variant': '#c6c5d6',
        'success-green': '#10B981',
        'on-primary': '#ffffff',
        'on-secondary': '#ffffff',
        'chart-primary': '#3572EF',
        'chart-secondary': '#3ABEF9',
      },
      fontFamily: {
        sans: ['Roboto', 'Noto Sans Bengali', 'system-ui', 'sans-serif'],
        bengali: ['Noto Sans Bengali', 'Roboto', 'system-ui', 'sans-serif'],
        headline: ['Roboto', 'system-ui', 'sans-serif'],
        body: ['Roboto', 'system-ui', 'sans-serif'],
        label: ['Noto Sans Bengali', 'Roboto', 'system-ui', 'sans-serif'],
      },
      screens: {
        sm: '640px',
        md: '768px',
        lg: '1024px',
        xl: '1280px',
      },
      spacing: {
        base: '4px',
        xs: '8px',
        sm: '16px',
        md: '24px',
        lg: '32px',
        xl: '48px',
        'margin-desktop': '40px',
        'margin-mobile': '16px',
        gutter: '16px',
        touch: '56px',
      },
      borderRadius: {
        DEFAULT: '0.25rem',
        lg: '0.5rem',
        xl: '0.75rem',
        '2xl': '1rem',
        '3xl': '1.5rem',
        full: '9999px',
      },
    },
  },
  plugins: [],
} satisfies Config;
```

---

## 16. Landing Page Patterns

### 16.1 Hero Section

- Full-width background image with gradient overlay (`bg-gradient-to-r from-primary-container/80 to-transparent`)
- Left-aligned content with Bengali headline + English accent
- Trust badge with icon (e.g., `Verified`) above headline
- Two CTAs: primary (Get Started) + secondary (Watch Demo)
- Right side: glass-card dashboard preview with mock chart data
- Touch targets: 56px min-height on all buttons

### 16.2 Feature Sections

- Offline feature: icon block + headline + bullet list with `CheckCircle` icons
- Feature cards: bento grid layout (`md:grid-cols-12`) with varying spans
- Cards use surface colors (`bg-white`, `bg-primary-container`, `bg-surface-container`, `bg-surface-container-high`)
- Tags/badges for report formats (`PDF`, `EXCEL`, `SYNC`)

### 16.3 Language Toggle

- Two pill buttons: English / বাংলা
- Active state: `bg-primary-container text-white`
- Inactive state: `bg-white border border-outline-variant text-on-surface`
- Used in CTA section and footer

### 16.4 Footer

- `bg-primary` (deep navy) with white text
- Three-column grid on desktop, stacked on mobile
- Quick links + Accessibility columns
- Copyright + brand tagline

---

## 17. Component Checklist for Agent

When generating UI code, verify:

- [ ] Colors use brand palette (not arbitrary hex values)
- [ ] Touch targets are minimum 56x56px
- [ ] Text uses defined type scale
- [ ] Dark mode variants included (`dark:`)
- [ ] Bengali font family applied where needed
- [ ] Currency formatted with `formatCurrency()`
- [ ] Numbers formatted with `formatNumber()`
- [ ] Lucide icons used (not emojis or other sets)
- [ ] Loading states handled (skeleton or spinner)
- [ ] Error states handled with error pattern
- [ ] Empty states handled with empty state pattern
- [ ] Responsive classes applied (`sm:`, `md:`, `lg:`)
- [ ] Accessibility: focus rings, aria-labels where needed
- [ ] Landing page sections follow hero → features → bento grid → CTA → footer pattern
- [ ] Navigation links use real routes (`/dashboard`, `/ponds`, `/inventory`, `/reports`) not `#`
