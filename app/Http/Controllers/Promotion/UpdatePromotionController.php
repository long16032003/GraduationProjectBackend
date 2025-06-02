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
                'required_points' => $request->required_points,
                'limit_per_user_count' => $request->limit_per_user_count,
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