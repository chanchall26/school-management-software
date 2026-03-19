<?php

declare(strict_types=1);

namespace App\Modules\Users;

use App\Contracts\ModuleInterface;
use App\Http\Middleware\RequirePanelAuth;
use App\Modules\Users\Livewire\UserList;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class UsersModule implements ModuleInterface
{
    // ── Identity ──────────────────────────────────────────────────────────────

    public static function id(): string
    {
        return 'users';
    }

    public static function name(): string
    {
        return 'Users';
    }

    public static function description(): string
    {
        return 'User management with role assignment, individual login restrictions, and a user stats dashboard widget.';
    }

    public static function icon(): string
    {
        return 'heroicon-o-users';
    }

    public static function color(): string
    {
        return '#6366F1'; // indigo
    }

    public static function version(): string
    {
        return '1.0.0';
    }

    // ── Navigation ────────────────────────────────────────────────────────────

    public static function navGroup(): string
    {
        return 'Users';
    }

    public static function navOrder(): int
    {
        return 5;
    }

    // ── Routes ────────────────────────────────────────────────────────────────

    public static function routes(): void
    {
        Route::middleware([
            'web',
            InitializeTenancyByDomain::class,
            PreventAccessFromCentralDomains::class,
            RequirePanelAuth::class,
        ])->prefix('panel')->name('panel.')->group(function () {
            Route::get('/users', UserList::class)->name('users.index');
        });
    }

    // ── Dashboard Widgets ─────────────────────────────────────────────────────

    public static function dashboardWidgets(): array
    {
        return [\App\Modules\Users\Livewire\Widgets\UserStatsWidget::class];
    }

    // ── Permissions ───────────────────────────────────────────────────────────

    public static function permissions(): array
    {
        return [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_user_restrictions',
        ];
    }

    // ── Migrations ────────────────────────────────────────────────────────────

    public static function migrationsPath(): string
    {
        return app_path('Modules/Users/Database/Migrations');
    }
}
