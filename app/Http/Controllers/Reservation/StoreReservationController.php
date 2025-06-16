<?php

namespace App\Http\Controllers\Reservation;

use App\Http\Controllers\Controller;
use App\Models\Customer;
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

            $user = $request->user();

            if ($user instanceof User) {
                $data['creator_id'] = $user->id;
                $data['creator_type'] = 'staff';
            }

//             If authenticated as customer, set customer_id as creator
            if ($user instanceof Customer) {
                $data['customer_id'] = $user->id;
                $data['creator_id'] = $user->id;
                $data['creator_type'] = 'customer';
            }

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
