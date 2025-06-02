<?php

namespace App\Http\Controllers\DishCategory;

use App\Http\Controllers\Controller;
use App\Models\DishCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreDishCategoryController extends Controller
{
    public function store(Request $request): JsonResponse
    {

        $auth = Auth::user();

        try {
            // Tạo bản ghi mới
            $category = DishCategory::create([
                'creator_id' => $auth->id,
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Danh mục món ăn được tạo thành công',
                'data' => $category
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo danh mục món ăn thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}