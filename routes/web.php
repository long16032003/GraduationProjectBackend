<?php

use App\Http\Controllers\CsrfCookieController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return view('welcome');
})->name('welcome');

Route::group(['prefix' => 'sanctum'], static function () {
    Route::get('/csrf-cookie', [CsrfCookieController::class, 'show'])
        ->name('sanctum.csrf-cookie');
});

Route::get('permissions', [PermissionController::class, 'show'])
    ->name('permissions');

Route::group(['prefix' => 'role', 'name' => 'role'], static function () {
    Route::post('/', [RoleController::class, 'store'])->name('store');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
