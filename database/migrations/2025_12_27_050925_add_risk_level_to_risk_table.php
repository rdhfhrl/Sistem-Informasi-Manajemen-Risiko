// database/migrations/xxxx_xx_xx_add_risk_level_to_risk_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('risk', function (Blueprint $table) {
            $table->enum('risk_level', ['sangat_rendah', 'rendah', 'sedang', 'tinggi', 'sangat_tinggi'])
                  ->nullable()
                  ->after('risk_description')
                  ->comment('Level risiko dari analisis terakhir');
            $table->integer('risk_score')->nullable()->after('risk_level');
            $table->integer('likelihood_level')->nullable()->after('risk_score');
            $table->integer('impact_level')->nullable()->after('likelihood_level');
            $table->date('last_analysis_date')->nullable()->after('impact_level');
        });
        
        // Update data existing dari risk_analyses
        DB::statement("
            UPDATE risk r
            LEFT JOIN (
                SELECT risk_analysis_risk_id, 
                       MAX(analysis_date) as latest_date
                FROM risk_analyses
                GROUP BY risk_analysis_risk_id
            ) latest ON r.risk_id = latest.risk_analysis_risk_id
            LEFT JOIN risk_analyses ra ON ra.risk_analysis_risk_id = r.risk_id 
                AND ra.analysis_date = latest.latest_date
            SET r.risk_level = ra.risk_level,
                r.risk_score = ra.risk_score,
                r.likelihood_level = ra.likelihood_level,
                r.impact_level = ra.impact_level,
                r.last_analysis_date = ra.analysis_date
        ");
    }

    public function down()
    {
        Schema::table('risk', function (Blueprint $table) {
            $table->dropColumn([
                'risk_level', 
                'risk_score', 
                'likelihood_level', 
                'impact_level',
                'last_analysis_date'
            ]);
        });
    }
};