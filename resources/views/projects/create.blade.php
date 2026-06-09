@extends('layouts.master')

@section('title', 'Tambah Proyek - SIMR')

@section('page-title', 'Tambah Proyek Baru')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Proyek</a></li>
    <li class="breadcrumb-item active">Tambah Baru</li>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 xl:col-span-8">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Form Tambah Proyek
                </h2>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                </a>
            </div>
            <form method="POST" action="{{ route('projects.store') }}" class="p-5" id="project-form">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Proyek -->
                    <div class="md:col-span-2">
                        <label for="pro_nama" class="form-label">
                            Nama Proyek <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="pro_nama" name="pro_nama" 
                               class="form-control w-full" 
                               value="{{ old('pro_nama') }}" 
                               placeholder="Contoh: Pembangunan Jalan Tol Medan-Binjai"
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
                                  placeholder="Alamat lengkap lokasi proyek..."
                                  required>{{ old('pro_lokasi') }}</textarea>
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
                                  rows="3" 
                                  placeholder="Deskripsi detail tentang proyek (ruang lingkup, tujuan, dll)...">{{ old('pro_deskripsi') }}</textarea>
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
                                   value="{{ old('pro_tanggal_mulai') }}" 
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
                                   value="{{ old('pro_tanggal_selesai') }}" 
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
                            <option value="">Pilih Status...</option>
                            <option value="Aktif" {{ old('pro_status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Selesai" {{ old('pro_status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Ditunda" {{ old('pro_status') == 'Ditunda' ? 'selected' : '' }}>Ditunda</option>
                            <option value="Dibatalkan" {{ old('pro_status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
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
                            <div id="duration-display" class="text-lg font-bold text-theme-1">
                                Hitung otomatis berdasarkan tanggal
                            </div>
                        </div>
                        <button type="button" onclick="calculateDuration()" class="btn btn-outline-secondary">
                            <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Hitung Ulang
                        </button>
                    </div>
                </div>
                
                <!-- Informasi Proyek -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                        <i data-feather="info" class="w-5 h-5 mr-2"></i> Informasi Penting:
                    </h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Pastikan tanggal selesai lebih besar atau sama dengan tanggal mulai</li>
                        <li>• Status "Aktif" untuk proyek yang sedang berjalan</li>
                        <li>• Status "Selesai" untuk proyek yang telah rampung</li>
                        <li>• Data proyek akan digunakan untuk manajemen risiko</li>
                    </ul>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end mt-8">
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary w-24 mr-3">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary w-32">
                        <i data-feather="save" class="w-4 h-4 mr-2"></i> Simpan
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
                    Preview Proyek
                </h2>
            </div>
            <div class="p-5">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 rounded-full bg-theme-1/10 flex items-center justify-center mx-auto mb-4">
                        <i data-feather="briefcase" class="w-10 h-10 text-theme-1"></i>
                    </div>
                    <h3 id="preview-nama" class="text-xl font-bold text-gray-800">Nama Proyek</h3>
                    <p id="preview-lokasi" class="text-gray-600 mt-1">Lokasi akan ditampilkan di sini</p>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="form-label text-gray-500">Status</label>
                        <div id="preview-status" class="mt-1">
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                Belum dipilih
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label text-gray-500">Mulai</label>
                            <div id="preview-mulai" class="mt-1 font-medium">-</div>
                        </div>
                        <div>
                            <label class="form-label text-gray-500">Selesai</label>
                            <div id="preview-selesai" class="mt-1 font-medium">-</div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="form-label text-gray-500">Durasi</label>
                        <div id="preview-durasi" class="mt-1 text-lg font-bold text-theme-1">-</div>
                    </div>
                    
                    <div>
                        <label class="form-label text-gray-500">Deskripsi</label>
                        <div id="preview-deskripsi" class="mt-1 text-sm text-gray-600">
                            Deskripsi akan ditampilkan di sini
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panduan -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 inline"></i> Panduan
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="map-pin" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Lokasi Proyek</h4>
                            <p class="text-sm text-gray-600">Sertakan alamat lengkap untuk identifikasi lokasi</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="calendar" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Jadwal Proyek</h4>
                            <p class="text-sm text-gray-600">Pastikan timeline realistis untuk monitoring risiko</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-3 flex-shrink-0">
                            <i data-feather="alert-triangle" class="w-4 h-4 text-theme-1"></i>
                        </div>
                        <div>
                            <h4 class="font-medium">Manajemen Risiko</h4>
                            <p class="text-sm text-gray-600">Setelah dibuat, Anda dapat menambahkan data risiko untuk proyek ini</p>
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
    
    // Initialize date inputs
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('pro_tanggal_mulai').min = today;
    document.getElementById('pro_tanggal_selesai').min = today;
    
    // Set default dates
    if (!document.getElementById('pro_tanggal_mulai').value) {
        document.getElementById('pro_tanggal_mulai').value = today;
    }
    
    if (!document.getElementById('pro_tanggal_selesai').value) {
        const nextMonth = new Date();
        nextMonth.setMonth(nextMonth.getMonth() + 1);
        document.getElementById('pro_tanggal_selesai').value = nextMonth.toISOString().split('T')[0];
    }
    
    // Calculate initial duration
    calculateDuration();
    
    // Real-time preview updates
    document.getElementById('pro_nama').addEventListener('input', updatePreview);
    document.getElementById('pro_lokasi').addEventListener('input', updatePreview);
    document.getElementById('pro_deskripsi').addEventListener('input', updatePreview);
    document.getElementById('pro_tanggal_mulai').addEventListener('change', updatePreview);
    document.getElementById('pro_tanggal_selesai').addEventListener('change', updatePreview);
    document.getElementById('pro_status').addEventListener('change', updatePreview);
    
    // Form validation
    document.getElementById('project-form').addEventListener('submit', function(e) {
        const startDate = new Date(document.getElementById('pro_tanggal_mulai').value);
        const endDate = new Date(document.getElementById('pro_tanggal_selesai').value);
        
        if (endDate < startDate) {
            e.preventDefault();
            alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai');
            document.getElementById('pro_tanggal_selesai').focus();
        }
    });
});

function updatePreview() {
    // Update name
    const nama = document.getElementById('pro_nama').value || 'Nama Proyek';
    document.getElementById('preview-nama').textContent = nama;
    
    // Update location
    const lokasi = document.getElementById('pro_lokasi').value || 'Lokasi akan ditampilkan di sini';
    document.getElementById('preview-lokasi').textContent = lokasi;
    
    // Update description
    const deskripsi = document.getElementById('pro_deskripsi').value || 'Deskripsi akan ditampilkan di sini';
    document.getElementById('preview-deskripsi').textContent = deskripsi;
    
    // Update dates
    const mulai = document.getElementById('pro_tanggal_mulai').value;
    const selesai = document.getElementById('pro_tanggal_selesai').value;
    
    if (mulai) {
        document.getElementById('preview-mulai').textContent = formatDate(mulai);
    }
    if (selesai) {
        document.getElementById('preview-selesai').textContent = formatDate(selesai);
    }
    
    // Update status
    const status = document.getElementById('pro_status').value;
    const statusColors = {
        'Aktif': 'bg-green-100 text-green-800',
        'Selesai': 'bg-blue-100 text-blue-800',
        'Ditunda': 'bg-yellow-100 text-yellow-800',
        'Dibatalkan': 'bg-red-100 text-red-800'
    };
    
    const statusText = {
        'Aktif': 'Aktif',
        'Selesai': 'Selesai',
        'Ditunda': 'Ditunda',
        'Dibatalkan': 'Dibatalkan',
        '': 'Belum dipilih'
    };
    
    const previewStatus = document.getElementById('preview-status');
    previewStatus.innerHTML = `
        <span class="px-3 py-1 rounded-full text-sm font-medium ${statusColors[status] || 'bg-gray-100 text-gray-800'}">
            ${statusText[status]}
        </span>
    `;
    
    // Calculate and update duration
    calculateDuration();
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
            document.getElementById('preview-durasi').textContent = durationText;
        } else {
            document.getElementById('duration-display').textContent = 'Tanggal tidak valid';
            document.getElementById('preview-durasi').textContent = '-';
        }
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}
</script>
@endpush