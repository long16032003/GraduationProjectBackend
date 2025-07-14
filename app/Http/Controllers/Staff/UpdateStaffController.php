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

    public function changePassword(Request $request, $uuid): JsonResponse
    {
        try {
            $auth = Auth::user();
            $staff = User::where('uuid', $uuid)->firstOrFail();

            // Chỉ cho phép staff đổi mật khẩu của chính mình hoặc admin có quyền
            // if ($auth->id !== $staff->id && !$auth->hasPermission('user:update')) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Bạn không có quyền thực hiện thao tác này',
            //     ], 403);
            // }

            // Validation cho các field
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Xác thực mật khẩu hiện tại
            if (!Hash::check($request->current_password, $staff->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không chính xác',
                    'errors' => ['current_password' => ['Mật khẩu hiện tại không chính xác']]
                ], 422);
            }

            // Cập nhật mật khẩu mới
            $staff->update([
                'password' => Hash::make($request->new_password),
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công',
                'data' => $staff->fresh()->makeHidden(['password'])
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
