<?php

namespace App\Http\Controllers\Dish;

use App\Http\Controllers\Controller;
use App\Models\Dish;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class StoreDishController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $auth = Auth::user();
        try {
            // Tạo bản ghi mới
            $dish = Dish::create([
                'creator_id' => $auth->id,
                'name' => $request->name,
                'description' => $request->description,
                'image_id' => $request->image_id,
                'price' => $request->price,
                'category_id' => $request->category_id,
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Món ăn được tạo thành công',
                'data' => $dish
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo món ăn thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}