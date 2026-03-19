<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Guard for all /panel/* routes.
 * Redirects to Filament login if the user is not authenticated or is inactive.
 */
class RequirePanelAuth
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! Auth::check()) {
            return redirect('/admin/login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            return redirect('/admin/login')
                ->withErrors(['email' => 'Your account has been deactivated. Contact your administrator.']);
        }

        return $next($request);
    }
}
