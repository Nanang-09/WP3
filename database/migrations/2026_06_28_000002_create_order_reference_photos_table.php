<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_reference_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('photo_path');                   // Path di storage/app/public
            $table->string('caption')->nullable();          // Keterangan foto
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_reference_photos');
    }
};
