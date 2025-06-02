<?php

namespace App\Http\Controllers\DishCategory;

use App\Http\Controllers\Controller;
use App\Models\DishCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UpdateDishCategoryController extends Controller
{
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $category = DishCategory::findOrFail($id);
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Danh mục món ăn được cập nhật thành công',
                'data' => $category
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật danh mục món ăn thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}