<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\PromotionCode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class UpdateBillController extends Controller
{
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $auth = Auth::user();
            $bill = Bill::findOrFail($id);
            $bill->update([
                'status' => $request->status,
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Hóa đơn được cập nhật thành công',
                'data' => $bill
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật hóa đơn thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pay(Request $request, $id): JsonResponse
    {
        try {
            $auth = Auth::guard('web')->user();
            $bill = Bill::findOrFail($id);
            $result = $bill->update([
                'status' => Bill::STATUS_PAID,
                'total_amount' => $request->total_amount,
                'discount_amount' => $request->discount_amount,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            $promotion_code = PromotionCode::where('code', $request->coupon_code)->first();
            if ($promotion_code && $result) {
                $promotion_code->update([
                    'used_at' => now(),
                ]);
            }

            return new JsonResponse([
                'success' => true,
                'message' => 'Thanh toán thành công',
                'data' => $bill
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật hóa đơn thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}