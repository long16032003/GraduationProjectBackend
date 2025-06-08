<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowPostController extends Controller
{
    /**
     * Lấy thông tin chi tiết đặt bàn
     */
    public function show(int $id): JsonResponse
    {
        try {
            $reservation = Reservation::with(['table', 'customer'])->findOrFail($id);

            return new JsonResponse([
                'success' => true,
                'message' => 'Lấy thông tin đặt bàn thành công',
                'data' => $reservation
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin đặt bàn',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}