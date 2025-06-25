<?php

namespace App\Http\Controllers\EnterIngredient;

use App\Http\Controllers\Controller;
use App\Models\EnterIngredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexEnterIngredientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $enterIngredients = EnterIngredient::filter($request->all())->with('creator', 'details', 'details.ingredient')->get();

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