<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\LogAdminActivity;
use App\Http\Middleware\SecureHeaders;
use App\Http\Middleware\SessionTimeout;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'role' => RoleMiddleware::class,
            'admin' => AdminOnly::class,
            'log.admin' => LogAdminActivity::class,
            'secure.headers' => SecureHeaders::class,
            'session.timeout' => SessionTimeout::class,
        ]);

        $middleware->appendToGroup('web', [
            SecureHeaders::class,
            SessionTimeout::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
