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
        Schema::create('project', function (Blueprint $table) {
            $table->unsignedBigInteger('pro_id')->primary()->autoIncrement();
            $table->string('pro_nama'); //Nama Proyek
            $table->text('pro_lokasi'); //Lokasi Proyek
            $table->text('pro_deskripsi')->nullable(); // Deskripsi proyek
            $table->date('pro_tanggal_mulai'); // Tanggal mulai proyek
            $table->date('pro_tanggal_selesai'); // Tanggal selesai proyek
            $table->enum('pro_status', ['Aktif', 'Selesai', 'Ditunda', 'Dibatalkan'])
                  ->default('Aktif'); //Status Proyek
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project');
    }
};
