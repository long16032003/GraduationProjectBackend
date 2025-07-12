<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDish;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UpdateOrderController extends Controller
{
    /**
     * Cập nhật order - có thể cập nhật status, hủy đơn, hoặc các thông tin khác
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:init,processing,finished process,not completed,done,cancelled',
            'note' => 'nullable|string|max:1000',
            'priority' => 'nullable|integer|min:1|max:10',

            // Cho việc hủy đơn
            'cancelled_reason' => 'required_if:status,cancelled|string|max:500',

            // Cho việc cập nhật order_dishes
            'order_dishes' => 'nullable|array',
            'order_dishes.*.dish_id' => 'required_with:order_dishes|exists:dishes,id',
            'order_dishes.*.quantity' => 'required_with:order_dishes|integer|min:1',
            'order_dishes.*.is_available' => 'nullable|boolean',
            'order_dishes.*.cancelled_reason' => 'nullable|string|max:500',
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

            $order = Order::findOrFail($id);
            $oldStatus = $order->status;
            $updateData = [];

            // Cập nhật thông tin cơ bản
            if ($request->has('note')) {
                $updateData['note'] = $request->note;
            }

            if ($request->has('priority')) {
                $updateData['priority'] = $request->priority;
            }

            // Xử lý cập nhật status
            if ($request->has('status')) {
                $newStatus = $request->status;

                // Kiểm tra logic status
                if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể thay đổi trạng thái của đơn đã bị hủy'
                    ], 400);
                }

                if ($oldStatus === 'done' && $newStatus !== 'done') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể thay đổi trạng thái của đơn đã hoàn thành'
                    ], 400);
                }

                $updateData['status'] = $newStatus;

                // Xử lý trường hợp hủy đơn
                if ($newStatus === 'cancelled') {
                    $updateData['cancelled_reason'] = $request->cancelled_reason;
                    $updateData['cancelled_by'] = Auth::id();
                    $updateData['cancelled_at'] = Carbon::now();

                    // Hủy tất cả order_dishes trong đơn
                    $order->order_dishes()->update([
                        'cancelled_reason' => $request->cancelled_reason,
                        'cancelled_by' => Auth::id(),
                        'cancelled_at' => Carbon::now(),
                        'is_available' => false,
                    ]);
                }
            }

            // Cập nhật thông tin order
            $order->update($updateData);

            // Xử lý cập nhật order_dishes nếu có
            if ($request->has('order_dishes')) {
                foreach ($request->order_dishes as $dishData) {
                    $orderDish = OrderDish::where('order_id', $order->id)
                                         ->where('dish_id', $dishData['dish_id'])
                                         ->first();

                    if ($orderDish) {
                        $dishUpdateData = [];

                        if (isset($dishData['quantity'])) {
                            $dishUpdateData['quantity'] = $dishData['quantity'];
                        }

                        if (isset($dishData['is_available'])) {
                            $dishUpdateData['is_available'] = $dishData['is_available'];
                        }

                        if (isset($dishData['cancelled_reason'])) {
                            $dishUpdateData['cancelled_reason'] = $dishData['cancelled_reason'];
                            $dishUpdateData['cancelled_by'] = Auth::id();
                            $dishUpdateData['cancelled_at'] = Carbon::now();
                            $dishUpdateData['is_available'] = false;
                        }

                        if (!empty($dishUpdateData)) {
                            $orderDish->update($dishUpdateData);
                        }
                    }
                }

                // Kiểm tra xem tất cả order_dishes đã sẵn sàng chưa để tự động cập nhật order status
                $this->checkAndUpdateOrderStatusToDone($order);
            }

            // Kiểm tra và tự động cập nhật order status thành 'done' nếu tất cả order_dishes đã sẵn sàng
            $this->checkAndUpdateOrderStatusToDone($order);

            DB::commit();

            // Load relationships để trả về
            $order->load([
                'table',
                'creator',
                'order_dishes.dish',
                'order_dishes.cancelledBy',
                'cancelledBy'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đơn hàng thành công',
                'data' => $order
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra và cập nhật trạng thái của đơn hàng thành 'done' nếu tất cả order_dishes (trừ những đã hủy) đều sẵn sàng.
     */
    private function checkAndUpdateOrderStatusToDone(Order $order)
    {
        // Chỉ kiểm tra nếu order chưa được hoàn thành hoặc hủy
        if (in_array($order->status, ['done', 'cancelled'])) {
            return;
        }

        $allOrderDishes = $order->order_dishes()->get();

        // Lọc ra những order_dishes chưa bị hủy (không có cancelled_reason và cancelled_at)
        $activeOrderDishes = $allOrderDishes->filter(function ($orderDish) {
            return is_null($orderDish->cancelled_reason) && is_null($orderDish->cancelled_at);
        });

        // Nếu không có order_dish nào còn active, không cần kiểm tra
        if ($activeOrderDishes->isEmpty()) {
            return;
        }

        // Kiểm tra xem tất cả order_dishes active đều có is_available = true không
        $allActiveAvailable = $activeOrderDishes->every(function ($orderDish) {
            return $orderDish->is_available == true;
        });

        // Nếu tất cả order_dishes active đều sẵn sàng, cập nhật order status thành 'done'
        if ($allActiveAvailable) {
            $order->update(['status' => 'done', 'priority' => 0]);
        }
    }
}
