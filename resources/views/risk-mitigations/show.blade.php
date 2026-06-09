@extends('layouts.master')

@section('title', 'Detail Rencana Mitigasi - SIMR')

@section('page-title', 'Detail Rencana Mitigasi')

@section('page-action')
<a href="{{ route('risk-mitigations.by-risk', $risk->risk_id) }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar
</a>
<a href="{{ route('risk-mitigations.edit', [$risk->risk_id, $mitigation->risk_mitigation_id]) }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Mitigasi
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 lg:col-span-8">
        <!-- Mitigation Details Card -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="shield" class="w-5 h-5 mr-2"></i>
                    Detail Rencana Mitigasi
                </h2>
                @php
                    $isOverdue = \Carbon\Carbon::parse($mitigation->deadline)->lt(now()) && 
                                !in_array($mitigation->status, ['selesai', 'dibatalkan']);
                @endphp
                @if($isOverdue)
                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                    <i data-feather="alert-triangle" class="w-3 h-3 mr-1 inline"></i> Terlambat
                </span>
                @endif
            </div>
            <div class="p-5">
                <!-- Risk Info -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Informasi Risiko</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-600 text-sm mb-1">Kode Risiko</div>
                            <div class="font-medium text-lg">{{ $risk->risk_code }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-600 text-sm mb-1">Deskripsi</div>
                            <div class="font-medium text-lg">{{ $risk->risk_description }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-600 text-sm mb-1">Skor Risiko</div>
                            <div class="font-medium text-lg 
                                @if($risk->risk_level == 'sangat_tinggi') text-red-600
                                @elseif($risk->risk_level == 'tinggi') text-orange-600
                                @elseif($risk->risk_level == 'sedang') text-yellow-600
                                @elseif($risk->risk_level == 'rendah') text-blue-600
                                @else text-green-600
                                @endif">
                                {{ $risk->risk_score ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mitigation Summary -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Ringkasan Mitigasi</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-6 rounded-lg text-center">
                            <div class="text-blue-600 text-sm mb-2">Status</div>
                            <div class="flex flex-col items-center">
                                <span class="px-4 py-2 rounded-full text-lg font-bold mb-2
                                    @if($mitigation->status == 'selesai') bg-green-100 text-green-800
                                    @elseif($mitigation->status == 'dalam proses') bg-blue-100 text-blue-800
                                    @elseif($mitigation->status == 'belum dimulai') bg-gray-100 text-gray-800
                                    @elseif($mitigation->status == 'ditunda') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @switch($mitigation->status)
                                        @case('belum dimulai') Belum Dimulai @break
                                        @case('dalam proses') Dalam Proses @break
                                        @case('selesai') Selesai @break
                                        @case('ditunda') Ditunda @break
                                        @case('dibatalkan') Dibatalkan @break
                                    @endswitch
                                </span>
                                <div class="text-blue-500 text-sm">
                                    @php
                                        $statusTime = match($mitigation->status) {
                                            'belum dimulai' => 'Menunggu dimulai',
                                            'dalam proses' => 'Sedang berjalan',
                                            'selesai' => 'Telah selesai',
                                            'ditunda' => 'Ditunda sementara',
                                            'dibatalkan' => 'Tidak dilanjutkan'
                                        };
                                    @endphp
                                    {{ $statusTime }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 p-6 rounded-lg text-center">
                            <div class="text-yellow-600 text-sm mb-2">Deadline</div>
                            <div class="text-2xl font-bold {{ $isOverdue ? 'text-red-600' : 'text-yellow-700' }}">
                                {{ $mitigation->deadline->format('d M Y') }}
                            </div>
                            <div class="text-sm mt-2">
                                @if($isOverdue)
                                    <span class="text-red-600">
                                        <i data-feather="alert-triangle" class="w-4 h-4 inline"></i> 
                                        Terlambat {{ \Carbon\Carbon::parse($mitigation->deadline)->diffInDays(now()) }} hari
                                    </span>
                                @elseif($mitigation->status == 'selesai')
                                    <span class="text-green-600">
                                        <i data-feather="check-circle" class="w-4 h-4 inline"></i> 
                                        Selesai tepat waktu
                                    </span>
                                @else
                                    <span class="text-yellow-600">
                                        <i data-feather="clock" class="w-4 h-4 inline"></i> 
                                        {{ \Carbon\Carbon::parse($mitigation->deadline)->diffInDays(now(), false) }} hari lagi
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-6 rounded-lg text-center">
                            <div class="text-green-600 text-sm mb-2">Penanggung Jawab</div>
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-2">
                                    <i data-feather="user" class="w-6 h-6 text-green-600"></i>
                                </div>
                                <div class="text-lg font-bold text-green-700">{{ $mitigation->responsible_party }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mitigation Plan Details -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Rencana Mitigasi</h4>
                    <div class="bg-white border rounded-lg p-6">
                        <div class="prose max-w-none">
                            {!! nl2br(e($mitigation->mitigation_plan)) !!}
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-700 mb-4">Informasi Tambahan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($mitigation->success_criteria)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i data-feather="target" class="w-4 h-4 mr-2 text-gray-500"></i>
                                <div class="text-gray-600">Kriteria Keberhasilan</div>
                            </div>
                            <div class="font-medium">{{ $mitigation->success_criteria }}</div>
                        </div>
                        @endif
                        
                        @if($mitigation->resources)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i data-feather="tool" class="w-4 h-4 mr-2 text-gray-500"></i>
                                <div class="text-gray-600">Sumber Daya</div>
                            </div>
                            <div class="font-medium">{{ $mitigation->resources }}</div>
                        </div>
                        @endif
                        
                        @if($mitigation->budget)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i data-feather="dollar-sign" class="w-4 h-4 mr-2 text-gray-500"></i>
                                <div class="text-gray-600">Anggaran</div>
                            </div>
                            <div class="font-medium">Rp {{ number_format($mitigation->budget, 0, ',', '.') }}</div>
                        </div>
                        @endif
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i data-feather="calendar" class="w-4 h-4 mr-2 text-gray-500"></i>
                                <div class="text-gray-600">Dibuat Pada</div>
                            </div>
                            <div class="font-medium">{{ $mitigation->created_at->format('d F Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end pt-5 border-t">
                    <a href="{{ route('risk-mitigations.by-risk', $risk->risk_id) }}" class="btn btn-outline-secondary mr-3">
                        <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                    </a>
                    <a href="{{ route('risk-mitigations.edit', [$risk->risk_id, $mitigation->risk_mitigation_id]) }}" class="btn btn-primary mr-3">
                        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Mitigasi
                    </a>
                    <form action="{{ route('risk-mitigations.destroy', [$risk->risk_id, $mitigation->risk_mitigation_id]) }}" 
                          method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rencana mitigasi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i data-feather="trash-2" class="w-4 h-4 mr-2"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-span-12 lg:col-span-4">
        <!-- Status Timeline -->
        <div class="intro-y box mb-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="activity" class="w-5 h-5 mr-2"></i>
                    Timeline Status
                </h2>
            </div>
            <div class="p-5">
                <div class="relative">
                    <!-- Timeline line -->
                    <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    
                    <!-- Timeline items -->
                    <div class="space-y-6">
                        <!-- Created -->
                        <div class="relative flex items-start">
                            <div class="absolute left-4 w-3 h-3 bg-blue-500 rounded-full -translate-x-1/2"></div>
                            <div class="ml-10">
                                <div class="text-sm font-medium text-gray-800">Dibuat</div>
                                <div class="text-xs text-gray-500">{{ $mitigation->created_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                        
                        <!-- Status changes -->
                        @if($mitigation->status != 'belum dimulai')
                        <div class="relative flex items-start">
                            <div class="absolute left-4 w-3 h-3 {{ $mitigation->status == 'selesai' ? 'bg-green-500' : 'bg-yellow-500' }} rounded-full -translate-x-1/2"></div>
                            <div class="ml-10">
                                <div class="text-sm font-medium text-gray-800">
                                    @if($mitigation->status == 'dalam proses')
                                        Dimulai
                                    @elseif($mitigation->status == 'selesai')
                                        Diselesaikan
                                    @else
                                        Di{{ $mitigation->status }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    Status: {{ ucwords($mitigation->status) }}
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Deadline -->
                        <div class="relative flex items-start">
                            <div class="absolute left-4 w-3 h-3 {{ $isOverdue ? 'bg-red-500' : 'bg-gray-500' }} rounded-full -translate-x-1/2"></div>
                            <div class="ml-10">
                                <div class="text-sm font-medium {{ $isOverdue ? 'text-red-600' : 'text-gray-800' }}">Deadline</div>
                                <div class="text-xs {{ $isOverdue ? 'text-red-500' : 'text-gray-500' }}">
                                    {{ $mitigation->deadline->format('d M Y') }}
                                    @if($isOverdue)
                                        (Terlambat)
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Last Updated -->
                        @if($mitigation->updated_at != $mitigation->created_at)
                        <div class="relative flex items-start">
                            <div class="absolute left-4 w-3 h-3 bg-gray-400 rounded-full -translate-x-1/2"></div>
                            <div class="ml-10">
                                <div class="text-sm font-medium text-gray-800">Terakhir Diupdate</div>
                                <div class="text-xs text-gray-500">{{ $mitigation->updated_at->format('d M Y H:i') }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Guide -->
        <div class="intro-y box">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="info" class="w-5 h-5 mr-2"></i>
                    Panduan Status Mitigasi
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    @php
                        $statusLevels = [
                            [
                                'level' => 'Selesai',
                                'color' => 'bg-green-100 text-green-800',
                                'description' => 'Mitigasi telah berhasil dilaksanakan dan selesai',
                                'icon' => 'check-circle'
                            ],
                            [
                                'level' => 'Dalam Proses',
                                'color' => 'bg-blue-100 text-blue-800',
                                'description' => 'Sedang dalam tahap pelaksanaan',
                                'icon' => 'activity'
                            ],
                            [
                                'level' => 'Belum Dimulai',
                                'color' => 'bg-gray-100 text-gray-800',
                                'description' => 'Rencana sudah dibuat, belum dilaksanakan',
                                'icon' => 'clock'
                            ],
                            [
                                'level' => 'Ditunda',
                                'color' => 'bg-yellow-100 text-yellow-800',
                                'description' => 'Ditunda sementara karena alasan tertentu',
                                'icon' => 'pause-circle'
                            ],
                            [
                                'level' => 'Dibatalkan',
                                'color' => 'bg-red-100 text-red-800',
                                'description' => 'Tidak akan dilanjutkan',
                                'icon' => 'x-circle'
                            ]
                        ];
                    @endphp
                    
                    @foreach($statusLevels as $statusInfo)
                        <div class="flex items-start p-3 rounded-lg {{ str_contains(ucwords($mitigation->status), $statusInfo['level']) ? 'border-2 border-theme-1' : '' }}">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 {{ explode(' ', $statusInfo['color'])[0] }}">
                                <i data-feather="{{ $statusInfo['icon'] }}" class="w-4 h-4 {{ explode(' ', $statusInfo['color'])[1] }}"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium {{ $statusInfo['color'] }} inline-block px-2 py-1 rounded-full text-xs mb-1">
                                    {{ $statusInfo['level'] }}
                                </div>
                                <div class="text-xs text-gray-600">
                                    {{ $statusInfo['description'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Current Status Indicator -->
                @php
                    $currentStatusIndex = match($mitigation->status) {
                        'selesai' => 0,
                        'dalam proses' => 1,
                        'belum dimulai' => 2,
                        'ditunda' => 3,
                        'dibatalkan' => 4,
                        default => 2
                    };
                @endphp
                
                <div class="mt-4 p-3 bg-theme-1/10 rounded-lg">
                    <div class="flex items-center">
                        <i data-feather="target" class="w-5 h-5 text-theme-1 mr-2"></i>
                        <div class="flex-1">
                            <div class="font-medium text-theme-1">Status Saat Ini:</div>
                            <div class="text-sm">{{ $statusLevels[$currentStatusIndex]['description'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="zap" class="w-5 h-5 mr-2"></i>
                    Aksi Cepat
                </h2>
            </div>
            <div class="p-5">
                <div class="space-y-2">
                    @if($mitigation->status != 'selesai')
                    <button onclick="updateStatus('dalam proses')" 
                            class="w-full flex items-center justify-center p-3 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors duration-300 {{ $mitigation->status == 'dalam proses' ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $mitigation->status == 'dalam proses' ? 'disabled' : '' }}>
                        <i data-feather="play-circle" class="w-4 h-4 mr-2"></i>
                        Mulai Mitigasi
                    </button>
                    
                    <button onclick="updateStatus('selesai')" 
                            class="w-full flex items-center justify-center p-3 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition-colors duration-300 {{ $mitigation->status == 'selesai' ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $mitigation->status == 'selesai' ? 'disabled' : '' }}>
                        <i data-feather="check-circle" class="w-4 h-4 mr-2"></i>
                        Tandai Selesai
                    </button>
                    @endif
                    
                    <button onclick="updateStatus('ditunda')" 
                            class="w-full flex items-center justify-center p-3 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 rounded-lg transition-colors duration-300 {{ $mitigation->status == 'ditunda' ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $mitigation->status == 'ditunda' ? 'disabled' : '' }}>
                        <i data-feather="pause-circle" class="w-4 h-4 mr-2"></i>
                        Tunda Mitigasi
                    </button>
                    
                    <a href="{{ route('risk-mitigations.create', $risk->risk_id) }}" 
                       class="block w-full text-center p-3 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg transition-colors duration-300">
                        <i data-feather="plus-circle" class="w-4 h-4 mr-2 inline"></i>
                        Buat Mitigasi Baru
                    </a>
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
    
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        });
    }, 5000);
});

function updateStatus(newStatus) {
    if (confirm(`Apakah Anda yakin ingin mengubah status menjadi "${newStatus}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/risk-mitigations/${newStatus === 'selesai' ? 'complete' : 'status'}/{{ $mitigation->risk_mitigation_id }}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = newStatus;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        
        form.appendChild(csrfToken);
        form.appendChild(statusInput);
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush