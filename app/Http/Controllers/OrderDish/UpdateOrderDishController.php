<?php

namespace App\Http\Controllers\OrderDish;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDish;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UpdateOrderDishController extends Controller
{
    /**
     * Cập nhật order_dish
     */
    public function updateOrderDish(Request $request, $orderId, $dishId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'is_available' => 'nullable|boolean',
            'quantity' => 'nullable|integer|min:1',
            'cancelled_reason' => 'nullable|string|max:500',
            'cancel_type' => 'nullable|in:out_of_stock,kitchen_issue,customer_request,other'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $orderDish = OrderDish::where('order_id', $orderId)
                ->where('dish_id', $dishId)
                ->firstOrFail();

            $updateData = [];

            // Cập nhật số lượng
            if ($request->has('quantity')) {
                $updateData['quantity'] = $request->quantity;
            }

            // Cập nhật trạng thái available
            if ($request->has('is_available')) {
                $updateData['is_available'] = $request->is_available;

                // Nếu set is_available = false và có lý do hủy
                if (!$request->is_available && $request->has('cancelled_reason')) {
                    $updateData['cancelled_reason'] = $request->cancelled_reason;
                    $updateData['cancelled_by'] = Auth::id();
                    $updateData['cancelled_at'] = Carbon::now();
                }

                // Nếu set is_available = true, clear thông tin hủy
                if ($request->is_available) {
                    $updateData['cancelled_reason'] = null;
                    $updateData['cancelled_by'] = null;
                    $updateData['cancelled_at'] = null;
                }
            }

            // Xử lý hủy món riêng biệt
            if ($request->has('cancelled_reason') && !$request->has('is_available')) {
                $updateData['cancelled_reason'] = $request->cancelled_reason;
                $updateData['cancelled_by'] = Auth::id();
                $updateData['cancelled_at'] = Carbon::now();
                $updateData['is_available'] = false;
            }

            $orderDish->update($updateData);

            DB::commit();

            // Load relationships manually
            $orderDish->load(['dish']);
            if ($orderDish->cancelled_by) {
                $orderDish->load(['cancelledBy']);
            }

            return response()->json([
                'success' => true,
                'data' => $orderDish,
                'message' => 'Cập nhật món thành công'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật món',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hủy nhiều order_dishes cùng lúc (theo dish_id)
     */
    public function cancelDishGroup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'dish_id' => 'required|exists:dishes,id',
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'cancelled_reason' => 'required|string|max:500',
            'cancel_type' => 'required|in:out_of_stock,kitchen_issue,customer_request,other'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Cập nhật tất cả order_dishes có dish_id trong các orders được chọn
            $updatedCount = OrderDish::whereIn('order_id', $request->order_ids)
                ->where('dish_id', $request->dish_id)
                ->where('is_available', true) // Chỉ hủy những món chưa bị hủy
                ->update([
                    'cancelled_reason' => $request->cancelled_reason,
                    'cancelled_by' => Auth::id(),
                    'cancelled_at' => Carbon::now(),
                    'is_available' => false
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã hủy {$updatedCount} món thành công",
                'data' => [
                    'updated_count' => $updatedCount,
                    'dish_id' => $request->dish_id,
                    'order_ids' => $request->order_ids
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hủy nhóm món',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Đặt lại món (reactivate cancelled dish)
     */
    public function reactivateDish(Request $request, $orderId, $dishId): JsonResponse
    {
        try {
            DB::beginTransaction();

            $orderDish = OrderDish::where('order_id', $orderId)
                ->where('dish_id', $dishId)
                ->firstOrFail();

            // Kiểm tra xem món có bị hủy không
            if ($orderDish->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => 'Món này chưa bị hủy'
                ], 400);
            }

            // Reactivate món
            $orderDish->update([
                'is_available' => true,
                'cancelled_reason' => null,
                'cancelled_by' => null,
                'cancelled_at' => null,
            ]);

            DB::commit();

            $orderDish->load(['dish']);

            return response()->json([
                'success' => true,
                'data' => $orderDish,
                'message' => 'Đã kích hoạt lại món thành công'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kích hoạt lại món',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật số lượng món
     */
    public function updateQuantity(Request $request, $orderId, $dishId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $orderDish = OrderDish::where('order_id', $orderId)
                ->where('dish_id', $dishId)
                ->firstOrFail();

            $oldQuantity = $orderDish->quantity;
            $orderDish->update(['quantity' => $request->quantity]);
            $orderDish->load(['dish']);

            return response()->json([
                'success' => true,
                'data' => $orderDish,
                'message' => 'Cập nhật số lượng thành công',
                'changes' => [
                    'old_quantity' => $oldQuantity,
                    'new_quantity' => $request->quantity
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật số lượng',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
