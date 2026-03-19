<?php

declare(strict_types=1);

use App\Http\Middleware\RequirePanelAuth;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// ── Public tenant routes (no auth required) ──────────────────────────────────
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', fn () => redirect('/admin/login'));
});

// ── Protected panel routes (/panel/*) ────────────────────────────────────────
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    RequirePanelAuth::class,
])->prefix('panel')->name('panel.')->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────────────────
    Route::get('/dashboard', \App\Livewire\Panel\Dashboard::class)->name('dashboard');

    // ── Users Module ──────────────────────────────────────────────────────────
    Route::get('/users', \App\Modules\Users\Livewire\UserList::class)->name('users.index');

    // ── Security Module ───────────────────────────────────────────────────────
    Route::get('/security',               \App\Modules\Security\Livewire\SecurityCenter::class)->name('security.center');
    Route::get('/security/login-attempts', \App\Modules\Security\Livewire\LoginAttempts::class)->name('security.login-attempts');
    Route::get('/security/settings',      \App\Modules\Security\Livewire\SecuritySettings::class)->name('security.settings');

    // ── Module Marketplace (Phase 3) ──────────────────────────────────────────
    // Route::get('/modules',            \App\Livewire\Panel\Modules\Marketplace::class)->name('modules.marketplace');

    // ── Settings (Phase 4) ────────────────────────────────────────────────────
    // Route::get('/settings/general',   \App\Livewire\Panel\Settings\General::class)->name('settings.general');
    // Route::get('/settings/login',     \App\Livewire\Panel\Settings\LoginMethods::class)->name('settings.login');
    // Route::get('/settings/security',  \App\Livewire\Panel\Settings\Security::class)->name('settings.security');
});
