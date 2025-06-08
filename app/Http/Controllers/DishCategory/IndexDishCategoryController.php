<?php

namespace App\Http\Controllers\DishCategory;

use App\Http\Controllers\Controller;
use App\Models\DishCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexDishCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $categories = DishCategory::filter($request->all())->get();

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