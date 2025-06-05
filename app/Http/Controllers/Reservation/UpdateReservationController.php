<?php

namespace App\Http\Controllers\Reservation;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateReservationController extends Controller
{
    public function update(Request $request, $id): JsonResponse
    {
        $reservation = Reservation::findOrFail($id);

        // Check permissions - only creator or the customer who made the reservation can update
        if (Auth::guard('web')->check()) {
            // Staff/admin can update any reservation
        } elseif (Auth::guard('customer')->check() && $reservation->customer_id !== Auth::guard('customer')->id()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Bạn không có quyền cập nhật đặt bàn này'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'table_id' => 'sometimes|exists:tables,id',
            'phone' => 'sometimes|string',
            'name' => 'sometimes|string',
            'reservation_date' => 'sometimes|date_format:Y-m-d H:i:s|after_or_equal:now',
            'status' => ['sometimes', Rule::in(['pending', 'confirmed', 'cancelled'])],
            'note' => 'nullable|string',
        ]);

        // Convert reservation_date to timestamp if provided
        if (isset($validated['reservation_date'])) {
            $validated['reservation_date'] = Carbon::parse($validated['reservation_date']);
        }

        $reservation->update($validated);

        return new JsonResponse([
            'success' => true,
            'message' => 'Cập nhật đặt bàn thành công',
            'data' => $reservation->fresh()
        ], JsonResponse::HTTP_OK);
    }
}
