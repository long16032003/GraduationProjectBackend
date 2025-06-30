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
        Schema::create('order_dishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('dish_id')->constrained('dishes')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price_at_order_time', 10, 2);
            $table->boolean('is_available')->default(true)->comment('Món sẵn sàng để phục vụ hay chưa');
            $table->text('cancelled_reason')->nullable()->comment('Lý do hủy món');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null')->comment('Mã người thực hiện hủy món');
            $table->timestamp('cancelled_at')->nullable()->comment('Thời gian hủy món');
            $table->timestamps();
            $table->unique(['order_id', 'dish_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_dishes');
    }
};
