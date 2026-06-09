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
        Schema::create('indicator_measurements', function (Blueprint $table) {
            $table->id('measurement_id');
            $table->unsignedBigInteger('risk_indicator_id');
            $table->decimal('measured_value', 10, 2);
            $table->date('measurement_date');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('measured_by');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('risk_indicator_id')->references('risk_indicator_id')->on('risk_indicators')->onDelete('cascade');
            $table->foreign('measured_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicator_measurements');
    }
};
