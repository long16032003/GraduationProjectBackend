<?php

namespace App\Http\Controllers\Dish;

use App\Http\Controllers\Controller;
use App\Models\Dish;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UpdateDishController extends Controller
{
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $dish = Dish::findOrFail($id);
            $dish->update([
                'name' => $request->name,
                'description' => $request->description,
                'image_id' => $request->image_id,
                'price' => $request->price,
                'category_id' => $request->category_id,
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Món ăn được cập nhật thành công',
                'data' => $dish
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật món ăn thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}