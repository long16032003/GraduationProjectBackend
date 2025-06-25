<?php

namespace App\Http\Controllers\Ingredient;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UpdateIngredientController extends Controller
{
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $ingredient = Ingredient::findOrFail($id);
            $ingredient->update([
                'name' => $request->name,
                'unit' => $request->unit,
                'min_quantity' => $request->min_quantity,
                'image_id' => $request->image_id,
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Nguyên liệu được cập nhật thành công',
                'data' => $ingredient
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật nguyên liệu thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}