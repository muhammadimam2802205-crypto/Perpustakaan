<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // ... middleware global (sudah ada)
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // ... middleware web (sudah ada)
        ],

        'api' => [
            // ... middleware api (sudah ada)
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        // ... middleware bawaan Laravel (sudah ada)
        
        // 👇 TAMBAHKAN INI
        'check.role' => \App\Http\Middleware\CheckRole::class,
    ];
}