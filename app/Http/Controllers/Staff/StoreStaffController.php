<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class StoreStaffController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Validate dữ liệu đầu vào
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|email|max:255',
        //     'password' => ['required', Rules\Password::defaults()],
        //     'phone' => 'required|string|max:255',
        //     'role' => 'required|string',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Validation error',
        //         'errors' => $validator->errors()
        //     ], 422);
        // }
        dd($request->all());
        try {
            // Sử dụng transaction để đảm bảo tính nhất quán của dữ liệu
            DB::beginTransaction();

            // Tạo tài khoản user với mật khẩu từ request
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(), // Xác thực email luôn
            ]);

            if($user){
                // Tạo bản ghi staff và liên kết với user
                $staff = Staff::create([
                    'user_id' => $user->id, // Thêm liên kết với user
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'role' => $request->role,
                ]);
            }

            // Commit transaction
            DB::commit();

            // Gửi email thông báo tài khoản cho nhân viên
            // TODO: Implement email notification with credentials

            return new JsonResponse([
                'success' => true,
                'message' => 'Nhân viên và tài khoản được tạo thành công',
                'data' => [
                    'staff' => $staff,
                    'user' => [
                        'email' => $user->email,
                        // Không trả về password trong response
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Tạo nhân viên thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}