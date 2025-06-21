<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Customer\Auth\CustomerController;
use App\Http\Controllers\Customer\Auth\LoginController;
use App\Http\Controllers\Dish\DeleteDishController;
use App\Http\Controllers\Dish\StoreDishController;
use App\Http\Controllers\Dish\UpdateDishController;
use App\Http\Controllers\DishCategory\DeleteDishCategoryController;
use App\Http\Controllers\DishCategory\StoreDishCategoryController;
use App\Http\Controllers\DishCategory\UpdateDishCategoryController;
use App\Http\Controllers\Media\StoreMediaController;
use App\Http\Controllers\Post\DeletePostController;
use App\Http\Controllers\Post\StorePostController;
use App\Http\Controllers\Post\UpdatePostController;
use App\Http\Controllers\Promotion\DeletePromotionController;
use App\Http\Controllers\Promotion\StorePromotionController;
use App\Http\Controllers\Promotion\UpdatePromotionController;
use App\Http\Controllers\Staff\IndexStaffController;
use App\Http\Controllers\Staff\StoreStaffController;
use App\Http\Controllers\Table\DeleteTableController;
use App\Http\Controllers\Table\StoreTableController;
use App\Http\Controllers\Table\UpdateTableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:web')->group(function () {
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth:web')->group(function () {
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->name('password.confirm');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::post('/upload-image', [StoreMediaController::class, 'store'])->name('upload-image');

    Route::post('dish-categories', [StoreDishCategoryController::class, 'store'])->name('dish-categories.store');
    Route::put('dish-categories/{id}', [UpdateDishCategoryController::class, 'update'])->name('dish-categories.update');
    Route::delete('dish-categories/{id}', [DeleteDishCategoryController::class, 'delete'])->name('dish-categories.delete');

    Route::post('posts', [StorePostController::class, 'store'])->name('posts.store');

    Route::put('posts/{id}', [UpdatePostController::class, 'update'])->name('posts.update');
    Route::delete('posts/{id}', [DeletePostController::class, 'delete'])->name('posts.delete');

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

    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
});

