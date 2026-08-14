<?php

use App\Http\Middleware\AuthenticateMiddleware;
use App\Http\Middleware\EnsureBoutiqueSelected;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SecurityHeadersMiddleware::class,
        ]);

        $middleware->alias([
            'auth' => AuthenticateMiddleware::class,
            'role' => RoleMiddleware::class,
            'boutique' => EnsureBoutiqueSelected::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
