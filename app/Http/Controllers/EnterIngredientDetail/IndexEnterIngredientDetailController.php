<?php

namespace App\Http\Controllers\EnterIngredientDetail;

use App\Http\Controllers\Controller;
use App\Models\EnterIngredientDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexEnterIngredientDetailController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $enterIngredients = EnterIngredientDetail::filter($request->all())->with('enterIngredient', 'ingredient')->get();

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Danh sách phiếu nhập nguyên liệu',
                'data' => $enterIngredients,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}