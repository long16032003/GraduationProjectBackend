<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Bill\IndexBillController;
use App\Http\Controllers\Bill\StoreBillController;
use App\Http\Controllers\Bill\UpdateBillController;
use App\Http\Controllers\Bill\DeleteBillController;
use App\Http\Controllers\Customer\Auth\CustomerController;
use App\Http\Controllers\Customer\Auth\LoginController;
use App\Http\Controllers\PromotionCode\IndexPromotionCodeController;
use App\Http\Controllers\PromotionCode\StorePromotionCodeController;
use App\Http\Controllers\Reservation\IndexReservationController;
use App\Http\Controllers\Reservation\StoreReservationController;
use App\Http\Controllers\Reservation\UpdateReservationController;
use App\Http\Controllers\Reservation\DeleteReservationController;
use Illuminate\Http\Request;

Route::middleware('guest:customer')->group(function () {
    Route::post('login-customer', [LoginController::class, 'login'])->name('login-customer');
});

Route::middleware('auth:customer')->group(function () {
    Route::post('logout-customer', [LoginController::class, 'logout'])
    ->name('logout-customer');

    Route::get('@customer', static function (Request $request) {
        return response()->json($request->user('customer'));
    })->name('@customer');

    Route::post('/promotion_codes', [StorePromotionCodeController::class, 'store'])->name('promotion_codes.store');

    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::put('/customers/change-password/{uuid}', [CustomerController::class, 'changePassword'])->name('customers.change-password');
});

