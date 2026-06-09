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
        Schema::table('risk', function (Blueprint $table) {
            $table->date('last_monitoring_date')->nullable()->after('last_analysis_date')->comment('Tanggal pemantauan terakhir');
            $table->date('last_evaluation_date')->nullable()->after('last_monitoring_date')->comment('Tanggal evaluasi terakhir');
            $table->enum('risk_status', ['draft', 'active', 'monitoring', 'mitigated', 'closed'])->default('draft')->after('risk_level')->comment('Status risiko');
            $table->timestamp('identified_at')->nullable()->after('created_at')->comment('Waktu identifikasi');
            $table->unsignedBigInteger('identified_by')->nullable()->after('identified_at')->comment('User yang mengidentifikasi');

            //Foreign Key
            $table->foreign('identified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risk', function (Blueprint $table) {
            $table->dropForeign(['identified_by']);
            $table->dropColumn([
                'last_monitoring_date',
                'last_evaluation_date',
                'risk_status',
                'identified_at',
                'identified_by'
            ]);
        });
    }
};
