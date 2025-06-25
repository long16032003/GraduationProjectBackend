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
        Schema::create('enter_ingredient_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enter_ingredient_id')->constrained('enter_ingredients');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('no action');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->string('supplier_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enter_ingredient_details');
    }
};
