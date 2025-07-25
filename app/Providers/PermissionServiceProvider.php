<?php

namespace App\Providers;

use App\Permission;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->singleton(Permission::class);

        // Register the permission at gate
        $this->callAfterResolving(Gate::class, function (Gate $gate, Application $app) {
            $gate->before(function (Authorizable $user, string $ability) {
                if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                    return true;
                }
                if (method_exists($user, 'hasPermission')) {
                    return $user->hasPermission($ability) ?: null;
                }
            });
        });
    }
}
