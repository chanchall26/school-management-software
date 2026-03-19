# Simption — Poora System Samjho (Basic se Depth Tak)

> Yeh document tumhare liye hai taaki tum apna hi banaya hua system fully samjho —
> kya kya bana hai, kaise bana hai, kyon bana hai, aur aage kaise better hoga.

---

## TABLE OF CONTENTS

1. [System Overview — Bird's Eye View](#1-system-overview)
2. [Tech Stack — Kya Use Kiya Hai](#2-tech-stack)
3. [Multi-Tenancy — Kaise Implement Ki Hai](#3-multi-tenancy-implementation)
4. [Admin Panel (Filament) — Kaise Setup Hai](#4-admin-panel-filament)
5. [Login System — 4 Tarike Se Login](#5-login-system)
6. [Modular Architecture — Modules Ka System](#6-modular-architecture)
7. [Security Module — Detail Mein](#7-security-module)
8. [Users Module — Detail Mein](#8-users-module)
9. [Database Structure — Tables Aur Schema](#9-database-structure)
10. [Frontend — UI Kaise Bani Hai](#10-frontend-ui)
11. [Routes Aur Middleware](#11-routes-aur-middleware)
12. [SMS Service — Multi-Provider Support](#12-sms-service)
13. [Step-by-Step: Ek Naya Tenant Add Kaise Hota Hai](#13-tenant-lifecycle)
14. [Step-by-Step: Login Flow Andar Se](#14-login-flow-andar-se)
15. [Future Improvements — Aage Kya Kar Sakte Ho](#15-future-improvements)

---

## 1. System Overview

**Simption ek multi-tenant School ERP system hai.**

### Iska matlab kya hai?

Socho ek software company ne ek hi codebase banaya, lekin uspar multiple schools chalti hain:

```
dps.localhost     ->  DPS Indore ki apni duniya (alag database, alag users, alag settings)
ryan.localhost    ->  Ryan International ki apni duniya
delhi.localhost   ->  Delhi Public School ki apni duniya
```

Har school ka:
- **Alag database** — ek school ke users doosre school ko nahi dekh sakte
- **Alag settings** — ek school SMS on kar sakti hai, doosri nahi
- **Alag modules** — ek school ko Fees module chahiye, doosri ko nahi
- **Alag branding** — apna logo, apna naam

Yeh sab ek hi codebase se manage hota hai. **Yahi multi-tenancy hai.**

---

## 2. Tech Stack

| Kya Kaam | Technology | Version |
|----------|-----------|---------|
| Backend Framework | Laravel | 12 |
| Admin Panel | Filament | 5.3 |
| Multi-Tenancy | Stancl/Tenancy | 3.9 |
| Reactive UI | Livewire | 3 |
| CSS Framework | Tailwind CSS | 4 |
| JS Reactivity | Alpine.js | (Filament ke saath) |
| Build Tool | Vite | 7 |
| RBAC (Roles) | Spatie Permission | 6.24 |
| Audit Logging | Spatie Activity Log | 4.12 |
| Admin Shield | Filament Shield | 4.1 |
| Database | MySQL / SQLite | - |
| PHP | PHP | 8.2+ |

### Kyun Yeh Stack?

- **Laravel 12** — Mature, stable, huge ecosystem
- **Filament** — CRUD panels quickly banana ke liye. Naya custom panel bhi hai (`/panel/*`) jo Livewire se banaya hai
- **Stancl/Tenancy** — Laravel ka sabse popular multi-tenancy package. Database-per-tenant strategy use ki hai
- **Livewire** — JavaScript likhne ki zaroorat nahi, PHP se hi reactive UI
- **Tailwind v4** — Utility-first CSS, dark mode easy

---

## 3. Multi-Tenancy Implementation

### Concept: Central DB vs Tenant DB

```
+---------------------------------------------+
|           CENTRAL DATABASE (shared)          |
|  tenants, domains, activity_log,             |
|  permissions, tenant_login_methods           |
+--------------------+------------------------+
                     |
       +-------------+-------------+
       v             v             v
+-----------+  +-----------+  +-----------+
| tenant_   |  | tenant_   |  | tenant_   |
| dps DB    |  | ryan DB   |  | delhi DB  |
|           |  |           |  |           |
| users     |  | users     |  | users     |
| login_    |  | login_    |  | login_    |
| activ.    |  | activ.    |  | activ.    |
| sessions  |  | sessions  |  | sessions  |
+-----------+  +-----------+  +-----------+
```

### Step 1: Stancl/Tenancy Install

Package `composer.json` mein hai: `stancl/tenancy: ^3.9`

Iske saath install hote hain:
- Central migrations (tenants table, domains table)
- `TenancyServiceProvider`
- `InitializeTenancyByDomain` middleware

### Step 2: Tenant Model

**File:** `app/Models/Tenant.php`

```php
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;

    public static function getCustomColumns(): array
    {
        return ['id', 'name'];  // Extra columns besides JSON 'data'
    }
}
```

- `BaseTenant` — Stancl ka base class, `data` JSON column handle karta hai
- `TenantWithDatabase` — Har tenant ka alag database create karega
- `HasDatabase` — Database operations (create, delete, switch)
- `id` — Custom ID use kiya (jaise `dps`, `ryan`)
- `name` — School ka naam
- `data` JSON column mein: `logo`, `modules` (enabled list), `login_methods`

### Step 3: Tenancy Config

**File:** `config/tenancy.php`

```php
'database' => [
    'central_connection' => env('DB_CONNECTION', 'mysql'),
    'prefix'  => 'tenant_',   // dps ka DB banega: tenant_dps
    'suffix'  => '',
],

'bootstrappers' => [
    DatabaseTenancyBootstrapper::class,    // DB switch karta hai
    FilesystemTenancyBootstrapper::class,  // Storage paths tenant-specific
    QueueTenancyBootstrapper::class,       // Queue jobs bhi tenant-aware
],

'migration_parameters' => [
    '--path'      => database_path('migrations/tenant'),
    '--realpath'  => true,
    '--force'     => true,
],
```

### Step 4: TenancyServiceProvider

**File:** `app/Providers/TenancyServiceProvider.php`

Yeh important hai — events register karta hai:

```
Tenant Create hone par:
  1. CreateDatabase job  -> MySQL mein naya DB banao
  2. MigrateDatabase job -> tenant/migrations/ saari files run karo

Tenant Delete hone par:
  1. DeleteDatabase job  -> DB delete karo
```

### Step 5: Domain-to-Tenant Mapping

Jab `dps.localhost` par request aati hai:
1. `InitializeTenancyByDomain` middleware chalti hai
2. `domains` table mein domain dhundha jata hai
3. Tenant mila -> us tenant ka DB activate hota hai
4. Baaki request us DB se kaam karti hai

```sql
-- Central DB mein
SELECT * FROM domains WHERE domain = 'dps.localhost';
-- Result: tenant_id = 'dps'
-- Ab MySQL connection switch: USE tenant_dps;
```

### Step 6: Middleware Registration

**File:** `app/Providers/Filament/AdminPanelProvider.php`

```php
->middleware([
    InitializeTenancyByDomain::class,        // Tenant identify karo
    PreventAccessFromCentralDomains::class,  // localhost se panel block karo
    ...
])
```

**File:** `app/Providers/AppServiceProvider.php`

Livewire ka route bhi tenancy middleware se guard kiya taaki
`wire:navigate` requests bhi tenant context mein chalein.

### Step 7: Tenant Seeder

**File:** `database/seeders/TenantSeeder.php`

3 schools pre-configured hain:
```php
$tenants = [
    ['id' => 'dps',   'name' => 'DPS Indore',          'domain' => 'dps.localhost'],
    ['id' => 'ryan',  'name' => 'Ryan International',  'domain' => 'ryan.localhost'],
    ['id' => 'delhi', 'name' => 'Delhi Public School', 'domain' => 'delhi.localhost'],
];
```

Har tenant ke liye:
1. Tenant create karo (trigger -> DB banao -> migrations chalao)
2. Domain mapping karo
3. `tenancy()->initialize($tenant)` -> us tenant ke DB mein jaao
4. Admin user banao
5. Login methods set karo

---

## 4. Admin Panel (Filament)

### Dono Panels Ko Samjho

System mein actually **2 alag interfaces** hain:

```
/admin/*  ->  Filament Panel (CRUD, settings, traditional admin)
/panel/*  ->  Custom Livewire Panel (tumhara custom dashboard)
```

### Filament Panel (`/admin`)

**File:** `app/Providers/Filament/AdminPanelProvider.php`

```php
FilamentPanel::make()
    ->id('admin')
    ->path('/admin')
    ->brandName('Simption')
    ->colors(['primary' => Color::Teal])
    ->login(Login::class)      // Custom login page
    ->darkMode(true)
    ->spa()                    // Single Page Application mode
    ->plugins([FilamentShieldPlugin::make()])  // RBAC
    ->discoverResources(in: app_path('Filament/Admin/Resources'))
    ->discoverPages(in: app_path('Filament/Admin/Pages'))
    ->middleware([
        InitializeTenancyByDomain::class,
        ...
    ])
```

**Filament Shield** — Automatically roles aur permissions manage karta hai Filament resources ke liye.

### Custom Panel (`/panel`)

Yeh Filament nahi hai — yeh tumhara khud ka Livewire-based dashboard hai.

**Main Layout:** `resources/views/panel/layouts/app.blade.php`

Iska structure:
```
+------------------------------------------+
|          HEADER (64px sticky)             |
|  [Logo] [Page Title]       [User Menu]    |
+-------------+----------------------------+
|  SIDEBAR    |     MAIN CONTENT           |
|  (260px)    |                            |
|  collapsed  |  @livewire component       |
|  to 72px    |                            |
+-------------+----------------------------+
```

### Login Response Redirect

**File:** `app/Http/Responses/LoginResponse.php`

Filament login ke baad normally `/admin` par jaata — humne override kiya:
```php
// Login successful -> /panel/dashboard pe jaao (custom panel)
return redirect()->intended('/panel/dashboard');
```

**AppServiceProvider** mein bind kiya:
```php
$this->app->bind(LoginResponseContract::class, LoginResponse::class);
```

---

## 5. Login System

### 4 Methods Ek Saath

**File:** `app/Filament/Admin/Pages/Auth/Login.php`

Login page par 4 tabs hain:

```
[Password]  [Email OTP]  [Login Code]  [Mobile OTP]
```

#### Method 1: Password Login

```
User -> email + password -> DB check -> is_active check -> login
```

Security:
- Failed attempts track hote hain: `auth:failed:{tenant_id}:{email}` (Cache)
- 5 attempts ke baad: 15 min ke liye lock
- `is_active = false` -> "Account inactive" error

#### Method 2: Email OTP

```
User -> email -> OTP generate (6 digits) -> email send -> user OTP daale -> check -> login
```

Security:
- OTP **hashed** store hoti hai Cache mein: `Hash::make($otp)`
- Expiry: 10 minutes
- 3 galat attempts -> OTP expire, nayi maangni padegi
- Mail class: `app/Mail/LoginOtpMail.php`
- Template: `resources/views/mail/login-otp.blade.php`

#### Method 3: Login Code (ABC123 format)

```
User -> 6-char code (3 letters + 3 digits) -> DB se match -> login
```

Har user create hone par ek unique code generate hota hai.
**Faida:** SMS/email ki zaroorat nahi, teachers/staff ko simple code dedo.

#### Method 4: Mobile OTP (SMS)

```
User -> phone number -> SMS OTP -> user daale -> login
```

`SmsService` use hota hai (Fast2SMS / MSG91 / Twilio).
Agar SMS configured nahi -> "SMS not configured" error.

### Security Features Jo Har Method Mein Hain

| Feature | Kaise |
|---------|-------|
| Per-account lockout | Cache: `auth:locked:{tenant}:{identifier}` |
| Failed attempts | Cache: `auth:failed:{tenant}:{identifier}` |
| Global rate limit | Livewire rate limiting (5 req/min) |
| Login activity log | `login_activities` table |
| Session tracking | `user_sessions` table |
| Spatie audit log | `activity_log` table |
| Last login update | `users.last_login_at`, `last_login_ip` |

### Login Ke Baad Kya Hota Hai

```
1. User authenticate hota hai
2. user.last_login_at = now(), user.last_login_ip = request IP
3. UserSession record create (method, IP, user agent)
4. LoginActivity record create (success = true)
5. Spatie activity log: "Logged in via password"
6. LoginResponse -> redirect to /panel/dashboard
```

---

## 6. Modular Architecture

### ModuleInterface Contract

**File:** `app/Contracts/ModuleInterface.php`

Har module ko yeh implement karna hai:

```php
interface ModuleInterface
{
    public function id(): string;               // 'security', 'users'
    public function name(): string;             // 'Security', 'Users'
    public function description(): string;
    public function icon(): string;             // Heroicon name
    public function color(): string;            // Tailwind color
    public function version(): string;
    public function navGroup(): string;         // Sidebar group
    public function navOrder(): int;            // Position
    public function routes(): void;             // Register Laravel routes
    public function dashboardWidgets(): array;  // Livewire widget classes
    public function permissions(): array;       // ['view_users', 'create_users', ...]
    public function migrationsPath(): string;   // Path to DB migrations
}
```

### ModuleRegistry

**File:** `app/Support/ModuleRegistry.php`

```php
// Sabhi modules yahan register hain
public function catalog(): array
{
    return [
        new SecurityModule(),
        new UsersModule(),
        // future: new AttendanceModule(), new FeesModule(), ...
    ];
}

// Sirf enabled modules (tenant ke data column se check)
public function enabled(): array
{
    $enabledIds = tenant('modules') ?? [];
    return collect($this->catalog())
        ->filter(fn($m) => in_array($m->id(), $enabledIds))
        ->all();
}
```

### Tenant Ke Modules

Har tenant ke `data` JSON mein:
```json
{
  "modules": ["users", "security", "attendance", "fees"]
}
```

Sidebar automatically sirf enabled modules dikhata hai.

---

## 7. Security Module

**File:** `app/Modules/Security/SecurityModule.php`

### 3 Pages

**SecurityCenter** (`/panel/security/`)
File: `app/Modules/Security/Livewire/SecurityCenter.php`

Dashboard jo dikhata hai:
- Total users, active users, locked accounts
- Failed logins aaj, successful logins aaj, failed logins is week
- Recent 8 failed attempts (last 24 hours)
- Currently locked users list
- **Unlock button** — account unlock karo (resets `is_locked`, `failed_login_attempts`)

**LoginAttempts** (`/panel/security/login-attempts`)
Login attempt history table with filters.

**SecuritySettings** (`/panel/security/settings`)
Security configure karo:
- Max failed attempts before lockout
- Lockout duration
- CAPTCHA settings
- Session timeout
- Device fingerprinting on/off
- IP whitelisting
- Time-based access restrictions

### 5 Database Tables (Security Module)

| Table | Kaam |
|-------|------|
| `security_settings` | Tenant ki security configuration |
| `login_attempts` | Sab failed/successful attempts (soft deletes) |
| `user_access_rules` | Per-user IP restriction, time restriction, MFA |
| `device_fingerprints` | Device hash storage, `is_trusted` flag |
| `security_audit_logs` | Kaun, kya, kab, kahan — detailed audit |

### 4 Services

| Service | Kaam |
|---------|------|
| `MathCaptchaService` | 4+7=? type simple CAPTCHA (no Google dependency) |
| `DeviceFingerprintService` | Browser/device ka hash banana |
| `LoginSecurityService` | Attempt tracking, lockout logic |
| `AccessControlService` | IP check, time check, MFA check |

### User Model Security Fields

```php
'is_locked'              // Admin ne manually lock kiya
'locked_until'           // Auto-lock expiry timestamp
'failed_login_attempts'  // Counter
'last_failed_attempt'    // Last fail ka time
'category'               // staff, teacher, other
'allowed_access_times'   // JSON: [{"start": "09:00", "end": "17:00"}]
'require_mfa'            // MFA required for this user
'mfa_method'             // totp, sms, email
'mfa_secret'             // TOTP secret
```

---

## 8. Users Module

**File:** `app/Modules/Users/UsersModule.php`

### UserList Component

**File:** `app/Modules/Users/Livewire/UserList.php`

Features:
- Real-time search (naam, email, phone)
- Filter by role (staff, teacher, other)
- Filter by status (active, inactive, locked)
- Pagination (15 per page)
- User preview panel (side panel mein details)

**2-Step Create/Edit Form:**

Step 1 — Basic Info:
- Name, Email, Phone
- Avatar upload (file upload -> `avatars` disk)
- Role type (staff/teacher/other)
- Password

Step 2 — Restrictions:
- `restrict_access` — Access restrict karo
- `can_login_app` — App login allow/deny
- `show_login_status` — Online status dikhao
- Time-based restrictions

**3 Actions:**
- `CreateUser` — Avatar upload, login code generate, user create
- `UpdateUser` — Details update, password optional
- `ToggleUserStatus` — Active/Inactive toggle

### User Model Extra Fields (Users Module)

```php
'avatar'            // File path in avatars disk
'role_type'         // staff | teacher | other
'role_label'        // Custom label
'restrict_access'   // bool
'can_login_app'     // bool
'show_login_status' // bool
```

---

## 9. Database Structure

### Central Database Tables

```
tenants                  — id (dps/ryan/delhi), name, data (JSON)
domains                  — domain, tenant_id (FK)
activity_log             — Central-level audit
permissions              — Spatie permission tables
roles
model_has_permissions
model_has_roles
role_has_permissions
tenant_login_methods     — Tenant-level login method config
```

### Tenant Database Tables (har tenant ke DB mein)

```
users                    — id, name, email, phone, password, login_code,
                           is_active, last_login_at, last_login_ip,
                           is_locked, locked_until, failed_login_attempts,
                           avatar, role_type, role_label, ...

login_activities         — user_id, identifier, method, is_success,
                           failure_reason, ip_address, user_agent, created_at

user_sessions            — user_id, session_id, method, ip_address,
                           user_agent, logged_in_at, logged_out_at, last_activity_at

staff_login_methods      — Staff ka login method configuration

permissions              — Tenant-level Spatie permissions
roles
model_has_permissions
model_has_roles
role_has_permissions

activity_log             — Tenant-level Spatie audit log

-- Security Module tables (jab module enabled ho):
security_settings
login_attempts
user_access_rules
device_fingerprints
security_audit_logs
```

### Migrations Ka Order

**Central:** `database/migrations/`
```
0001_01_01_000000  — users (system)
0001_01_01_000001  — cache
0001_01_01_000002  — jobs
2019_09_15_000010  — tenants
2019_09_15_000020  — domains
2026_03_16_*       — activity_log tables
2026_03_17_*       — permissions, tenant_login_methods
```

**Tenant:** `database/migrations/tenant/`
```
0001_01_01_000000  — users (base)
0001_01_01_000001  — cache
0001_01_01_000002  — jobs
2026_03_16_000001  — activity_log
2026_03_16_000009  — alter users (add login fields)
2026_03_16_000010  — login_activities
2026_03_16_000011  — user_sessions
2026_03_17_*       — staff_login_methods, permissions
2026_03_18_*       — activity_log extra columns
```

**Module-Specific:** `app/Modules/*/Database/Migrations/`
```
Security:
  2026_03_17_000001  — security_settings, login_attempts, user_access_rules,
                       device_fingerprints, security_audit_logs
  2026_03_17_000002  — users mein security fields add karo

Users:
  2026_03_18_000001  — users mein module fields add karo
  2026_03_18_000002  — role_label column
```

---

## 10. Frontend UI

### Panel Layout (Splash -> Dashboard)

**File:** `resources/views/panel/layouts/app.blade.php`

Loading Sequence:
1. Page load -> Splash screen dikhta hai (Gmail jaisi loading)
2. Assets load -> Splash fade out
3. Navigation progress bar `wire:navigate` ke liye
4. Sidebar + Header + Content visible

Dark Mode:
- Toggle button header mein
- `localStorage.theme` mein save hota hai
- `<html class="dark">` Tailwind dark variants activate karta hai

Sidebar:
- Expanded: 260px
- Collapsed: 72px (sirf icons)
- Mobile: Overlay drawer

### Login Page Design

**File:** `resources/views/filament/admin/pages/auth/login.blade.php`

```
+------------------------------------------+
|                                          |
|       [School Logo / Initial Avatar]     |
|           School Name                    |
|          [Admin Portal badge]            |
|                                          |
|  --------- Sign in to continue -------   |
|                                          |
|  [Password] [Email OTP] [Code] [SMS]     |
|                                          |
|  [Email field with icon]                 |
|  [Password field with icon]              |
|                                          |
|  [          Sign In          ]           |
|                                          |
|  (Secure)  (Encrypted)  (Rate Limited)  |
|                                          |
|          Powered by SIMPTION             |
+------------------------------------------+
```

Animations: Har element staggered fade-up ke saath aata hai.

### Blade Components

`resources/views/components/panel/`

| Component | Use |
|-----------|-----|
| `badge.blade.php` | Status badges |
| `card.blade.php` | Content cards |
| `nav-item.blade.php` | Sidebar navigation links |
| `page-header.blade.php` | Page title + breadcrumbs |
| `skeleton.blade.php` | Loading shimmer placeholder |
| `stat-card.blade.php` | Dashboard stats with icon |

### CSS Architecture

**File:** `resources/css/app.css`

Tailwind v4 + custom utilities:
```
.smp-btn-primary   — Primary button
.smp-btn-outline   — Outline button
.smp-card          — Card wrapper
.smp-icon-btn      — Icon-only button
.smp-skeleton      — Shimmer animation
.smp-sidebar       — Collapsible sidebar
.smp-table         — Styled table
.smp-mono          — Monospace font (codes/IDs ke liye)
```

---

## 11. Routes Aur Middleware

### Route Structure

**File:** `routes/tenant.php`

Sab routes tenant context mein chalte hain.

```
GET /                                  -> redirect to /admin/login

-- Protected (RequirePanelAuth middleware):
GET /panel/dashboard                   -> Dashboard Livewire
GET /panel/users                       -> UserList Livewire
GET /panel/security/                   -> SecurityCenter Livewire
GET /panel/security/login-attempts     -> LoginAttempts Livewire
GET /panel/security/settings           -> SecuritySettings Livewire
```

### RequirePanelAuth Middleware

**File:** `app/Http/Middleware/RequirePanelAuth.php`

```
Request aai /panel/* par
  |
  v
User authenticated? (Filament auth guard)
  No  -> redirect /admin/login
  Yes
  |
  v
user.is_active == false?
  Yes -> logout, redirect /admin/login ("Account inactive")
  No
  |
  v
Request proceed karo
```

### Filament Middleware Stack

```
InitializeTenancyByDomain       -> Domain se tenant identify karo
PreventAccessFromCentralDomains -> localhost se /admin block karo
EncryptCookies
StartSession
AuthenticateSession
VerifyCsrfToken
Authenticate                    -> Filament auth
```

---

## 12. SMS Service

**File:** `app/Services/SmsService.php`

Ek unified interface 3 providers ke liye:

```
SMS_PROVIDER=fast2sms  (in .env)

SmsService::send($phone, $message)
  |
  switch on SMS_PROVIDER
    fast2sms -> Fast2SMS API (10-digit number, strip +91)
    msg91    -> MSG91 API (+91 prefix, DLT template ID)
    twilio   -> Twilio API (international format)
    none     -> return false, log warning
```

Public Methods:
- `send($phone, $message)` — Boolean return
- `sendOtp($phone, $otp, $schoolName)` — "Your OTP for {school}: {otp}. Valid 10 min."
- `isConfigured()` — Koi bhi provider set hai?

Agar SMS configure nahi hai: Mobile OTP method error deta hai. Baaki 3 methods kaam karte rehte hain.

---

## 13. Tenant Lifecycle (Step by Step)

### Naya Tenant Create Karna

```
1. $tenant = Tenant::create(['id' => 'abc', 'name' => 'ABC School']);
   // Event: TenantCreated fire hoti hai
   // Job 1: CreateDatabase  -> MySQL mein 'tenant_abc' DB banta hai
   // Job 2: MigrateDatabase -> saari tenant migrations run hoti hain

2. $tenant->domains()->create(['domain' => 'abc.localhost']);
   // Domain mapping complete

3. tenancy()->initialize($tenant);
   // Ab hum tenant_abc DB mein hain

4. User::create([...admin user data...]);
   // tenant_abc.users mein admin user bana

5. tenancy()->end();
   // Central DB par wapas

6. abc.localhost par jaao -> tenant identify -> DB switch -> login karo
```

### Tenant Delete Karna

```
$tenant->delete();
// Job: DeleteDatabase -> tenant_abc DB drop ho jaata hai
// Domain records bhi delete
// Central data bhi delete
```

---

## 14. Login Flow (Andar Se)

### Password Login Ka Complete Flow

```
User dps.localhost/admin/login par jaata hai

1. REQUEST AATI HAI:
   InitializeTenancyByDomain middleware
   -> domains table: dps.localhost -> tenant 'dps'
   -> DatabaseTenancyBootstrapper: MySQL switch to tenant_dps
   -> FilesystemTenancyBootstrapper: storage paths update

2. LOGIN PAGE RENDER:
   Login.php Livewire component render
   -> Tenant model load (logo, name, available methods)
   -> Blade template render (school branding ke saath)

3. USER FORM SUBMIT:
   -> Livewire action: authenticate()
   -> Check: is account locked? (Cache: auth:locked:dps:admin@dps.com)
   -> Check: failed attempts count
   -> DB query: users table mein email dhundho
   -> Hash::check(password, user.password)

4. IF PASSWORD WRONG:
   -> Increment: auth:failed:dps:admin@dps.com
   -> LoginActivity::create(is_success: false, reason: 'wrong_password')
   -> Agar attempts >= 5: account lock 15 min ke liye

5. IF PASSWORD CORRECT:
   -> Auth::login($user)
   -> user.last_login_at = now()
   -> user.last_login_ip = request()->ip()
   -> UserSession::create(method: 'password', ...)
   -> LoginActivity::create(is_success: true)
   -> activity('auth')->log('Logged in via password')
   -> Cache clear failed attempts

6. REDIRECT:
   -> LoginResponse::toResponse()
   -> redirect to /panel/dashboard

7. PANEL LOAD:
   -> RequirePanelAuth middleware check
   -> is_active check
   -> Dashboard Livewire component render
```

---

## 15. Future Improvements

### A. Baaki Modules Banana (High Priority)

Abhi sirf 2 modules hain (Security, Users). Baaki comment mein hain:

| Module | Kya Karna Hai |
|--------|---------------|
| **Attendance** | Staff/student attendance, biometric integration |
| **Fees** | Fee structure, challan, payment tracking |
| **Exam** | Marks entry, report card generation |
| **Library** | Book management, issue/return |
| **Transport** | Bus routes, driver management |
| **Timetable** | Period scheduling |

### B. Module Marketplace UI

`/panel/modules` route already commented hai `routes/tenant.php` mein.

Banao ek UI jahan:
- Saare available modules tiles mein dikhain
- Enable/Disable toggle
- Module enable karne par tenant DB mein migration auto-run ho

### C. Settings Pages

```
/panel/settings/general   -> School name, logo upload, timezone
/panel/settings/login     -> Konse login methods allow hain
/panel/settings/security  -> CAPTCHA, lockout config
```

### D. Super Admin Panel

Abhi har tenant ka apna admin hai. Ek **super-admin** interface chahiye:
- Saare tenants ki list
- Usage stats (kitne users, last login)
- Tenant create/delete
- Module enable/disable centrally

Iske liye ek **separate central domain** pe panel banao.

### E. Two-Factor Authentication (MFA)

Infrastructure already hai (fields hain: `require_mfa`, `mfa_method`, `mfa_secret`) lekin implementation pending:
- **TOTP (Google Authenticator)** — `pragmarx/google2fa` package
- **Email OTP as MFA** — Already email OTP code hai, reuse karo
- Per-user MFA enforcement via `user_access_rules`

### F. API Layer

Mobile app ke liye:
- `routes/api.php` banao (tenant-aware)
- Sanctum token authentication
- `/api/login`, `/api/me`, `/api/attendance`, etc.

### G. Real-time Features

- **Live Notifications** — Livewire broadcasting se
- **Online Status** — Currently online users dashboard pe
- **Session Monitoring** — Active sessions real-time

### H. Performance

- **Redis Cache** for session/OTP instead of file cache
- **Queue workers** for email/SMS (`jobs` table already hai)
- **Horizon** for queue monitoring
- **Database indexing** on `login_activities(created_at, user_id)`

### I. CAPTCHA Improvements

Abhi Math CAPTCHA hai. Aage:
- After 3 failed attempts auto-show karo
- Google reCAPTCHA v3 support (invisible)
- hCaptcha support (privacy-friendly)

### J. Testing

Abhi tests nahi hain. Banana chahiye:
- `tests/Feature/Auth/LoginTest.php` — Sab 4 methods test
- `tests/Feature/Tenancy/TenantIsolationTest.php` — Data isolation check
- `tests/Unit/SmsServiceTest.php` — Provider switching
- Dusk browser tests for UI

### K. Deployment

- `Dockerfile` + `docker-compose.yml`
- Nginx config for wildcard subdomains (`*.simption.com`)
- Database backup scripts per tenant
- `.env.production` example

### L. Tenant-Level Email Config

Abhi ek single mail config sab tenants ke liye. Better approach:
- Har tenant ka apna SMTP encrypted JSON mein save karo
- Emails school ke email se aayein (not Simption ke server se)

### M. Audit Log Viewer

Abhi tables hain lekin proper UI nahi:
- Filterable, sortable table
- Export to CSV/Excel
- Date range filter
- Per-user activity timeline

---

## Quick Reference: Kahan Kya Milega

| Dhundh Rahe Ho | File |
|----------------|------|
| Login logic (4 methods) | `app/Filament/Admin/Pages/Auth/Login.php` |
| Tenant model | `app/Models/Tenant.php` |
| User model (all fields) | `app/Models/User.php` |
| Tenancy config | `config/tenancy.php` |
| Security config | `config/security.php` |
| SMS config | `config/services.php` |
| Panel routes | `routes/tenant.php` |
| Auth middleware | `app/Http/Middleware/RequirePanelAuth.php` |
| Post-login redirect | `app/Http/Responses/LoginResponse.php` |
| Module interface | `app/Contracts/ModuleInterface.php` |
| Module registry | `app/Support/ModuleRegistry.php` |
| Security module | `app/Modules/Security/` |
| Users module | `app/Modules/Users/` |
| SMS service | `app/Services/SmsService.php` |
| Panel layout | `resources/views/panel/layouts/app.blade.php` |
| Login UI | `resources/views/filament/admin/pages/auth/login.blade.php` |
| CSS utilities | `resources/css/app.css` |
| Tenant seeder | `database/seeders/TenantSeeder.php` |
| Filament panel setup | `app/Providers/Filament/AdminPanelProvider.php` |
| App service provider | `app/Providers/AppServiceProvider.php` |
| Tenancy events | `app/Providers/TenancyServiceProvider.php` |

---

*Yeh document tumhari current codebase ka complete map hai.*
*Koi bhi feature add karna ho ya kuch samajhna ho — pehle yahan dekho.*
