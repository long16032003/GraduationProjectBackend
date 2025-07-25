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
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('users');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->foreign('image_id')
                  ->references('id')
                  ->on('media')
                ->nullOnDelete();
            $table->integer('price');
            $table->foreignId('category_id')->constrained('dish_categories');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
