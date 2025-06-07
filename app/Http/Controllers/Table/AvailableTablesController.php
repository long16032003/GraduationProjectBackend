<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailableTablesController extends Controller
{
    /**
     * Lấy danh sách bàn trống theo ngày, giờ và số người
     */
    public function getAvailableTables(Request $request): JsonResponse
    {
        // $validated = $request->validate([
        //     'date' => 'required|date_format:Y-m-d',
        //     'time' => 'required|date_format:H:i',
        //     'guests' => 'required|integer|min:1',
        // ]);
        // dd($validated);

        try {
            // Tạo datetime từ date và time
            $reservationDateTime = Carbon::parse($request->date . ' ' . $request->time);


            // Thời gian đặt bàn (giả sử mỗi lượt đặt bàn kéo dài 2 giờ)
            $reservationStartTime = $reservationDateTime->copy();
            $reservationEndTime = $reservationDateTime->copy()->addHours(2);

            // Lấy tất cả các bàn có sức chứa phù hợp
            $tables = Table::where('capacity', '>=', intval($request->guests))
                ->get();

            // Lọc ra các bàn đã được đặt trong khoảng thời gian này
            $reservedTableIds = Reservation::where('status', '!=', 'cancelled')
                ->where(function ($query) use ($reservationStartTime, $reservationEndTime) {
                    // Kiểm tra xem có đặt bàn nào trong khoảng thời gian này không
                    $query->where(function ($q) use ($reservationStartTime, $reservationEndTime) {
                        $q->where('reservation_date', '>=', $reservationStartTime)
                          ->where('reservation_date', '<', $reservationEndTime);
                    });
                })
                ->pluck('table_id')
                ->toArray();

            // Lọc bỏ các bàn đã được đặt
            $availableTables = $tables->filter(function ($table) use ($reservedTableIds) {
                return !in_array($table->id, $reservedTableIds);
            })->values();

            return new JsonResponse([
                'success' => true,
                'message' => 'Lấy danh sách bàn trống thành công',
                'data' => $availableTables
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách bàn trống',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Kiểm tra xem bàn có trống vào thời điểm cụ thể không
     */
    public function checkTableAvailability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
        ]);

        try {
            // Tạo datetime từ date và time
            $reservationDateTime = Carbon::parse($validated['date'] . ' ' . $validated['time']);

            // Thời gian đặt bàn (giả sử mỗi lượt đặt bàn kéo dài 2 giờ)
            $reservationStartTime = $reservationDateTime->copy();
            $reservationEndTime = $reservationDateTime->copy()->addHours(2);

            // Kiểm tra xem bàn có đang được đặt trong khoảng thời gian này không
            $isReserved = Reservation::where('table_id', $validated['table_id'])
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($reservationStartTime, $reservationEndTime) {
                    $query->where(function ($q) use ($reservationStartTime, $reservationEndTime) {
                        $q->where('reservation_date', '>=', $reservationStartTime)
                          ->where('reservation_date', '<', $reservationEndTime);
                    });
                })
                ->exists();

            // Kiểm tra trạng thái bàn
            $table = Table::find($validated['table_id']);
            $isAvailable = $table->status === 'available' && !$isReserved;

            return new JsonResponse([
                'success' => true,
                'message' => $isAvailable ? 'Bàn còn trống' : 'Bàn đã được đặt',
                'data' => [
                    'is_available' => $isAvailable,
                    'table' => $table
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể kiểm tra trạng thái bàn',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}