<?php

use App\Models\Bill;
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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->string('customer_phone');
            $table->string('customer_name')->nullable();
            $table->foreignId('table_id')->constrained('tables');
            $table->enum('payment_method', [Bill::PAYMENT_METHOD_CASH, Bill::PAYMENT_METHOD_CARD, Bill::PAYMENT_METHOD_BOTH])->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->enum('status', [Bill::STATUS_PAID, Bill::STATUS_UNPAID, Bill::STATUS_CANCELLED])->default(Bill::STATUS_UNPAID);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
