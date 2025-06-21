<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowBillController extends Controller
{
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $bill = Bill::filter($request->all())
                ->with(['orders.order_dishes.dish', 'table'])
                ->findOrFail($id);

            return new JsonResponse([
                'success' => true,
                'message' => 'Lấy hóa đơn thành công',
                'data' => $bill,
            ], JsonResponse::HTTP_OK);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ], JsonResponse::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}