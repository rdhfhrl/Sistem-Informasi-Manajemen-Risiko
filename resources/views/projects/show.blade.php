@extends('layouts.master')

@section('title', $project->pro_nama . ' - SIMR')

@section('page-title', 'Detail Proyek: ' . $project->pro_nama)

@section('page-action')
<div class="flex">
    <a href="{{ route('projects.edit', $project->pro_id) }}" 
       class="btn btn-primary shadow-md mr-2">
        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit
    </a>
    <a href="{{ route('risks.create', ['pro_id' => $project->pro_id]) }}" 
       class="btn btn-success shadow-md mr-2">
        <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Risiko
    </a>
    <div class="dropdown">
        <button class="dropdown-toggle btn btn-outline-secondary shadow-md" aria-expanded="false">
            <i data-feather="more-vertical" class="w-4 h-4 mr-2"></i> Lainnya
        </button>
        <div class="dropdown-menu w-40">
            <div class="dropdown-content">
                <!-- Hapus atau komentari rute yang tidak terdefinisi -->
                {{-- 
                <a href="{{ route('projects.report', $project->pro_id) }}" class="dropdown-item">
                    <i data-feather="file-text" class="w-4 h-4 mr-2"></i> Laporan
                </a>
                <a href="{{ route('projects.export', $project->pro_id) }}" class="dropdown-item">
                    <i data-feather="download" class="w-4 h-4 mr-2"></i> Export
                </a>
                <div class="dropdown-divider"></div>
                --}}
                <form method="POST" action="{{ route('projects.destroy', $project->pro_id) }}" 
                      class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger" 
                            onclick="return confirm('Hapus proyek ini? Semua data risiko terkait juga akan dihapus.')">
                        <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
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
                        Informasi Proyek
                    </h2>
                </div>
                <div class="flex items-center space-x-2">
                    @php
                        $statusColors = [
                            'Aktif' => 'bg-green-100 text-green-800',
                            'Selesai' => 'bg-blue-100 text-blue-800',
                            'Ditunda' => 'bg-yellow-100 text-yellow-800',
                            'Dibatalkan' => 'bg-red-100 text-red-800'
                        ];
                        $statusColor = $statusColors[$project->pro_status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                        {{ $project->pro_status }}
                    </span>
                    @if($project->pro_status == 'Aktif' && \Carbon\Carbon::parse($project->pro_tanggal_selesai)->lt(now()))
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i data-feather="alert-circle" class="w-4 h-4 inline mr-1"></i> Terlambat
                        </span>
                    @endif
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label class="form-label text-gray-500">Nama Proyek</label>
                            <div class="mt-1 text-lg font-bold text-gray-800">
                                {{ $project->pro_nama }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label text-gray-500">Lokasi Proyek</label>
                            <div class="mt-1 text-gray-700">
                                {{ $project->pro_lokasi }}
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label text-gray-500">Deskripsi Proyek</label>
                            <div class="mt-1 text-gray-700 whitespace-pre-line">
                                {{ $project->pro_deskripsi ?: 'Tidak ada deskripsi' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="form-label text-gray-500">Jadwal Proyek</label>
                            <div class="mt-1 space-y-2">
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="text-sm text-gray-500">Tanggal Mulai</div>
                                        <div class="font-medium">{{ \Carbon\Carbon::parse($project->pro_tanggal_mulai)->format('d F Y') }}</div>
                                    </div>
                                    <i data-feather="calendar" class="w-5 h-5 text-gray-400"></i>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="text-sm text-gray-500">Tanggal Selesai</div>
                                        <div class="font-medium">{{ \Carbon\Carbon::parse($project->pro_tanggal_selesai)->format('d F Y') }}</div>
                                    </div>
                                    <i data-feather="calendar" class="w-5 h-5 text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label text-gray-500">Durasi & Progress</label>
                            <div class="mt-1">
                                @php
                                    $startDate = \Carbon\Carbon::parse($project->pro_tanggal_mulai);
                                    $endDate = \Carbon\Carbon::parse($project->pro_tanggal_selesai);
                                    $totalDays = $startDate->diffInDays($endDate);
                                    
                                    if($project->pro_status == 'Aktif') {
                                        $daysPassed = $startDate->diffInDays(now());
                                        $progressPercent = min(100, max(0, ($daysPassed / $totalDays) * 100));
                                        $daysLeft = $endDate->diffInDays(now(), false);
                                    } else {
                                        $progressPercent = 100;
                                        $daysLeft = 0;
                                    }
                                @endphp
                                
                                <div class="flex justify-between text-sm mb-1">
                                    <span>Progress Waktu</span>
                                    <span class="font-medium">{{ round($progressPercent) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-theme-1 h-2 rounded-full" 
                                         style="width: {{ $progressPercent }}%"></div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                                        <div class="text-lg font-bold text-blue-600">{{ $totalDays }}</div>
                                        <div class="text-xs text-gray-600">Total Hari</div>
                                    </div>
                                    @if($project->pro_status == 'Aktif')
                                    <div class="text-center p-3 {{ $daysLeft < 0 ? 'bg-red-50' : 'bg-green-50' }} rounded-lg">
                                        <div class="text-lg font-bold {{ $daysLeft < 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ abs(floor($daysLeft)) }}
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            {{ $daysLeft < 0 ? 'Hari Terlambat' : 'Hari Tersisa' }}
                                        </div>
                                    </div>
                                    @endif
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
                    Ringkasan Risiko Proyek
                    <span class="text-gray-500 text-sm ml-2">({{ $project->risks_count ?? 0 }} data risiko)</span>
                </h2>
                <a href="{{ route('risks.index', ['project' => $project->pro_id]) }}" 
                   class="btn btn-outline-secondary btn-sm">
                    Lihat Semua <i data-feather="arrow-right" class="w-4 h-4 ml-2"></i>
                </a>
            </div>
            <div class="p-5">
                @if($project->risks_count > 0)
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
                                foreach($project->risks as $risk) {
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
                                        <th class="whitespace-nowrap">DESKRIPSI</th>
                                        <th class="whitespace-nowrap">KATEGORI</th>
                                        <th class="whitespace-nowrap">LEVEL</th>
                                        <th class="whitespace-nowrap">TANGGAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->risks->take(5) as $risk)
                                    <tr class="intro-x hover:bg-gray-50 cursor-pointer" 
    onclick="window.location='{{ route('risks.show', $risk) }}'">
                                        <td class="font-medium">
                                            <span class="text-theme-1">{{ $risk->risk_code }}</span>
                                        </td>
                                        <td>
                                            <div class="font-medium">{{ Str::limit($risk->risk_description, 40) }}</div>
                                        </td>
                                        <td>
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $risk->category->risk_category_name ?? 'N/A' }}
                                            </span>
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
                        <p class="text-gray-500 mb-6">Tambahkan risiko untuk memulai manajemen risiko proyek ini</p>
                        <a href="{{ route('risks.create', ['pro_id' => $project->pro_id]) }}" 
                           class="btn btn-primary">
                            <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Risiko Pertama
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
                    <a href="{{ route('risks.create', ['pro_id' => $project->pro_id]) }}" 
                       class="flex items-center p-3 bg-theme-1/5 hover:bg-theme-1/10 rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full bg-theme-1/10 flex items-center justify-center mr-3">
                            <i data-feather="plus-circle" class="w-5 h-5 text-theme-1"></i>
                        </div>
                        <div>
                            <div class="font-medium">Tambah Risiko Baru</div>
                            <div class="text-xs text-gray-600">Identifikasi risiko untuk proyek ini</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('risks.index', ['project' => $project->pro_id]) }}" 
                       class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i data-feather="alert-triangle" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Lihat Semua Risiko</div>
                            <div class="text-xs text-gray-600">Kelola semua risiko proyek ini</div>
                        </div>
                    </a>
                    
                    {{-- Komentari atau hapus bagian ini sampai rute didefinisikan --}}
                    {{--
                    <a href="{{ route('reports.create', ['project_id' => $project->pro_id]) }}" 
                       class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <i data-feather="file-text" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Buat Laporan Proyek</div>
                            <div class="text-xs text-gray-600">Generate laporan risiko proyek</div>
                        </div>
                    </a>
                    --}}
                    
                    <a href="{{ route('projects.edit', $project->pro_id) }}" 
                       class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                            <i data-feather="edit" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Edit Informasi Proyek</div>
                            <div class="text-xs text-gray-600">Update data proyek</div>
                        </div>
                    </a>
                    
                    {{-- Hapus atau ganti dengan modal sederhana --}}
                    <button onclick="showStatusForm()" 
                            class="flex items-center w-full p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                            <i data-feather="refresh-cw" class="w-5 h-5 text-yellow-600"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <div class="font-medium">Update Status Proyek</div>
                            <div class="text-xs text-gray-600">Ubah status proyek saat ini</div>
                        </div>
                        <i data-feather="chevron-right" class="w-5 h-5 text-gray-400"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Timeline Proyek -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Timeline Proyek
                </h2>
            </div>
            <div class="p-5">
                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    
                    <div class="space-y-6 relative">
                        <!-- Start Date -->
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4 z-10">
                                <i data-feather="play-circle" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">Mulai Proyek</div>
                                <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($project->pro_tanggal_mulai)->format('d F Y') }}</div>
                                <div class="text-xs text-gray-500 mt-1">Proyek dimulai</div>
                            </div>
                        </div>
                        
                        <!-- Current Date -->
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4 z-10">
                                <i data-feather="calendar" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">Hari Ini</div>
                                <div class="text-sm text-gray-600">{{ now()->format('d F Y') }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    @php
                                        $daysPassed = floor(\Carbon\Carbon::parse($project->pro_tanggal_mulai)->diffInDays(now()));
                                        $totalDays = floor(\Carbon\Carbon::parse($project->pro_tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($project->pro_tanggal_selesai)));
                                    @endphp
                                    Hari ke-{{ $daysPassed }} dari {{ $totalDays }} hari
                                </div>
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full {{ $project->pro_status == 'Selesai' ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center mr-4 z-10">
                                <i data-feather="flag" class="w-5 h-5 {{ $project->pro_status == 'Selesai' ? 'text-green-600' : 'text-gray-600' }}"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium">Target Selesai</div>
                                <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($project->pro_tanggal_selesai)->format('d F Y') }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    @php
                                        // Hitung daysLeft dengan floor() agar bilangan bulat
                                        $daysLeft = floor(now()->diffInDays(\Carbon\Carbon::parse($project->pro_tanggal_selesai), false));
                                    @endphp
                                    @if($project->pro_status == 'Selesai')
                                        Proyek telah selesai
                                    @elseif($daysLeft < 0)
                                        <span class="text-red-600">Terlambat {{ abs($daysLeft) }} hari</span>
                                    @else
                                        {{ $daysLeft }} hari tersisa
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informasi Sistem -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="info" class="w-5 h-5 mr-2 inline"></i> Informasi Sistem
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID Proyek:</span>
                        <span class="font-medium">{{ $project->pro_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Dibuat pada:</span>
                        <span class="font-medium">{{ $project->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Terakhir diupdate:</span>
                        <span class="font-medium">{{ $project->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah Risiko:</span>
                        <span class="font-medium">{{ $project->risks_count ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Modal styling */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    
    .modal--show {
        display: flex;
    }
    
    .modal__content {
        background-color: white;
        border-radius: 0.5rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }
</style>
@endpush

@push('scripts')
<script>
function showStatusForm() {
    // Buat form sederhana tanpa modal kompleks
    const status = prompt('Masukkan status baru (Aktif/Selesai/Ditunda/Dibatalkan):', '{{ $project->pro_status }}');
    
    if (status && ['Aktif', 'Selesai', 'Ditunda', 'Dibatalkan'].includes(status)) {
        const notes = prompt('Catatan (opsional):');
        
        // Kirim request update status
        fetch('{{ route("projects.update", $project->pro_id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: JSON.stringify({
                pro_status: status,
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Status berhasil diperbarui!');
                location.reload();
            } else {
                alert('Gagal memperbarui status: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memperbarui status');
        });
    } else if (status) {
        alert('Status tidak valid. Pilih: Aktif, Selesai, Ditunda, atau Dibatalkan');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush