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
            // Xử lý upload ảnh nếu có
            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imagePath = Storage::disk('public')->put('dishes', $file);
            }

            // Tạo bản ghi mới
            $dish = Dish::create([
                'creator_id' => $auth->id,
                'name' => $request->name,
                'description' => $request->description,
                'image' => $imagePath,
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