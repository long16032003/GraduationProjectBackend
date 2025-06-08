<?php

namespace App\Http\Controllers\Reservation;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreReservationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'phone' => 'required|string',
            'name' => 'required|string',
            'reservation_date' => 'required|integer',
            'status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled'])],
            'notes' => 'nullable|string',
            'number_of_guests' => 'required|integer',
        ]);

        try {
            $data = $validated;

            // Convert Unix timestamp to MySQL datetime format
            $timestamp = (int) $data['reservation_date'];
            $data['reservation_date'] = Carbon::createFromTimestamp($timestamp)->format('Y-m-d H:i:s');

            // If authenticated as user, set creator_id and type
            dd(Auth::guard('web')->check(), Auth::guard('customer')->check());

            if (Auth::guard('web')->check()) {
                $data['creator_id'] = Auth::guard('web')->id();
                $data['creator_type'] = 'staff';
            }

            // If authenticated as customer, set customer_id as creator
            if (Auth::guard('customer')->check()) {
                $data['customer_id'] = Auth::guard('customer')->id();
                $data['creator_id'] = Auth::guard('customer')->id();
                $data['creator_type'] = 'customer';
            }

            dd($data);

            $reservation = Reservation::create($data);

            return new JsonResponse([
                'success' => true,
                'message' => 'Đặt bàn thành công',
                'data' => $reservation
            ], JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đặt bàn thất bại',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
