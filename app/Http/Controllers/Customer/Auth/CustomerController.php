<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Get the authenticated customer.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Auth::guard('customer')->user()
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $customer = Customer::filter($request->all())
            ->with(['promotionCodes', 'bills', 'reservations'])->get();

        return response()->json([
            'success' => true,
            'data' => $customer
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $customer = Customer::find($id);
        return response()->json([
            'success' => true,
            'data' => $customer
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $customer = Customer::find($id);
        $customer->update($request->all());
        return new JsonResponse([
            'success' => true,
            'data' => $customer
        ], JsonResponse::HTTP_OK);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $customer = Customer::find($id);
        $customer->delete();
        return response()->json([
            'success' => true,
            'data' => $customer
        ]);
    }

    public function changePassword(Request $request, $uuid): JsonResponse
    {
        try {
            $auth = Auth::guard('customer')->user();
            $customer = Customer::where('uuid', $uuid)->firstOrFail();

            // Chỉ cho phép customer đổi mật khẩu của chính mình
            if ($auth->id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện thao tác này',
                ], 403);
            }

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
            if (!Hash::check($request->current_password, $customer->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không chính xác',
                    'errors' => ['current_password' => ['Mật khẩu hiện tại không chính xác']]
                ], 422);
            }

            // Cập nhật mật khẩu mới
            $customer->update([
                'password' => Hash::make($request->new_password),
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công',
                'data' => $customer->fresh()->makeHidden(['password'])
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
