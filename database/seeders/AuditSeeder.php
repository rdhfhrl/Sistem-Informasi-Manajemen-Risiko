<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat data audit...');
        
        // Ambil 20 risiko acak (dengan prioritas tinggi)
        $risks = DB::table('risk')
            ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
            ->inRandomOrder()
            ->limit(15)
            ->get();
        
        // Ambil UPTD Medan
        $uptd = DB::table('organizations')
            ->where('organization_type', 'UPTD')
            ->where('organization_name', 'LIKE', '%Medan%')
            ->first();
        
        if ($risks->isEmpty()) {
            $this->command->error('❌ Data risiko tidak ditemukan. Jalankan RiskSeeder terlebih dahulu.');
            return;
        }

        $audits = [];
        $auditorOptions = [
            'Tim Audit Internal DPUPR Sumut',
            'Badan Pengawasan Daerah',
            'Konsultan Independen',
            'Auditor Eksternal',
        ];
        
        $findingsTemplates = [
            'Kepatuhan terhadap prosedur K3 mencapai 85%',
            'Dokumentasi mitigasi risiko perlu diperbaiki',
            'Pelaporan monitoring perlu lebih terstruktur',
            'Efektivitas mitigasi perlu ditingkatkan',
            'Tidak ada temuan signifikan',
            'Koordinasi antar tim perlu ditingkatkan',
        ];
        
        $recommendationsTemplates = [
            'Perlu peningkatan frekuensi monitoring',
            'Dokumentasi harus lebih lengkap',
            'Pelatihan tambahan diperlukan untuk tim',
            'Sistem pelaporan perlu diotomatisasi',
            'Koordinasi rutin dengan kontraktor',
            'Review prosedur keselamatan',
        ];
        
        foreach ($risks as $risk) {
            // 80% risiko diaudit
            if (rand(1, 100) <= 80) {
                $auditDate = Carbon::parse($risk->created_at)->addDays(rand(30, 90));
                
                $audits[] = [
                    'risk_id' => $risk->risk_id,
                    'organization_id' => $uptd?->organization_id,
                    'auditor' => $auditorOptions[array_rand($auditorOptions)],
                    'audit_date' => $auditDate->format('Y-m-d'),
                    'audit_findings' => $findingsTemplates[array_rand($findingsTemplates)],
                    'audit_recommendations' => $recommendationsTemplates[array_rand($recommendationsTemplates)],
                    'audit_report' => 'Laporan Audit Risiko ' . $risk->risk_code . ' - ' . $auditDate->format('F Y'),
                    'created_at' => $auditDate,
                    'updated_at' => $auditDate,
                ];
            }
        }
        
        // Tambahkan 5 audit umum (tidak terkait risiko spesifik)
        for ($i = 1; $i <= 5; $i++) {
            $auditDate = Carbon::now()->subDays(rand(1, 180));
            
            $audits[] = [
                'risk_id' => null,
                'organization_id' => $uptd?->organization_id,
                'auditor' => $auditorOptions[array_rand($auditorOptions)],
                'audit_date' => $auditDate->format('Y-m-d'),
                'audit_findings' => 'Audit sistem manajemen risiko organisasi',
                'audit_recommendations' => 'Perlu pengembangan kapasitas SDM dalam manajemen risiko',
                'audit_report' => 'Laporan Audit Sistem Manajemen Risiko - ' . $auditDate->format('F Y'),
                'created_at' => $auditDate,
                'updated_at' => $auditDate,
            ];
        }

        DB::table('audits')->insert($audits);
        
        $totalAudits = DB::table('audits')->count();
        $auditsWithFindings = DB::table('audits')->whereNotNull('audit_findings')->count();
        
        $this->command->info("✓ {$totalAudits} data audit berhasil dibuat");
        $this->command->info("  - Dengan temuan: {$auditsWithFindings}");
    }
}