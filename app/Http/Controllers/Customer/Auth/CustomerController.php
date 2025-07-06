<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
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
}
