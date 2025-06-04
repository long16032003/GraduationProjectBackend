<?php

use App\Http\Controllers\CsrfCookieController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

use App\Http\Controllers\Dish\DeleteDishController;
use App\Http\Controllers\Dish\IndexDishController;
use App\Http\Controllers\Dish\StoreDishController;
use App\Http\Controllers\Dish\UpdateDishController;

use App\Http\Controllers\DishCategory\DeleteDishCategoryController;
use App\Http\Controllers\DishCategory\IndexDishCategoryController;
use App\Http\Controllers\DishCategory\StoreDishCategoryController;
use App\Http\Controllers\DishCategory\UpdateDishCategoryController;
use App\Http\Controllers\Media\StoreMediaController;
use App\Http\Controllers\Post\DeletePostController;
use App\Http\Controllers\Post\IndexPostController;
use App\Http\Controllers\Post\ShowPostController;
use App\Http\Controllers\Post\StorePostController;
use App\Http\Controllers\Post\UpdatePostController;

use App\Http\Controllers\Promotion\DeletePromotionController;
use App\Http\Controllers\Promotion\IndexPromotionController;
use App\Http\Controllers\Promotion\StorePromotionController;
use App\Http\Controllers\Promotion\UpdatePromotionController;

use App\Http\Controllers\Staff\IndexStaffController;
use App\Http\Controllers\Staff\StoreStaffController;
use App\Http\Controllers\Table\DeleteTableController;
use App\Http\Controllers\Table\IndexTableController;
use App\Http\Controllers\Table\StoreTableController;
use App\Http\Controllers\Table\UpdateTableController;
use App\Http\Controllers\Upload\UploadImageController;
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

Route::middleware('auth')->group(function () {
    Route::post('/upload-image', [StoreMediaController::class, 'store'])->name('upload-image');

    Route::post('dish-categories', [StoreDishCategoryController::class, 'store'])->name('dish-categories.store');
    Route::get('/dish-categories', [IndexDishCategoryController::class, 'index'])->name('dish-categories.index');
    Route::put('dish-categories/{id}', [UpdateDishCategoryController::class, 'update'])->name('dish-categories.update');
    Route::delete('dish-categories/{id}', [DeleteDishCategoryController::class, 'delete'])->name('dish-categories.delete');

    Route::post('posts', [StorePostController::class, 'store'])->name('posts.store');
    Route::get('/posts', [IndexPostController::class, 'index'])->name('posts.index');
    Route::put('posts/{id}', [UpdatePostController::class, 'update'])->name('posts.update');
    Route::delete('posts/{id}', [DeletePostController::class, 'delete'])->name('posts.delete');
    Route::get('posts/{id}', [ShowPostController::class, 'show'])->name('posts.show');

    Route::post('staffs', [StoreStaffController::class, 'store'])->name('staffs.store');
    Route::get('/staffs', [IndexStaffController::class, 'index'])->name('staffs.index');

    Route::post('dishes', [StoreDishController::class, 'store'])->name('dishes.store');
    Route::put('dishes/{id}', [UpdateDishController::class, 'update'])->name('dishes.update');
    Route::delete('dishes/{id}', [DeleteDishController::class, 'delete'])->name('dishes.delete');

    Route::post('promotions', [StorePromotionController::class, 'store'])->name('promotions.store');
    Route::put('promotions/{id}', [UpdatePromotionController::class, 'update'])->name('promotions.update');
    Route::delete('promotions/{id}', [DeletePromotionController::class, 'delete'])->name('promotions.delete');

    // Table routes
    Route::post('tables', [StoreTableController::class, 'store'])->name('tables.store');
    Route::put('tables/{table}', [UpdateTableController::class, 'update'])->name('tables.update');
    Route::delete('tables/{table}', [DeleteTableController::class, 'delete'])->name('tables.delete');
});
Route::get('/dishes', [IndexDishController::class, 'index'])->name('dishes.index');

Route::get('/promotions', [IndexPromotionController::class, 'index'])->name('promotions.index');

// Public table routes
Route::get('/tables', [IndexTableController::class, 'index'])->name('tables.index');
