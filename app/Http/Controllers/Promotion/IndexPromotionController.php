<?php

namespace App\Http\Controllers\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use EloquentFilter\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexPromotionController extends Controller
{
    use Filterable;
    public function index(Request $request): JsonResponse
    {
        $promotions = Promotion::filter($request->all())->get();
        return new JsonResponse([
            'success' => true,
            'message' => 'Lấy danh sách khuyến mãi thành công',
            'data' => $promotions
        ], JsonResponse::HTTP_OK);
    }
}