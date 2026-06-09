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
        Schema::create('strategic_objectives', function (Blueprint $table) {
            $table->id('strategic_objective_id');
            $table->unsignedBigInteger('strategic_objective_organization_id'); // Foreign key to organizations table
            $table->string('strategic_objective_name'); // Nama Tujuan Strategis
            $table->timestamps();
            
            // Foreign Key
            $table->foreign('strategic_objective_organization_id')->references('organization_id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strategic_objectives');
    }
};
