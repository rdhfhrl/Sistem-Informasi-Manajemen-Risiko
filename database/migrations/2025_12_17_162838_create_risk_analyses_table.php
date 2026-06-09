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
        Schema::create('risk_analyses', function (Blueprint $table) {
            $table->id('risk_analysis_id');
            $table->unsignedBigInteger('risk_analysis_risk_id'); // Foreign Key to risk table
            $table->integer('likelihood_level')->comment('Skala 1-5'); // Kemungkinan Terjadi
            $table->integer('impact_level')->comment('Skala 1-5'); // Dampak Terjadi
            $table->integer('risk_score')->comment('likelihood * impact'); // Skor Risiko
            $table->enum('risk_level', ['sangat_rendah', 'rendah', 'sedang', 'tinggi', 'sangat_tinggi']); // Level Risiko  
            $table->date('analysis_date'); // Tanggal Analisis
            $table->timestamps();

            // Foreign key
            $table->foreign('risk_analysis_risk_id')->references('risk_id')->on('risk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_analyses');
    }
};
