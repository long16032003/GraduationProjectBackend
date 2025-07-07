<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\UserPermissionController;
use App\Http\Controllers\Bill\DeleteBillController;
use App\Http\Controllers\Bill\IndexBillController;
use App\Http\Controllers\Bill\ShowBillController;
use App\Http\Controllers\Bill\StoreBillController;
use App\Http\Controllers\Bill\UpdateBillController;
use App\Http\Controllers\Customer\Auth\CustomerController;
use App\Http\Controllers\Customer\Auth\LoginController;
use App\Http\Controllers\Dish\DeleteDishController;
use App\Http\Controllers\Dish\StoreDishController;
use App\Http\Controllers\Dish\UpdateDishController;
use App\Http\Controllers\DishCategory\DeleteDishCategoryController;
use App\Http\Controllers\DishCategory\StoreDishCategoryController;
use App\Http\Controllers\DishCategory\UpdateDishCategoryController;
use App\Http\Controllers\EnterIngredient\IndexEnterIngredientController;
use App\Http\Controllers\EnterIngredient\StoreEnterIngredientController;
use App\Http\Controllers\ExportIngredient\IndexExportIngredientController;
use App\Http\Controllers\ExportIngredient\StoreExportIngredientController;
use App\Http\Controllers\Ingredient\DeleteIngredientController;
use App\Http\Controllers\Ingredient\IndexIngredientController;
use App\Http\Controllers\Ingredient\StoreIngredientController;
use App\Http\Controllers\Ingredient\UpdateIngredientController;
use App\Http\Controllers\Media\StoreMediaController;
use App\Http\Controllers\Order\IndexOrderController;
use App\Http\Controllers\Order\StoreOrderController;
use App\Http\Controllers\Order\UpdateOrderController;
use App\Http\Controllers\OrderDish\UpdateOrderDishController;
use App\Http\Controllers\Post\DeletePostController;
use App\Http\Controllers\Post\StorePostController;
use App\Http\Controllers\Post\UpdatePostController;
use App\Http\Controllers\Promotion\DeletePromotionController;
use App\Http\Controllers\Promotion\StorePromotionController;
use App\Http\Controllers\Promotion\UpdatePromotionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Settings\SiteSettingController;
use App\Http\Controllers\Staff\DeleteStaffController;
use App\Http\Controllers\Staff\IndexStaffController;
use App\Http\Controllers\Staff\StoreStaffController;
use App\Http\Controllers\Staff\UpdateStaffController;
use App\Http\Controllers\User\UserRoleController;
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

    Route::get('@me', static function (Request $request) {
        $user = $request->user('web');

        if (!$user) {
            return response()->json(null, 401);
        }

        // Load roles với permissions của chúng
        $user->load('roles');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'superadmin' => $user->isSuperAdmin(),
            'roles' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'level' => $role->level,
                    'status' => $role->status,
                    'permissions' => $role->permissions
                ];
            }),
            'permissions' => $user->permissions()->toArray()
        ]);
    })->name('@me');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->name('password.confirm');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Role management - both /role and /roles for compatibility
    Route::prefix('role')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('permission:role:browse')->name('role.index');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:role:create')->name('role.store');
        Route::put('/{id}', [RoleController::class, 'update'])->middleware('permission:role:update')->name('role.update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->middleware('permission:role:delete')->name('role.delete');
        Route::get('/{id}', [RoleController::class, 'show'])->middleware('permission:role:read')->name('role.show');
    });

    Route::post('/upload-image', [StoreMediaController::class, 'store'])->name('upload-image');

    // Public bill routes
    Route::prefix('bills')->group(function () {
        Route::post('', [StoreBillController::class, 'store'])->middleware('permission:bill:create')->name('bills.store');
        // Route::get('', [IndexBillController::class, 'index'])->middleware('permission:bill:browse')->name('bills.index');
        Route::get('/{id}', [ShowBillController::class, 'show'])->name('bills.show');
        Route::put('/{id}', [UpdateBillController::class, 'update'])->name('bills.update');
        Route::post('/{id}/pay', [UpdateBillController::class, 'pay'])->middleware('permission:bill:update')->name('bills.pay');
        Route::delete('/{id}', [DeleteBillController::class, 'delete'])->name('bills.delete');
    });

    // Dish Categories - WITH PERMISSIONS
    Route::post('dish-categories', [StoreDishCategoryController::class, 'store'])->middleware('permission:dish-category:create')->name('dish-categories.store');
    Route::put('dish-categories/{id}', [UpdateDishCategoryController::class, 'update'])->middleware('permission:dish-category:update')->name('dish-categories.update');
    Route::delete('dish-categories/{id}', [DeleteDishCategoryController::class, 'delete'])->middleware('permission:dish-category:delete')->name('dish-categories.delete');

    // Posts - WITH PERMISSIONS
    Route::post('posts', [StorePostController::class, 'store'])->middleware('permission:post:create')->name('posts.store');
    Route::put('posts/{id}', [UpdatePostController::class, 'update'])->middleware('permission:post:update')->name('posts.update');
    Route::delete('posts/{id}', [DeletePostController::class, 'delete'])->middleware('permission:post:delete')->name('posts.delete');

    // Staff - WITH PERMISSIONS
    Route::post('staffs', [StoreStaffController::class, 'store'])->middleware('permission:user:create')->name('staff.store');
    Route::get('/staffs', [IndexStaffController::class, 'index'])->middleware('permission:user:browse')->name('staff.index');
    Route::put('/staffs/{id}', [UpdateStaffController::class, 'update'])->middleware('permission:user:update')->name('staff.update');
    Route::delete('/staffs/{id}', [DeleteStaffController::class, 'delete'])->middleware('permission:user:delete')->name('staff.delete');

    // Staff Role Assignment - WITH PERMISSIONS
    Route::post('/staffs/{id}/roles', [UserRoleController::class, 'store'])->middleware('permission:user:update')->name('staff.assign-roles');

    // Dishes - WITH PERMISSIONS
    Route::post('dishes', [StoreDishController::class, 'store'])->middleware('permission:dish:create')->name('dishes.store');
    Route::put('dishes/{id}', [UpdateDishController::class, 'update'])->middleware('permission:dish:update')->name('dishes.update');
    Route::delete('dishes/{id}', [DeleteDishController::class, 'delete'])->middleware('permission:dish:delete')->name('dishes.delete');

    // Promotions - WITH PERMISSIONS
    Route::post('promotions', [StorePromotionController::class, 'store'])->middleware('permission:promotion:create')->name('promotions.store');
    Route::put('promotions/{id}', [UpdatePromotionController::class, 'update'])->middleware('permission:promotion:update')->name('promotions.update');
    Route::delete('promotions/{id}', [DeletePromotionController::class, 'delete'])->middleware('permission:promotion:delete')->name('promotions.delete');

    // Table routes - WITH PERMISSIONS
    Route::post('tables', [StoreTableController::class, 'store'])->middleware('permission:table:create')->name('tables.store');
    Route::put('tables/{table}', [UpdateTableController::class, 'update'])->middleware('permission:table:update')->name('tables.update');
    Route::delete('tables/{table}', [DeleteTableController::class, 'delete'])->middleware('permission:table:delete')->name('tables.delete');

    // Customers - WITH PERMISSIONS
    Route::get('customers', [CustomerController::class, 'index'])->middleware('permission:customer:browse')->name('customers.index');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->middleware('permission:customer:delete')->name('customers.destroy');

    // Ingredients - WITH PERMISSIONS
    Route::post('ingredients', [StoreIngredientController::class, 'store'])->middleware('permission:ingredient:create')->name('ingredients.store');
    Route::get('ingredients', [IndexIngredientController::class, 'index'])->middleware('permission:ingredient:browse')->name('ingredients.index');

    // Route cụ thể phải đặt trước route với parameter
    Route::put('ingredients/update-quantitys', [UpdateIngredientController::class, 'updateQuantitys'])->middleware('permission:ingredient:update')->name('ingredients.update-quantitys');

    Route::put('ingredients/{id}', [UpdateIngredientController::class, 'update'])->middleware('permission:ingredient:update')->name('ingredients.update');
    Route::delete('ingredients/{id}', [DeleteIngredientController::class, 'delete'])->middleware('permission:ingredient:delete')->name('ingredients.delete');

    // Enter Ingredients - WITH PERMISSIONS
    Route::post('enter-ingredients', [StoreEnterIngredientController::class, 'store'])->middleware('permission:enter-ingredient:create')->name('enter-ingredients.store');
    Route::get('enter-ingredients', [IndexEnterIngredientController::class, 'index'])->middleware('permission:enter-ingredient:browse')->name('enter-ingredients.index');

    // Export Ingredients - WITH PERMISSIONS
    Route::post('export-ingredients', [StoreExportIngredientController::class, 'store'])->middleware('permission:export-ingredient:create')->name('export-ingredients.store');
    Route::get('export-ingredients', [IndexExportIngredientController::class, 'index'])->middleware('permission:export-ingredient:browse')->name('export-ingredients.index');

    // User Permission Management Routes
    Route::prefix('user-permissions')->group(function () {
        Route::get('/', [UserPermissionController::class, 'getUserPermissions'])->name('user-permissions.index');
        Route::post('check', [UserPermissionController::class, 'checkPermission'])->name('user-permissions.check');
        Route::post('check-any', [UserPermissionController::class, 'checkAnyPermissions'])->name('user-permissions.check-any');
        Route::post('check-all', [UserPermissionController::class, 'checkAllPermissions'])->name('user-permissions.check-all');
    });


    Route::post('orders', [StoreOrderController::class, 'store'])->middleware('permission:order:create')->name('orders.store');
    Route::put('orders/{id}', [UpdateOrderController::class, 'update'])->middleware('permission:order:update')->name('orders.update');

    // Order Dishes Management - WITH PERMISSIONS
    Route::prefix('order-dishes')->group(function () {
        // Cập nhật order_dish cơ bản (số lượng, trạng thái, hủy món)
        Route::put('/{orderId}/{dishId}', [UpdateOrderDishController::class, 'updateOrderDish'])
            ->middleware('permission:order-dish:update')
            ->name('order-dishes.update');

        // Hủy nhiều món cùng loại trong nhiều đơn hàng
        Route::post('/cancel-group', [UpdateOrderDishController::class, 'cancelDishGroup'])
            ->middleware('permission:order-dish:update')
            ->name('order-dishes.cancel-group');

        // Kích hoạt lại món đã hủy
        Route::put('/{orderId}/{dishId}/reactivate', [UpdateOrderDishController::class, 'reactivateDish'])
            ->middleware('permission:order-dish:update')
            ->name('order-dishes.reactivate');

        // Cập nhật số lượng món
        Route::put('/{orderId}/{dishId}/quantity', [UpdateOrderDishController::class, 'updateQuantity'])
            ->middleware('permission:order-dish:update')
            ->name('order-dishes.update-quantity');
    });
});

