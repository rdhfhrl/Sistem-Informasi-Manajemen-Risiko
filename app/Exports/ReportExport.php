<?php

namespace App\Exports;

use Spatie\SimpleExcel\SimpleExcelWriter;

class ReportExport
{
    protected $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function export($filePath)
    {
        $writer = SimpleExcelWriter::create($filePath);

        $writer->addRow(['==== LAPORAN RISIKO ====']);
        $writer->addHeader(['Informasi', 'Nilai'])
            ->addRows([
                ['Periode', $this->report['periode'] ?? '-'],
                ['Nama Proyek', $this->report['project_name'] ?? '-'],
                ['Total Risiko', $this->report['total'] ?? 0],
                ['Strategi Mitigasi Teratas', $this->report['data_risiko']['top_strategy'] ?? '-'],
                ['Proyek Risiko Tertinggi', $this->report['data_risiko']['top_project'] ?? '-'],
            ]);

        $writer->addRow([]);
        $writer->addRow(['==== DISTRIBUSI RISIKO ====']);

        // --- Sheet 2: Distribusi Risiko ---
        $writer->addHeader(['Level Risiko', 'Jumlah']);
        foreach ($this->report['data_risiko']['per_level'] ?? [] as $level => $jumlah) {
            $writer->addRow([ucfirst($level), $jumlah]);
        }

        $writer->addRow([]);
        $writer->addRow(['==== TOP 5 RISIKO TERTINGGI ====']);

        // --- Sheet 3: Top Risiko ---
        $writer->addHeader(['ID Risiko', 'Nama Risiko', 'Tingkat', 'Level']);
        foreach ($this->report['data_risiko']['top_risks'] ?? [] as $r) {
            $writer->addRow([
                $r['id'] ?? '-',
                $r['nama'] ?? '-',
                $r['tingkat'] ?? '-',
                $r['level'] ?? '-',
            ]);
        }

        $writer->close();
    }
}
