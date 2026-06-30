<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\Authenticate; // <-- Tambahkan ini

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
<<<<<<< HEAD
        // Daftarkan middleware alias
        $middleware->alias([
            'check.role' => CheckRole::class,
            'auth' => Authenticate::class, // <-- Tambahkan ini
=======
        // Tambahkan alias middleware di sini
        $middleware->alias([
            'check.role' => \App\Http\Middleware\CheckRole::class,
>>>>>>> d9a3b0e92034a948b299d0a6b30054d3ce569d7b
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();