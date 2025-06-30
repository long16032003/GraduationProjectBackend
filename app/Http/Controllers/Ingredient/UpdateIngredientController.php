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

    /** Cập nhật số lượng tồn kho của mỗi nguyên liệu */
    public function updateQuantitys(Request $request): JsonResponse
    {
        try {
            $ingredientData = $request->all();
            $updatedIngredients = [];
            $errors = [];

            // Validate dữ liệu đầu vào
            foreach ($ingredientData as $ingredientId => $quantity) {
                if (!is_numeric($ingredientId) || !is_numeric($quantity) || $quantity < 0) {
                    $errors[] = "Invalid data for ingredient ID: {$ingredientId}";
                    continue;
                }

                // Tìm và cập nhật ingredient
                $ingredient = Ingredient::find($ingredientId);
                if ($ingredient) {
                    $oldQuantity = $ingredient->quantity;
                    $ingredient->update(['quantity' => $quantity]);

                    $updatedIngredients[] = [
                        'id' => $ingredient->id,
                        'name' => $ingredient->name,
                        'old_quantity' => $oldQuantity,
                        'new_quantity' => $quantity,
                        'updated_at' => $ingredient->updated_at
                    ];
                } else {
                    $errors[] = "Ingredient not found with ID: {$ingredientId}";
                }
            }

            // Nếu có lỗi nhưng vẫn có ingredients được cập nhật thành công
            if (!empty($errors) && !empty($updatedIngredients)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật một phần thành công',
                    'data' => $updatedIngredients,
                    'errors' => $errors,
                    'updated_count' => count($updatedIngredients),
                    'error_count' => count($errors)
                ], 200);
            }

            // Nếu có lỗi và không có gì được cập nhật
            if (!empty($errors) && empty($updatedIngredients)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cập nhật thất bại',
                    'errors' => $errors
                ], 400);
            }

            // Thành công hoàn toàn
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật số lượng tồn kho thành công',
                'data' => $updatedIngredients,
                'updated_count' => count($updatedIngredients)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật số lượng tồn kho thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}