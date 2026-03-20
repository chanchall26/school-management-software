# CLAUDE.md — Simption ERP Frontend Guide

> **Role:** Senior UI developer building a premium, pixel-perfect admin panel.
> **Stack:** Laravel 12 · Filament 5 · Livewire 3 · Alpine.js · Tailwind CSS 4 · Vite 7
> **Reference template:** `chanchall26/school-erp` (design inspiration only — not code copy)

---

## 1. PROJECT OVERVIEW

Multi-tenant school ERP. Each school (tenant) gets its own subdomain (e.g. `demo.localhost`).
`localhost` = central domain (no routes). `demo.localhost:8000/admin/login` = tenant login.

**Key files:**
| File | Purpose |
|------|---------|
| `app/Filament/Admin/Pages/Auth/Login.php` | Login controller (4 auth methods, rate-limit, lockout) |
| `resources/views/filament/admin/pages/auth/login.blade.php` | Login page template |
| `app/Providers/Filament/AdminPanelProvider.php` | Filament panel config |
| `resources/css/app.css` | Global design system CSS (Tailwind + `smp-*` classes) |
| `resources/views/panel/layouts/app.blade.php` | Main panel layout |
| `resources/views/livewire/panel/` | Dashboard, header, sidebar Livewire views |
| `resources/views/components/panel/` | Reusable Blade components |

---

## 2. DESIGN SYSTEM TOKENS

All tokens are defined as CSS custom properties in `resources/css/app.css` under `:root {}`.
**Never hardcode hex values in component CSS. Always use tokens.**

```css
:root {
  /* ── Brand / Primary ─── */
  --clr-primary:        #14B8A6;   /* teal-500 */
  --clr-primary-hover:  #0D9488;   /* teal-600 */
  --clr-primary-active: #0F766E;   /* teal-700 */
  --clr-primary-light:  #CCFBF1;   /* teal-100 */
  --clr-primary-faint:  #F0FDFA;   /* teal-50  */

  /* ── Neutrals (light mode) ─── */
  --clr-bg:         #F8FAFC;   /* page background */
  --clr-surface:    #FFFFFF;   /* card background */
  --clr-border:     #E2E8F0;   /* borders */
  --clr-border-2:   #CBD5E1;   /* hover borders */
  --clr-text:       #1E293B;   /* headings */
  --clr-text-2:     #475569;   /* body text */
  --clr-text-3:     #94A3B8;   /* muted / labels */
  --clr-text-4:     #CBD5E1;   /* placeholder */

  /* ── Semantic ─── */
  --clr-success:    #22C55E;
  --clr-warning:    #F59E0B;
  --clr-danger:     #EF4444;
  --clr-info:       #3B82F6;
  --clr-violet:     #8B5CF6;

  /* ── Layout ─── */
  --sidebar-w:       260px;
  --sidebar-w-sm:    72px;
  --header-h:        64px;
  --content-pad:     24px;

  /* ── Radius ─── */
  --r-sm:   4px;
  --r-md:   6px;
  --r-lg:   8px;
  --r-xl:   12px;
  --r-full: 9999px;

  /* ── Shadow ─── */
  --shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
  --shadow-sm: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
  --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);

  /* ── Typography ─── */
  --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
  --font-mono: 'JetBrains Mono', ui-monospace, monospace;

  /* ── Transition ─── */
  --t-fast:   150ms ease-out;
  --t-normal: 200ms ease-out;
  --t-slow:   300ms ease-out;
}

/* ── Dark mode overrides ─── */
.dark {
  --clr-bg:       #0F172A;
  --clr-surface:  #1E293B;
  --clr-border:   #334155;
  --clr-border-2: #475569;
  --clr-text:     #F1F5F9;
  --clr-text-2:   #CBD5E1;
  --clr-text-3:   #64748B;
  --clr-text-4:   #475569;
}
```

---

## 3. CSS ARCHITECTURE — STRICT RULES

### Rule 1: One class to rule them all
Every reusable element has **one canonical CSS class** defined in `app.css`.
Changing that class updates ALL usages automatically.

```css
/* ✅ CORRECT — defined once in app.css, used everywhere */
.smp-btn-primary { background: var(--clr-primary); ... }

/* ❌ WRONG — never inline styles or duplicate rules */
style="background: #14B8A6"
```

### Rule 2: Component isolation via CSS layers
Each page/component has its OWN scoped CSS block in its blade file.
Scoped styles use a unique page prefix so they cannot leak.

```html
{{-- Login page blade: scoped to .smp-login ──────────────── --}}
<style>
  /* Only affects login page — no global pollution */
  .smp-login__card    { ... }
  .smp-login__logo    { ... }
  .smp-login__heading { ... }
</style>
<div class="smp-login">...</div>
```

```html
{{-- Dashboard blade: scoped to .smp-dash ───────────────── --}}
<style>
  .smp-dash__stat-card  { ... }
  .smp-dash__chart-wrap { ... }
</style>
<div class="smp-dash">...</div>
```

### Rule 3: Global design-system classes (app.css only)
These classes are shared across ALL pages. Define them ONCE in `app.css`:

| Class | Element |
|-------|---------|
| `.smp-btn-primary` | Teal filled button |
| `.smp-btn-outline` | Border button |
| `.smp-btn-danger` | Red destructive button |
| `.smp-card` | White bordered card |
| `.smp-badge` | Inline status pill |
| `.smp-input` | Text input field |
| `.smp-table` | Data table |
| `.smp-sidebar` | Left sidebar |
| `.smp-icon-btn` | Icon-only button |
| `.smp-skeleton` | Shimmer placeholder |
| `.smp-dropdown-item` | Menu item |

### Rule 4: No Tailwind utility soup in templates
Use `smp-*` classes in HTML. Use Tailwind `@apply` inside CSS if needed.
Exception: layout utilities (`flex`, `grid`, `gap-*`, `hidden`, `w-full`) are OK in HTML.

```html
{{-- ✅ Clean HTML --}}
<button class="smp-btn-primary w-full">Sign In</button>

{{-- ❌ Utility soup --}}
<button class="bg-teal-500 text-white px-4 py-2 rounded-md font-semibold hover:bg-teal-600 ...">Sign In</button>
```

### Rule 5: Dark mode via CSS custom properties only
Never write `.dark:bg-slate-800`. Use `var(--clr-surface)` which auto-switches.

```css
/* ✅ CORRECT */
.smp-card { background: var(--clr-surface); border: 1px solid var(--clr-border); }

/* ❌ WRONG */
.smp-card { background: white; }
.dark .smp-card { background: #1E293B; }
```

---

## 4. LOGIN PAGE SPECIFICATION

### 4.1 Visual Design (inspired by template)
- **Layout:** Full-screen centered. Decorative teal SVG blobs top-left + bottom-right.
- **Card:** 480px max-width, `var(--clr-surface)` bg, `var(--shadow-md)`, `var(--r-xl)` radius.
- **No background pattern** — clean, minimal.

### 4.2 Page Flow

```
Step 1 — Login Page (/admin/login)
├── School logo + name (from tenant)
├── "Sign in to continue" subtitle
├── [User Type Dropdown]  ← NEW REQUIREMENT
│     Options: Student · Teacher · Staff · Admin
├── [Email / ID field]    ← changes label based on user type
├── [Password field]
├── [Sign In button]
└── Footer: "Powered by Simption · Secure · Encrypted"

Step 2 — 2FA Page (shown after password verified)
├── "Two-Factor Authentication" heading
├── OTP method info (email/SMS/app)
├── [6-digit OTP input with auto-advance]
├── [Verify button]
└── [Resend OTP link]
```

### 4.3 User Type Dropdown Behavior
- The `user_type` select changes the Email/ID field label:
  - **Admin** → "Email Address"
  - **Teacher** → "Email Address"
  - **Staff** → "Email or Login Code"
  - **Student** → "Student ID or Email"
- The selected `user_type` is submitted with the form for backend logging.
- Implemented as a Filament `Select` component in `Login.php` form schema.

### 4.4 CSS for Login Page (scoped)
```css
/* Defined inside login.blade.php <style> block — NOT in app.css */
.smp-login { ... }
.smp-login__blob { ... }          /* decorative background blobs */
.smp-login__card { ... }          /* main card */
.smp-login__logo { ... }          /* school logo */
.smp-login__school-name { ... }   /* tenant name */
.smp-login__subtitle { ... }
.smp-login__user-type { ... }     /* user type select wrapper */
.smp-login__footer { ... }
```

### 4.5 OTP Input (6 boxes, auto-advance)
```html
<!-- 6 separate single-char inputs wired via Alpine.js -->
<div class="smp-otp-grid" x-data="otpInput()">
  <template x-for="i in 6">
    <input class="smp-otp-box" maxlength="1" @input="advance($event, i)" @keydown.backspace="retreat($event, i)">
  </template>
</div>
```
CSS for `.smp-otp-grid` and `.smp-otp-box` goes in the 2FA page's scoped `<style>` block.

---

## 5. COMPONENT PATTERNS

### Button
```html
{{-- Primary --}}
<button class="smp-btn-primary">{{ $label }}</button>

{{-- Outline --}}
<button class="smp-btn-outline">Cancel</button>

{{-- Danger --}}
<button class="smp-btn-danger">Delete</button>

{{-- Loading state (Alpine) --}}
<button class="smp-btn-primary" :class="{ 'smp-btn--loading': loading }" :disabled="loading">
  <svg x-show="loading" class="smp-spinner" ...></svg>
  <span x-text="loading ? 'Signing in…' : 'Sign In'"></span>
</button>
```

### Card
```html
<div class="smp-card">
  <div class="smp-card__header">{{ $title }}</div>   {{-- optional --}}
  <div class="smp-card__body">{{ $slot }}</div>
</div>
```

### Badge
```html
{{-- Use data-color attribute — CSS handles the color --}}
<span class="smp-badge" data-color="teal">Admin</span>
<span class="smp-badge" data-color="blue">Teacher</span>
<span class="smp-badge" data-color="violet">Staff</span>
<span class="smp-badge" data-color="amber">Student</span>
<span class="smp-badge" data-color="green">Active</span>
<span class="smp-badge" data-color="red">Inactive</span>
```

```css
/* app.css — one block covers ALL badge colors */
.smp-badge { ... base styles ... }
.smp-badge[data-color="teal"]   { background: var(--clr-primary-faint); color: var(--clr-primary); }
.smp-badge[data-color="green"]  { background: #F0FDF4; color: var(--clr-success); }
/* etc. */
```

### Input
```html
<div class="smp-input-wrap">
  <span class="smp-input-icon"><!-- icon svg --></span>
  <input class="smp-input" type="email" placeholder="you@school.edu">
</div>
```

### Skeleton
```html
<div class="smp-skeleton" style="height: 20px; width: 60%;"></div>
```
Only width/height are inline — everything else is in `.smp-skeleton` in app.css.

---

## 6. FILAMENT CUSTOMIZATION RULES

### AdminPanelProvider.php
```php
$panel
    ->colors(['primary' => Color::Teal])
    ->font('Inter')
    ->darkMode(true)
    ->spa()
    ->sidebarCollapsibleOnDesktop()
    ->brandName('Simption')
    ->topNavigation(false)       // sidebar layout, not top nav
```

### Filament CSS Overrides
Filament-specific overrides go in `resources/css/filament.css` (separate file, loaded via `vite.config.js`).
Never patch Filament styles inside `app.css` — keep them isolated.

```css
/* resources/css/filament.css — Filament overrides only */
.fi-btn-primary { /* match smp-btn-primary look */ }
.fi-input { /* match smp-input look */ }
.fi-tabs-tab { /* match login tab style */ }
```

### Login view override
`protected string $view = 'filament.admin.pages.auth.login';` — already set in Login.php.
The blade file at `resources/views/filament/admin/pages/auth/login.blade.php` is fully custom.

---

## 7. CODE STYLE RULES

1. **PHP:** `declare(strict_types=1)` on all files. Named arguments where >2 args.
2. **Blade:** No logic in blade. Pass data from Livewire/controller.
3. **CSS:** `smp-*` prefix for all custom classes. BEM-style modifiers: `smp-btn-primary--loading`.
4. **JS:** Alpine.js only. No jQuery. No vanilla DOM manipulation outside Alpine.
5. **Animations:** Max 300ms. `ease-out` on enter, `ease-in` on exit. `prefers-reduced-motion` respected.
6. **No inline styles** except dynamic values from PHP/Alpine (heights, widths from data).
7. **Fonts:** Inter for UI. JetBrains Mono for IDs, tokens, IPs, codes, timestamps.
8. **Icons:** Heroicons only (Filament's built-in). SVG inline for custom icons.
9. **No spinners** for loading states — use skeleton screens. Exception: button loading state.

---

## 8. ANTI-PATTERNS (NEVER DO)

- ❌ Hardcode `#14B8A6` anywhere — use `var(--clr-primary)`
- ❌ `.dark:bg-slate-800` in HTML — use CSS custom properties
- ❌ Put page-specific styles in `app.css` — use scoped `<style>` blocks
- ❌ Put global design system styles inside blade `<style>` blocks — use `app.css`
- ❌ Use Tailwind utility classes for repeated visual patterns — extract to `smp-*`
- ❌ Write JavaScript outside Alpine.js `x-data` functions
- ❌ Use `@apply` for one-off rules — only for shared `smp-*` classes
- ❌ Import CSS frameworks (Bootstrap, etc.) — Tailwind + `smp-*` only
- ❌ Use JS spinners — use CSS skeletons
- ❌ Duplicate CSS — if you write the same rule twice, extract it to a token or class

---

## 9. FILE CREATION CHECKLIST

When creating a new page/component:
1. Create Livewire component PHP + blade in `app/Livewire/` + `resources/views/livewire/`
2. Add route in `routes/tenant.php`
3. Add `<style>` block at bottom of blade with `.smp-{page}__*` scoped classes
4. Add navigation item in `panel-sidebar.blade.php`
5. Use `<x-panel.page-header>`, `<x-panel.card>` components for consistency
6. Add skeleton placeholders for any async data

---

## 10. TEMPLATE DESIGN REFERENCE SUMMARY

From `chanchall26/school-erp`:

| Element | Template style | Our implementation |
|---------|---------------|-------------------|
| Primary color | `#14b8a6` (teal-500) | `var(--clr-primary)` ✓ |
| Login card | Shadow, centered, 500px | `.smp-login__card` |
| Login page bg | Decorative teal SVG blobs | CSS pseudo-elements |
| Input focus | `border-custom-500` teal | `var(--clr-primary)` border |
| Sidebar | Fixed, 260px, collapsible | `.smp-sidebar` ✓ |
| Active nav | Left border + teal bg | `.smp-nav-item--active` |
| Badges | Rounded pill, semantic colors | `.smp-badge[data-color]` |
| Tables | No alternating rows, hover | `.smp-table` ✓ |
| Dark mode | Full support | CSS variables ✓ |
| Font | Public Sans (template) → **Inter** (ours) | `var(--font-sans)` |

**DO NOT copy template's Bootstrap/Tailwind classes or JS plugins.**
Extract only: colors, spacing feel, card style, login layout concept.
