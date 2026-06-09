@extends('layouts.master')

@section('title', 'Pencarian Lanjutan - SIMR')

@section('page-title', 'Pencarian Lanjutan')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('search.index') }}">Pencarian</a></li>
<li class="breadcrumb-item active">Lanjutan</li>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="search" class="w-5 h-5 mr-2 text-blue-500"></i>
                    Pencarian Lanjutan
                </h2>
            </div>
            <div class="p-5">
                <form action="{{ route('search.perform') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Keyword Search -->
                        <div class="md:col-span-2">
                            <label class="form-label">Kata Kunci</label>
                            <div class="relative">
                                <input type="text" 
                                       name="keyword" 
                                       class="form-control w-full pr-10" 
                                       placeholder="Masukkan kata kunci pencarian...">
                                <i data-feather="search" class="absolute right-3 top-3 text-gray-400"></i>
                            </div>
                            <div class="text-gray-500 text-xs mt-1">
                                Cari berdasarkan nama, deskripsi, atau kode
                            </div>
                        </div>

                        <!-- Organization Filter -->
                        <div>
                            <label class="form-label">Organisasi</label>
                            <select name="organization_id" class="form-select">
                                <option value="">Semua Organisasi</option>
                                @foreach($organizations as $org)
                                    <option value="{{ $org->organization_id }}">
                                        {{ $org->organization_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Project Filter -->
                        <div>
                            <label class="form-label">Proyek</label>
                            <select name="project_id" class="form-select">
                                <option value="">Semua Proyek</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->pro_id }}">
                                        {{ $project->pro_nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Risk Level Filter -->
                        <div>
                            <label class="form-label">Tingkat Risiko</label>
                            <select name="risk_level" class="form-select">
                                <option value="">Semua Tingkat</option>
                                <option value="sangat_rendah">Sangat Rendah</option>
                                <option value="rendah">Rendah</option>
                                <option value="sedang">Sedang</option>
                                <option value="tinggi">Tinggi</option>
                                <option value="sangat_tinggi">Sangat Tinggi</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="form-label">Status</label>
                            <select name="risk_status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="draft">Draft</option>
                                <option value="active">Aktif</option>
                                <option value="monitoring">Dalam Pemantauan</option>
                                <option value="mitigated">Telah Dimitigasi</option>
                                <option value="closed">Ditutup</option>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div>
                            <label class="form-label">Tanggal Dari</label>
                            <input type="date" name="date_from" class="form-control">
                        </div>

                        <div>
                            <label class="form-label">Tanggal Sampai</label>
                            <input type="date" name="date_to" class="form-control">
                        </div>

                        <!-- Sort Options -->
                        <div>
                            <label class="form-label">Urutkan Berdasarkan</label>
                            <select name="sort_by" class="form-select">
                                <option value="created_at">Tanggal Dibuat</option>
                                <option value="risk_score">Skor Risiko</option>
                                <option value="risk_name">Nama Risiko</option>
                                <option value="last_monitoring_date">Tanggal Monitoring Terakhir</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Urutan</label>
                            <select name="sort_dir" class="form-select">
                                <option value="desc">Menurun (Terbaru)</option>
                                <option value="asc">Menaik (Terlama)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('search.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="x" class="w-4 h-4 mr-2"></i> Batal
                        </a>
                        <button type="reset" class="btn btn-outline-warning">
                            <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="search" class="w-4 h-4 mr-2"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Card -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 text-purple-500"></i>
                    Panduan Pencarian Lanjutan
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i data-feather="filter" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <h3 class="font-medium text-blue-700">Filter Spesifik</h3>
                        </div>
                        <p class="text-sm text-gray-600">
                            Gunakan filter untuk mencari data dengan kriteria spesifik seperti organisasi, proyek, tingkat risiko, atau status.
                        </p>
                    </div>
                    
                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i data-feather="calendar" class="w-4 h-4 text-green-600"></i>
                            </div>
                            <h3 class="font-medium text-green-700">Rentang Tanggal</h3>
                        </div>
                        <p class="text-sm text-gray-600">
                            Tentukan rentang tanggal untuk menemukan data yang dibuat atau dimodifikasi dalam periode tertentu.
                        </p>
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
    
    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    const lastMonth = new Date();
    lastMonth.setMonth(lastMonth.getMonth() - 1);
    const lastMonthFormatted = lastMonth.toISOString().split('T')[0];
    
    document.querySelector('input[name="date_from"]').value = lastMonthFormatted;
    document.querySelector('input[name="date_to"]').value = today;
});
</script>
@endpush