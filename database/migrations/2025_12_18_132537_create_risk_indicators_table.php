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
        Schema::create('risk_indicators', function (Blueprint $table) {
            $table->id('risk_indicator_id');
            $table->unsignedBigInteger('risk_indicator_risk_id'); // Foreign Key to risk table
            $table->enum('indicator_type', ['akar_masalah', 'penyebab', 'dampak', 'lainnya']);
            $table->string('indicator_name');
            $table->text('indicator_description')->nullable();
            $table->decimal('threshold', 10, 2)->comment('Ambang batas');
            $table->string('unit')->nullable()->comment('Satuan');
            $table->timestamps();

            // Foreign key
            $table->foreign('risk_indicator_risk_id')->references('risk_id')->on('risk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_indicators');
    }
};
