<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use EloquentFilter\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::filter($request->all())->with('order_dishes.dish', 'table', 'creator')->get();

        return new JsonResponse([
            'success' => true,
            'message' => 'Lấy danh sách đơn hàng thành công',
            'data' => $orders
        ], JsonResponse::HTTP_OK);
    }
}