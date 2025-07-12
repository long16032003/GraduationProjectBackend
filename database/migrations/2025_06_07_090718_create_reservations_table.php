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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained('tables');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->string('phone');
            $table->string('name');
            $table->timestamp('reservation_date');
            $table->integer('number_of_guests');
            $table->enum('status', ['pending', 'confirmed', 'cancelled']);
            $table->text('notes')->nullable();
            $table->bigInteger('creator_id');
            $table->string('creator_type')->default('user');
            $table->index(['creator_id', 'creator_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
