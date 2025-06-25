<?php

namespace App\Http\Controllers\Ingredient;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexIngredientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $categories = Ingredient::filter($request->all())->with('image')->get();

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Dish categories fetched successfully',
                'data' => $categories,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}