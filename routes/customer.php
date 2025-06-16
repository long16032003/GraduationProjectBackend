<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Bill\IndexBillController;
use App\Http\Controllers\Bill\StoreBillController;
use App\Http\Controllers\Bill\UpdateBillController;
use App\Http\Controllers\Bill\DeleteBillController;
use App\Http\Controllers\Customer\Auth\LoginController;
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

    Route::get('/bills', [IndexBillController::class, 'index'])->name('bills.index');
    // Route::post('/bills', [StoreBillController::class, 'store'])->name('bills.store');
    // Route::put('/bills/{id}', [UpdateBillController::class, 'update'])->name('bills.update');
    // Route::delete('/bills/{id}', [DeleteBillController::class, 'delete'])->name('bills.delete');

    Route::get('/reservations', [IndexReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations', [StoreReservationController::class, 'store'])->name('reservations.store');
    Route::put('/reservations/{id}', [UpdateReservationController::class, 'update'])->name('reservations.update');
    Route::delete('/reservations/{id}', [DeleteReservationController::class, 'delete'])->name('reservations.delete');
});

