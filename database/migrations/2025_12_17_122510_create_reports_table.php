<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            
            // Metadata Laporan
            $table->enum('report_type', [
                'monitoring', 
                'risk_profile', 
                'executive_summary', 
                'mitigation_effectiveness', 
                'custom'
            ])->default('monitoring');
            
            $table->string('title');
            $table->string('period')->nullable(); // Untuk laporan berkala: bulan, triwulan, tahun
            $table->date('report_date');
            
            // Scope Laporan
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('risk_id')->nullable();
            
            // Data Laporan
            $table->json('data')->nullable(); // Menyimpan data laporan dalam format JSON
            $table->string('file_path')->nullable(); // Jika laporan di-export ke file
            
            // Status dan Metadata
            $table->enum('status', ['draft', 'generated', 'published', 'archived'])->default('draft');
            $table->unsignedBigInteger('generated_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('approval_date')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('organization_id')->references('organization_id')->on('organizations')->onDelete('set null');
            $table->foreign('project_id')->references('pro_id')->on('project')->onDelete('set null');
            $table->foreign('risk_id')->references('risk_id')->on('risk')->onDelete('set null');
            $table->foreign('generated_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};