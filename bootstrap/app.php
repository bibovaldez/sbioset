<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // global middleware
        $middleware->use([
            \App\Http\Middleware\LimitUserSessions::class,
        ]);

        $middleware->alias([
            'honeypot' => \Spatie\Honeypot\ProtectAgainstSpam::class,
            'checkRole' => App\Http\Middleware\CheckRole::class,
            'check.team.status' => \App\Http\Middleware\CheckTeamStatus::class,
            'check.not.blocked' => \App\Http\Middleware\CheckNotBlocked::class,
            'limit.sessions' => \App\Http\Middleware\LimitUserSessions::class,
            'xframe' => \App\Http\Middleware\XFrameOptions::class,
            'secure.headers' => \App\Http\Middleware\SecureHeaders::class,
            'hsts' => \App\Http\Middleware\HSTS::class,
            'http.redirect' => \App\Http\Middleware\HttpRedirect::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
