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
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('priority')->default(1)->comment('Độ ưu tiên của đơn gọi món');
            $table->text('cancelled_reason')->nullable()->comment('Lý do hủy đơn');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null')->comment('Mã người thực hiện hủy đơn');
            $table->timestamp('cancelled_at')->nullable()->comment('Thời gian hủy đơn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['priority', 'cancelled_reason', 'cancelled_by', 'cancelled_at']);
        });
    }
};
