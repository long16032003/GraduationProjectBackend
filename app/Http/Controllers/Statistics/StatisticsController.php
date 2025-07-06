<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statistics\StatisticsRequest;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatisticsController extends Controller
{
    /**
     * Lấy thống kê doanh thu
     */
    public function getRevenue(StatisticsRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            // Khởi tạo query builder
            $query = Bill::query()
                ->where('status', Bill::STATUS_PAID)
                ->whereBetween('created_at', [
                    $validatedData['start_date'],
                    $validatedData['end_date'] . ' 23:59:59'
                ]);

            // Lọc theo phương thức thanh toán nếu có
            if (!empty($validatedData['payment_method']) && $validatedData['payment_method'] !== 'all') {
                $query->where('payment_method', $validatedData['payment_method']);
            }

            // Lấy dữ liệu tổng quan
            $summary = $this->getSummaryData($query->clone(), $validatedData);

            // Lấy dữ liệu biểu đồ theo loại thống kê
            $chartData = $this->getChartData($query->clone(), $validatedData);

            // Lấy dữ liệu phân tích chi tiết
            $breakdown = $this->getBreakdownData($query->clone());

            return response()->json([
                'status' => 'success',
                'message' => 'Lấy thống kê thành công',
                'data' => [
                    'summary' => $summary,
                    'chart_data' => $chartData,
                    'breakdown' => $breakdown
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Statistics error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi hệ thống, vui lòng thử lại sau',
                'data' => null
            ], 500);
        }
    }

    /**
     * Lấy dữ liệu tổng quan
     */
    private function getSummaryData($query, array $params): array
    {
        $result = $query->select([
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_amount - discount_amount) as total_revenue'),
            DB::raw('AVG(total_amount - discount_amount) as average_order_value')
        ])->first();

        return [
            'total_revenue' => (float) ($result->total_revenue ?? 0),
            'total_orders' => (int) ($result->total_orders ?? 0),
            'average_order_value' => (float) ($result->average_order_value ?? 0),
            'period' => $params['start_date'] . ' to ' . $params['end_date']
        ];
    }

    /**
     * Lấy dữ liệu biểu đồ theo loại thống kê
     */
    private function getChartData($query, array $params): array
    {
        $type = $params['type'];
        $startDate = Carbon::parse($params['start_date']);
        $endDate = Carbon::parse($params['end_date']);

        switch ($type) {
            case 'daily':
                return $this->getDailyChartData($query, $startDate, $endDate);
            case 'monthly':
                return $this->getMonthlyChartData($query, $startDate, $endDate);
            case 'yearly':
                return $this->getYearlyChartData($query, $startDate, $endDate);
            default:
                return [];
        }
    }

    /**
     * Lấy dữ liệu biểu đồ theo ngày
     */
    private function getDailyChartData($query, Carbon $startDate, Carbon $endDate): array
    {
        $data = $query->select([
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount - discount_amount) as revenue'),
            DB::raw('COUNT(*) as orders')
        ])
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

        $result = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayData = $data->get($dateStr);

            $result[] = [
                'date' => $dateStr,
                'revenue' => $dayData ? (float) $dayData->revenue : 0,
                'orders' => $dayData ? (int) $dayData->orders : 0,
                'label' => $currentDate->format('d/m/Y')
            ];

            $currentDate->addDay();
        }

        return $result;
    }

    /**
     * Lấy dữ liệu biểu đồ theo tháng
     */
    private function getMonthlyChartData($query, Carbon $startDate, Carbon $endDate): array
    {
        $data = $query->select([
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount - discount_amount) as revenue'),
            DB::raw('COUNT(*) as orders')
        ])
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get()
        ->keyBy(function ($item) {
            return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
        });

        $result = [];
        $currentDate = $startDate->copy()->startOfMonth();
        $endDate = $endDate->copy()->endOfMonth();

        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m');
            $monthData = $data->get($monthKey);

            $result[] = [
                'date' => $monthKey,
                'revenue' => $monthData ? (float) $monthData->revenue : 0,
                'orders' => $monthData ? (int) $monthData->orders : 0,
                'label' => $currentDate->format('m/Y')
            ];

            $currentDate->addMonth();
        }

        return $result;
    }

    /**
     * Lấy dữ liệu biểu đồ theo năm
     */
    private function getYearlyChartData($query, Carbon $startDate, Carbon $endDate): array
    {
        $data = $query->select([
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total_amount - discount_amount) as revenue'),
            DB::raw('COUNT(*) as orders')
        ])
        ->groupBy('year')
        ->orderBy('year')
        ->get()
        ->keyBy('year');

        $result = [];
        $currentYear = $startDate->year;
        $endYear = $endDate->year;

        for ($year = $currentYear; $year <= $endYear; $year++) {
            $yearData = $data->get($year);

            $result[] = [
                'date' => (string) $year,
                'revenue' => $yearData ? (float) $yearData->revenue : 0,
                'orders' => $yearData ? (int) $yearData->orders : 0,
                'label' => (string) $year
            ];
        }

        return $result;
    }

    /**
     * Lấy dữ liệu phân tích chi tiết
     */
    private function getBreakdownData($query): array
    {
        // Thống kê theo phương thức thanh toán
        $paymentMethodData = $query->clone()
            ->select([
                'payment_method',
                DB::raw('SUM(total_amount - discount_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            ])
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        // Thống kê theo ngày trong tuần
        $dayOfWeekData = $query->clone()
            ->select([
                DB::raw('DAYOFWEEK(created_at) as day_of_week'),
                DB::raw('SUM(total_amount - discount_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            ])
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();

        // Chuyển đổi dữ liệu ngày trong tuần
        $dayNames = ['', 'Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        $dayOfWeekFormatted = [];

        foreach ($dayOfWeekData as $day) {
            $dayOfWeekFormatted[$dayNames[$day->day_of_week]] = [
                'revenue' => (float) $day->revenue,
                'orders' => (int) $day->orders
            ];
        }

        return [
            'by_payment_method' => [
                'cash' => [
                    'revenue' => $paymentMethodData->get('cash')->revenue ?? 0,
                    'orders' => $paymentMethodData->get('cash')->orders ?? 0
                ],
                'card' => [
                    'revenue' => $paymentMethodData->get('card')->revenue ?? 0,
                    'orders' => $paymentMethodData->get('card')->orders ?? 0
                ],
                'both' => [
                    'revenue' => $paymentMethodData->get('both')->revenue ?? 0,
                    'orders' => $paymentMethodData->get('both')->orders ?? 0
                ]
            ],
            'by_day_of_week' => $dayOfWeekFormatted
        ];
    }

    /**
     * Lấy thống kê dashboard tổng quan
     */
    public function getDashboard(): JsonResponse
    {
        try {
            $today = now();
            $currentMonth = $today->copy()->startOfMonth();
            $currentYear = $today->copy()->startOfYear();

            // Doanh thu hôm nay
            $todayRevenue = Bill::where('status', Bill::STATUS_PAID)
                ->whereDate('created_at', $today->toDateString())
                ->sum(DB::raw('total_amount - discount_amount'));

            // Doanh thu tháng này
            $monthRevenue = Bill::where('status', Bill::STATUS_PAID)
                ->whereBetween('created_at', [$currentMonth, $today])
                ->sum(DB::raw('total_amount - discount_amount'));

            // Doanh thu năm này
            $yearRevenue = Bill::where('status', Bill::STATUS_PAID)
                ->whereBetween('created_at', [$currentYear, $today])
                ->sum(DB::raw('total_amount - discount_amount'));

            // Số đơn hàng hôm nay
            $todayOrders = Bill::where('status', Bill::STATUS_PAID)
                ->whereDate('created_at', $today->toDateString())
                ->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'today_revenue' => (float) $todayRevenue,
                    'month_revenue' => (float) $monthRevenue,
                    'year_revenue' => (float) $yearRevenue,
                    'today_orders' => (int) $todayOrders
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard statistics error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi hệ thống, vui lòng thử lại sau'
            ], 500);
        }
    }
}
