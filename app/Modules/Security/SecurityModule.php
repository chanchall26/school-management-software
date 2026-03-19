<?php

declare(strict_types=1);

namespace App\Modules\Security;

use App\Contracts\ModuleInterface;
use App\Http\Middleware\RequirePanelAuth;
use App\Modules\Security\Livewire\LoginAttempts;
use App\Modules\Security\Livewire\SecurityCenter;
use App\Modules\Security\Livewire\SecuritySettings;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class SecurityModule implements ModuleInterface
{
    // ── Identity ──────────────────────────────────────────────────────────────

    public static function id(): string
    {
        return 'security';
    }

    public static function name(): string
    {
        return 'Security';
    }

    public static function description(): string
    {
        return 'Login protection, account lockout, access control, audit logs, and device fingerprinting for your school.';
    }

    public static function icon(): string
    {
        return 'heroicon-o-shield-check';
    }

    public static function color(): string
    {
        return '#EF4444'; // red — security brand color
    }

    public static function version(): string
    {
        return '1.0.0';
    }

    // ── Navigation ────────────────────────────────────────────────────────────

    public static function navGroup(): string
    {
        return 'Security';
    }

    public static function navOrder(): int
    {
        return 10;
    }

    // ── Routes ────────────────────────────────────────────────────────────────

    public static function routes(): void
    {
        Route::middleware([
            'web',
            InitializeTenancyByDomain::class,
            PreventAccessFromCentralDomains::class,
            RequirePanelAuth::class,
        ])->prefix('panel/security')->name('panel.security.')->group(function () {
            Route::get('/',               SecurityCenter::class)->name('center');
            Route::get('/login-attempts', LoginAttempts::class)->name('login-attempts');
            Route::get('/settings',       SecuritySettings::class)->name('settings');
        });
    }

    // ── Dashboard Widgets ─────────────────────────────────────────────────────

    public static function dashboardWidgets(): array
    {
        return [];
    }

    // ── Permissions ───────────────────────────────────────────────────────────

    public static function permissions(): array
    {
        return [
            'view_security_center',
            'view_login_attempts',
            'manage_security_settings',
            'unlock_accounts',
            'manage_access_rules',
            'view_audit_logs',
        ];
    }

    // ── Migrations ────────────────────────────────────────────────────────────

    public static function migrationsPath(): string
    {
        return app_path('Modules/Security/Database/Migrations');
    }
}
