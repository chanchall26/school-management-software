<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;

/**
 * After a successful login, redirect to the custom panel dashboard
 * instead of Filament's default /admin/dashboard.
 */
class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): mixed
    {
        return redirect('/panel/dashboard');
    }
}
