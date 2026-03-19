<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Redirect to custom panel after Filament login (instead of /admin/dashboard)
        $this->app->bind(LoginResponseContract::class, LoginResponse::class);
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Blade::component('filament-panels::page.simple', 'filament-panels::page.auth');

        Livewire::setUpdateRoute(function ($handle) {
            return \Route::post('/livewire/update', $handle)->middleware([
                'web',
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
            ]);
        });

    }
}