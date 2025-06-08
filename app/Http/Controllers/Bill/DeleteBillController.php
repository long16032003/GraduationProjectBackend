<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DeleteBillController extends Controller
{
    public function delete(Request $request, $id): JsonResponse
    {
        try {
            $bill = Bill::findOrFail($id);
            $bill->delete();

            return new JsonResponse([
                'success' => true,
                'message' => 'Hóa đơn được xóa thành công',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa hóa đơn thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}