<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Update the authenticated customer's profile.
     */
    // public function update(Request $request): JsonResponse
    // {
    //     $customer = Auth::guard('customer')->user();

    //     $validated = $request->validate([
    //         'name' => 'sometimes|string|max:255',
    //         'phone' => 'sometimes|string|max:20|unique:customers,phone,' . $customer->id,
    //         'email' => 'sometimes|string|email|max:255|unique:customers,email,' . $customer->id,
    //     ]);

    //     $customer->update($validated);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Cập nhật thông tin thành công',
    //         'data' => $customer->fresh()
    //     ]);
    // }
}
