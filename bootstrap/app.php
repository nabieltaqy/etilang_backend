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
        //middleware aliases
        $middleware->alias([
            'Google2FA' => PragmaRX\Google2FALaravel\Facade::class,
            'ensure2FA' => App\Http\Middleware\ensure2FA::class,
            'ensure2FAEnabled' => App\Http\Middleware\ensure2FAEnabled::class,
            'police' => App\Http\Middleware\PoliceMiddleware::class,
            'admin' => App\Http\Middleware\AdminMiddleware::class,
            'guest' => App\Http\Middleware\GuestMiddleware::class,
            'validate.violation.token' => App\Http\Middleware\ValidateViolationToken::class,
            'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'check.ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            'checkViolationTicket' => App\Http\Middleware\CheckViolationTicket::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();