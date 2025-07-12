<?php

namespace App\Http\Controllers\Order;

use App\Events\NewOrderEvent;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDish;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreOrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $auth = Auth::user();
        $validator = Validator::make($request->all(), [
            'table_id' => 'required|exists:tables,id',
            'bill_id' => 'required|exists:bills,id',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $dataCreate = [
            'creator_id' => $auth->id,
            'table_id' => $request->table_id,
            'bill_id' => $request->bill_id,
            'note' => $request->note,
            'status' => Order::STATUS_INIT,
            'order_time' => now(),
        ];

        try {
            $order = Order::create($dataCreate);
            if($order) {
                foreach($request->order_dishes as $order_dish) {
                    $orderDish = OrderDish::create([
                        'order_id' => $order->id,
                        'dish_id' => $order_dish['dish_id'],
                        'quantity' => $order_dish['quantity'],
                        'price_at_order_time' => $order_dish['price_at_order_time'],
                    ]);
                }

                // // Load relationships for broadcasting
                // $order->load(['orderDishes.dish', 'table']);

                // // Broadcast new order event
                // NewOrderEvent::dispatch($order);
            }
            return new JsonResponse($order, JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo đơn hàng thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
