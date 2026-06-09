<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat data user berdasarkan data penelitian...');
        
        // Dapatkan ID organisasi
        $dinasPUPR = DB::table('organizations')
            ->where('organization_type', 'Dinas')
            ->first();
            
        $uptd = DB::table('organizations')
            ->where('organization_type', 'UPTD')
            ->first();
            
        if (!$dinasPUPR || !$uptd) {
            $this->command->error('❌ Data organisasi tidak ditemukan. Jalankan OrganizationSeeder terlebih dahulu.');
            return;
        }

        // Data users berdasarkan data scan
        $users = [
            // 1. Super Admin (di Dinas PUPR)
            [
                'name' => 'Administrator Sistem',
                'email' => 'admin.sistem@dpupr-sumut.test',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'organization_id' => $dinasPUPR->organization_id,
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // 2. Petugas K3 (berdasarkan data scan: Aidul Akbar Lubis)
            [
                'name' => 'Aidul Akbar Lubis',
                'email' => 'aidul.lubis@uptd-medan.test',
                'password' => Hash::make('password123'),
                'role' => 'unit_pemilik_risiko',
                'organization_id' => $uptd->organization_id,
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // 3. Pelaksana Proyek (berdasarkan data scan: Elvida Medica S.)
            [
                'name' => 'Elvida Medica S.',
                'email' => 'elvida.medica@uptd-medan.test',
                'password' => Hash::make('password123'),
                'role' => 'unit_pemilik_risiko',
                'organization_id' => $uptd->organization_id,
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // 4. Wakil Direktur (berdasarkan data scan: Jhon Michael Sitepu)
            [
                'name' => 'Jhon Michael Sitepu',
                'email' => 'jhon.sitepu@globalnusantara.test',
                'password' => Hash::make('password123'),
                'role' => 'unit_pemilik_risiko',
                'organization_id' => $uptd->organization_id,
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // 5. Mandor (berdasarkan data scan: Indra Zunaidi)
            [
                'name' => 'Indra Zunaidi',
                'email' => 'indra.zunaidi@globalnusantara.test',
                'password' => Hash::make('password123'),
                'role' => 'unit_pemilik_risiko',
                'organization_id' => $uptd->organization_id,
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // 6. Auditor Internal
            [
                'name' => 'Auditor Internal PUPR',
                'email' => 'auditor@dpupr-sumut.test',
                'password' => Hash::make('password123'),
                'role' => 'auditor',
                'organization_id' => $dinasPUPR->organization_id,
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 7. Admin Dinas
            [
                'name' => 'Admin Dinas PUPR',
                'email' => 'admin@dpupr.test',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'organization_id' => $dinasPUPR->organization_id,
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 8. Risk Manager UPTD
            [
                'name' => 'Risk Manager UPTD Medan',
                'email' => 'riskmanager@uptd.test',
                'password' => Hash::make('password123'),
                'role' => 'unit_pemilik_risiko',
                'organization_id' => $uptd->organization_id,
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 9. Auditor
            [
                'name' => 'Auditor Internal',
                'email' => 'auditor@simr.test',
                'password' => Hash::make('password123'),
                'role' => 'auditor',
                'organization_id' => $dinasPUPR->organization_id,
                'is_active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert data
        DB::table('users')->insert($users);
        
        $totalUsers = DB::table('users')->count();
        $adminCount = DB::table('users')->where('role', 'admin')->count();
        $riskManagerCount = DB::table('users')->where('role', 'unit_pemilik_risiko')->count();
        $auditorCount = DB::table('users')->where('role', 'auditor')->count();
        $activeCount = DB::table('users')->where('is_active', true)->count();
        
        $this->command->info("✓ {$totalUsers} user berhasil dibuat berdasarkan data penelitian:");
        $this->command->info("  - Admin: {$adminCount} user");
        $this->command->info("  - Unit Pemilik Risiko: {$riskManagerCount} user (termasuk petugas K3, pelaksana, mandor)");
        $this->command->info("  - Auditor: {$auditorCount} user");
        $this->command->info("  - Aktif: {$activeCount} user");
    }
}