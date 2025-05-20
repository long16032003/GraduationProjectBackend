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
//        Permission::group(['web' => 'Web Group'], static function() {
//            Permission::resource(['post' => 'Post Management'], static function() {
//                Permission::action('update', 'Update existing post');
//                Permission::action('delete', 'Delete post');
//
//                Permission::resource(['comment' => 'Comment Management'], static function() {
//                    Permission::action('create', 'Create comment');
//                    Permission::action('delete', 'Delete comment');
//                });
//            });
//        });
//
//        Permission::group(['api' => 'Api Group'], static function() {
//            Permission::resource(['news' => 'News Management'], static function() {
//                Permission::action('update', 'Update existing news');
//                Permission::action('delete', 'Delete news');
//
//                Permission::resource(['comment' => 'Comment Management'], static function() {
//                    Permission::action('create', 'Create comment');
//                    Permission::action('delete', 'Delete comment');
//                });
//            });
//        });
//        dump(Permission::structure());
//        dump(Permission::all());


//        $this->callAfterResolving(Gate::class, function (Gate $gate, Application $app) {
//            $gate->before(function (Authorizable $user, string $ability) {
//                if (method_exists($user, 'hasPermission')) {
//                    return $user->hasPermission($ability) ?: null;
//                }
//            });
//        });

    }
}
