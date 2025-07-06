<?php

// Test Email Script
require_once 'vendor/autoload.php';

use App\Models\Reservation;
use App\Models\Customer;
use App\Mail\ReservationConfirmationMail;
use Illuminate\Support\Facades\Mail;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "🚀 Bắt đầu test gửi email...\n";

    // Tạo dummy data
    $reservation = new Reservation([
        'id' => 1,
        'customer_name' => 'Nguyễn Văn Test',
        'customer_phone' => '0901234567',
        'customer_email' => 'test@example.com',
        'reservation_date' => now()->addDay(),
        'reservation_time' => '19:00:00',
        'guest_count' => 4,
        'note' => 'Test đặt bàn',
        'status' => 'pending'
    ]);

    $customer = new Customer([
        'name' => 'Nguyễn Văn Test',
        'email' => 'test@example.com',
        'phone' => '0901234567'
    ]);

    // Fake table relationship
    $table = (object) [
        'name' => 'Bàn số 5',
        'capacity' => 6
    ];
    $reservation->setRelation('table', $table);
    $reservation->setRelation('customer', $customer);

    echo "📧 Đang gửi email test...\n";

    // Gửi email test (thay your-email@gmail.com bằng email của bạn)
    Mail::to('your-email@gmail.com')->send(new ReservationConfirmationMail($reservation, $customer));

    echo "✅ Gửi email thành công!\n";
    echo "🔍 Kiểm tra hộp thư của bạn.\n";

} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    echo "🔧 Kiểm tra lại cấu hình SMTP trong .env\n";
}
