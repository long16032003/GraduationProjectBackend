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
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // https://laravel.com/docs/12.x/routing#throttling-with-redis
        // https://laravel.com/docs/12.x/rate-limiting#cache-configuration
        $middleware->throttleWithRedis();

        $middleware
            // we will handle cors in .htaccess file or apache config, so we dont need this middleware
            ->remove(HandleCors::class)
            // help sanitize user input to prevent XSS attack
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
