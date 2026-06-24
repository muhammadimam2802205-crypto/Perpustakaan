<?php
// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;

// class CheckRole
// {
//     public function handle(Request $request, Closure $next, $role)
//     {
//         $user = auth()->user();
//         if (!$user) {
//             abort(403);
//         }

//         // role could be 'admin' or 'member'
//         if ($role === 'admin' && !$user->isAdmin()) {
//             abort(403);
//         }

//         if ($role === 'member' && !$user->isMember()) {
//             abort(403);
//         }

//         return $next($request);
//     }
// }

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silahkan login terlebih dahulu.');
        }

        $user = Auth::user();

        // Jika tidak ada role yang dispesifikasi, allow semua
        if (empty($roles)) {
            return $next($request);
        }

        // Cek apakah user memiliki salah satu role yang diizinkan
        foreach ($roles as $role) {
            if ($role === 'admin' && $user->isAdmin()) {
                return $next($request);
            }
            
            if ($role === 'member' && $user->isMember()) {
                return $next($request);
            }
        }

        // Jika tidak memiliki akses
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}