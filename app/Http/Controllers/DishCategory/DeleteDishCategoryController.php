<?php

namespace App\Http\Controllers\DishCategory;

use App\Http\Controllers\Controller;
use App\Models\DishCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DeleteDishCategoryController extends Controller
{
    public function delete(Request $request, $id): JsonResponse
    {
        try {
            $category = DishCategory::findOrFail($id);
            $category->delete();

            return new JsonResponse([
                'success' => true,
                'message' => 'Danh mục món ăn được xóa thành công',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa danh mục món ăn thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}