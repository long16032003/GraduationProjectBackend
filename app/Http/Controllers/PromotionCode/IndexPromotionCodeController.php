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

        // Sử dụng EloquentFilter - đã có các method trong PromotionCodeFilter
        $promotionCodes = PromotionCode::filter($request->all())
            ->with(['promotion', 'customer'])
            ->get();

        return new JsonResponse([
            'success' => true,
            'message' => 'Lấy danh sách mã khuyến mãi thành công',
            'data' => $promotionCodes
        ], JsonResponse::HTTP_OK);
    }
}
