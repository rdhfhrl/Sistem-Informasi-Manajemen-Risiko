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
        Schema::create('risk_identifications', function (Blueprint $table) {
            $table->id('risk_identification_id');
            $table->unsignedBigInteger('risk_identification_risk_id');
            $table->enum('loss_type', ['Reputasi', 'Operasional','Kepatuhan', 'Lainnya'])->nullable(); // Jenis Kerugian
            $table->enum('violation_type', ['Hukum', 'SOP', 'Kontrak', 'Lainnya'])->nullable(); // Jenis Pelanggaran
            $table->enum('failure_type', ['Manusia', 'Proses', 'Sistem', 'Lainnya'])->nullable(); // Jenis Kegagalan
            $table->enum('error_type', ['Human Error', 'Technical Error', 'Lainnya'])->nullable(); // Jenis Kesalahan
            $table->timestamps();

            // Foreign Key
            $table->foreign('risk_identification_risk_id')->references('risk_id')->on('risk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_identifications');
    }
};

