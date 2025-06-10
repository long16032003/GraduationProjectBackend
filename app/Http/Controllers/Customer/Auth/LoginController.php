<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle a login request to the application.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the customer
        if (Auth::guard('customer')->attempt($request->only('phone', 'password'), $request->boolean('remember'))) {
            // Regenerate the session to prevent session fixation
            $request->session()->regenerate(true);

            // Get the authenticated customer
            $customer = Auth::guard('customer')->user();

            // Return success response with customer data and session info
            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'data' => $customer,
                'session_id' => $request->session()->getId(),
            ])->withHeaders([
                'Access-Control-Allow-Credentials' => 'true',
            ]);
        }

        throw ValidationException::withMessages([
            'phone' => ['Thông tin đăng nhập không chính xác.'],
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công'
        ]);
    }
}
