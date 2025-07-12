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

        // Handle sorting
        $sortField = $request->input('sort_field', 'reservation_date');
        $sortOrder = $request->input('sort_order', 'desc');

        // Support for sorters array format (from query key)
        if ($request->has('sorters')) {
            $sorters = $request->input('sorters');
            if (is_string($sorters)) {
                $sorters = json_decode($sorters, true);
            }

            if (is_array($sorters) && !empty($sorters)) {
                $sorter = $sorters[0]; // Use first sorter
                $sortField = $sorter['field'] ?? 'reservation_date';
                $sortOrder = $sorter['order'] ?? 'desc';
            }
        }

        // Apply sorting based on allowed fields
        $allowedSortFields = ['reservation_date', 'created_at', 'updated_at', 'name', 'phone', 'status'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('reservation_date', 'desc');
        }

        // Handle pagination
        $page = $request->input('current', $request->input('page', 1));
        $pageSize = $request->input('pageSize', $request->input('per_page', 10));

        if ($request->input('hasPagination', true)) {
            $reservations = $query->with(['table', 'customer'])
                                 ->paginate($pageSize, ['*'], 'page', $page);

            return new JsonResponse([
                'success' => true,
                'data' => $reservations->items(),
                'pagination' => [
                    'current' => $reservations->currentPage(),
                    'pageSize' => $reservations->perPage(),
                    'total' => $reservations->total(),
                    'totalPages' => $reservations->lastPage(),
                ]
            ], JsonResponse::HTTP_OK);
        } else {
            $reservations = $query->with(['table', 'customer'])->get();

            return new JsonResponse([
                'success' => true,
                'data' => $reservations
            ], JsonResponse::HTTP_OK);
        }
    }
}
