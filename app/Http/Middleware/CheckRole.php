<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
<<<<<<< HEAD
    public function handle(Request $request, Closure $next, ...$roles)
=======
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
>>>>>>> d9a3b0e92034a948b299d0a6b30054d3ce569d7b
    {
        if (!Auth::check()) {
<<<<<<< HEAD
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Cek apakah user memiliki role yang diizinkan
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Jika tidak memiliki akses, redirect ke dashboard dengan pesan error
        return redirect()->route('dashboard')
            ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
=======
            abort(403, 'Unauthorized - Silakan login terlebih dahulu.');
        }

        // Cek apakah role user sesuai dengan yang diharapkan
        // Asumsikan kolom 'role' ada di tabel users
        if (Auth::user()->role !== $role) {
            abort(403, 'Unauthorized - Anda tidak memiliki akses sebagai ' . $role);
        }

        return $next($request);
>>>>>>> d9a3b0e92034a948b299d0a6b30054d3ce569d7b
    }
}