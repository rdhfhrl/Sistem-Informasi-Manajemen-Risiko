@extends('layouts.master')

@section('title', 'Edit Jadwal Laporan - SIMR')

@section('page-title', 'Edit Jadwal Laporan')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('report-schedules.index') }}">Jadwal Laporan</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('page-action')
<a href="{{ route('report-schedules.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
</a>
<a href="{{ route('report-schedules.show', $reportSchedule->schedule_id) }}" class="btn btn-outline-primary shadow-md mr-2">
    <i data-feather="eye" class="w-4 h-4 mr-2"></i> Lihat Detail
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Form Card -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="edit" class="w-5 h-5 mr-2 text-blue-500"></i>
                    Form Edit Jadwal Laporan
                </h2>
                <div class="flex items-center space-x-2">
                    @if($reportSchedule->is_active)
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i data-feather="check-circle" class="w-3 h-3 inline mr-1"></i>
                            Aktif
                        </span>
                    @else
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i data-feather="x-circle" class="w-3 h-3 inline mr-1"></i>
                            Nonaktif
                        </span>
                    @endif
                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ID: {{ $reportSchedule->schedule_id }}
                    </span>
                </div>
            </div>
            
            <form action="{{ route('report-schedules.update', $reportSchedule->schedule_id) }}" method="POST" id="schedule-form">
                @csrf
                @method('PUT')
                <div class="p-5">
                    <!-- Basic Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            <i data-feather="info" class="w-5 h-5 inline mr-2 text-blue-500"></i>
                            Informasi Dasar
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Schedule Name -->
                            <div class="md:col-span-2">
                                <label for="schedule_name" class="form-label">Nama Jadwal <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="schedule_name" 
                                       name="schedule_name" 
                                       class="form-control w-full @error('schedule_name') border-red-500 @enderror" 
                                       value="{{ old('schedule_name', $reportSchedule->schedule_name) }}"
                                       placeholder="Contoh: Laporan Risiko Bulanan Departemen IT"
                                       required>
                                @error('schedule_name')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Report Type -->
                            <div>
                                <label for="report_type" class="form-label">Jenis Laporan <span class="text-red-500">*</span></label>
                                <select id="report_type" 
                                        name="report_type" 
                                        class="form-select w-full @error('report_type') border-red-500 @enderror" 
                                        required>
                                    <option value="">Pilih Jenis Laporan</option>
                                    <option value="monitoring" {{ old('report_type', $reportSchedule->report_type) == 'monitoring' ? 'selected' : '' }}>Laporan Pemantauan</option>
                                    <option value="risk_profile" {{ old('report_type', $reportSchedule->report_type) == 'risk_profile' ? 'selected' : '' }}>Profil Risiko</option>
                                    <option value="executive_summary" {{ old('report_type', $reportSchedule->report_type) == 'executive_summary' ? 'selected' : '' }}>Ringkasan Eksekutif</option>
                                    <option value="mitigation_effectiveness" {{ old('report_type', $reportSchedule->report_type) == 'mitigation_effectiveness' ? 'selected' : '' }}>Efektivitas Mitigasi</option>
                                </select>
                                @error('report_type')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Frequency -->
                            <div>
                                <label for="frequency" class="form-label">Frekuensi <span class="text-red-500">*</span></label>
                                <select id="frequency" 
                                        name="frequency" 
                                        class="form-select w-full @error('frequency') border-red-500 @enderror" 
                                        required>
                                    <option value="">Pilih Frekuensi</option>
                                    <option value="daily" {{ old('frequency', $reportSchedule->frequency) == 'daily' ? 'selected' : '' }}>Harian</option>
                                    <option value="weekly" {{ old('frequency', $reportSchedule->frequency) == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                    <option value="monthly" {{ old('frequency', $reportSchedule->frequency) == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="quarterly" {{ old('frequency', $reportSchedule->frequency) == 'quarterly' ? 'selected' : '' }}>Triwulan</option>
                                    <option value="yearly" {{ old('frequency', $reportSchedule->frequency) == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                                </select>
                                @error('frequency')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Schedule Details -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            <i data-feather="clock" class="w-5 h-5 inline mr-2 text-green-500"></i>
                            Detail Jadwal
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Generation Time -->
                            <div>
                                <label for="generation_time" class="form-label">Waktu Generate</label>
                                <input type="time" 
                                       id="generation_time" 
                                       name="generation_time" 
                                       class="form-control w-full @error('generation_time') border-red-500 @enderror" 
                                       value="{{ old('generation_time', $reportSchedule->generation_time ? \Carbon\Carbon::parse($reportSchedule->generation_time)->format('H:i') : '') }}">
                                @error('generation_time')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                                <div class="text-gray-500 text-xs mt-1">
                                    Waktu untuk auto generate (format: HH:MM)
                                </div>
                            </div>
                            
                            <!-- Day of Month (for monthly frequency) -->
                            <div id="day-of-month-field" class="{{ $reportSchedule->frequency == 'monthly' ? '' : 'hidden' }}">
                                <label for="day_of_month" class="form-label">Tanggal di Bulan</label>
                                <select id="day_of_month" 
                                        name="day_of_month" 
                                        class="form-select w-full @error('day_of_month') border-red-500 @enderror">
                                    <option value="">Pilih Tanggal</option>
                                    @for($i = 1; $i <= 31; $i++)
                                        <option value="{{ $i }}" {{ old('day_of_month', $reportSchedule->day_of_month) == $i ? 'selected' : '' }}>
                                            Tanggal {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('day_of_month')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Month of Year (for yearly frequency) -->
                            <div id="month-of-year-field" class="{{ $reportSchedule->frequency == 'yearly' ? '' : 'hidden' }}">
                                <label for="month_of_year" class="form-label">Bulan di Tahun</label>
                                <select id="month_of_year" 
                                        name="month_of_year" 
                                        class="form-select w-full @error('month_of_year') border-red-500 @enderror">
                                    <option value="">Pilih Bulan</option>
                                    <option value="January" {{ old('month_of_year', $reportSchedule->month_of_year) == 'January' ? 'selected' : '' }}>Januari</option>
                                    <option value="February" {{ old('month_of_year', $reportSchedule->month_of_year) == 'February' ? 'selected' : '' }}>Februari</option>
                                    <option value="March" {{ old('month_of_year', $reportSchedule->month_of_year) == 'March' ? 'selected' : '' }}>Maret</option>
                                    <option value="April" {{ old('month_of_year', $reportSchedule->month_of_year) == 'April' ? 'selected' : '' }}>April</option>
                                    <option value="May" {{ old('month_of_year', $reportSchedule->month_of_year) == 'May' ? 'selected' : '' }}>Mei</option>
                                    <option value="June" {{ old('month_of_year', $reportSchedule->month_of_year) == 'June' ? 'selected' : '' }}>Juni</option>
                                    <option value="July" {{ old('month_of_year', $reportSchedule->month_of_year) == 'July' ? 'selected' : '' }}>Juli</option>
                                    <option value="August" {{ old('month_of_year', $reportSchedule->month_of_year) == 'August' ? 'selected' : '' }}>Agustus</option>
                                    <option value="September" {{ old('month_of_year', $reportSchedule->month_of_year) == 'September' ? 'selected' : '' }}>September</option>
                                    <option value="October" {{ old('month_of_year', $reportSchedule->month_of_year) == 'October' ? 'selected' : '' }}>Oktober</option>
                                    <option value="November" {{ old('month_of_year', $reportSchedule->month_of_year) == 'November' ? 'selected' : '' }}>November</option>
                                    <option value="December" {{ old('month_of_year', $reportSchedule->month_of_year) == 'December' ? 'selected' : '' }}>Desember</option>
                                </select>
                                @error('month_of_year')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Auto Settings -->
                            <div>
                                <div class="flex items-center mb-4">
                                    <input type="checkbox" 
                                           id="auto_generate" 
                                           name="auto_generate" 
                                           value="1"
                                           class="form-check-input"
                                           {{ old('auto_generate', $reportSchedule->auto_generate) ? 'checked' : '' }}>
                                    <label for="auto_generate" class="ml-2">
                                        Auto Generate
                                    </label>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="auto_send_email" 
                                           name="auto_send_email" 
                                           value="1"
                                           class="form-check-input"
                                           {{ old('auto_send_email', $reportSchedule->auto_send_email) ? 'checked' : '' }}>
                                    <label for="auto_send_email" class="ml-2">
                                        Kirim Email Otomatis
                                    </label>
                                </div>
                                
                                <div class="flex items-center mt-4">
                                    <input type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           class="form-check-input"
                                           {{ old('is_active', $reportSchedule->is_active) ? 'checked' : '' }}>
                                    <label for="is_active" class="ml-2">
                                        Jadwal Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Parameters -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            <i data-feather="settings" class="w-5 h-5 inline mr-2 text-purple-500"></i>
                            Parameter Laporan
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Organization -->
                            <div>
                                <label for="parameters_organization_id" class="form-label">Organisasi (Opsional)</label>
                                <select id="parameters_organization_id" 
                                        name="parameters[organization_id]" 
                                        class="form-select w-full">
                                    <option value="">Semua Organisasi</option>
                                    @foreach($organizations as $org)
                                        <option value="{{ $org->organization_id }}" 
                                            {{ old('parameters.organization_id', $reportSchedule->parameters['organization_id'] ?? '') == $org->organization_id ? 'selected' : '' }}>
                                            {{ $org->organization_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Project -->
                            <div>
                                <label for="parameters_project_id" class="form-label">Proyek (Opsional)</label>
                                <select id="parameters_project_id" 
                                        name="parameters[project_id]" 
                                        class="form-select w-full">
                                    <option value="">Semua Proyek</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->pro_id }}" 
                                            {{ old('parameters.project_id', $reportSchedule->parameters['project_id'] ?? '') == $project->pro_id ? 'selected' : '' }}>
                                            {{ $project->pro_nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Period -->
                            <div>
                                <label for="parameters_period" class="form-label">Periode (Opsional)</label>
                                <select id="parameters_period" 
                                        name="parameters[period]" 
                                        class="form-select w-full">
                                    <option value="">Pilih Periode</option>
                                    <option value="bulanan" {{ old('parameters.period', $reportSchedule->parameters['period'] ?? '') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="triwulan" {{ old('parameters.period', $reportSchedule->parameters['period'] ?? '') == 'triwulan' ? 'selected' : '' }}>Triwulan</option>
                                    <option value="tahunan" {{ old('parameters.period', $reportSchedule->parameters['period'] ?? '') == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                                </select>
                            </div>
                            
                            <!-- Custom Parameters -->
                            <div class="md:col-span-2">
                                <label for="custom_parameters" class="form-label">Parameter Kustom (JSON)</label>
                                @php
                                    $customParams = isset($reportSchedule->parameters['custom']) 
                                        ? json_encode($reportSchedule->parameters['custom'], JSON_PRETTY_PRINT) 
                                        : '';
                                @endphp
                                <textarea id="custom_parameters" 
                                          name="parameters[custom]" 
                                          class="form-control w-full @error('parameters') border-red-500 @enderror" 
                                          rows="3"
                                          placeholder='{"key": "value"}'>{{ old('parameters.custom', $customParams) }}</textarea>
                                @error('parameters')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                                <div class="text-gray-500 text-xs mt-1">
                                    Parameter tambahan dalam format JSON
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recipients -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4 text-gray-700 border-b pb-2">
                            <i data-feather="users" class="w-5 h-5 inline mr-2 text-orange-500"></i>
                            Penerima Email
                        </h3>
                        
                        <div class="mb-4">
                            <label class="form-label">Pilih Penerima (Opsional)</label>
                            <div class="space-y-2 max-h-60 overflow-y-auto p-3 border rounded">
                                @php
                                    $currentRecipients = $reportSchedule->recipients ?? [];
                                    $selectedRecipients = old('recipients', $currentRecipients);
                                @endphp
                                @foreach($users as $user)
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               id="recipient_{{ $user->id }}" 
                                               name="recipients[]" 
                                               value="{{ $user->email }}"
                                               class="form-check-input"
                                               {{ in_array($user->email, $selectedRecipients) ? 'checked' : '' }}>
                                        <label for="recipient_{{ $user->id }}" class="ml-2">
                                            {{ $user->name }} ({{ $user->email }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('recipients')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="custom_recipients" class="form-label">Email Kustom (Opsional)</label>
                            @php
                                $customRecipients = collect($currentRecipients)
                                    ->filter(function($email) use ($users) {
                                        return !$users->pluck('email')->contains($email);
                                    })
                                    ->implode(', ');
                            @endphp
                            <textarea id="custom_recipients" 
                                      name="custom_recipients" 
                                      class="form-control w-full" 
                                      rows="2"
                                      placeholder="email1@domain.com, email2@domain.com">{{ old('custom_recipients', $customRecipients) }}</textarea>
                            <div class="text-gray-500 text-xs mt-1">
                                Masukkan email tambahan dipisahkan dengan koma
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            <div class="flex items-center">
                                <i data-feather="calendar" class="w-4 h-4 mr-2"></i>
                                <span>Terakhir diupdate: {{ $reportSchedule->updated_at->format('d M Y H:i') }}</span>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('report-schedules.index') }}" 
                               class="btn btn-outline-secondary w-32">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary w-32" id="submit-btn">
                                <i data-feather="save" class="w-4 h-4 mr-2"></i> Update
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Schedule Info Card -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="info" class="w-5 h-5 mr-2 text-green-500"></i>
                    Informasi Jadwal
                </h2>
                <div class="dropdown">
                    <button class="dropdown-toggle btn btn-outline-secondary" aria-expanded="false">
                        <i data-feather="more-vertical" class="w-4 h-4"></i>
                    </button>
                    <div class="dropdown-menu w-40">
                        <div class="dropdown-content">
                            <button type="button" class="dropdown-item generate-btn"
                                    data-schedule-id="{{ $reportSchedule->schedule_id }}"
                                    data-schedule-name="{{ $reportSchedule->schedule_name }}">
                                <i data-feather="play" class="w-4 h-4 mr-2"></i> Generate Sekarang
                            </button>
                            <div class="dropdown-divider"></div>
                            @if($reportSchedule->is_active)
                                <form action="{{ route('report-schedules.toggle-active', $reportSchedule->schedule_id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="dropdown-item text-yellow-600">
                                        <i data-feather="pause" class="w-4 h-4 mr-2"></i> Nonaktifkan
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('report-schedules.toggle-active', $reportSchedule->schedule_id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="dropdown-item text-green-600">
                                        <i data-feather="play" class="w-4 h-4 mr-2"></i> Aktifkan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Ringkasan Eksekusi</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">{{ $reportSchedule->reports()->where('status', 'success')->count() }}</div>
                                    <div class="text-xs text-gray-600">Berhasil</div>
                                </div>
                                <div class="text-center p-3 bg-red-50 rounded-lg">
                                    <div class="text-2xl font-bold text-red-600">{{ $reportSchedule->reports()->where('status', 'failed')->count() }}</div>
                                    <div class="text-xs text-gray-600">Gagal</div>
                                </div>
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ $reportSchedule->reports()->count() }}</div>
                                    <div class="text-xs text-gray-600">Total</div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Eksekusi Terakhir</h4>
                            @if($lastReport = $reportSchedule->reports()->latest()->first())
                                <div class="flex items-center text-gray-600">
                                    <i data-feather="clock" class="w-4 h-4 mr-2"></i>
                                    <span>{{ $lastReport->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <div class="mt-1 text-sm {{ $lastReport->status == 'success' ? 'text-green-600' : 'text-red-600' }}">
                                    <i data-feather="{{ $lastReport->status == 'success' ? 'check-circle' : 'x-circle' }}" class="w-4 h-4 mr-1 inline"></i>
                                    {{ $lastReport->status == 'success' ? 'Berhasil' : 'Gagal' }}
                                </div>
                            @else
                                <div class="text-gray-500 italic">Belum pernah dieksekusi</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Eksekusi Selanjutnya</h4>
                            @if($reportSchedule->is_active)
                                @if($reportSchedule->next_run_date)
                                    <div class="flex items-center text-gray-600">
                                        <i data-feather="calendar" class="w-4 h-4 mr-2"></i>
                                        <span>{{ $reportSchedule->next_run_date->format('d M Y H:i') }}</span>
                                    </div>
                                    <div class="mt-2 text-sm text-blue-600">
                                        <i data-feather="clock" class="w-4 h-4 mr-1 inline"></i>
                                        {{ $reportSchedule->next_run_date->diffForHumans() }}
                                    </div>
                                @else
                                    <div class="text-gray-500 italic">Tidak dijadwalkan</div>
                                @endif
                            @else
                                <div class="text-red-500 italic">Jadwal nonaktif</div>
                            @endif
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-gray-700 mb-2">Lihat Laporan</h4>
                            <a href="{{ route('reports.index', ['schedule_id' => $reportSchedule->schedule_id]) }}" 
                               class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                <i data-feather="file-text" class="w-4 h-4 mr-1"></i>
                                Lihat Semua Laporan ({{ $reportSchedule->reports()->count() }})
                            </a>
                        </div>
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
    
    const form = document.getElementById('schedule-form');
    const submitBtn = document.getElementById('submit-btn');
    const frequencySelect = document.getElementById('frequency');
    const dayOfMonthField = document.getElementById('day-of-month-field');
    const monthOfYearField = document.getElementById('month-of-year-field');
    
    // Show/hide fields based on frequency
    function toggleFrequencyFields() {
        const frequency = frequencySelect.value;
        
        // Hide all fields first
        dayOfMonthField.classList.add('hidden');
        monthOfYearField.classList.add('hidden');
        
        // Show appropriate fields
        if (frequency === 'monthly') {
            dayOfMonthField.classList.remove('hidden');
        } else if (frequency === 'yearly') {
            monthOfYearField.classList.remove('hidden');
        }
    }
    
    // Initial toggle
    toggleFrequencyFields();
    
    // Listen for frequency changes
    frequencySelect.addEventListener('change', toggleFrequencyFields);
    
    // Form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validate custom parameters JSON
            const customParams = document.getElementById('custom_parameters').value;
            if (customParams) {
                try {
                    JSON.parse(customParams);
                } catch (error) {
                    e.preventDefault();
                    alert('Parameter kustom harus dalam format JSON yang valid!');
                    return;
                }
            }
            
            // Show loading
            submitBtn.innerHTML = '<i data-feather="loader" class="w-4 h-4 animate-spin mr-2"></i> Processing...';
            submitBtn.disabled = true;
            feather.replace();
        });
    }
    
    // Generate modal functionality
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
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === generateModal) {
            generateModal.classList.remove('show');
            generateModal.style.display = 'none';
        }
    });
    
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