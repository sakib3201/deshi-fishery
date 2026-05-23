---
name: Deshi Fishery
description: Fisheries management platform for Bangladeshi fish farmers
colors:
  deep-monsoon-navy: "#000265"
  primary-container: "#050C9C"
  secondary-blue: "#3572EF"
  sky-accent: "#3ABEF9"
  mist-light: "#A7E6FF"
  surface-base: "#F0F9FF"
  surface-container: "#d6f2ff"
  surface-container-high: "#c7eeff"
  surface-container-low: "#e5f6ff"
  surface-bright: "#f3fbff"
  text-primary: "#001f28"
  text-muted: "#4a5568"
  border-subtle: "#c6c5d6"
  success-green: "#10B981"
  danger-red: "#DC2626"
  warning-amber: "#EAB308"
  white: "#FFFFFF"
typography:
  display:
    fontFamily: "'Roboto', 'Noto Sans Bengali', system-ui, sans-serif"
    fontSize: "clamp(1.875rem, 5vw, 3rem)"
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: "normal"
  headline:
    fontFamily: "'Roboto', 'Noto Sans Bengali', system-ui, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 700
    lineHeight: 1.3
    letterSpacing: "normal"
  title:
    fontFamily: "'Roboto', 'Noto Sans Bengali', system-ui, sans-serif"
    fontSize: "1.125rem"
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: "normal"
  body:
    fontFamily: "'Roboto', 'Noto Sans Bengali', system-ui, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: "normal"
  label:
    fontFamily: "'Noto Sans Bengali', 'Roboto', system-ui, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 500
    lineHeight: 1.375
    letterSpacing: "normal"
rounded:
  sm: "4px"
  md: "8px"
  lg: "12px"
  xl: "16px"
  "2xl": "24px"
  full: "9999px"
spacing:
  xs: "8px"
  sm: "16px"
  md: "24px"
  lg: "32px"
  xl: "48px"
  touch: "56px"
components:
  button-primary:
    backgroundColor: "{colors.primary-container}"
    textColor: "{colors.white}"
    rounded: "{rounded.md}"
    padding: "12px 24px"
  button-primary-hover:
    backgroundColor: "#040a7a"
  button-secondary:
    backgroundColor: "{colors.secondary-blue}"
    textColor: "{colors.white}"
    rounded: "{rounded.md}"
    padding: "12px 24px"
  button-outline:
    backgroundColor: "transparent"
    textColor: "{colors.primary-container}"
    rounded: "{rounded.md}"
    padding: "12px 24px"
  button-outline-hover:
    backgroundColor: "{colors.primary-container}"
    textColor: "{colors.white}"
  card-summary:
    backgroundColor: "{colors.white}"
    textColor: "{colors.text-primary}"
    rounded: "{rounded.lg}"
    padding: "24px"
  card-data:
    backgroundColor: "{colors.white}"
    textColor: "{colors.text-primary}"
    rounded: "{rounded.md}"
    padding: "16px"
  input-outlined:
    backgroundColor: "{colors.white}"
    textColor: "{colors.text-primary}"
    rounded: "{rounded.md}"
    padding: "12px 16px"
---

# Design System: Deshi Fishery

## 1. Overview

**Creative North Star: "The Fisherman's Companion"**

Deshi Fishery is designed to feel like a trusted partner standing beside a fish farmer at the pond's edge. The interface is warm, unhurried, and rooted in the rural Bangladeshi context where it is used. Every element is sized for real hands, real sunlight, and real urgency. There is no corporate sterility here, no futuristic gloss. The design speaks the language of ledgers, water, and weather: clear numbers, solid surfaces, and a palette drawn from the monsoon sky.

The system rejects everything that would alienate a 50-year-old farm owner using an entry-level Android phone outdoors. No tiny fields. No dense tables without hierarchy. No neon accents, sci-fi gradients, or glassmorphism as decoration. No government-form density. The interface must feel like a natural extension of the farm itself: dependable, local, and immediately understood.

**Key Characteristics:**
- Light mode by default for daylight readability; dark mode available for nighttime use
- 56px minimum touch targets on all interactive elements
- Ocean blue palette rooted in water and sky, never cold or corporate
- Bengali and English bilingual support with Bengali numerals in Bangla mode
- Offline-first: the app feels fully functional without connectivity
- Progressive disclosure: the essential number or action first, details one tap away

## 2. Colors

The palette is built around Deep Monsoon Navy, a color family that carries authority without coldness. It evokes the sky before a Bangladeshi monsoon: deep, trustworthy, and intimately familiar to anyone who works outdoors.

### Primary
- **Deep Monsoon Navy** (`#000265`): The deepest anchor. Used for hero overlays, footer backgrounds, and moments that demand gravitas. Rare; its scarcity is the point.
- **Primary Container** (`#050C9C`): The workhorse primary. Buttons, active navigation states, brand accents. Solid and dependable.

### Secondary
- **Secondary Blue** (`#3572EF`): Links, secondary buttons, chart primary series, highlights. Brighter than the primary but still restrained. Used for interactive cues.
- **Secondary Container** (`#2f6dea`): Hover states for secondary elements, filled secondary actions.

### Tertiary
- **Sky Accent** (`#3ABEF9`): Icons, badges, chart accents, hover border highlights. The lightest saturated tone. Used sparingly for energy and focus.
- **Mist Light** (`#A7E6FF`): Background tints, skeleton shimmer, disabled states, icon container backgrounds. The palest breath of the palette.

### Neutral
- **Surface Base** (`#F0F9FF`): The default page background. A very light blue tint that keeps the interface airy without sterile white.
- **Surface Bright** (`#f3fbff`): Brighter section backgrounds, elevated cards on surface base.
- **Surface Container** (`#d6f2ff`), **Surface Container High** (`#c7eeff`), **Surface Container Low** (`#e5f6ff`): Tonal layering for cards, feature sections, and hover states. No shadows needed when tonal elevation does the work.
- **Text Primary** (`#001f28`): Headings, primary text. Near-black with a subtle cool tint.
- **Text Muted** (`#4a5568`): Body text, descriptions, secondary labels.
- **Border Subtle** (`#c6c5d6`): Dividers, outlines, subtle borders.
- **White** (`#FFFFFF`): Card backgrounds, input backgrounds. Never the page background.

### Semantic
- **Success Green** (`#10B981`): Profit indicators, paid status, positive trends.
- **Danger Red** (`#DC2626`): Errors, unpaid status, mortality alerts, deletions.
- **Warning Amber** (`#EAB308`): Low stock alerts, pending payments, offline banners.

### Named Rules
**The One Voice Rule.** The primary accent (`#050C9C`) is used on no more than 10% of any given screen. Its rarity signals importance. If everything is primary, nothing is.

**The No Pure White Rule.** The page background is never `#FFFFFF`. Always use `Surface Base` (`#F0F9FF`) or a tonal variant. Pure white is reserved for cards and inputs to create lift through contrast.

## 3. Typography

**Display / Body / Label Font:** Roboto, with Noto Sans Bengali (Google Fonts, free)
**Fallback:** system-ui sans-serif

**Character:** Neutral, widely supported, and unpretentious. Roboto carries numbers and Latin text with clarity. Noto Sans Bengali ensures the Bengali interface feels as native as the English one. The pairing is functional, not decorative. It gets out of the way so the data can speak.

### Hierarchy
- **Display** (700, clamp(1.875rem, 5vw, 3rem), 1.2): Hero headlines, dashboard big numbers. Used sparingly.
- **Headline** (700, 1.5rem / 24px, 1.3): Page titles, section headings, card headlines.
- **Title** (600, 1.125rem / 18px, 1.4): Subheadings, card titles, form section labels.
- **Body** (400, 1rem / 16px, 1.5): Body text, descriptions, table content. Max line length 65–75ch.
- **Label** (500, 0.875rem / 14px, 1.375): Buttons, navigation, badges, timestamps. Slightly heavier weight for scannability.
- **Caption** (400, 0.75rem / 12px, 1.25): Timestamps, helper text, fine print.

### Named Rules
**The Number-First Rule.** In low-literacy contexts, numbers must be larger and bolder than their labels. A stock level of "4,250 kg" should read as a headline; the word "Stock Level" beneath it can be a caption.

## 4. Elevation

The system uses ambient shadows for breathing room and tonal layering for structural depth. Shadows are soft, diffuse, and never harsh. They create space, not hierarchy. The landing page hero uses a single glassmorphism card (`backdrop-filter: blur(12px)`) as a deliberate exception; it is the only glass element in the product interface.

### Shadow Vocabulary
- **Resting** (`box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05)`): Default card state. Barely perceptible; prevents floating.
- **Hover** (`box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1)`): Cards on hover. A gentle lift, not a leap.
- **Elevated** (`box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1)`): Modals, dropdowns, floating action buttons. Structural separation.
- **Hero** (`box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25)`): Landing page dashboard preview only. Dramatic, but justified by context.

### Named Rules
**The Flat-By-Default Rule.** Surfaces are flat at rest. Shadows appear only as a response to state (hover, elevation, focus) or context (hero section). If a screen feels busy, remove shadows before removing content.

## 5. Components

### Buttons
- **Character:** Solid and dependable. No ghosting, no transparency at rest.
- **Shape:** Gently curved edges (8px radius, `rounded-lg`). Not pill-shaped (except for language toggles and landing page CTAs, which are exceptions).
- **Primary:** Deep navy background (`#050C9C`), white text, 12px vertical / 24px horizontal padding, 56px minimum height. Elevated with `shadow-md`.
- **Hover / Focus:** Background darkens to `#040a7a`. Active state scales to 0.98. Transition 150ms ease.
- **Secondary:** Secondary blue background (`#3572EF`), white text, same sizing. `shadow-sm`.
- **Outline:** 2px primary border, primary text, transparent background. Hover fills with primary and inverts text.
- **Disabled:** Light tint background (`#A7E6FF`), primary text at 60% opacity.
- **Icon Button:** 56x56px circle, sky accent background (`#3ABEF9`), white icon. For floating action buttons and toolbar actions.

### Cards / Containers
- **Character:** Clean islands of white on a tinted sea.
- **Corner Style:** 12px radius (`rounded-xl`) for summary cards, 8px (`rounded-lg`) for data cards.
- **Background:** White (`#FFFFFF`) on surface base. Dark mode: `#1E293B` on slate-900.
- **Shadow Strategy:** Resting shadow by default. Hover shadow on interaction.
- **Border:** 1px `border-blue-100` (`#BFDBFE`) subtle border. Dark mode: `#334155`.
- **Internal Padding:** 24px for summary cards, 16px for data cards.

### Inputs / Fields
- **Character:** Outlined, material-style. The field is a container, not a line.
- **Style:** 2px border (`#BFDBFE`), white background, 8px radius, 12px vertical / 16px horizontal padding, 56px minimum height.
- **Focus:** Border shifts to secondary blue (`#3572EF`), 2px ring in mist light (`#A7E6FF`). No outline removal without replacement.
- **Error:** Border and text turn danger red (`#DC2626`). Error icon (`AlertCircle`) + message below field.
- **Number inputs:** `inputmode="numeric"`, right-aligned text, Bengali numerals in Bangla mode.

### Navigation
- **Desktop:** Top bar, sticky, surface background (`#F0F9FF`), `shadow-sm`. Links are label-sized, muted by default, secondary blue on hover. Active page has a 2px secondary underline.
- **Mobile:** Hamburger menu, slide-in sidebar from left. Brand header with icon + tagline. Navigation items with 20px Lucide icons. Current page highlighted with primary background tint (`#050C9C` at 10% opacity).
- **Touch targets:** All nav items 56px minimum height.

### Badges
- **Style:** Pill-shaped (`rounded-full`), 12px font, medium weight. Background tints matching semantic color.
- **Variants:** Paid (green-100 / green-800), Pending (yellow-100 / yellow-800), Unpaid (red-100 / red-800), Info (blue-100 / blue-800).

### Tables
- **Style:** White container, rounded-xl, overflow-hidden. Header row in surface base (`#F0F9FF`). Rows divided by 1px blue-100 lines. Hover highlight in surface base.
- **Amounts:** Green for positive, red for negative. Currency symbol (৳) always present.

### Offline Banner
- **Style:** Fixed top, warning amber background (`#EAB308`), white text, 16px icon + message. Dismisses automatically when online.

## 6. Do's and Don'ts

### Do:
- **Do** use 56px minimum touch targets on every interactive element.
- **Do** lead with the number or action in low-literacy contexts; labels can be smaller.
- **Do** pair every icon with a text label in the navigation and primary actions.
- **Do** use Bengali numerals (০-৯) throughout when the user selects Bangla.
- **Do** format currency with the ৳ symbol always: `৳ 1,250.00` (English) or `৳ ১,২৫০.০০` (Bengali).
- **Do** respect system font size for accessibility.
- **Do** use high contrast mode for sunlight readability.
- **Do** show skeleton screens during loading, not spinners.
- **Do** handle empty states with friendly illustrations and a primary CTA.
- **Do** make the app feel fully functional offline; sync status is informative, not alarming.

### Don't:
- **Don't** use generic government form patterns: dense tables, tiny fields, no visual hierarchy, bureaucratic language.
- **Don't** use overly edgy or sci-fi aesthetics: neon accents, dark-mode-as-default-gimmick, futuristic icons, gradients-for-everything.
- **Don't** create cluttered dashboards with information overload, competing CTAs, or nested cards.
- **Don't** use corporate SaaS minimalism: sterile whites, abstract illustrations, impersonal copy.
- **Don't** force complex onboarding: multi-step tutorials, tooltips everywhere, forced feature tours.
- **Don't** use border-left or border-right greater than 1px as a colored stripe on cards, lists, or alerts.
- **Don't** use gradient text (`background-clip: text`). Use a single solid color; emphasis via weight or size.
- **Don't** use glassmorphism as a default. It is permitted only in the landing page hero dashboard preview.
- **Don't** use the hero-metric template (big number, small label, supporting stats, gradient accent).
- **Don't** create identical card grids (icon + heading + text, repeated endlessly).
- **Don't** reach for a modal as the first solution. Exhaust inline and progressive alternatives first.
- **Don't** use em dashes. Use commas, colons, semicolons, periods, or parentheses.
