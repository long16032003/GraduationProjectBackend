<?php

namespace App\Http\Controllers\OrderDish;

use App\Http\Controllers\Controller;
use App\Models\OrderDish;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreOrderDishController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $auth = Auth::user();
        $validator = Validator::make($request->all(), [
            'table_id' => 'required|exists:tables,id',
            'bill_id' => 'required|exists:bills,id',
            'dish_id' => 'required|exists:dishes,id',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'total_price' => 'required|numeric',
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
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'total_price' => $request->total_price,
        ];

        try {
            $orderDish = OrderDish::create($dataCreate);

            return new JsonResponse($orderDish, JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo đơn món thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}