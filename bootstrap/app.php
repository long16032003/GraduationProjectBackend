<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            // Route::middleware(['web', 'auth:web'])
            //     ->group(base_path('routes/admin.php'));

            // Route::middleware(['web', 'auth:customer'])
            //     ->prefix('customer')
            //     ->name('customer.')
            //     ->group(base_path('routes/customer.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->encryptCookies(except: [
            'XDEBUG_SESSION',
        ]);

        $middleware->alias([
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);

        // https://laravel.com/docs/12.x/routing#throttling-with-redis
        // https://laravel.com/docs/12.x/rate-limiting#cache-configuration
        $middleware->throttleWithRedis();

        // https://laravel.com/docs/12.x/middleware#manually-managing-laravels-default-global-middleware
        $middleware->use([
            \Illuminate\Http\Middleware\ValidatePathEncoding::class,
            \Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks::class,
//            \Illuminate\Http\Middleware\TrustHosts::class,
            \Illuminate\Http\Middleware\TrustProxies::class,
//            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Http\Middleware\ValidatePostSize::class,
//            Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \App\Http\Middleware\Sanitizer::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        // https://laravel.com/docs/12.x/middleware#manually-managing-laravels-default-middleware-groups
        // https://laravel.com/docs/12.x/authentication#invalidating-sessions-on-other-devices
        // https://laravel.com/docs/12.x/middleware#sorting-middleware
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            'throttle:web',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
        ]);

        $middleware->group('api', [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
             'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
