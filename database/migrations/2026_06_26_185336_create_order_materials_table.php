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
        Schema::create('order_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('material_name');
            $table->string('length')->nullable();
            $table->string('shape')->nullable();
            $table->integer('quantity')->default(1);
            $table->bigInteger('unit_price')->default(0);
            $table->bigInteger('subtotal')->default(0);
            $table->string('example_image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_materials');
    }
};
