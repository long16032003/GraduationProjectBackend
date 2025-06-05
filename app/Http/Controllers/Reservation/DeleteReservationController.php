<?php

namespace App\Http\Controllers\Reservation;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeleteReservationController extends Controller
{
    public function delete(Request $request, $id): JsonResponse
    {
        $reservation = Reservation::findOrFail($id);

        // Check permissions - only creator or the customer who made the reservation can delete
        if (Auth::guard('web')->check()) {
            // Staff/admin can delete any reservation
        } elseif (Auth::guard('customer')->check() && $reservation->customer_id !== Auth::guard('customer')->id()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Bạn không có quyền xóa đặt bàn này'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $reservation->delete();

        return new JsonResponse([
            'success' => true,
            'message' => 'Xóa đặt bàn thành công'
        ], JsonResponse::HTTP_OK);
    }
}
