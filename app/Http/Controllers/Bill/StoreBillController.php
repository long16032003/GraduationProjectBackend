<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Customer;
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
            'table_id' => 'required|exists:tables,id',
            'customer_name' => 'nullable|string',
            'customer_phone' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $dataCreate = [
            'creator_id' => $auth->id,
            'table_id' => $request->table_id,
            'customer_phone' => $request->customer_phone,
            'customer_name' => isset($request->customer_name) ? $request->customer_name : null,
            'notes' => isset($request->notes) ? $request->notes : null,
        ];

        $customer = Customer::where('phone', $request->customer_phone)->first();
        if($customer) {
            $dataCreate['customer_id'] = $customer->id;
            $dataCreate['customer_name'] = $customer->name;
        }

        try {
            $bill = Bill::create($dataCreate);

            return new JsonResponse([
                'success' => true,
                'message' => 'Tạo hóa đơn thành công',
                'data' => $bill
            ], JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo hóa đơn thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}