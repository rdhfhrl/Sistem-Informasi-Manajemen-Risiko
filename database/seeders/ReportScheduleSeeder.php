<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat data jadwal laporan...');
        
        // Dapatkan user admin
        $admin = DB::table('users')
            ->where('role', 'admin')
            ->first();
            
        if (!$admin) {
            $this->command->error('❌ User admin tidak ditemukan. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // Dapatkan UPTD Medan
        $uptd = DB::table('organizations')
            ->where('organization_type', 'UPTD')
            ->where('organization_name', 'LIKE', '%Medan%')
            ->first();
            
        if (!$uptd) {
            $this->command->error('❌ UPTD Medan tidak ditemukan.');
            return;
        }

        $schedules = [
            // Jadwal bulanan untuk laporan pemantauan
            [
                'schedule_name' => 'Laporan Pemantauan Risiko Bulanan',
                'report_type' => 'monitoring',
                'frequency' => 'monthly',
                'parameters' => json_encode([
                    'period' => 'bulanan',
                    'organization_id' => $uptd->organization_id,
                    'include_comparison' => true,
                    'include_recommendations' => true,
                ]),
                'recipients' => json_encode([
                    'admin.sistem@dpupr-sumut.test',
                    'aidul.lubis@uptd-medan.test',
                    'elvida.medica@uptd-medan.test',
                ]),
                'auto_generate' => true,
                'auto_send_email' => true,
                'generation_time' => '09:00:00',
                'day_of_month' => 5, // Tanggal 5 setiap bulan
                'month_of_year' => null,
                'is_active' => true,
                'created_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Jadwal triwulan untuk ringkasan eksekutif
            [
                'schedule_name' => 'Ringkasan Eksekutif Triwulan',
                'report_type' => 'executive_summary',
                'frequency' => 'quarterly',
                'parameters' => json_encode([
                    'period' => 'triwulan',
                    'organization_id' => $uptd->organization_id,
                    'include_comparison' => true,
                    'include_recommendations' => true,
                ]),
                'recipients' => json_encode([
                    'admin.sistem@dpupr-sumut.test',
                    'jhon.sitepu@globalnusantara.test',
                    'auditor@dpupr-sumut.test',
                ]),
                'auto_generate' => true,
                'auto_send_email' => true,
                'generation_time' => '10:00:00',
                'day_of_month' => 10,
                'month_of_year' => null,
                'is_active' => true,
                'created_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Jadwal bulanan untuk profil risiko
            [
                'schedule_name' => 'Profil Risiko Bulanan',
                'report_type' => 'risk_profile',
                'frequency' => 'monthly',
                'parameters' => json_encode([
                    'organization_id' => $uptd->organization_id,
                    'include_trend_analysis' => true,
                ]),
                'recipients' => json_encode([
                    'aidul.lubis@uptd-medan.test',
                    'elvida.medica@uptd-medan.test',
                ]),
                'auto_generate' => true,
                'auto_send_email' => false,
                'generation_time' => '14:00:00',
                'day_of_month' => 15,
                'month_of_year' => null,
                'is_active' => true,
                'created_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Jadwal tahunan untuk efektivitas mitigasi
            [
                'schedule_name' => 'Laporan Efektivitas Mitigasi Tahunan',
                'report_type' => 'mitigation_effectiveness',
                'frequency' => 'yearly',
                'parameters' => json_encode([
                    'period' => 'tahunan',
                    'organization_id' => $uptd->organization_id,
                    'include_cost_analysis' => true,
                ]),
                'recipients' => json_encode([
                    'admin.sistem@dpupr-sumut.test',
                    'auditor@dpupr-sumut.test',
                ]),
                'auto_generate' => true,
                'auto_send_email' => true,
                'generation_time' => '11:00:00',
                'day_of_month' => 20,
                'month_of_year' => 'December',
                'is_active' => true,
                'created_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('report_schedules')->insert($schedules);
        
        $totalSchedules = DB::table('report_schedules')->count();
        $activeSchedules = DB::table('report_schedules')->where('is_active', true)->count();
        
        $this->command->info("✓ {$totalSchedules} jadwal laporan berhasil dibuat");
        $this->command->info("  - Aktif: {$activeSchedules} jadwal");
        
        // Tampilkan detail jadwal
        foreach ($schedules as $schedule) {
            $frequencyLabel = match($schedule['frequency']) {
                'daily' => 'Harian',
                'weekly' => 'Mingguan',
                'monthly' => 'Bulanan',
                'quarterly' => 'Triwulan',
                'yearly' => 'Tahunan',
                default => $schedule['frequency']
            };
            
            $reportTypeLabel = match($schedule['report_type']) {
                'monitoring' => 'Pemantauan',
                'risk_profile' => 'Profil Risiko',
                'executive_summary' => 'Ringkasan Eksekutif',
                'mitigation_effectiveness' => 'Efektivitas Mitigasi',
                default => $schedule['report_type']
            };
            
            $this->command->info("    • {$schedule['schedule_name']} ({$reportTypeLabel} - {$frequencyLabel})");
        }
    }
}