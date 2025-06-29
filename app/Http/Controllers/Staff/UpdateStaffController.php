<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdateStaffController extends Controller
{
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $auth = Auth::user();
            $staff = User::findOrFail($id);

            // Validation cho các field có thể được cập nhật
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
                'phone' => 'sometimes|nullable|string|max:20',
                'role' => 'sometimes|nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Chỉ cập nhật các field được gửi trong request
            $updateData = [];

            if ($request->has('name')) {
                $updateData['name'] = $request->name;
            }

            if ($request->has('email')) {
                $updateData['email'] = $request->email;
            }

            if ($request->has('phone')) {
                $updateData['phone'] = $request->phone;
            }
            if ($request->has('role')) {
                $updateData['role'] = $request->role;
            }

            // Cập nhật chỉ những field có trong $updateData
            if (!empty($updateData)) {
                $staff->update($updateData);
            }

            return new JsonResponse([
                'success' => true,
                'message' => 'Nhân viên được cập nhật thành công',
                'data' => $staff->fresh() // Lấy dữ liệu mới từ database
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật nhân viên thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request, $id): JsonResponse
    {
        try {
            $auth = Auth::user();
            $staff = User::findOrFail($id);

            // Validation cho các field có thể được cập nhật
            $validator = Validator::make($request->all(), [
                'password' => 'sometimes|required|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $staff->update([
                'password' => Hash::make($request->password),
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công',
                'data' => $staff->fresh() // Lấy dữ liệu mới từ database
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đổi mật khẩu thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}