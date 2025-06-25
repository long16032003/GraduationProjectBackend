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
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'image_id' => 'sometimes|integer|exists:media,id',
            'price' => 'sometimes|integer|min:0',
            'category_id' => 'sometimes|integer|exists:dish_categories,id',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dish = Dish::findOrFail($id);

            // Chỉ update những trường có trong request
            $updateData = [];

            if ($request->has('name')) {
                $updateData['name'] = $request->name;
            }

            if ($request->has('description')) {
                $updateData['description'] = $request->description;
            }

            if ($request->has('image_id')) {
                $updateData['image_id'] = $request->image_id;
            }

            if ($request->has('price')) {
                $updateData['price'] = $request->price;
            }

            if ($request->has('category_id')) {
                $updateData['category_id'] = $request->category_id;
            }

            if ($request->has('is_active')) {
                $updateData['is_active'] = $request->is_active;
            }

            if ($request->has('is_featured')) {
                $updateData['is_featured'] = $request->is_featured;
            }

            $dish->update($updateData);

            return new JsonResponse([
                'success' => true,
                'message' => 'Món ăn được cập nhật thành công',
                'data' => $dish->fresh()
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