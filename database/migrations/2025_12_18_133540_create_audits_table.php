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
        Schema::create('audits', function (Blueprint $table) {
            $table->id('audit_id');
            $table->unsignedBigInteger('risk_id')->nullable()->comment('Bisa null untuk audit umum');
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->string('auditor')->comment('Nama auditor');
            $table->date('audit_date');
            $table->text('audit_findings')->nullable()->comment('Temuan audit');
            $table->text('audit_recommendations')->nullable()->comment('Rekomendasi audit');
            $table->text('audit_report')->nullable()->comment('Laporan audit');
            $table->timestamps();

            // Foreign key
            $table->foreign('risk_id')->references('risk_id')->on('risk')->onDelete('set null');
            $table->foreign('organization_id')->references('organization_id')->on('organizations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
