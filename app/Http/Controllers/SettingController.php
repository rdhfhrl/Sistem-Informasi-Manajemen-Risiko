<?php
// app/Http/Controllers/SettingController.php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        
        // Pastikan pengaturan default ada
        $this->ensureDefaultSettings();
        
        $settings = Setting::all()->groupBy('group');
        
        return view('settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        try {
            foreach ($request->except('_token', '_method') as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                
                if ($setting) {
                    $type = $setting->type;
                    
                    // Handle checkbox (boolean)
                    if ($type === 'boolean') {
                        $value = $request->has($key) ? '1' : '0';
                    }
                    
                    Setting::set($key, $value, $type);
                }
            }

            return redirect()
                ->route('settings.index')
                ->with('success', 'Pengaturan berhasil disimpan');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Ensure default settings exist
     */
    private function ensureDefaultSettings()
    {
        $defaults = [
            // GENERAL SETTINGS
            [
                'key' => 'app_name',
                'value' => 'SIMR - Sistem Informasi Manajemen Risiko',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Nama aplikasi'
            ],
            [
                'key' => 'app_description',
                'value' => 'Sistem Manajemen Risiko Terintegrasi',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Deskripsi aplikasi'
            ],
            [
                'key' => 'app_timezone',
                'value' => 'Asia/Jakarta',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Zona waktu'
            ],
            [
                'key' => 'records_per_page',
                'value' => '10',
                'type' => 'number',
                'group' => 'general',
                'description' => 'Jumlah data per halaman'
            ],

            // NOTIFICATION SETTINGS
            [
                'key' => 'notify_risk_created',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notification',
                'description' => 'Notifikasi saat risiko baru dibuat'
            ],
            [
                'key' => 'notify_high_risk',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notification',
                'description' => 'Notifikasi saat risiko tinggi terdeteksi'
            ],
            [
                'key' => 'notify_risk_updated',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notification',
                'description' => 'Notifikasi saat risiko diupdate'
            ],
            [
                'key' => 'notification_retention_days',
                'value' => '30',
                'type' => 'number',
                'group' => 'notification',
                'description' => 'Hapus notifikasi yang sudah dibaca setelah (hari)'
            ],

            // EMAIL SETTINGS
            [
                'key' => 'email_notifications',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'email',
                'description' => 'Aktifkan notifikasi email'
            ],
            [
                'key' => 'admin_email',
                'value' => 'admin@simr.com',
                'type' => 'text',
                'group' => 'email',
                'description' => 'Email administrator'
            ],

            // RISK MANAGEMENT SETTINGS
            [
                'key' => 'risk_auto_calculate',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'risk',
                'description' => 'Otomatis hitung tingkat dan level risiko'
            ],
            [
                'key' => 'risk_high_threshold',
                'value' => '15',
                'type' => 'number',
                'group' => 'risk',
                'description' => 'Threshold risiko tinggi (≥)'
            ],
            [
                'key' => 'risk_medium_threshold',
                'value' => '5',
                'type' => 'number',
                'group' => 'risk',
                'description' => 'Threshold risiko sedang (≥)'
            ],
        ];

        foreach ($defaults as $default) {
            Setting::firstOrCreate(
                ['key' => $default['key']],
                $default
            );
        }
    }
}