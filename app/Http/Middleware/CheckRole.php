<?php

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
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            abort(403, 'Unauthorized - Silakan login terlebih dahulu.');
        }

        // Cek apakah role user sesuai dengan yang diharapkan
        // Asumsikan kolom 'role' ada di tabel users
        if (Auth::user()->role !== $role) {
            abort(403, 'Unauthorized - Anda tidak memiliki akses sebagai ' . $role);
        }

        return $next($request);
    }
}