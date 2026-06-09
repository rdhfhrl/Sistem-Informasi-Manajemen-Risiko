<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'app_name', 'value' => 'SIMR', 'type' => 'text', 'group' => 'general', 'description' => 'Nama aplikasi'],
            ['key' => 'app_description', 'value' => 'Sistem Informasi Manajemen Risiko', 'type' => 'text', 'group' => 'general', 'description' => 'Deskripsi aplikasi'],
            ['key' => 'app_timezone', 'value' => 'Asia/Jakarta', 'type' => 'text', 'group' => 'general', 'description' => 'Zona waktu'],
            ['key' => 'records_per_page', 'value' => '10', 'type' => 'number', 'group' => 'general', 'description' => 'Jumlah data per halaman'],
            
            // Notification
            ['key' => 'notify_risk_created', 'value' => '1', 'type' => 'boolean', 'group' => 'notification', 'description' => 'Notifikasi risiko baru'],
            ['key' => 'notify_high_risk', 'value' => '1', 'type' => 'boolean', 'group' => 'notification', 'description' => 'Notifikasi risiko tinggi'],
            ['key' => 'notify_risk_updated', 'value' => '1', 'type' => 'boolean', 'group' => 'notification', 'description' => 'Notifikasi update risiko'],
            ['key' => 'notification_retention_days', 'value' => '30', 'type' => 'number', 'group' => 'notification', 'description' => 'Hapus notifikasi lama (hari)'],
            
            // Email
            ['key' => 'email_notifications', 'value' => '0', 'type' => 'boolean', 'group' => 'email', 'description' => 'Aktifkan email notifikasi'],
            ['key' => 'admin_email', 'value' => 'admin@simr.com', 'type' => 'text', 'group' => 'email', 'description' => 'Email administrator'],
            
            // Risk
            ['key' => 'risk_auto_calculate', 'value' => '1', 'type' => 'boolean', 'group' => 'risk', 'description' => 'Hitung otomatis tingkat risiko'],
            ['key' => 'risk_high_threshold', 'value' => '15', 'type' => 'number', 'group' => 'risk', 'description' => 'Threshold risiko tinggi'],
            ['key' => 'risk_medium_threshold', 'value' => '5', 'type' => 'number', 'group' => 'risk', 'description' => 'Threshold risiko sedang'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

