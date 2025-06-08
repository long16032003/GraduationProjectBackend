<?php

namespace App\Http\Controllers\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DeletePromotionController extends Controller
{
    public function delete(Request $request, $id): JsonResponse
    {
        try {
            $promotion = Promotion::findOrFail($id);
            $promotion->delete();

            return new JsonResponse([
                'success' => true,
                'message' => 'Khuyến mãi được xóa thành công',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa khuyến mãi thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}