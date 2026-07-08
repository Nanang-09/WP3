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
            $table->boolean('is_consultation_confirmed')->default(false)->after('agreement_notes');
        });

        // Set is_consultation_confirmed to true for orders that already have a consultation scheduled
        \DB::table('orders')
            ->whereNotNull('consultation_date')
            ->update(['is_consultation_confirmed' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_consultation_confirmed');
        });
    }
};
