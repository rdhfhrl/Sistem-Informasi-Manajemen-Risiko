@extends('layouts.master')

@section('title', 'Buat Laporan Baru - SIMR')

@section('page-title', 'Buat Laporan Baru')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Laporan</a></li>
<li class="breadcrumb-item active">Buat Baru</li>
@endsection

@section('page-action')
<a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Report Type Selection -->
        @if(!$presetType)
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="clipboard" class="w-5 h-5 mr-2 text-blue-500"></i>
                    Pilih Jenis Laporan
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('reports.create', ['type' => 'monitoring']) }}" 
                       class="flex flex-col items-center p-6 bg-blue-50 rounded-lg border border-blue-100 hover:bg-blue-100 transition-colors text-center">
                        <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                            <i data-feather="activity" class="w-8 h-8 text-blue-600"></i>
                        </div>
                        <h3 class="font-bold text-lg text-blue-700 mb-2">Laporan Pemantauan</h3>
                        <p class="text-sm text-gray-600">Pemantauan risiko harian/mingguan dengan indikator performa</p>
                    </a>
                    
                    <a href="{{ route('reports.create', ['type' => 'risk_profile']) }}" 
                       class="flex flex-col items-center p-6 bg-green-50 rounded-lg border border-green-100 hover:bg-green-100 transition-colors text-center">
                        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mb-4">
                            <i data-feather="pie-chart" class="w-8 h-8 text-green-600"></i>
                        </div>
                        <h3 class="font-bold text-lg text-green-700 mb-2">Profil Risiko</h3>
                        <p class="text-sm text-gray-600">Analisis profil risiko organisasi dan distribusi level</p>
                    </a>
                    
                    <a href="{{ route('reports.create', ['type' => 'executive_summary']) }}" 
                       class="flex flex-col items-center p-6 bg-purple-50 rounded-lg border border-purple-100 hover:bg-purple-100 transition-colors text-center">
                        <div class="w-16 h-16 rounded-full bg-purple-100 flex items-center justify-center mb-4">
                            <i data-feather="briefcase" class="w-8 h-8 text-purple-600"></i>
                        </div>
                        <h3 class="font-bold text-lg text-purple-700 mb-2">Ringkasan Eksekutif</h3>
                        <p class="text-sm text-gray-600">Ringkasan untuk manajemen senior dengan metrik kunci</p>
                    </a>
                    
                    <a href="{{ route('reports.create', ['type' => 'mitigation_effectiveness']) }}" 
                       class="flex flex-col items-center p-6 bg-orange-50 rounded-lg border border-orange-100 hover:bg-orange-100 transition-colors text-center">
                        <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center mb-4">
                            <i data-feather="trending-up" class="w-8 h-8 text-orange-600"></i>
                        </div>
                        <h3 class="font-bold text-lg text-orange-700 mb-2">Efektivitas Mitigasi</h3>
                        <p class="text-sm text-gray-600">Evaluasi efektivitas tindakan mitigasi yang diimplementasikan</p>
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Report Form -->
        @if($presetType)
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="file-plus" class="w-5 h-5 mr-2 text-green-500"></i>
                    Form Buat Laporan {{ ucfirst(str_replace('_', ' ', $presetType)) }}
                </h2>
            </div>
            
            <form action="{{ route('reports.store') }}" method="POST" id="report-form">
                @csrf
                <div class="p-5">
                    <!-- Basic Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            <i data-feather="info" class="w-5 h-5 inline mr-2 text-blue-500"></i>
                            Informasi Dasar
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Report Type (Hidden) -->
                            <input type="hidden" name="report_type" value="{{ $presetType }}">
                            
                            <!-- Title -->
                            <div class="md:col-span-2">
                                <label for="title" class="form-label">Judul Laporan <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="title" 
                                       name="title" 
                                       class="form-control w-full @error('title') border-red-500 @enderror" 
                                       value="{{ old('title', $presetParams['title'] ?? '') }}"
                                       placeholder="Contoh: Laporan Pemantauan Risiko Bulan Januari 2024"
                                       required>
                                @error('title')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Schedule Selection -->
                            <div>
                                <label for="schedule_id" class="form-label">Berdasarkan Schedule (Opsional)</label>
                                <select id="schedule_id" 
                                        name="schedule_id" 
                                        class="form-select w-full @error('schedule_id') border-red-500 @enderror">
                                    <option value="">Pilih Schedule (Opsional)</option>
                                    @foreach($schedules as $schedule)
                                        <option value="{{ $schedule->schedule_id }}" 
                                            {{ old('schedule_id', $presetParams['schedule_id'] ?? '') == $schedule->schedule_id ? 'selected' : '' }}>
                                            {{ $schedule->schedule_name }} ({{ $schedule->frequency }} - {{ $schedule->report_type }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('schedule_id')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                                <div class="text-gray-500 text-xs mt-1">
                                    Jika dipilih, parameter akan diambil dari schedule
                                </div>
                            </div>
                            
                            <!-- Period -->
                            <div>
                                <label for="period" class="form-label">Periode Laporan <span class="text-red-500">*</span></label>
                                <select id="period" 
                                        name="period" 
                                        class="form-select w-full @error('period') border-red-500 @enderror" 
                                        required>
                                    <option value="">Pilih Periode</option>
                                    <option value="bulanan" {{ old('period', $presetParams['period'] ?? '') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="triwulan" {{ old('period', $presetParams['period'] ?? '') == 'triwulan' ? 'selected' : '' }}>Triwulan</option>
                                    <option value="tahunan" {{ old('period', $presetParams['period'] ?? '') == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                                    <option value="custom" {{ old('period', $presetParams['period'] ?? '') == 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                                @error('period')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Report Date -->
                            <div>
                                <label for="report_date" class="form-label">Tanggal Laporan <span class="text-red-500">*</span></label>
                                <input type="date" 
                                       id="report_date" 
                                       name="report_date" 
                                       class="form-control w-full @error('report_date') border-red-500 @enderror" 
                                       value="{{ old('report_date', date('Y-m-d')) }}"
                                       required>
                                @error('report_date')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Scope Selection -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            <i data-feather="target" class="w-5 h-5 inline mr-2 text-green-500"></i>
                            Scope Laporan
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Organization -->
                            <div>
                                <label for="organization_id" class="form-label">Organisasi (Opsional)</label>
                                <select id="organization_id" 
                                        name="organization_id" 
                                        class="form-select w-full @error('organization_id') border-red-500 @enderror">
                                    <option value="">Semua Organisasi</option>
                                    @foreach($organizations as $org)
                                        <option value="{{ $org->organization_id }}" 
                                            {{ old('organization_id', $presetParams['organization_id'] ?? '') == $org->organization_id ? 'selected' : '' }}>
                                            {{ $org->organization_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('organization_id')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Project -->
                            <div>
                                <label for="project_id" class="form-label">Proyek (Opsional)</label>
                                <select id="project_id" 
                                        name="project_id" 
                                        class="form-select w-full @error('project_id') border-red-500 @enderror">
                                    <option value="">Semua Proyek</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->pro_id }}" 
                                            {{ old('project_id', $presetParams['project_id'] ?? '') == $project->pro_id ? 'selected' : '' }}>
                                            {{ $project->pro_nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Specific Risk (for certain report types) -->
                            @if(in_array($presetType, ['monitoring', 'custom']))
                            <div>
                                <label for="risk_id" class="form-label">Risiko Spesifik (Opsional)</label>
                                <select id="risk_id" 
                                        name="risk_id" 
                                        class="form-select w-full @error('risk_id') border-red-500 @enderror">
                                    <option value="">Semua Risiko</option>
                                    @foreach($risks as $risk)
                                        <option value="{{ $risk->risk_id }}" 
                                            {{ old('risk_id', $presetParams['risk_id'] ?? '') == $risk->risk_id ? 'selected' : '' }}>
                                            {{ $risk->risk_code }} - {{ Str::limit($risk->risk_description, 40) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('risk_id')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Report Parameters -->
                    @if($presetType == 'custom')
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            <i data-feather="settings" class="w-5 h-5 inline mr-2 text-purple-500"></i>
                            Parameter Kustom
                        </h3>
                        
                        <div>
                            <label for="custom_data" class="form-label">Data Kustom (JSON)</label>
                            <textarea id="custom_data" 
                                      name="custom_data" 
                                      class="form-control w-full @error('custom_data') border-red-500 @enderror" 
                                      rows="6"
                                      placeholder='{"key": "value", "metrics": [], "sections": []}'>{{ old('custom_data', $presetParams['custom_data'] ?? '') }}</textarea>
                            @error('custom_data')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                            <div class="text-gray-500 text-xs mt-1">
                                Masukkan data dalam format JSON yang valid
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Additional Settings -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            <i data-feather="sliders" class="w-5 h-5 inline mr-2 text-orange-500"></i>
                            Pengaturan Tambahan
                        </h3>
                        
                        <div class="space-y-4">
                            <!-- Notes -->
                            <div>
                                <label for="notes" class="form-label">Catatan (Opsional)</label>
                                <textarea id="notes" 
                                          name="notes" 
                                          class="form-control w-full @error('notes') border-red-500 @enderror" 
                                          rows="3"
                                          placeholder="Tambahan catatan untuk laporan...">{{ old('notes', $presetParams['notes'] ?? '') }}</textarea>
                                @error('notes')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Status -->
                            <div>
                                <label for="status" class="form-label">Status Awal</label>
                                <select id="status" 
                                        name="status" 
                                        class="form-select w-full @error('status') border-red-500 @enderror">
                                    <option value="draft" {{ old('status', $presetParams['status'] ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="generated" {{ old('status', $presetParams['status'] ?? '') == 'generated' ? 'selected' : '' }}>Generated</option>
                                </select>
                                @error('status')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Generate PDF Option -->
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="generate_pdf" 
                                       name="generate_pdf" 
                                       value="1"
                                       class="form-check-input"
                                       {{ old('generate_pdf') ? 'checked' : '' }}>
                                <label for="generate_pdf" class="ml-2">
                                    Generate PDF secara otomatis
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('reports.index') }}" 
                           class="btn btn-outline-secondary w-32 mr-3">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary w-32" id="submit-btn">
                            <i data-feather="save" class="w-4 h-4 mr-2"></i> Buat Laporan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Report Type Guide -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 text-purple-500"></i>
                    Panduan {{ ucfirst(str_replace('_', ' ', $presetType)) }}
                </h2>
            </div>
            <div class="p-5">
                @if($presetType == 'monitoring')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-lg text-blue-700 mb-3">Apa yang akan dimuat:</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-green-500 mr-2 mt-0.5"></i>
                                <span>Distribusi level risiko</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-green-500 mr-2 mt-0.5"></i>
                                <span>Aktivitas pemantauan terbaru</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-green-500 mr-2 mt-0.5"></i>
                                <span>Status implementasi mitigasi</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-green-500 mr-2 mt-0.5"></i>
                                <span>Analisis tren risiko</span>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-medium text-lg text-blue-700 mb-3">Direkomendasikan untuk:</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-start">
                                <i data-feather="users" class="w-4 h-4 text-blue-500 mr-2 mt-0.5"></i>
                                <span>Tim operasional</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="calendar" class="w-4 h-4 text-blue-500 mr-2 mt-0.5"></i>
                                <span>Review bulanan/triwulan</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="bar-chart" class="w-4 h-4 text-blue-500 mr-2 mt-0.5"></i>
                                <span>Meeting status update</span>
                            </li>
                        </ul>
                    </div>
                </div>
                @elseif($presetType == 'risk_profile')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-lg text-green-700 mb-3">Apa yang akan dimuat:</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-green-500 mr-2 mt-0.5"></i>
                                <span>Matriks risiko (likelihood vs impact)</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-green-500 mr-2 mt-0.5"></i>
                                <span>Distribusi per kategori/organisasi</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-green-500 mr-2 mt-0.5"></i>
                                <span>Top 10 risiko terbesar</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-green-500 mr-2 mt-0.5"></i>
                                <span>Rekomendasi perbaikan</span>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-medium text-lg text-green-700 mb-3">Direkomendasikan untuk:</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-start">
                                <i data-feather="users" class="w-4 h-4 text-green-500 mr-2 mt-0.5"></i>
                                <span>Management review</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="target" class="w-4 h-4 text-green-500 mr-2 mt-0.5"></i>
                                <span>Perencanaan strategis</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="pie-chart" class="w-4 h-4 text-green-500 mr-2 mt-0.5"></i>
                                <span>Analisis portfolio risiko</span>
                            </li>
                        </ul>
                    </div>
                </div>
                @elseif($presetType == 'executive_summary')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-lg text-purple-700 mb-3">Apa yang akan dimuat:</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-purple-500 mr-2 mt-0.5"></i>
                                <span>Overview eksekutif</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-purple-500 mr-2 mt-0.5"></i>
                                <span>Metrik kunci performa</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-purple-500 mr-2 mt-0.5"></i>
                                <span>Risiko kritis & status</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-purple-500 mr-2 mt-0.5"></i>
                                <span>Rekomendasi strategis</span>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-medium text-lg text-purple-700 mb-3">Direkomendasikan untuk:</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-start">
                                <i data-feather="briefcase" class="w-4 h-4 text-purple-500 mr-2 mt-0.5"></i>
                                <span>Executive management</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="award" class="w-4 h-4 text-purple-500 mr-2 mt-0.5"></i>
                                <span>Board meetings</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="trending-up" class="w-4 h-4 text-purple-500 mr-2 mt-0.5"></i>
                                <span>Strategic decision making</span>
                            </li>
                        </ul>
                    </div>
                </div>
                @elseif($presetType == 'mitigation_effectiveness')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-lg text-orange-700 mb-3">Apa yang akan dimuat:</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-orange-500 mr-2 mt-0.5"></i>
                                <span>Rate penyelesaian mitigasi</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-orange-500 mr-2 mt-0.5"></i>
                                <span>Analisis on-time delivery</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-orange-500 mr-2 mt-0.5"></i>
                                <span>Budget vs actual cost</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check-circle" class="w-4 h-4 text-orange-500 mr-2 mt-0.5"></i>
                                <span>Effectiveness rating</span>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-medium text-lg text-orange-700 mb-3">Direkomendasikan untuk:</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-start">
                                <i data-feather="dollar-sign" class="w-4 h-4 text-orange-500 mr-2 mt-0.5"></i>
                                <span>Budget review meetings</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="check-square" class="w-4 h-4 text-orange-500 mr-2 mt-0.5"></i>
                                <span>Project performance review</span>
                            </li>
                            <li class="flex items-start">
                                <i data-feather="activity" class="w-4 h-4 text-orange-500 mr-2 mt-0.5"></i>
                                <span>Continuous improvement</span>
                            </li>
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    const form = document.getElementById('report-form');
    const submitBtn = document.getElementById('submit-btn');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validate custom data if custom report type
            const reportType = document.querySelector('input[name="report_type"]').value;
            if (reportType === 'custom') {
                const customData = document.getElementById('custom_data').value;
                if (customData) {
                    try {
                        JSON.parse(customData);
                    } catch (error) {
                        e.preventDefault();
                        alert('Data kustom harus dalam format JSON yang valid!');
                        return;
                    }
                }
            }
            
            // Show loading
            submitBtn.innerHTML = '<i data-feather="loader" class="w-4 h-4 animate-spin mr-2"></i> Processing...';
            submitBtn.disabled = true;
            feather.replace();
        });
    }
    
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        });
    }, 5000);
});
</script>
@endpush