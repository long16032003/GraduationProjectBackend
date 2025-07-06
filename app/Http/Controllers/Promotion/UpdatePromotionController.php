<?php

namespace App\Http\Controllers\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class UpdatePromotionController extends Controller
{
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $auth = Auth::user();
            $promotion = Promotion::findOrFail($id);
            $promotion->update([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'discount_percentage' => $request->discount_percentage,
                'discount_amount' => $request->discount_amount,
                'discount_type' => $request->discount_type,
                'min_order_amount' => $request->min_order_amount,
                'max_discount_amount' => $request->max_discount_amount,
                'required_points' => $request->required_points,
                'image_id' => $request->image_id,
                'limit' => $request->limit,
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Khuyến mãi được cập nhật thành công',
                'data' => $promotion
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật khuyến mãi thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}