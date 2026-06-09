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
        Schema::create('risk_mitigations', function (Blueprint $table) {
            $table->id('risk_mitigation_id');
            $table->unsignedBigInteger('risk_mitigation_risk_id'); // Foreign Key to risk table
            $table->text('mitigation_plan')->comment('Rencana mitigasi');
            $table->string('responsible_party')->comment('Penanggung jawab');
            $table->date('deadline');
            $table->enum('status', ['belum dimulai', 'dalam proses', 'selesai', 'ditunda', 'dibatalkan'])->default('belum dimulai');
            $table->timestamps();

            // Foreign key
            $table->foreign('risk_mitigation_risk_id')->references('risk_id')->on('risk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_mitigations');
    }
};
