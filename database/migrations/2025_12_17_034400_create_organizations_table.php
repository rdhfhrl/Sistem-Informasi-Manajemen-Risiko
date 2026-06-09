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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id('organization_id');
            $table->string('organization_name')->default('UPTD PUPR Medan'); // Nama tetap UPTD PUPR Medan
            $table->enum('organization_type', ['Dinas', 'UPTD'])->default('UPTD');
            $table->string('organization_code')->nullable()->unique(); // Kode: UPTD-MEDAN
            $table->string('location')->nullable(); // Lokasi UPTD (Medan, dll)
            $table->text('organization_description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable(); // Parent adalah Dinas PUPR
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('organization_id')->on('organizations')->onDelete('set null');
            
            // Index untuk performa query
            $table->index('organization_type');
            $table->index('parent_id');
            $table->index('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};