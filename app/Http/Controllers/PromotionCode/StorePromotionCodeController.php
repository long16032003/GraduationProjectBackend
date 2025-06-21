<?php

namespace App\Http\Controllers\PromotionCode;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PromotionCode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StorePromotionCodeController extends Controller
{
    function generateCode($length = 10) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'promotion_id' => 'required|exists:promotions,id',
            'points_used' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $code = $this->generateCode(10);

        try {
            $auth = Auth::guard('customer')->user();
            // Tạo bản ghi mới
            $promotionCode = PromotionCode::create([
                'promotion_id' => $request->promotion_id,
                'code' => $code,
                'customer_id' => $auth->id,
            ]);
            if($promotionCode){
                $customer = Customer::find($auth->id);
                $customer->point -= $request->points_used;
                $customer->save();
            }

            return new JsonResponse($promotionCode, JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo mã khuyến mãi thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}