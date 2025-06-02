<?php

namespace App\Http\Controllers\Dish;

use App\Http\Controllers\Controller;
use App\Models\Dish;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DeleteDishController extends Controller
{
    public function delete(Request $request, $id): JsonResponse
    {
        try {
            $dish = Dish::findOrFail($id);
            $dish->delete();

            return new JsonResponse([
                'success' => true,
                'message' => 'Món ăn được xóa thành công',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa món ăn thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}