<?php

namespace App\Http\Controllers\Ingredient;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreIngredientController extends Controller
{
    public function store(Request $request): JsonResponse
    {

        $auth = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'min_quantity' => 'required|integer',
            'image_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            // Tạo nguyên liệu mới
            $ingredient = Ingredient::create([
                'creator_id' => $auth->id,
                'name' => $request->name,
                'unit' => $request->unit,
                'quantity' => 0,
                'min_quantity' => $request->min_quantity,
                'image_id' => $request->image_id,
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Nguyên liệu được tạo thành công',
                'data' => $ingredient
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo nguyên liệu thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}