<?php


use App\Http\Controllers\CsrfCookieController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Settings\SiteSettingController;

use App\Http\Controllers\Bill\IndexBillController;
use App\Http\Controllers\Bill\StoreBillController;
use App\Http\Controllers\Bill\UpdateBillController;
use App\Http\Controllers\Bill\DeleteBillController;
use App\Http\Controllers\Bill\ShowBillController;
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
use App\Http\Controllers\Reservation\DeleteReservationController;
use App\Http\Controllers\Reservation\IndexReservationController;
use App\Http\Controllers\Reservation\StoreReservationController;
use App\Http\Controllers\Reservation\UpdateReservationController;

use App\Http\Controllers\Staff\IndexStaffController;
use App\Http\Controllers\Staff\StoreStaffController;
use App\Http\Controllers\Table\AvailableTablesController;
use App\Http\Controllers\Table\DeleteTableController;
use App\Http\Controllers\Table\IndexTableController;
use App\Http\Controllers\Table\StoreTableController;
use App\Http\Controllers\Table\UpdateTableController;
use App\Http\Controllers\Upload\UploadImageController;
use App\Http\Controllers\Media\MediaController;
use App\Http\Controllers\Order\IndexOrderController;
use App\Http\Controllers\Order\StoreOrderController;
use App\Http\Controllers\PromotionCode\IndexPromotionCodeController;
use App\Http\Controllers\Statistics\StatisticsController;
use App\Http\Controllers\Payment\VNPayController;
use Illuminate\Http\Request;
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
require __DIR__.'/customer.php';

Route::get('/posts', [IndexPostController::class, 'index'])->name('posts.index');
Route::get('posts/{id}', [ShowPostController::class, 'show'])->name('posts.show');

Route::get('/dishes', [IndexDishController::class, 'index'])->name('dishes.index');

Route::get('/promotions', [IndexPromotionController::class, 'index'])->name('promotions.index');

// Public table routes


// Kiểm tra bàn trống
Route::get('/dish-categories', [IndexDishCategoryController::class, 'index'])->name('dish-categories.index');

// Public bill routes
Route::prefix('bills')->group(function () {
    Route::post('', [StoreBillController::class, 'store'])->name('bills.store');
    Route::get('', [IndexBillController::class, 'index'])->name('bills.index');
    Route::get('/{id}', [ShowBillController::class, 'show'])->name('bills.show');
    Route::put('/{id}', [UpdateBillController::class, 'update'])->name('bills.update');
    Route::post('/{id}/pay', [UpdateBillController::class, 'pay'])->name('bills.pay');
    Route::delete('/{id}', [DeleteBillController::class, 'delete'])->name('bills.delete');
});

Route::post('orders', [StoreOrderController::class, 'store'])->name('orders.store');
Route::get('orders', [IndexOrderController::class, 'index'])->name('orders.index');
// Route::put('orders/{id}', [UpdateOrderController::class, 'update'])->name('orders.update');
// Route::delete('orders/{id}', [DeleteOrderController::class, 'delete'])->name('orders.delete');

// Media routes
Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');
Route::delete('/media/delete', [MediaController::class, 'delete'])->name('media.delete');
Route::get('/media/list', [MediaController::class, 'list'])->name('media.list');

// Statistics routes - Require authentication and proper role
Route::middleware(['auth'])->group(function () {
    // Dashboard statistics - quick overview
    Route::get('/statistics/dashboard', [StatisticsController::class, 'getDashboard'])->name('statistics.dashboard');
    // Detailed revenue statistics with filters
    Route::get('/statistics/revenue', [StatisticsController::class, 'getRevenue'])->name('statistics.revenue');
});

Route::middleware(['auth:web,customer'])->group(function () {
    Route::get('@me', static function (Request $request) {
        return response()->json($request->user());
    })->name('@me');
    Route::get('/tables', [IndexTableController::class, 'index'])->name('tables.index');

    Route::get('/available-tables', [AvailableTablesController::class, 'getAvailableTables']);
    Route::get('/check-availability', [AvailableTablesController::class, 'checkTableAvailability']);

    Route::get('/promotion_codes', [IndexPromotionCodeController::class, 'index'])->name('promotion_codes.index');

    Route::prefix('reservations')->group(function () {
        Route::get('/', [IndexReservationController::class, 'index'])->name('reservations.index');
        Route::post('/', [StoreReservationController::class, 'store'])->name('reservations.store');
        Route::put('{id}', [UpdateReservationController::class, 'update'])->name('reservations.update');
        Route::delete('{id}', [DeleteReservationController::class, 'delete'])->name('reservations.delete');
    });
});

Route::prefix('vnpay')->group(function () {
    Route::post('/create-payment', [VNPayController::class, 'createPayment'])->name('vnpay.create');
    Route::get('/return', [VNPayController::class, 'returnUrl'])->name('vnpay.return');
    Route::get('/ipn', [VNPayController::class, 'ipn'])->name('vnpay.ipn');
    Route::get('/payment/{payment}', [VNPayController::class, 'getPaymentStatus'])->name('vnpay.status');
    Route::get('/history/{bill}', [VNPayController::class, 'getPaymentHistory'])->name('vnpay.history');
    Route::post('/cancel/{payment}', [VNPayController::class, 'cancelPayment'])->name('vnpay.cancel');
});

// Site Settings routes - Require authentication
Route::middleware(['auth:web'])->prefix('site-settings')->group(function () {
    Route::get('/', [SiteSettingController::class, 'index'])->name('site-settings.index');
    Route::get('/{key}', [SiteSettingController::class, 'show'])->name('site-settings.show');
    Route::post('/', [SiteSettingController::class, 'store'])->name('site-settings.store');
    Route::put('/', [SiteSettingController::class, 'update'])->name('site-settings.update');
    // Route với pattern số để hỗ trợ refine format
    Route::put('/{id}', [SiteSettingController::class, 'update'])->where('id', '[0-9]+')->name('site-settings.update-by-id');
    Route::put('/{key}', [SiteSettingController::class, 'updateSingle'])->name('site-settings.update-single');
    Route::delete('/{key}', [SiteSettingController::class, 'destroy'])->name('site-settings.destroy');
});
