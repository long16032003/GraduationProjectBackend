<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
            $table->string('vnp_TxnRef')->unique(); // Mã đơn hàng của merchant
            $table->string('vnp_TransactionNo')->nullable(); // Mã giao dịch của VNPay
            $table->decimal('vnp_Amount', 15, 2); // Số tiền thanh toán
            $table->string('vnp_OrderInfo'); // Thông tin đơn hàng
            $table->string('vnp_ResponseCode')->nullable(); // Mã phản hồi
            $table->string('vnp_TransactionStatus')->nullable(); // Trạng thái giao dịch
            $table->string('vnp_PayDate')->nullable(); // Thời gian thanh toán
            $table->string('vnp_BankCode')->nullable(); // Mã ngân hàng
            $table->string('vnp_BankTranNo')->nullable(); // Mã giao dịch ngân hàng
            $table->string('vnp_CardType')->nullable(); // Loại thẻ
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled'])->default('pending');
            $table->text('vnpay_response')->nullable(); // Toàn bộ response từ VNPay
            $table->timestamps();

            // Indexes
            $table->index(['status', 'created_at']);
            $table->index('vnp_TxnRef');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
