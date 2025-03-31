<?php

use App\Http\Middleware\Sanitizer;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Http\Middleware\HandleCors;
//use Illuminate\Cookie\Middleware\EncryptCookies;
//use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware
            ->remove(HandleCors::class)
            ->replace(TrimStrings::class, Sanitizer::class)
            ->web(
                replace: [
                    //
                ],
            );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
