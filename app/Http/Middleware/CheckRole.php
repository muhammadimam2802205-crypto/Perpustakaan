<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                abort(403, 'Unauthorized');
            }

            return redirect()->route('login');
        }

        $user = Auth::user();

        if (empty($roles) || in_array($user->role, $roles, true)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, 'Unauthorized');
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}