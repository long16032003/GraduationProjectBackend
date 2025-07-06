<?php

namespace App\Http\Controllers\ExportIngredient;

use App\Http\Controllers\Controller;
use App\Models\ExportIngredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexExportIngredientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $exportIngredients = ExportIngredient::filter($request->all())->with('creator', 'details', 'details.ingredient')->get();

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Danh sách phiếu xuất nguyên liệu',
                'data' => $exportIngredients,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}