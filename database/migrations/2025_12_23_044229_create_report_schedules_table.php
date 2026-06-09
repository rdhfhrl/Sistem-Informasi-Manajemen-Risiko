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
        Schema::create('report_schedules', function (Blueprint $table) {
            $table->id('schedule_id');
            $table->string('schedule_name');
            $table->enum('report_type', [
                'monitoring',
                'risk_profile', 
                'executive_summary',
                'mitigation_effectiveness'
            ]);
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly']);
            $table->json('parameters')->nullable();
            $table->json('recipients')->nullable();
            $table->boolean('auto_generate')->default(true);
            $table->boolean('auto_send_email')->default(false);
            $table->time('generation_time')->nullable();
            $table->integer('day_of_month')->nullable();
            $table->string('month_of_year')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_schedules');
    }
};
