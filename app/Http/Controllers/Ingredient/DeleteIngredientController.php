<?php

namespace App\Http\Controllers\Ingredient;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DeleteIngredientController extends Controller
{
    public function delete(Request $request, $id): JsonResponse
    {
        try {
            $ingredient = Ingredient::findOrFail($id);
            $ingredient->delete(); // Soft delete

            return new JsonResponse([
                'success' => true,
                'message' => 'Nguyên liệu được xóa thành công',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa nguyên liệu thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
