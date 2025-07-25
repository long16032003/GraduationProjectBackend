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
        Schema::create('export_ingredient_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_ingredient_id')->constrained('export_ingredients');
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('no action');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_ingredient_details');
    }
};
