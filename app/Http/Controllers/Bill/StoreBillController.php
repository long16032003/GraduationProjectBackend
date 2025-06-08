<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreBillController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $auth = Auth::user();

        $validator = Validator::make($request->all(), [

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bill = Bill::create([
                'customer_id' => $auth->id,
                'total_amount' => $request->total_amount,
                'status' => $request->status,
            ]);

            return new JsonResponse($bill, JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo khuyến mãi thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}