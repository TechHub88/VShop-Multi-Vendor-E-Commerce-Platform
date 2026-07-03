<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\BlockIpMiddleware::class);

        // Register your custom middleware with an alias
        $middleware->alias([
            'check.type' => \App\Http\Middleware\CheckUserType::class,
        ]);

        // You can also add it to middleware groups if needed
        $middleware->appendToGroup('admin', 'check.type:product');
        
        // Or add to web middleware group
        // $middleware->appendToGroup('web', 'check.type');

        // You can also add other middleware here if needed
        // $middleware->use([
        //     // \App\Http\Middleware\TrustHosts::class,
        //     \App\Http\Middleware\TrustProxies::class,
        //     \Illuminate\Http\Middleware\HandleCors::class,
        //     \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        //     \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        //     \App\Http\Middleware\TrimStrings::class,
        //     \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();