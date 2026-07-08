<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->unsignedTinyInteger('progress_percent')->nullable();
            $table->string('photo_path')->nullable();
            $table->date('update_date');
            $table->string('status_after_update')->nullable();
            $table->boolean('is_visible_to_customer')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_updates');
    }
};
