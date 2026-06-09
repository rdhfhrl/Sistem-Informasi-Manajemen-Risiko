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
        Schema::create('risk_evaluations', function (Blueprint $table) {
            $table->id('risk_evaluation_id');
            $table->unsignedBigInteger('risk_evaluation_risk_id'); // Foreign Key to risk table
            $table->enum('risk_evaluation_priority', ['rendah', 'sedang', 'tinggi', 'sangat tinggi']); // Prioritas Penanganan Risiko
            $table->text('mitigation_decision')->comment('Apa yang diputuskan'); // Keputusan Mitigasi
            $table->decimal('projected_risk_score', 5, 2)->nullable()->comment('Proyeksi besaran risiko akhir periode'); // Proyeksi Skor Risiko
            $table->date('evaluation_date'); // Tanggal Evaluasi
            $table->timestamps();

            // Foreign key
            $table->foreign('risk_evaluation_risk_id')->references('risk_id')->on('risk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_evaluations');
    }
};
