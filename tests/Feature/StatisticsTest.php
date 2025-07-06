<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class StatisticsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $managerUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo users để test
        $this->managerUser = User::factory()->create([
            'email' => 'manager@test.com'
        ]);

        $this->regularUser = User::factory()->create([
            'email' => 'staff@test.com'
        ]);

        // Tạo sample bills để test
        $this->createSampleBills();
    }

    private function createSampleBills()
    {
        // Bills hôm nay
        Bill::factory()->create([
            'total_amount' => 500000,
            'discount_amount' => 50000,
            'status' => Bill::STATUS_PAID,
            'payment_method' => Bill::PAYMENT_METHOD_CASH,
            'created_at' => now()
        ]);

        Bill::factory()->create([
            'total_amount' => 300000,
            'discount_amount' => 0,
            'status' => Bill::STATUS_PAID,
            'payment_method' => Bill::PAYMENT_METHOD_CARD,
            'created_at' => now()
        ]);

        // Bills tháng trước
        Bill::factory()->create([
            'total_amount' => 400000,
            'discount_amount' => 40000,
            'status' => Bill::STATUS_PAID,
            'payment_method' => Bill::PAYMENT_METHOD_CASH,
            'created_at' => now()->subMonth()
        ]);

        // Bills unpaid
        Bill::factory()->create([
            'total_amount' => 200000,
            'discount_amount' => 0,
            'status' => Bill::STATUS_UNPAID,
            'payment_method' => Bill::PAYMENT_METHOD_CASH,
            'created_at' => now()
        ]);
    }

    /**
     * Test dashboard statistics - successful access
     */
    public function test_dashboard_statistics_success_with_manager()
    {
        $response = $this->actingAs($this->managerUser)
            ->getJson('/statistics/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'today_revenue',
                    'month_revenue',
                    'year_revenue',
                    'today_orders'
                ]
            ]);

        // Kiểm tra dữ liệu có đúng không
        $data = $response->json('data');
        $this->assertEquals(750000, $data['today_revenue']); // (500000-50000) + (300000-0)
    }

    /**
     * Test dashboard statistics - unauthorized access
     */
    public function test_dashboard_statistics_unauthorized()
    {
        $response = $this->getJson('/statistics/dashboard');

        $response->assertStatus(401);
    }

    /**
     * Test dashboard statistics - forbidden for regular user
     */
    public function test_dashboard_statistics_forbidden_for_regular_user()
    {
        $response = $this->actingAs($this->regularUser)
            ->getJson('/statistics/dashboard');

        $response->assertStatus(403);
    }

    /**
     * Test revenue statistics - daily type success
     */
    public function test_revenue_statistics_daily_success()
    {
        $startDate = now()->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=daily&start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'summary' => [
                        'total_revenue',
                        'total_orders',
                        'average_order_value',
                        'period'
                    ],
                    'chart_data',
                    'breakdown' => [
                        'by_payment_method',
                        'by_day_of_week'
                    ]
                ]
            ]);

        $data = $response->json('data');
        $this->assertEquals(750000, $data['summary']['total_revenue']);
        $this->assertEquals(2, $data['summary']['total_orders']);
    }

    /**
     * Test revenue statistics - monthly type success
     */
    public function test_revenue_statistics_monthly_success()
    {
        $startDate = now()->startOfYear()->format('Y-m-d');
        $endDate = now()->endOfYear()->format('Y-m-d');

        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=monthly&start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertIsArray($data['chart_data']);
    }

    /**
     * Test revenue statistics - yearly type success
     */
    public function test_revenue_statistics_yearly_success()
    {
        $startDate = '2020-01-01';
        $endDate = now()->format('Y-m-d');

        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=yearly&start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertIsArray($data['chart_data']);
    }

    /**
     * Test revenue statistics - validation error for missing type
     */
    public function test_revenue_statistics_validation_error_missing_type()
    {
        $startDate = now()->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    /**
     * Test revenue statistics - validation error for invalid type
     */
    public function test_revenue_statistics_validation_error_invalid_type()
    {
        $startDate = now()->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=invalid&start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    /**
     * Test revenue statistics - validation error for missing dates
     */
    public function test_revenue_statistics_validation_error_missing_dates()
    {
        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=daily");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_date', 'end_date']);
    }

    /**
     * Test revenue statistics - validation error for invalid date format
     */
    public function test_revenue_statistics_validation_error_invalid_date_format()
    {
        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=daily&start_date=invalid-date&end_date=also-invalid");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_date', 'end_date']);
    }

    /**
     * Test revenue statistics - validation error for start_date > end_date
     */
    public function test_revenue_statistics_validation_error_start_date_after_end_date()
    {
        $startDate = now()->format('Y-m-d');
        $endDate = now()->subDay()->format('Y-m-d');

        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=daily&start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_date']);
    }

    /**
     * Test revenue statistics - validation error for end_date > today
     */
    public function test_revenue_statistics_validation_error_end_date_future()
    {
        $startDate = now()->format('Y-m-d');
        $endDate = now()->addDay()->format('Y-m-d');

        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=daily&start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    /**
     * Test revenue statistics - validation error for date range > 1 year
     */
    public function test_revenue_statistics_validation_error_date_range_too_large()
    {
        $startDate = now()->subYears(2)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=daily&start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    /**
     * Test revenue statistics - with payment method filter
     */
    public function test_revenue_statistics_with_payment_method_filter()
    {
        $startDate = now()->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=daily&start_date={$startDate}&end_date={$endDate}&payment_method=cash");

        $response->assertStatus(200);

        $data = $response->json('data');
        // Chỉ có 1 bill cash với revenue = 450000 (500000-50000)
        $this->assertEquals(450000, $data['summary']['total_revenue']);
        $this->assertEquals(1, $data['summary']['total_orders']);
    }

    /**
     * Test revenue statistics - with invalid payment method
     */
    public function test_revenue_statistics_validation_error_invalid_payment_method()
    {
        $startDate = now()->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=daily&start_date={$startDate}&end_date={$endDate}&payment_method=invalid");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['payment_method']);
    }

    /**
     * Test revenue statistics - no data in date range
     */
    public function test_revenue_statistics_no_data_in_range()
    {
        $startDate = now()->subYears(10)->format('Y-m-d');
        $endDate = now()->subYears(10)->format('Y-m-d');

        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=daily&start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertEquals(0, $data['summary']['total_revenue']);
        $this->assertEquals(0, $data['summary']['total_orders']);
    }

    /**
     * Test revenue statistics breakdown data structure
     */
    public function test_revenue_statistics_breakdown_data_structure()
    {
        $startDate = now()->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=daily&start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200);

        $breakdown = $response->json('data.breakdown');

        // Kiểm tra cấu trúc breakdown
        $this->assertArrayHasKey('by_payment_method', $breakdown);
        $this->assertArrayHasKey('by_day_of_week', $breakdown);

        // Kiểm tra payment methods
        $paymentMethods = $breakdown['by_payment_method'];
        $this->assertArrayHasKey('cash', $paymentMethods);
        $this->assertArrayHasKey('card', $paymentMethods);
        $this->assertArrayHasKey('both', $paymentMethods);

        // Kiểm tra structure của từng payment method
        foreach (['cash', 'card', 'both'] as $method) {
            $this->assertArrayHasKey('revenue', $paymentMethods[$method]);
            $this->assertArrayHasKey('orders', $paymentMethods[$method]);
        }
    }

    /**
     * Test chart data format for different types
     */
    public function test_chart_data_format_for_different_types()
    {
        $startDate = now()->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        // Test daily chart data
        $response = $this->actingAs($this->managerUser)
            ->getJson("/statistics/revenue?type=daily&start_date={$startDate}&end_date={$endDate}");

        $chartData = $response->json('data.chart_data');
        $this->assertIsArray($chartData);

        if (!empty($chartData)) {
            $firstItem = $chartData[0];
            $this->assertArrayHasKey('date', $firstItem);
            $this->assertArrayHasKey('revenue', $firstItem);
            $this->assertArrayHasKey('orders', $firstItem);
            $this->assertArrayHasKey('label', $firstItem);
        }
    }

    /**
     * Test basic functionality
     */
    public function test_basic_test()
    {
        $this->assertTrue(true);
    }
}
