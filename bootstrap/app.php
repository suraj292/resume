<?php

use App\Http\Middleware\HandleAppearance;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ]);

        // Enable sessions for API routes to support session-based authentication
        $middleware->api(prepend: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \App\Http\Middleware\ConfigureCors::class,
        ]);
        
        // Register middleware aliases
        $middleware->alias([
            'virus.scan' => \App\Http\Middleware\VirusScanMiddleware::class,
            'throttle.ai' => \App\Http\Middleware\ThrottleAIRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
