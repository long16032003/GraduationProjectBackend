<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DeleteStaffController extends Controller
{
    public function delete(Request $request, $id): JsonResponse
    {
        try {
            $staff = User::findOrFail($id);
            $staff->delete();

            return new JsonResponse([
                'success' => true,
                'message' => 'Nhân viên được xóa thành công',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa nhân viên thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}