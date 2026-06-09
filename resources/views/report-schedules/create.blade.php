@extends('layouts.master')

@section('title', 'Buat Jadwal Laporan - SIMR')

@section('page-title', 'Buat Jadwal Laporan Baru')

@section('breadcrumb')
@parent
<li class="breadcrumb-item"><a href="{{ route('report-schedules.index') }}">Jadwal Laporan</a></li>
<li class="breadcrumb-item active">Buat Baru</li>
@endsection

@section('page-action')
<a href="{{ route('report-schedules.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Form Card -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="calendar" class="w-5 h-5 mr-2 text-green-500"></i>
                    Form Buat Jadwal Laporan
                </h2>
            </div>
            
            <form action="{{ route('report-schedules.store') }}" method="POST" id="schedule-form">
                @csrf
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
                                       value="{{ old('schedule_name') }}"
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
                                    <option value="monitoring" {{ old('report_type') == 'monitoring' ? 'selected' : '' }}>Laporan Pemantauan</option>
                                    <option value="risk_profile" {{ old('report_type') == 'risk_profile' ? 'selected' : '' }}>Profil Risiko</option>
                                    <option value="executive_summary" {{ old('report_type') == 'executive_summary' ? 'selected' : '' }}>Ringkasan Eksekutif</option>
                                    <option value="mitigation_effectiveness" {{ old('report_type') == 'mitigation_effectiveness' ? 'selected' : '' }}>Efektivitas Mitigasi</option>
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
                                    <option value="daily" {{ old('frequency') == 'daily' ? 'selected' : '' }}>Harian</option>
                                    <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                    <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="quarterly" {{ old('frequency') == 'quarterly' ? 'selected' : '' }}>Triwulan</option>
                                    <option value="yearly" {{ old('frequency') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
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
                                       value="{{ old('generation_time') }}">
                                @error('generation_time')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                                <div class="text-gray-500 text-xs mt-1">
                                    Waktu untuk auto generate (format: HH:MM)
                                </div>
                            </div>
                            
                            <!-- Day of Month (for monthly frequency) -->
                            <div id="day-of-month-field" class="hidden">
                                <label for="day_of_month" class="form-label">Tanggal di Bulan</label>
                                <select id="day_of_month" 
                                        name="day_of_month" 
                                        class="form-select w-full @error('day_of_month') border-red-500 @enderror">
                                    <option value="">Pilih Tanggal</option>
                                    @for($i = 1; $i <= 31; $i++)
                                        <option value="{{ $i }}" {{ old('day_of_month') == $i ? 'selected' : '' }}>
                                            Tanggal {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('day_of_month')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Month of Year (for yearly frequency) -->
                            <div id="month-of-year-field" class="hidden">
                                <label for="month_of_year" class="form-label">Bulan di Tahun</label>
                                <select id="month_of_year" 
                                        name="month_of_year" 
                                        class="form-select w-full @error('month_of_year') border-red-500 @enderror">
                                    <option value="">Pilih Bulan</option>
                                    <option value="January" {{ old('month_of_year') == 'January' ? 'selected' : '' }}>Januari</option>
                                    <option value="February" {{ old('month_of_year') == 'February' ? 'selected' : '' }}>Februari</option>
                                    <option value="March" {{ old('month_of_year') == 'March' ? 'selected' : '' }}>Maret</option>
                                    <option value="April" {{ old('month_of_year') == 'April' ? 'selected' : '' }}>April</option>
                                    <option value="May" {{ old('month_of_year') == 'May' ? 'selected' : '' }}>Mei</option>
                                    <option value="June" {{ old('month_of_year') == 'June' ? 'selected' : '' }}>Juni</option>
                                    <option value="July" {{ old('month_of_year') == 'July' ? 'selected' : '' }}>Juli</option>
                                    <option value="August" {{ old('month_of_year') == 'August' ? 'selected' : '' }}>Agustus</option>
                                    <option value="September" {{ old('month_of_year') == 'September' ? 'selected' : '' }}>September</option>
                                    <option value="October" {{ old('month_of_year') == 'October' ? 'selected' : '' }}>Oktober</option>
                                    <option value="November" {{ old('month_of_year') == 'November' ? 'selected' : '' }}>November</option>
                                    <option value="December" {{ old('month_of_year') == 'December' ? 'selected' : '' }}>Desember</option>
                                </select>
                                @error('month_of_year')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Auto Generate -->
                            <div>
                                <div class="flex items-center mb-4">
                                    <input type="checkbox" 
                                           id="auto_generate" 
                                           name="auto_generate" 
                                           value="1"
                                           class="form-check-input"
                                           {{ old('auto_generate', true) ? 'checked' : '' }}>
                                    <label for="auto_generate" class="ml-2">
                                        Auto Generate
                                    </label>
                                </div>
                                
                                <!-- Auto Send Email -->
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="auto_send_email" 
                                           name="auto_send_email" 
                                           value="1"
                                           class="form-check-input"
                                           {{ old('auto_send_email') ? 'checked' : '' }}>
                                    <label for="auto_send_email" class="ml-2">
                                        Kirim Email Otomatis
                                    </label>
                                </div>
                                
                                <!-- Active Status -->
                                <div class="flex items-center mt-4">
                                    <input type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           class="form-check-input"
                                           {{ old('is_active', true) ? 'checked' : '' }}>
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
                                            {{ old('parameters.organization_id') == $org->organization_id ? 'selected' : '' }}>
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
                                            {{ old('parameters.project_id') == $project->pro_id ? 'selected' : '' }}>
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
                                    <option value="bulanan" {{ old('parameters.period') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="triwulan" {{ old('parameters.period') == 'triwulan' ? 'selected' : '' }}>Triwulan</option>
                                    <option value="tahunan" {{ old('parameters.period') == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                                </select>
                            </div>
                            
                            <!-- Custom Parameters -->
                            <div class="md:col-span-2">
                                <label for="custom_parameters" class="form-label">Parameter Kustom (JSON)</label>
                                <textarea id="custom_parameters" 
                                          name="parameters[custom]" 
                                          class="form-control w-full @error('parameters') border-red-500 @enderror" 
                                          rows="3"
                                          placeholder='{"key": "value"}'>{{ old('parameters.custom') }}</textarea>
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
                                @foreach($users as $user)
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               id="recipient_{{ $user->id }}" 
                                               name="recipients[]" 
                                               value="{{ $user->email }}"
                                               class="form-check-input"
                                               {{ in_array($user->email, old('recipients', [])) ? 'checked' : '' }}>
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
                            <textarea id="custom_recipients" 
                                      name="custom_recipients" 
                                      class="form-control w-full" 
                                      rows="2"
                                      placeholder="email1@domain.com, email2@domain.com">{{ old('custom_recipients') }}</textarea>
                            <div class="text-gray-500 text-xs mt-1">
                                Masukkan email tambahan dipisahkan dengan koma
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex justify-end mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('report-schedules.index') }}" 
                           class="btn btn-outline-secondary w-32 mr-3">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary w-32" id="submit-btn">
                            <i data-feather="save" class="w-4 h-4 mr-2"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Card -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="help-circle" class="w-5 h-5 mr-2 text-purple-500"></i>
                    Panduan Konfigurasi Jadwal
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i data-feather="calendar" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <h3 class="font-medium text-blue-700">Frekuensi</h3>
                        </div>
                        <p class="text-sm text-gray-600">
                            <strong>Harian:</strong> Tiap hari pada jam yang ditentukan<br>
                            <strong>Mingguan:</strong> Setiap hari Senin<br>
                            <strong>Bulanan:</strong> Tanggal tertentu setiap bulan<br>
                            <strong>Triwulan:</strong> Setiap 3 bulan sekali<br>
                            <strong>Tahunan:</strong> Bulan tertentu setiap tahun
                        </p>
                    </div>
                    
                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i data-feather="zap" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <h3 class="font-medium text-green-700">Auto Generate</h3>
                        </div>
                        <p class="text-sm text-gray-600">
                            Jika diaktifkan, sistem akan secara otomatis membuat laporan sesuai jadwal. Pastikan sistem berjalan 24/7 untuk fitur ini.
                        </p>
                    </div>
                    
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                <i data-feather="mail" class="w-5 h-5 text-purple-600"></i>
                            </div>
                            <h3 class="font-medium text-purple-700">Auto Email</h3>
                        </div>
                        <p class="text-sm text-gray-600">
                            Jika diaktifkan, laporan yang digenerate akan otomatis dikirim ke email penerima. Pastikan konfigurasi email server sudah benar.
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