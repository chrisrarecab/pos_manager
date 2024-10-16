<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        // Global HTTP middleware
        \App\Http\Middleware\CorsMiddleware::class,
    ];

    protected $middlewareGroups = [];
    protected $routeMiddleware = [
        'cors' => \App\Http\Middleware\CorsMiddleware::class, 
    ];
}
