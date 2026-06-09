<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiskCategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Membuat kategori risiko berdasarkan penelitian...');
        
        $categories = [
            ['Waktu', 'Risiko terkait ketepatan waktu penyelesaian proyek, keterlambatan pengiriman, dan jadwal pelaksanaan'],
            ['Lingkungan', 'Risiko terkait dampak lingkungan, perizinan lingkungan, dan perubahan kondisi lingkungan'],
            ['Manajemen', 'Risiko terkait manajemen proyek, koordinasi tim, pengambilan keputusan, dan komunikasi'],
            ['Hukum', 'Risiko terkait aspek hukum, kontrak, perizinan, regulasi, dan tuntutan hukum'],
            ['SDM', 'Risiko terkait sumber daya manusia, kompetensi, turnover, dan produktivitas'],
            ['K3', 'Risiko terkait keselamatan dan kesehatan kerja, kecelakaan kerja, dan kondisi kerja'],
        ];

        $categoryData = [];
        foreach ($categories as $category) {
            $categoryData[] = [
                'risk_category_name' => $category[0],
                'risk_category_description' => $category[1],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('risk_categories')->insert($categoryData);
        
        $totalCategories = DB::table('risk_categories')->count();
        $this->command->info("✓ {$totalCategories} kategori risiko berhasil dibuat");
    }
}