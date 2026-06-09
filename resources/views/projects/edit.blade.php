@extends('layouts.master')

@section('title', 'Edit Proyek - SIMR')

@section('page-title', 'Edit Proyek: ' . $project->pro_nama)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Proyek</a></li>
    <li class="breadcrumb-item"><a href="{{ route('projects.show', $project->pro_id) }}">
        {{ Str::limit($project->pro_nama, 20) }}
    </a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Form Edit Proyek
                </h2>
                <a href="{{ route('projects.show', $project->pro_id) }}" 
                   class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                </a>
            </div>
            <form method="POST" action="{{ route('projects.update', $project->pro_id) }}" 
                  class="p-5" id="project-form">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Proyek -->
                    <div class="md:col-span-2">
                        <label for="pro_nama" class="form-label">
                            Nama Proyek <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="pro_nama" name="pro_nama" 
                               class="form-control w-full" 
                               value="{{ old('pro_nama', $project->pro_nama) }}" 
                               required autofocus>
                        @error('pro_nama')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Lokasi Proyek -->
                    <div class="md:col-span-2">
                        <label for="pro_lokasi" class="form-label">
                            Lokasi Proyek <span class="text-danger">*</span>
                        </label>
                        <textarea id="pro_lokasi" name="pro_lokasi" 
                                  class="form-control w-full" 
                                  rows="2" 
                                  required>{{ old('pro_lokasi', $project->pro_lokasi) }}</textarea>
                        @error('pro_lokasi')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Deskripsi Proyek -->
                    <div class="md:col-span-2">
                        <label for="pro_deskripsi" class="form-label">
                            Deskripsi Proyek
                        </label>
                        <textarea id="pro_deskripsi" name="pro_deskripsi" 
                                  class="form-control w-full" 
                                  rows="3">{{ old('pro_deskripsi', $project->pro_deskripsi) }}</textarea>
                        @error('pro_deskripsi')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Tanggal Mulai -->
                    <div>
                        <label for="pro_tanggal_mulai" class="form-label">
                            Tanggal Mulai <span class="text-danger">*</span>
                        </label>
                        <div class="relative">
                            <input type="date" id="pro_tanggal_mulai" name="pro_tanggal_mulai" 
                                   class="form-control w-full pl-10" 
                                   value="{{ old('pro_tanggal_mulai', $project->pro_tanggal_mulai->format('Y-m-d')) }}" 
                                   required>
                            <div class="absolute left-3 top-2.5">
                                <i data-feather="calendar" class="w-5 h-5 text-gray-400"></i>
                            </div>
                        </div>
                        @error('pro_tanggal_mulai')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Tanggal Selesai -->
                    <div>
                        <label for="pro_tanggal_selesai" class="form-label">
                            Tanggal Selesai <span class="text-danger">*</span>
                        </label>
                        <div class="relative">
                            <input type="date" id="pro_tanggal_selesai" name="pro_tanggal_selesai" 
                                   class="form-control w-full pl-10" 
                                   value="{{ old('pro_tanggal_selesai', $project->pro_tanggal_selesai->format('Y-m-d')) }}" 
                                   required>
                            <div class="absolute left-3 top-2.5">
                                <i data-feather="calendar" class="w-5 h-5 text-gray-400"></i>
                            </div>
                        </div>
                        @error('pro_tanggal_selesai')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Status Proyek -->
                    <div class="md:col-span-2">
                        <label for="pro_status" class="form-label">
                            Status Proyek <span class="text-danger">*</span>
                        </label>
                        <select id="pro_status" name="pro_status" class="form-select w-full" required>
                            <option value="Aktif" {{ old('pro_status', $project->pro_status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Selesai" {{ old('pro_status', $project->pro_status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Ditunda" {{ old('pro_status', $project->pro_status) == 'Ditunda' ? 'selected' : '' }}>Ditunda</option>
                            <option value="Dibatalkan" {{ old('pro_status', $project->pro_status) == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                        @error('pro_status')
                            <div class="text-danger mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Durasi Estimate -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="form-label">Estimasi Durasi</label>
                            @php
                                $startDate = \Carbon\Carbon::parse($project->pro_tanggal_mulai);
                                $endDate = \Carbon\Carbon::parse($project->pro_tanggal_selesai);
                                $totalDays = $startDate->diffInDays($endDate);
                                
                                if($totalDays === 0) {
                                    $durationText = 'Hari yang sama';
                                } else if($totalDays < 30) {
                                    $durationText = $totalDays . ' hari';
                                } else if($totalDays < 365) {
                                    $months = floor($totalDays / 30);
                                    $days = $totalDays % 30;
                                    $durationText = $months . ' bulan' . ($days > 0 ? ' ' . $days . ' hari' : '');
                                } else {
                                    $years = floor($totalDays / 365);
                                    $months = floor(($totalDays % 365) / 30);
                                    $durationText = $years . ' tahun' . ($months > 0 ? ' ' . $months . ' bulan' : '');
                                }
                            @endphp
                            <div id="duration-display" class="text-lg font-bold text-theme-1">
                                {{ $durationText }}
                            </div>
                        </div>
                        <button type="button" onclick="calculateDuration()" class="btn btn-outline-secondary">
                            <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Hitung Ulang
                        </button>
                    </div>
                </div>
                
                <!-- Peringatan jika ada risiko -->
                @if($project->risks_count > 0)
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start">
                        <i data-feather="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3 mt-0.5"></i>
                        <div>
                            <h4 class="font-medium text-yellow-800 mb-1">Perhatian!</h4>
                            <p class="text-sm text-yellow-700">
                                Proyek ini memiliki <strong>{{ $project->risks_count }} data risiko</strong>. 
                                Perubahan tanggal atau status proyek dapat memengaruhi:
                            </p>
                            <ul class="text-sm text-yellow-700 mt-2 list-disc list-inside">
                                <li>Monitoring dan evaluasi risiko</li>
                                <li>Jadwal mitigasi risiko</li>
                                <li>Pelaporan dan analisis risiko</li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Informasi Proyek -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                        <i data-feather="info" class="w-5 h-5 mr-2"></i> Catatan:
                    </h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Pastikan tanggal selesai lebih besar atau sama dengan tanggal mulai</li>
                        <li>• Perubahan status dari "Selesai" ke "Aktif" akan mengaktifkan kembali monitoring risiko</li>
                        <li>• Data proyek yang sudah direvisi akan tercatat dalam history sistem</li>
                    </ul>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end mt-8">
                    <a href="{{ route('projects.show', $project->pro_id) }}" 
                       class="btn btn-outline-secondary w-24 mr-3">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary w-32">
                        <i data-feather="save" class="w-4 h-4 mr-2"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-span-12 xl:col-span-4">
        <!-- Preview Card -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Data Saat Ini
                </h2>
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
            </div>
            <div class="p-5">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 rounded-full bg-theme-1/10 flex items-center justify-center mx-auto mb-4">
                        <i data-feather="briefcase" class="w-10 h-10 text-theme-1"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">{{ $project->pro_nama }}</h3>
                    <p class="text-gray-600 mt-1">{{ Str::limit($project->pro_lokasi, 40) }}</p>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="form-label text-gray-500">Status Saat Ini</label>
                        <div class="mt-1">
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                                {{ $project->pro_status }}
                            </span>
                            @if($project->pro_status == 'Aktif' && \Carbon\Carbon::parse($project->pro_tanggal_selesai)->lt(now()))
                                <div class="text-xs text-red-600 mt-1">
                                    <i data-feather="alert-circle" class="w-3 h-3 inline mr-1"></i>
                                    Terlambat {{ \Carbon\Carbon::parse($project->pro_tanggal_selesai)->diffInDays(now()) }} hari
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label text-gray-500">Mulai</label>
                            <div class="mt-1 font-medium">
                                {{ $project->pro_tanggal_mulai->format('d/m/Y') }}
                            </div>
                        </div>
                        <div>
                            <label class="form-label text-gray-500">Selesai</label>
                            <div class="mt-1 font-medium">
                                {{ $project->pro_tanggal_selesai->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="form-label text-gray-500">Durasi</label>
                        <div class="mt-1 text-lg font-bold text-theme-1">{{ $durationText }}</div>
                    </div>
                    
                    <div>
                        <label class="form-label text-gray-500">Statistik Risiko</label>
                        <div class="mt-1">
                            @if($project->risks_count > 0)
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600">Total Risiko:</span>
                                    <span class="font-medium">{{ $project->risks_count }}</span>
                                </div>
                                <div class="text-center">
                                    <a href="{{ route('risks.index', ['project' => $project->pro_id]) }}" 
                                       class="text-theme-1 text-sm hover:underline">
                                        Lihat daftar risiko →
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-2">
                                    <span class="text-gray-500">Belum ada data risiko</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- History Perubahan -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="clock" class="w-5 h-5 mr-2 inline"></i> Riwayat
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="calendar" class="w-4 h-4 text-gray-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Dibuat</div>
                            <div class="text-sm text-gray-600">
                                {{ $project->created_at->format('d F Y H:i') }}
                                @if($project->created_at->diffInDays(now()) > 0)
                                    <span class="text-gray-400 ml-2">
                                        ({{ $project->created_at->diffForHumans() }})
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="refresh-cw" class="w-4 h-4 text-gray-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Terakhir Diupdate</div>
                            <div class="text-sm text-gray-600">
                                {{ $project->updated_at->format('d F Y H:i') }}
                                @if($project->updated_at->diffInHours(now()) > 0)
                                    <span class="text-gray-400 ml-2">
                                        ({{ $project->updated_at->diffForHumans() }})
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($project->risks_count > 0)
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="alert-triangle" class="w-4 h-4 text-red-600"></i>
                        </div>
                        <div>
                            <div class="font-medium">Data Risiko</div>
                            <div class="text-sm text-gray-600">
                                Memiliki {{ $project->risks_count }} data risiko aktif
                                @if($project->risks_count > 0)
                                    <div class="text-xs text-red-600 mt-1">
                                        * Perubahan jadwal dapat memengaruhi monitoring risiko
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Panduan Edit -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 inline"></i> Panduan Edit
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="calendar" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Perubahan Jadwal</h4>
                            <p class="text-sm text-gray-600">
                                Perubahan tanggal akan mempengaruhi timeline monitoring risiko
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="refresh-cw" class="w-4 h-4 text-yellow-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Update Status</h4>
                            <p class="text-sm text-gray-600">
                                Status "Selesai" akan menghentikan monitoring risiko aktif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="save" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Penyimpanan Data</h4>
                            <p class="text-sm text-gray-600">
                                Semua perubahan akan tercatat dalam history sistem
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Initialize date validation
    document.getElementById('pro_tanggal_mulai').addEventListener('change', validateDates);
    document.getElementById('pro_tanggal_selesai').addEventListener('change', validateDates);
    
    // Calculate initial duration
    calculateDuration();
    
    // Form validation
    document.getElementById('project-form').addEventListener('submit', function(e) {
        if (!validateDates()) {
            e.preventDefault();
        }
    });
});

function validateDates() {
    const startDate = new Date(document.getElementById('pro_tanggal_mulai').value);
    const endDate = new Date(document.getElementById('pro_tanggal_selesai').value);
    
    if (endDate < startDate) {
        alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai');
        document.getElementById('pro_tanggal_selesai').focus();
        return false;
    }
    
    return true;
}

function calculateDuration() {
    const startDate = document.getElementById('pro_tanggal_mulai').value;
    const endDate = document.getElementById('pro_tanggal_selesai').value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        if (end >= start) {
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            let durationText = '';
            if (diffDays === 0) {
                durationText = 'Hari yang sama';
            } else if (diffDays < 30) {
                durationText = `${diffDays} hari`;
            } else if (diffDays < 365) {
                const months = Math.floor(diffDays / 30);
                const days = diffDays % 30;
                durationText = `${months} bulan${days > 0 ? ` ${days} hari` : ''}`;
            } else {
                const years = Math.floor(diffDays / 365);
                const months = Math.floor((diffDays % 365) / 30);
                durationText = `${years} tahun${months > 0 ? ` ${months} bulan` : ''}`;
            }
            
            document.getElementById('duration-display').textContent = durationText;
        } else {
            document.getElementById('duration-display').textContent = 'Tanggal tidak valid';
        }
    }
}
</script>
@endpush