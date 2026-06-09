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
        Schema::create('risk_categories', function (Blueprint $table) {
            $table->id('risk_category_id');
            $table->enum('risk_category_name', ['Waktu', 'Lingkungan', 'Manajemen', 'Hukum', 'SDM', 'K3'])->unique(); // Nama Kategori Risiko
            $table->string('risk_category_description'); // Deskripsi Kategori Risiko
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_categories');
    }
};
