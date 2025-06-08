<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class UpdateBillController extends Controller
{
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $auth = Auth::user();
            $bill = Bill::findOrFail($id);
            $bill->update([
                'status' => $request->status,
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Hóa đơn được cập nhật thành công',
                'data' => $bill
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật hóa đơn thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}