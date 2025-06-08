<?php

namespace App\Http\Controllers\Reservation;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Reservation::filter($request->all());

        // If customer is logged in, only show their reservations
        // if (Auth::guard('customer')->check()) {
        //     $query->where('customer_id', Auth::guard('customer')->id());
        // }

        $reservations = $query->with(['table', 'customer'])->get();

        return new JsonResponse([
            'success' => true,
            'data' => $reservations
        ], JsonResponse::HTTP_OK);
    }
}
