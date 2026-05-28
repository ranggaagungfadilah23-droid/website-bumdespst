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

        // ← Tambahkan baris ini
        $middleware->prepend(\App\Http\Middleware\RoleBasedSession::class);

        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role'        => \App\Http\Middleware\RoleMiddleware::class,
            'mitra_check' => \App\Http\Middleware\CheckMitraStatus::class,
            'guest'       => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'auth'        => \App\Http\Middleware\Authenticate::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'midtrans/callback',
            'logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
