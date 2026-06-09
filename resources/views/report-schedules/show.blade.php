@extends('layouts.master')

@section('title', $reportSchedule->schedule_name . ' - SIMR')

@section('page-title', 'Detail Jadwal Laporan')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('report-schedules.index') }}">Jadwal Laporan</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('page-action')
<a href="{{ route('report-schedules.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
</a>
<div class="flex items-center space-x-2">
    <a href="{{ route('report-schedules.edit', $reportSchedule->schedule_id) }}" 
       class="btn btn-primary shadow-md">
        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit
    </a>
    
    <form action="{{ route('report-schedules.toggle-active', $reportSchedule->schedule_id) }}" 
          method="POST" 
          onsubmit="return confirm('Apakah Anda yakin ingin mengubah status jadwal ini?')">
        @csrf
        @method('PUT')
        <button type="submit" class="btn {{ $reportSchedule->is_active ? 'btn-warning' : 'btn-success' }} shadow-md">
            @if($reportSchedule->is_active)
                <i data-feather="pause-circle" class="w-4 h-4 mr-2"></i> Nonaktifkan
            @else
                <i data-feather="play-circle" class="w-4 h-4 mr-2"></i> Aktifkan
            @endif
        </button>
    </form>
    
    <form action="{{ route('report-schedules.destroy', $reportSchedule->schedule_id) }}" 
          method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger shadow-md">
            <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
        </button>
    </form>
</div>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Header Card -->
        <div class="intro-y box mb-6">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <div class="flex-1">
                    <h2 class="text-lg font-bold">
                        <i data-feather="calendar" class="w-5 h-5 inline mr-2 text-blue-500"></i>
                        {{ $reportSchedule->schedule_name }}
                    </h2>
                    <div class="text-gray-600 text-sm mt-1">
                        Jenis: <span class="font-medium">{{ $reportSchedule->report_type_label }}</span> • 
                        Frekuensi: <span class="font-medium">{{ $reportSchedule->frequency_label }}</span> • 
                        Status: <span class="font-medium">{{ $reportSchedule->status_label }}</span>
                    </div>
                </div>
                <div class="mt-3 sm:mt-0">
                    @if($reportSchedule->is_active)
                        <span class="px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i data-feather="check-circle" class="w-4 h-4 inline mr-2"></i>
                            Aktif
                        </span>
                    @else
                        <span class="px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <i data-feather="x-circle" class="w-4 h-4 inline mr-2"></i>
                            Nonaktif
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="p-5 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
                    <div>
                        <div class="text-sm text-gray-600 mb-2">FREKUENSI</div>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ $reportSchedule->frequency_label }}
                        </div>
                        <div class="text-gray-500 text-sm">
                            @if($reportSchedule->frequency == 'monthly' && $reportSchedule->day_of_month)
                                Tanggal {{ $reportSchedule->day_of_month }}
                            @elseif($reportSchedule->frequency == 'yearly' && $reportSchedule->month_of_year)
                                Bulan {{ $reportSchedule->month_of_year }}
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-600 mb-2">JALAN BERIKUTNYA</div>
                        <div class="text-2xl font-bold text-green-600">
                            {{ $reportSchedule->next_run_date?->format('d M Y') ?? '-' }}
                        </div>
                        <div class="text-gray-500 text-sm">
                            @if($reportSchedule->next_run_date)
                                {{ $reportSchedule->next_run_date->format('H:i') }} • 
                                {{ \Carbon\Carbon::parse($reportSchedule->next_run_date)->diffForHumans() }}
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-600 mb-2">LAPORAN TERBUAT</div>
                        <div class="text-3xl font-bold text-purple-600">
                            {{ $reportSchedule->reports->count() }}
                        </div>
                        <div class="text-gray-500 text-sm">
                            @if($reportSchedule->reports->count() > 0)
                                Terakhir: {{ $reportSchedule->reports->last()->created_at->format('d M Y') }}
                            @else
                                Belum ada laporan
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-600 mb-2">PENGATURAN</div>
                        <div class="flex flex-col space-y-1">
                            @if($reportSchedule->auto_generate)
                                <span class="inline-flex items-center text-green-600">
                                    <i data-feather="zap" class="w-4 h-4 mr-1"></i>
                                    Auto Generate
                                </span>
                            @endif
                            @if($reportSchedule->auto_send_email)
                                <span class="inline-flex items-center text-blue-600">
                                    <i data-feather="mail" class="w-4 h-4 mr-1"></i>
                                    Auto Email
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Creator Info -->
            <div class="p-5 border-b">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <div class="text-gray-600 text-sm mb-1">DIBUAT OLEH</div>
                        <div class="font-medium">{{ $reportSchedule->creator->name ?? 'System' }}</div>
                    </div>
                    
                    <div>
                        <div class="text-gray-600 text-sm mb-1">DIBUAT PADA</div>
                        <div class="font-medium">{{ $reportSchedule->created_at->format('d M Y H:i') }}</div>
                    </div>
                    
                    <div>
                        <div class="text-gray-600 text-sm mb-1">DIPERBARUI PADA</div>
                        <div class="font-medium">{{ $reportSchedule->updated_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Schedule Details -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base mr-auto">
                            <i data-feather="settings" class="w-5 h-5 mr-2 text-teal-500"></i>
                            Detail Jadwal
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="form-label">Jenis Laporan</label>
                                <div class="font-medium">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        @if($reportSchedule->report_type == 'monitoring') bg-blue-100 text-blue-800
                                        @elseif($reportSchedule->report_type == 'risk_profile') bg-green-100 text-green-800
                                        @elseif($reportSchedule->report_type == 'executive_summary') bg-purple-100 text-purple-800
                                        @elseif($reportSchedule->report_type == 'mitigation_effectiveness') bg-orange-100 text-orange-800
                                        @endif">
                                        {{ $reportSchedule->report_type_label }}
                                    </span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label">Frekuensi</label>
                                <div class="font-medium">{{ $reportSchedule->frequency_label }}</div>
                            </div>
                            
                            <div>
                                <label class="form-label">Waktu Generate</label>
                                <div class="font-medium">
                                    @if($reportSchedule->generation_time)
                                        {{ $reportSchedule->generation_time->format('H:i') }}
                                    @else
                                        <span class="text-gray-400">Tidak ditentukan</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label">Auto Generate</label>
                                <div class="font-medium">
                                    @if($reportSchedule->auto_generate)
                                        <span class="text-green-600">Ya</span>
                                    @else
                                        <span class="text-gray-600">Tidak</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label">Auto Send Email</label>
                                <div class="font-medium">
                                    @if($reportSchedule->auto_send_email)
                                        <span class="text-blue-600">Ya</span>
                                    @else
                                        <span class="text-gray-600">Tidak</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label">Status</label>
                                <div class="font-medium">
                                    @if($reportSchedule->is_active)
                                        <span class="text-green-600">Aktif</span>
                                    @else
                                        <span class="text-red-600">Nonaktif</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parameters -->
                @if(!empty($reportSchedule->parameters))
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="sliders" class="w-5 h-5 mr-2 text-blue-500"></i>
                            Parameter Laporan
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $parameters = $reportSchedule->getParametersArray();
                            @endphp
                            
                            @if(isset($parameters['organization_id']))
                                @php
                                    $org = \App\Models\Organization::find($parameters['organization_id']);
                                @endphp
                                <div>
                                    <label class="form-label">Organisasi</label>
                                    <div class="font-medium">{{ $org->organization_name ?? 'Semua Organisasi' }}</div>
                                </div>
                            @endif
                            
                            @if(isset($parameters['project_id']))
                                @php
                                    $project = \App\Models\Project::find($parameters['project_id']);
                                @endphp
                                <div>
                                    <label class="form-label">Proyek</label>
                                    <div class="font-medium">{{ $project->pro_nama ?? 'Semua Proyek' }}</div>
                                </div>
                            @endif
                            
                            @if(isset($parameters['period']))
                                <div>
                                    <label class="form-label">Periode</label>
                                    <div class="font-medium">
                                        @switch($parameters['period'])
                                            @case('bulanan') Bulanan @break
                                            @case('triwulan') Triwulan @break
                                            @case('tahunan') Tahunan @break
                                            @default {{ $parameters['period'] }}
                                        @endswitch
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($parameters['custom']))
                                <div class="md:col-span-2">
                                    <label class="form-label">Parameter Kustom</label>
                                    <div class="font-mono text-sm bg-gray-50 p-3 rounded border">
                                        {{ json_encode($parameters['custom'], JSON_PRETTY_PRINT) }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Recipients -->
                @if(!empty($reportSchedule->recipients))
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="users" class="w-5 h-5 mr-2 text-orange-500"></i>
                            Penerima Email
                        </h3>
                        <span class="text-gray-500 text-sm">
                            {{ count($reportSchedule->getRecipientsArray()) }} penerima
                        </span>
                    </div>
                    <div class="p-5">
                        <div class="space-y-2">
                            @foreach($reportSchedule->getRecipientsArray() as $recipient)
                                <div class="flex items-center p-2 bg-gray-50 rounded hover:bg-gray-100">
                                    <i data-feather="mail" class="w-4 h-4 text-gray-400 mr-2"></i>
                                    <span class="text-sm">{{ $recipient }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Actions Panel -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="zap" class="w-5 h-5 mr-2 text-yellow-500"></i>
                            Aksi
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="space-y-3">
                            @if($reportSchedule->canGenerate())
                            <button type="button" 
                                    class="btn btn-primary w-full generate-btn"
                                    data-schedule-id="{{ $reportSchedule->schedule_id }}"
                                    data-schedule-name="{{ $reportSchedule->schedule_name }}">
                                <i data-feather="play-circle" class="w-4 h-4 mr-2"></i> Generate Sekarang
                            </button>
                            @endif
                            
                            <a href="{{ route('report-schedules.edit', $reportSchedule->schedule_id) }}" 
                               class="btn btn-outline-blue w-full">
                                <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Jadwal
                            </a>
                            
                            <form action="{{ route('report-schedules.toggle-active', $reportSchedule->schedule_id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin mengubah status jadwal ini?')">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn {{ $reportSchedule->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} w-full">
                                    @if($reportSchedule->is_active)
                                        <i data-feather="pause-circle" class="w-4 h-4 mr-2"></i> Nonaktifkan
                                    @else
                                        <i data-feather="play-circle" class="w-4 h-4 mr-2"></i> Aktifkan
                                    @endif
                                </button>
                            </form>
                            
                            <a href="{{ route('report-schedules.index') }}" 
                               class="btn btn-outline-secondary w-full">
                                <i data-feather="list" class="w-4 h-4 mr-2"></i> Daftar Jadwal
                            </a>
                            
                            <form action="{{ route('report-schedules.destroy', $reportSchedule->schedule_id) }}" 
                                  method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-full">
                                    <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus Jadwal
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Report History -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="file-text" class="w-5 h-5 mr-2 text-green-500"></i>
                            Histori Laporan
                        </h3>
                        <span class="text-gray-500 text-sm">
                            {{ $reportSchedule->reports->count() }} laporan
                        </span>
                    </div>
                    <div class="p-5">
                        @if($reportSchedule->reports->count() > 0)
                            <div class="space-y-3 max-h-80 overflow-y-auto">
                                @foreach($reportSchedule->reports->take(10) as $report)
                                    <div class="flex items-center p-3 bg-white rounded-lg border hover:bg-gray-50">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                            <i data-feather="file-text" class="w-5 h-5 text-blue-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium">{{ Str::limit($report->title, 40) }}</div>
                                            <div class="text-sm text-gray-600">
                                                {{ $report->created_at->format('d M Y H:i') }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Status: 
                                                @if($report->status == 'published')
                                                    <span class="text-green-600">Published</span>
                                                @elseif($report->status == 'generated')
                                                    <span class="text-blue-600">Generated</span>
                                                @else
                                                    <span class="text-gray-600">{{ $report->status }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <a href="{{ route('reports.show', $report->report_id) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i data-feather="eye" class="w-3 h-3"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if($reportSchedule->reports->count() > 10)
                                <div class="text-center mt-4">
                                    <a href="{{ route('reports.index', ['schedule_id' => $reportSchedule->schedule_id]) }}" 
                                       class="text-blue-500 text-sm hover:underline">
                                        Lihat semua laporan →
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i data-feather="file-text" class="w-12 h-12 mx-auto mb-3"></i>
                                <p>Belum ada laporan yang dibuat</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Next Run Info -->
                <div class="intro-y box">
                    <div class="flex items-center p-5 border-b border-gray-200">
                        <h3 class="font-medium text-base">
                            <i data-feather="clock" class="w-5 h-5 mr-2 text-purple-500"></i>
                            Jadwal Berikutnya
                        </h3>
                    </div>
                    <div class="p-5">
                        @if($reportSchedule->is_active)
                            @if($reportSchedule->next_run_date)
                                <div class="text-center mb-4">
                                    <div class="text-3xl font-bold text-purple-600">
                                        {{ $reportSchedule->next_run_date->format('d M Y') }}
                                    </div>
                                    <div class="text-gray-600">
                                        {{ $reportSchedule->next_run_date->format('H:i') }}
                                    </div>
                                    <div class="text-sm text-gray-500 mt-2">
                                        {{ \Carbon\Carbon::parse($reportSchedule->next_run_date)->diffForHumans() }}
                                    </div>
                                </div>
                                
                                <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                                    <div class="text-sm text-purple-700">
                                        @if($reportSchedule->auto_generate)
                                            <div class="flex items-center mb-2">
                                                <i data-feather="zap" class="w-4 h-4 mr-2"></i>
                                                <span>Laporan akan digenerate otomatis</span>
                                            </div>
                                        @endif
                                        
                                        @if($reportSchedule->auto_send_email)
                                            <div class="flex items-center">
                                                <i data-feather="mail" class="w-4 h-4 mr-2"></i>
                                                <span>Email akan dikirim ke {{ count($reportSchedule->getRecipientsArray()) }} penerima</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-6 text-gray-500">
                                <i data-feather="pause-circle" class="w-12 h-12 mx-auto mb-3"></i>
                                <p>Jadwal tidak aktif</p>
                                <p class="text-sm mt-2">Aktifkan jadwal untuk menjalankan laporan otomatis</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Modal -->
<div class="modal" id="generateModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Generate Laporan dari Jadwal</h2>
                <button data-dismiss="modal" class="btn btn-outline-secondary hidden sm:flex">
                    <i data-feather="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="generate-form" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label">Nama Jadwal</label>
                        <div class="font-medium" id="modal-schedule-name"></div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="report_date" class="form-label">Tanggal Laporan</label>
                        <input type="date" 
                               id="report_date" 
                               name="report_date" 
                               class="form-control w-full" 
                               value="{{ date('Y-m-d') }}">
                        <div class="text-gray-500 text-xs mt-1">
                            Kosongkan untuk menggunakan tanggal saat ini
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="flex items-center">
                            <i data-feather="info" class="w-5 h-5 text-blue-600 mr-2"></i>
                            <div class="text-sm text-blue-700">
                                Laporan akan dibuat berdasarkan parameter yang telah ditentukan di jadwal.
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" id="schedule_id" name="schedule_id">
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-outline-secondary w-20">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary w-20">
                        Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
    
    // Generate modal
    const generateButtons = document.querySelectorAll('.generate-btn');
    const generateModal = document.getElementById('generateModal');
    const generateForm = document.getElementById('generate-form');
    
    generateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const scheduleId = this.dataset.scheduleId;
            const scheduleName = this.dataset.scheduleName;
            
            // Set modal values
            document.getElementById('modal-schedule-name').textContent = scheduleName;
            document.getElementById('schedule_id').value = scheduleId;
            
            // Set form action
            generateForm.action = `/reports/generate-from-schedule/${scheduleId}`;
            
            // Show modal
            generateModal.classList.add('show');
            generateModal.style.display = 'block';
        });
    });
    
    // Close modal
    const closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            modal.classList.remove('show');
            modal.style.display = 'none';
        });
    });
    
    // Form submission
    generateForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const scheduleId = document.getElementById('schedule_id').value;
        
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast('success', data.message);
                
                // Close modal
                generateModal.classList.remove('show');
                generateModal.style.display = 'none';
                
                // Redirect to the new report
                if (data.report_id) {
                    window.location.href = `/reports/${data.report_id}`;
                } else {
                    // Reload page after 1 second
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Terjadi kesalahan saat menggenerate laporan');
        });
    });
    
    // Toast notification function
    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `toast toast--${type} show`;
        toast.innerHTML = `
            <div class="toast__icon">
                <i data-feather="${type === 'success' ? 'check-circle' : 'alert-circle'}"></i>
            </div>
            <div class="toast__content">${message}</div>
            <div class="toast__close">
                <i data-feather="x"></i>
            </div>
        `;
        
        document.body.appendChild(toast);
        feather.replace();
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
    
    // Close toast on click
    document.addEventListener('click', function(e) {
        if (e.target.closest('.toast__close')) {
            e.target.closest('.toast').remove();
        }
    });
});
</script>
@endpush