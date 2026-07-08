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
            $table->date('preferred_consultation_date')->nullable();
            $table->string('preferred_consultation_time')->nullable();
            $table->date('consultation_date')->nullable();
            $table->string('consultation_time')->nullable();
            $table->string('consultation_place')->nullable();
            $table->date('project_start_date')->nullable();
            $table->date('project_end_date')->nullable();
            $table->bigInteger('project_price')->nullable();
            $table->text('agreement_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_consultation_date',
                'preferred_consultation_time',
                'consultation_date',
                'consultation_time',
                'consultation_place',
                'project_start_date',
                'project_end_date',
                'project_price',
                'agreement_notes'
            ]);
        });
    }
};
