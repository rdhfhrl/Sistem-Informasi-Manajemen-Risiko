@extends('layouts.master')

@section('title', $objective->strategic_objective_name . ' - SIMR')

@section('page-title', 'Detail Tujuan Strategis: ' . $objective->strategic_objective_name)

@section('page-action')
<div class="flex">
    <a href="{{ route('strategic-objectives.edit', $objective->strategic_objective_id) }}" 
       class="btn btn-primary shadow-md mr-2">
        <i data-feather="edit-2"></i> Edit
    </a>
    <a href="{{ route('risks.create', ['strategic_objective_id' => $objective->strategic_objective_id]) }}" 
       class="btn btn-success shadow-md mr-2">
        <i data-feather="plus-circle"></i> Tambah Risiko
    </a>
    <div class="dropdown">
        <button class="dropdown-toggle btn btn-outline-secondary shadow-md" aria-expanded="false">
            <i data-feather="more-vertical"></i> Lainnya
        </button>
        <div class="dropdown-menu w-40">
            <div class="dropdown-content">
                <!-- Hapus atau komentari rute yang tidak terdefinisi -->
                {{-- 
                <a href="{{ route('strategic-objectives.report', $objective->strategic_objective_id) }}" class="dropdown-item">
                    <i data-feather="file-text"></i> Laporan
                </a>
                <a href="{{ route('strategic-objectives.export', $objective->strategic_objective_id) }}" class="dropdown-item">
                    <i data-feather="download"></i> Export
                </a>
                <div class="dropdown-divider"></div>
                --}}
                <form method="POST" action="{{ route('strategic-objectives.destroy', $objective->strategic_objective_id) }}" 
                      class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger" 
                            onclick="return confirm('Hapus tujuan strategis ini? Semua data risiko terkait juga akan dihapus.')">
                        <i data-feather="trash-2"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <!-- Informasi Utama -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <div class="flex-1">
                    <h2 class="font-medium text-base mr-auto">
                        Informasi Tujuan Strategis
                    </h2>
                </div>
                <div class="flex items-center space-x-2">
                    @php
                        $statusColors = [
                            'Aktif' => 'bg-green-100 text-green-800',
                            'Tidak Aktif' => 'bg-gray-100 text-gray-800',
                        ];
                        $statusColor = $statusColors[$objective->is_active ? 'Aktif' : 'Tidak Aktif'] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                        {{ $objective->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                    @if(!$objective->is_active)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i data-feather="alert-triangle"></i> Nonaktif
                        </span>
                    @endif
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label class="form-label text-gray-500">Nama Tujuan Strategis</label>
                            <div class="mt-1 text-lg font-bold text-gray-800">
                                {{ $objective->strategic_objective_name }}
                            </div>
                        </div>
                        
                        @if($objective->organization)
                        <div>
                            <label class="form-label text-gray-500">UPTD</label>
                            <div class="mt-1">
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i data-feather="briefcase"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $objective->organization->organization_code }}</div>
                                        <div class="text-sm text-gray-600">{{ $objective->organization->location }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div>
                            <label class="form-label text-gray-500">Deskripsi Tujuan</label>
                            <div class="mt-1 text-gray-700 whitespace-pre-line bg-gray-50 p-4 rounded-lg">
                                {{ $objective->strategic_objective_description ?: 'Tidak ada deskripsi' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="form-label text-gray-500">Informasi Waktu</label>
                            <div class="mt-1 space-y-2">
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="text-sm text-gray-500">Tanggal Dibuat</div>
                                        <div class="font-medium">{{ \Carbon\Carbon::parse($objective->created_at)->format('d F Y') }}</div>
                                    </div>
                                    <i data-feather="calendar"></i>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="text-sm text-gray-500">Terakhir Diperbarui</div>
                                        <div class="font-medium">{{ \Carbon\Carbon::parse($objective->updated_at)->format('d F Y') }}</div>
                                    </div>
                                    <i data-feather="calendar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ringkasan Risiko -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Risiko Terkait
                    <span class="text-gray-500 text-sm ml-2">({{ $objective->risks->count() }} data risiko)</span>
                </h2>
                <a href="{{ route('risks.index', ['strategic_objective' => $objective->strategic_objective_id]) }}" 
                   class="btn btn-outline-secondary btn-sm">
                    Lihat Semua <i data-feather="arrow-right"></i>
                </a>
            </div>
            <div class="p-5">
                @if($objective->risks->count() > 0)
                    <!-- Risk Level Distribution -->
                    <div class="mb-6">
                        <h4 class="font-medium mb-3">Distribusi Level Risiko</h4>
                        <div class="grid grid-cols-5 gap-4">
                            @php
                                $riskLevels = [
                                    'sangat_tinggi' => ['label' => 'Sangat Tinggi', 'color' => 'bg-red-100 text-red-800', 'count' => 0],
                                    'tinggi' => ['label' => 'Tinggi', 'color' => 'bg-orange-100 text-orange-800', 'count' => 0],
                                    'sedang' => ['label' => 'Sedang', 'color' => 'bg-yellow-100 text-yellow-800', 'count' => 0],
                                    'rendah' => ['label' => 'Rendah', 'color' => 'bg-green-100 text-green-800', 'count' => 0],
                                    'sangat_rendah' => ['label' => 'Sangat Rendah', 'color' => 'bg-blue-100 text-blue-800', 'count' => 0],
                                ];
                                
                                // Count risks by level
                                foreach($objective->risks as $risk) {
                                    if(isset($riskLevels[$risk->risk_level])) {
                                        $riskLevels[$risk->risk_level]['count']++;
                                    }
                                }
                            @endphp
                            
                            @foreach($riskLevels as $level => $data)
                            <div class="text-center">
                                <div class="p-4 {{ $data['color'] }} rounded-lg">
                                    <div class="text-2xl font-bold">{{ $data['count'] }}</div>
                                    <div class="text-xs mt-1">{{ $data['label'] }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Recent Risks -->
                    <div>
                        <h4 class="font-medium mb-3">Risiko Terbaru</h4>
                        <div class="overflow-x-auto">
                            <table class="table table-report -mt-2">
                                <thead>
                                    <tr>
                                        <th class="whitespace-nowrap">KODE</th>
                                        <th class="whitespace-nowrap">NAMA RISIKO</th>
                                        <th class="whitespace-nowrap">LEVEL</th>
                                        <th class="whitespace-nowrap">TANGGAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($objective->risks->take(5) as $risk)
                                    @if($risk && $risk->risk_id)
                                        <tr class="intro-x hover:bg-gray-50 cursor-pointer" 
                                            onclick="window.location='{{ route('risks.show', $risk) }}'">
                                            <td class="font-medium">
                                                <span class="text-theme-1">{{ $risk->risk_code }}</span>
                                            </td>
                                            <td>
                                                <div class="font-medium">{{ Str::limit($risk->risk_description, 40) }}</div>
                                            </td>
                                            <td>
                                                @php
                                                    $levelColor = match($risk->risk_level) {
                                                        'sangat_rendah' => 'green',
                                                        'rendah' => 'green',
                                                        'sedang' => 'yellow',
                                                        'tinggi' => 'red',
                                                        'sangat_tinggi' => 'red',
                                                        default => 'gray'
                                                    };
                                                    $levelText = match($risk->risk_level) {
                                                        'sangat_rendah' => 'Sangat Rendah',
                                                        'rendah' => 'Rendah',
                                                        'sedang' => 'Sedang',
                                                        'tinggi' => 'Tinggi',
                                                        'sangat_tinggi' => 'Sangat Tinggi',
                                                        default => '-'
                                                    };
                                                @endphp
                                                <span class="px-2 py-1 rounded text-xs font-medium bg-{{ $levelColor }}-100 text-{{ $levelColor }}-800">
                                                    {{ $levelText }}
                                                </span>
                                            </td>
                                            <td class="text-gray-600 text-sm">
                                                {{ $risk->created_at->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="alert-triangle" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada data risiko</h3>
                        <p class="text-gray-500 mb-6">Tambahkan risiko yang terkait dengan tujuan strategis ini</p>
                        <a href="{{ route('risks.create', ['strategic_objective_id' => $objective->strategic_objective_id]) }}" 
                           class="btn btn-primary">
                            <i data-feather="plus-circle"></i> Tambah Risiko Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-span-12 xl:col-span-4">
        <!-- Aksi Cepat -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Aksi Cepat
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <a href="{{ route('risks.create', ['strategic_objective_id' => $objective->strategic_objective_id]) }}" 
                       class="flex items-center p-3 bg-theme-1/5 hover:bg-theme-1/10 rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full bg-theme-1/10 flex items-center justify-center mr-3">
                            <i data-feather="plus-circle"></i>
                        </div>
                        <div>
                            <div class="font-medium">Tambah Risiko Baru</div>
                            <div class="text-xs text-gray-600">Identifikasi risiko untuk tujuan ini</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('risks.index', ['strategic_objective' => $objective->strategic_objective_id]) }}" 
                       class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i data-feather="alert-triangle"></i>
                        </div>
                        <div>
                            <div class="font-medium">Lihat Semua Risiko</div>
                            <div class="text-xs text-gray-600">Kelola semua risiko terkait</div>
                        </div>
                    </a>
                    
                    @if($objective->organization)
                    <a href="{{ route('organizations.show', $objective->organization->organization_id) }}" 
                       class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                            <i data-feather="briefcase"></i>
                        </div>
                        <div>
                            <div class="font-medium">Lihat UPTD</div>
                            <div class="text-xs text-gray-600">Detail UPTD terkait</div>
                        </div>
                    </a>
                    @endif
                    
                    <a href="{{ route('strategic-objectives.edit', $objective->strategic_objective_id) }}" 
                       class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <i data-feather="edit-2"></i>
                        </div>
                        <div>
                            <div class="font-medium">Edit Tujuan Strategis</div>
                            <div class="text-xs text-gray-600">Update data tujuan</div>
                        </div>
                    </a>
                    
                    <button onclick="toggleStatus()" 
                            class="flex items-center w-full p-3 {{ $objective->is_active ? 'bg-yellow-50 hover:bg-yellow-100' : 'bg-green-50 hover:bg-green-100' }} rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full {{ $objective->is_active ? 'bg-yellow-100' : 'bg-green-100' }} flex items-center justify-center mr-3">
                            <i data-feather="{{ $objective->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <div class="font-medium">{{ $objective->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Tujuan</div>
                            <div class="text-xs text-gray-600">{{ $objective->is_active ? 'Matikan status tujuan' : 'Aktifkan tujuan' }}</div>
                        </div>
                        <i data-feather="chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Informasi Risiko -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Analisis Risiko
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    @php
                        $totalRiskScore = $objective->risks->sum('risk_score');
                        $avgRiskScore = $objective->risks->count() > 0 ? $totalRiskScore / $objective->risks->count() : 0;
                        $riskTrend = 'stabil';
                        
                        // Hitung status berdasarkan rata-rata skor risiko
                        $riskStatus = 'Rendah';
                        $statusColor = 'bg-green-100 text-green-800';
                        
                        if ($avgRiskScore >= 15) {
                            $riskStatus = 'Sangat Tinggi';
                            $statusColor = 'bg-red-100 text-red-800';
                        } elseif ($avgRiskScore >= 9) {
                            $riskStatus = 'Tinggi';
                            $statusColor = 'bg-orange-100 text-orange-800';
                        } elseif ($avgRiskScore >= 4) {
                            $riskStatus = 'Sedang';
                            $statusColor = 'bg-yellow-100 text-yellow-800';
                        } elseif ($avgRiskScore > 0) {
                            $riskStatus = 'Rendah';
                            $statusColor = 'bg-green-100 text-green-800';
                        }
                    @endphp
                    
                    <div class="text-center p-4 rounded-lg border">
                        <div class="text-sm text-gray-500 mb-2">Status Risiko Keseluruhan</div>
                        <div class="text-2xl font-bold mb-2">{{ number_format($avgRiskScore, 1) }}</div>
                        <div class="text-lg font-medium {{ $statusColor }} px-3 py-1 rounded-full inline-block">
                            {{ $riskStatus }}
                        </div>
                        <div class="text-xs text-gray-500 mt-2">
                            Rata-rata skor risiko
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Risiko Tertinggi:</span>
                            @if($objective->risks->count() > 0)
                                @php
                                    $highestRisk = $objective->risks->sortByDesc('risk_score')->first();
                                @endphp
                                <span class="font-medium text-red-600">{{ $highestRisk->risk_score }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Risiko Terendah:</span>
                            @if($objective->risks->count() > 0)
                                @php
                                    $lowestRisk = $objective->risks->sortBy('risk_score')->first();
                                @endphp
                                <span class="font-medium text-green-600">{{ $lowestRisk->risk_score }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Skor Risiko:</span>
                            <span class="font-medium">{{ $totalRiskScore }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Tren:</span>
                            <span class="font-medium text-blue-600 capitalize">{{ $riskTrend }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informasi Sistem -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="info"></i> Informasi Sistem
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID Tujuan:</span>
                        <span class="font-medium">{{ $objective->strategic_objective_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Dibuat pada:</span>
                        <span class="font-medium">{{ $objective->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Terakhir diupdate:</span>
                        <span class="font-medium">{{ $objective->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah Risiko:</span>
                        <span class="font-medium">{{ $objective->risks->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium {{ $statusColor }} px-2 py-1 rounded text-xs">
                            {{ $objective->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    @if($objective->organization)
                    <div class="flex justify-between">
                        <span class="text-gray-600">UPTD:</span>
                        <span class="font-medium text-right">
                            {{ $objective->organization->organization_code }}<br>
                            <span class="text-xs text-gray-500">{{ $objective->organization->location }}</span>
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleStatus() {
    const currentStatus = {{ $objective->is_active ? 'true' : 'false' }};
    const newStatus = !currentStatus;
    const action = newStatus ? 'mengaktifkan' : 'menonaktifkan';
    
    if (confirm(`Apakah Anda yakin ingin ${action} tujuan strategis ini?`)) {
        fetch('{{ route("strategic-objectives.update", $objective->strategic_objective_id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: JSON.stringify({
                is_active: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Tujuan strategis berhasil di${newStatus ? 'aktifkan' : 'nonaktifkan'}!`);
                location.reload();
            } else {
                alert('Gagal memperbarui status: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memperbarui status');
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush