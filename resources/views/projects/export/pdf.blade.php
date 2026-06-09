<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Proyek - SIMR</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        .summary {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .summary-item {
            text-align: center;
            flex: 1;
        }
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
        }
        .summary-label {
            font-size: 11px;
            color: #7f8c8d;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #34495e;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .status-aktif { background: #d4edda; color: #155724; }
        .status-selesai { background: #cce5ff; color: #004085; }
        .status-ditunda { background: #fff3cd; color: #856404; }
        .status-dibatalkan { background: #f8d7da; color: #721c24; }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-red { background: #f8d7da; color: #721c24; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-gray { background: #e9ecef; color: #495057; }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #7f8c8d;
        }
        .filter-info {
            background: #f1f8ff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DAFTAR PROYEK KONSTRUKSI</h1>
        <p>Sistem Informasi Manajemen Risiko (SIMR)</p>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    
    @if($filters['search'] || $filters['status'] || $filters['year'])
    <div class="filter-info">
        <strong>Filter yang diterapkan:</strong>
        @if($filters['search'])
        <br>Pencarian: "{{ $filters['search'] }}"
        @endif
        @if($filters['status'])
        <br>Status: {{ $filters['status'] }}
        @endif
        @if($filters['year'])
        <br>Tahun: {{ $filters['year'] }}
        @endif
    </div>
    @endif
    
    <div class="summary">
        <div class="summary-row">
            <div class="summary-item">
                <div class="summary-value">{{ $totalProjects }}</div>
                <div class="summary-label">Total Proyek</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $activeProjects }}</div>
                <div class="summary-label">Proyek Aktif</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $delayedProjects }}</div>
                <div class="summary-label">Proyek Terlambat</div>
            </div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Proyek</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Risiko</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $index => $project)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $project->pro_nama }}</td>
                <td>{{ $project->pro_lokasi }}</td>
                <td>
                    @php
                        $statusClass = 'status-' . strtolower($project->pro_status);
                    @endphp
                    <span class="status {{ $statusClass }}">{{ $project->pro_status }}</span>
                    @if($project->pro_status == 'Aktif' && \Carbon\Carbon::parse($project->pro_tanggal_selesai)->lt(now()))
                    <br><span class="badge badge-red">TERLAMBAT</span>
                    @endif
                </td>
                <td>
                    @if($project->risks_count > 0)
                    <span class="badge badge-red">{{ $project->risks_count }} risiko</span>
                    @else
                    <span class="badge badge-gray">-</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($project->pro_tanggal_mulai)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($project->pro_tanggal_selesai)->format('d/m/Y') }}</td>
                <td>
                    @if($project->pro_deskripsi)
                    {{ Str::limit($project->pro_deskripsi, 50) }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak oleh: {{ Auth::user()->name }}</p>
        <p>Halaman: <span class="page-number"></span></p>
        <p>&copy; {{ date('Y') }} SIMR - Sistem Informasi Manajemen Risiko</p>
    </div>
</body>
</html>