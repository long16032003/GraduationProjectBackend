<?php

namespace App\Http\Controllers\PromotionCode;

use App\Http\Controllers\Controller;
use App\Models\PromotionCode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class IndexPromotionCodeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $auth = Auth::user();

        $query = PromotionCode::filter($request->all())->with(['promotion', 'customer']);

        // Filter by customer_id if provided
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by used status
        if ($request->has('used')) {
            if ($request->used === 'true' || $request->used === '1') {
                $query->whereNotNull('used_at');
            } elseif ($request->used === 'false' || $request->used === '0') {
                $query->whereNull('used_at');
            }
        }

        // Filter by promotion_id
        if ($request->has('promotion_id')) {
            $query->where('promotion_id', $request->promotion_id);
        }

        $promotionCodes = $query->get();

        return new JsonResponse([
            'success' => true,
            'message' => 'Lấy danh sách mã khuyến mãi thành công',
            'data' => $promotionCodes
        ], JsonResponse::HTTP_OK);
    }
}
