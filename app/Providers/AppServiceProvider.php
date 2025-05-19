<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());
        Model::shouldBeStrict();

        Relation::enforceMorphMap([
            'user' => \App\Models\User::class,
        ]);

        // dont hash key before save to redis
        ThrottleRequestsWithRedis::shouldHashKeys(false);

        // https://laravel.com/docs/12.x/routing#defining-rate-limiters
        RateLimiter::for('web', static function (Request $request) {
            $route = Route::current();
            $key = $request->user()?->id ?: $request->ip();

            if (is_null($route)) {
                return Limit::perHour(1000)->by('hour:' . $key);
            }

            $key = $route->getName() . ':' . $request->method() . ':' . $key;

            return [
                Limit::perMinute(30)->by('minute:' . $key),
                Limit::perHour(1000)->by('hour:' . $key),
            ];
        });

        RateLimiter::for('api', static function (Request $request) {
            if (is_null($user = $request->user())) {
                return Limit::perMinute(60)->by($request->ip());
            }
            return Limit::perMinute(300)->by($user->id);
        });
    }
}
