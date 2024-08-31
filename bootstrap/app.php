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
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web([
            \App\Http\Middleware\HSTS::class,
            \App\Http\Middleware\HttpRedirect::class,
            \Bepsvpt\SecureHeaders\SecureHeadersMiddleware::class,
        ]);
        $middleware->alias([
            'checkRole' => App\Http\Middleware\CheckRole::class,
            'check.team.status' => \App\Http\Middleware\CheckTeamStatus::class,
            'check.not.blocked' => \App\Http\Middleware\CheckNotBlocked::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
