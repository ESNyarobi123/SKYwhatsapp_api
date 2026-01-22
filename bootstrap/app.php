<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.key' => \App\Http\Middleware\AuthenticateApiKey::class,
            'internal.api' => \App\Http\Middleware\AuthenticateInternalApi::class,
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
            'rate.limit' => \App\Http\Middleware\CheckRateLimit::class,
            'admin' => \App\Http\Middleware\CheckAdmin::class,
        ]);

        // Disable CSRF for API routes
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'api/v1/*',
            'payments/webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
