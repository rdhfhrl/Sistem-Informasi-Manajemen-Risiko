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
        Schema::create('risk_monitorings', function (Blueprint $table) {
            $table->id('risk_monitoring_id');
            $table->unsignedBigInteger('risk_monitoring_risk_id'); // Foreign Key to risk table
            $table->date('monitoring_date')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('Tanggal pemantauan');
            $table->decimal('current_risk_score', 5, 2)->comment('Skor risiko saat pemantauan');
            $table->text('monitoring_result')->nullable()->comment('Hasil pemantauan');
            $table->text('monitoring_report')->nullable()->comment('Laporan pemantauan');
            $table->timestamps();

            // Foreign key
            $table->foreign('risk_monitoring_risk_id')->references('risk_id')->on('risk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_monitorings');
    }
};
