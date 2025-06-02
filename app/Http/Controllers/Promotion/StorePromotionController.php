<?php

namespace App\Http\Controllers\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StorePromotionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $auth = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'discount_percentage' => 'required|integer|min:0|max:100',
            'required_points' => 'required|integer|min:0',
            'limit_per_user_count' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $promotion = Promotion::create([
                'creator_id' => $auth->id,
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'discount_percentage' => $request->discount_percentage,
                'required_points' => $request->required_points,
                'limit_per_user_count' => $request->limit_per_user_count,
            ]);

            return new JsonResponse($promotion, JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo khuyến mãi thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}