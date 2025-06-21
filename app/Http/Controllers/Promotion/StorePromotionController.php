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
            'discount_percentage' => 'required_if:discount_type,percentage|numeric|min:0|max:100|nullable',
            'discount_amount' => 'required_if:discount_type,fixed_amount|numeric|min:0|nullable',
            'discount_type' => 'required|string|in:percentage,fixed_amount',
            'min_order_amount' => 'numeric|min:0|nullable',
            'max_discount_amount' => 'numeric|min:0|nullable',
            'required_points' => 'integer|min:0|nullable',
            'limit' => 'nullable|integer|min:0',
            'image_id' => 'nullable|integer|exists:media,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dataCreate = [
                'creator_id' => $auth->id,
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'discount_type' => $request->discount_type,
                'min_order_amount' => $request->min_order_amount,
                'max_discount_amount' => $request->max_discount_amount,
                'required_points' => $request->required_points,
                'image_id' => $request->image_id,
                'limit' => $request->limit,
            ];

            // Chỉ thêm discount_percentage hoặc discount_amount dựa trên discount_type
            if ($request->discount_type === 'percentage') {
                $dataCreate['discount_percentage'] = $request->discount_percentage;
                $dataCreate['discount_amount'] = 0; // Hoặc giá trị mặc định
            } else {
                $dataCreate['discount_amount'] = $request->discount_amount;
                $dataCreate['discount_percentage'] = 0; // Hoặc giá trị mặc định
            }

            $promotion = Promotion::create($dataCreate);

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
