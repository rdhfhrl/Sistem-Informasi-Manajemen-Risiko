<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToRiskMonitoringsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('risk_monitorings', function (Blueprint $table) {
            $table->string('current_risk_level', 20)->nullable()->after('current_risk_score')->comment('Level risiko saat pemantauan');
            $table->tinyInteger('effectiveness_rating')->nullable()->after('monitoring_report')->comment('Penilaian efektivitas mitigasi (1-5)');
            $table->string('monitored_by', 255)->nullable()->after('effectiveness_rating')->comment('Nama pemantau');
            $table->date('next_monitoring_date')->nullable()->after('monitored_by')->comment('Jadwal pemantauan berikutnya');
            $table->text('recommendations')->nullable()->after('next_monitoring_date')->comment('Rekomendasi tindak lanjut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risk_monitorings', function (Blueprint $table) {
            $table->dropColumn([
                'current_risk_level',
                'effectiveness_rating',
                'monitored_by',
                'next_monitoring_date',
                'recommendations'
            ]);
        });
    }
}