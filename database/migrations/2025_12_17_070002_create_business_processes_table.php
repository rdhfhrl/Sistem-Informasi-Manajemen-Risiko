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
        Schema::create('business_processes', function (Blueprint $table) {
            $table->id('business_process_id');
            $table->unsignedBigInteger('business_process_organization_id'); // Foreign key to organizations table
            $table->string('business_process_name'); // Nama Proses Bisnis
            $table->string('business_process_description'); // Deskripsi Proses Bisnis
            $table->timestamps();

            // Foreign Key
            $table->foreign('business_process_organization_id')->references('organization_id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_processes');
    }
};
