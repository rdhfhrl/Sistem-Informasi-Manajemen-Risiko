@extends('layouts.master')

@section('title', 'Data Identifikasi Risiko - SIMR')

@section('page-title', 'Data Identifikasi Risiko')

@section('page-action')
<a href="{{ route('risks.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Risiko
</a>
<a href="{{ route('risks.index') }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="plus-circle" class="w-4 h-4 mr-2"></i> Pilih Risiko
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                                <i data-feather="search" class="w-6 h-6 text-red-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $identifications->total() }}</div>
                            <div class="text-base text-gray-600 mt-1">Total Identifikasi</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-orange-100">
                                <i data-feather="alert-octagon" class="w-6 h-6 text-orange-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            @php
                                $reputasiCount = $identifications->where('loss_type', 'Reputasi')->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $reputasiCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Kerugian Reputasi</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100">
                                <i data-feather="clipboard" class="w-6 h-6 text-blue-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            @php
                                $sopCount = $identifications->where('violation_type', 'SOP')->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $sopCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Pelanggaran SOP</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-teal-100">
                                <i data-feather="users" class="w-6 h-6 text-teal-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            @php
                                $manusiaCount = $identifications->where('failure_type', 'Manusia')->count();
                            @endphp
                            <div class="text-3xl font-bold leading-8">{{ $manusiaCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Kegagalan Manusia</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Daftar Identifikasi Risiko
                    <span class="text-gray-500 text-sm ml-2">({{ $identifications->total() }} data)</span>
                </h2>
            </div>
            <div class="p-5">
                @if($identifications->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">RISIKO</th>
                                    <th class="whitespace-nowrap">JENIS KERUGIAN</th>
                                    <th class="whitespace-nowrap">JENIS PELANGGARAN</th>
                                    <th class="whitespace-nowrap">JENIS KEGAGALAN</th>
                                    <th class="whitespace-nowrap">JENIS KESALAHAN</th>
                                    <th class="whitespace-nowrap">PROYEK</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($identifications as $identification)
                                    @php
                                        $risk = $identification->risk;
                                    @endphp
                                    <tr class="intro-x hover:bg-gray-50">
                                        <td>
                                            @if($risk)
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                                        <i data-feather="alert-triangle" class="w-5 h-5 text-red-600"></i>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('risks.show', $risk->risk_id) }}" 
                                                           class="font-medium hover:text-red-600">
                                                            {{ $risk->risk_code }}
                                                        </a>
                                                        <div class="text-gray-500 text-xs mt-0.5">
                                                            {{ Str::limit($risk->risk_description, 30) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400">Risiko tidak ditemukan</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($identification->loss_type)
                                                <span class="px-2 py-1 rounded text-xs font-medium 
                                                    @if($identification->loss_type == 'Reputasi') bg-red-100 text-red-800
                                                    @elseif($identification->loss_type == 'Operasional') bg-orange-100 text-orange-800
                                                    @elseif($identification->loss_type == 'Kepatuhan') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $identification->loss_type }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($identification->violation_type)
                                                <span class="px-2 py-1 rounded text-xs font-medium 
                                                    @if($identification->violation_type == 'Hukum') bg-red-100 text-red-800
                                                    @elseif($identification->violation_type == 'SOP') bg-blue-100 text-blue-800
                                                    @elseif($identification->violation_type == 'Kontrak') bg-purple-100 text-purple-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $identification->violation_type }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($identification->failure_type)
                                                <span class="px-2 py-1 rounded text-xs font-medium 
                                                    @if($identification->failure_type == 'Manusia') bg-teal-100 text-teal-800
                                                    @elseif($identification->failure_type == 'Proses') bg-indigo-100 text-indigo-800
                                                    @elseif($identification->failure_type == 'Sistem') bg-pink-100 text-pink-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $identification->failure_type }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($identification->error_type)
                                                <span class="px-2 py-1 rounded text-xs font-medium 
                                                    @if($identification->error_type == 'Human Error') bg-amber-100 text-amber-800
                                                    @elseif($identification->error_type == 'Technical Error') bg-rose-100 text-rose-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $identification->error_type }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($risk && $risk->project)
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full bg-theme-1/10 flex items-center justify-center mr-2">
                                                        <i data-feather="folder" class="w-3 h-3 text-theme-1"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-medium text-sm truncate">
                                                            {{ Str::limit($risk->project->pro_nama, 15) }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ \Carbon\Carbon::parse($risk->project->pro_tanggal_mulai)->format('d/m/Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        
                                        <td class="table-report__action w-56">
                                            <div class="flex justify-center items-center">
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risks.show', $risk ? $risk->risk_id : '#') }}">
                                                    <i data-feather="eye" class="w-4 h-4 mr-1"></i> Detail
                                                </a>
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-identifications.edit', $risk ? $risk->risk_id : '#') }}">
                                                    <i data-feather="edit" class="w-4 h-4 mr-1"></i> Edit
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($identifications->hasPages())
                    <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $identifications->firstItem() }} - {{ $identifications->lastItem() }} dari {{ $identifications->total() }} identifikasi
                        </div>
                        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                            {{ $identifications->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="search" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        @if(request()->hasAny(['search', 'loss_type', 'violation_type']))
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak ditemukan</h3>
                            <p class="text-gray-500 mb-6">Tidak ada identifikasi risiko yang sesuai dengan filter yang dipilih</p>
                            <a href="{{ route('risk-identifications.index') }}" 
                               class="btn btn-secondary mr-2">
                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Reset Filter
                            </a>
                        @else
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada data identifikasi</h3>
                            <p class="text-gray-500 mb-6">Identifikasi risiko akan muncul setelah dilakukan identifikasi pada masing-masing risiko</p>
                        @endif
                        <a href="{{ route('risks.index') }}" 
                           class="btn btn-primary">
                            <i data-feather="alert-triangle" class="w-4 h-4 mr-2"></i> Lihat Daftar Risiko
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics Charts -->
        @if($identifications->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="pie-chart" class="w-5 h-5 mr-2"></i> Statistik Identifikasi
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Loss Type Chart -->
                    <div>
                        <h4 class="font-medium mb-3 flex items-center">
                            <i data-feather="alert-triangle" class="w-4 h-4 mr-2 text-red-600"></i>
                            Jenis Kerugian
                        </h4>
                        <div class="space-y-3">
                            @php
                                $lossTypes = $identifications->groupBy('loss_type');
                                $totalLoss = $identifications->whereNotNull('loss_type')->count();
                            @endphp
                            @foreach(['Reputasi', 'Operasional', 'Kepatuhan', 'Lainnya'] as $type)
                                @php
                                    $count = $lossTypes->get($type) ? $lossTypes->get($type)->count() : 0;
                                    $percentage = $totalLoss > 0 ? round(($count / $totalLoss) * 100, 1) : 0;
                                    $color = match($type) {
                                        'Reputasi' => 'bg-red-100 text-red-800 border-red-200',
                                        'Operasional' => 'bg-orange-100 text-orange-800 border-orange-200',
                                        'Kepatuhan' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        default => 'bg-gray-100 text-gray-800 border-gray-200'
                                    };
                                    $barColor = match($type) {
                                        'Reputasi' => 'bg-red-500',
                                        'Operasional' => 'bg-orange-500',
                                        'Kepatuhan' => 'bg-yellow-500',
                                        default => 'bg-gray-500'
                                    };
                                @endphp
                                <div class="p-3 rounded-lg border {{ $color }}">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium">{{ $type }}</span>
                                        <span class="text-sm font-bold">{{ $count }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 rounded-full h-2 mr-3">
                                            <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium whitespace-nowrap">{{ $percentage }}%</span>
                                    </div>
                                </div>
                            @endforeach
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Total Identifikasi</span>
                                    <span class="text-lg font-bold text-blue-600">{{ $totalLoss }}</span>
                                </div>
                                <div class="text-xs text-gray-600 mt-1">
                                    Data berdasarkan jenis kerugian yang teridentifikasi
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Violation Type Chart -->
                    <div>
                        <h4 class="font-medium mb-3 flex items-center">
                            <i data-feather="file-text" class="w-4 h-4 mr-2 text-blue-600"></i>
                            Jenis Pelanggaran
                        </h4>
                        <div class="space-y-3">
                            @php
                                $violationTypes = $identifications->groupBy('violation_type');
                                $totalViolation = $identifications->whereNotNull('violation_type')->count();
                            @endphp
                            @foreach(['Hukum', 'SOP', 'Kontrak', 'Lainnya'] as $type)
                                @php
                                    $count = $violationTypes->get($type) ? $violationTypes->get($type)->count() : 0;
                                    $percentage = $totalViolation > 0 ? round(($count / $totalViolation) * 100, 1) : 0;
                                    $color = match($type) {
                                        'Hukum' => 'bg-red-100 text-red-800 border-red-200',
                                        'SOP' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'Kontrak' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        default => 'bg-gray-100 text-gray-800 border-gray-200'
                                    };
                                    $barColor = match($type) {
                                        'Hukum' => 'bg-red-500',
                                        'SOP' => 'bg-blue-500',
                                        'Kontrak' => 'bg-purple-500',
                                        default => 'bg-gray-500'
                                    };
                                @endphp
                                <div class="p-3 rounded-lg border {{ $color }}">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium">{{ $type }}</span>
                                        <span class="text-sm font-bold">{{ $count }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 rounded-full h-2 mr-3">
                                            <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium whitespace-nowrap">{{ $percentage }}%</span>
                                    </div>
                                </div>
                            @endforeach
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Total Identifikasi</span>
                                    <span class="text-lg font-bold text-blue-600">{{ $totalViolation }}</span>
                                </div>
                                <div class="text-xs text-gray-600 mt-1">
                                    Data berdasarkan jenis pelanggaran yang teridentifikasi
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Failure & Error Types Summary -->
                    <div>
                        <h4 class="font-medium mb-3 flex items-center">
                            <i data-feather="alert-circle" class="w-4 h-4 mr-2 text-amber-600"></i>
                            Ringkasan Jenis
                        </h4>
                        <div class="space-y-4">
                            <!-- Failure Types -->
                            <div>
                                <h5 class="font-medium text-sm mb-2 text-gray-700">Jenis Kegagalan</h5>
                                <div class="space-y-2">
                                    @php
                                        $failureTypes = $identifications->groupBy('failure_type');
                                        $totalFailure = $identifications->whereNotNull('failure_type')->count();
                                    @endphp
                                    @foreach(['Manusia', 'Proses', 'Sistem', 'Lainnya'] as $type)
                                        @php
                                            $count = $failureTypes->get($type) ? $failureTypes->get($type)->count() : 0;
                                            $percentage = $totalFailure > 0 ? round(($count / $totalFailure) * 100, 1) : 0;
                                        @endphp
                                        @if($count > 0)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                            <div class="flex items-center">
                                                <i data-feather="{{ match($type) {
                                                    'Manusia' => 'users',
                                                    'Proses' => 'repeat',
                                                    'Sistem' => 'cpu',
                                                    default => 'more-horizontal'
                                                } }}" class="w-3 h-3 mr-2 text-gray-600"></i>
                                                <span class="text-sm">{{ $type }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-xs font-medium text-gray-600 mr-2">{{ $percentage }}%</span>
                                                <span class="text-sm font-bold">{{ $count }}</span>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Error Types -->
                            <div>
                                <h5 class="font-medium text-sm mb-2 text-gray-700">Jenis Kesalahan</h5>
                                <div class="space-y-2">
                                    @php
                                        $errorTypes = $identifications->groupBy('error_type');
                                        $totalError = $identifications->whereNotNull('error_type')->count();
                                    @endphp
                                    @foreach(['Human Error', 'Technical Error', 'Lainnya'] as $type)
                                        @php
                                            $count = $errorTypes->get($type) ? $errorTypes->get($type)->count() : 0;
                                            $percentage = $totalError > 0 ? round(($count / $totalError) * 100, 1) : 0;
                                        @endphp
                                        @if($count > 0)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                            <div class="flex items-center">
                                                <i data-feather="{{ match($type) {
                                                    'Human Error' => 'user-x',
                                                    'Technical Error' => 'tool',
                                                    default => 'more-horizontal'
                                                } }}" class="w-3 h-3 mr-2 text-gray-600"></i>
                                                <span class="text-sm">{{ $type }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-xs font-medium text-gray-600 mr-2">{{ $percentage }}%</span>
                                                <span class="text-sm font-bold">{{ $count }}</span>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Overall Statistics -->
                            <div class="p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600 mb-1">
                                        {{ $identifications->count() }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-700">Total Identifikasi</div>
                                    <div class="text-xs text-gray-500 mt-2">
                                        {{ $identifications->whereNotNull('loss_type')->count() }} Kerugian •
                                        {{ $identifications->whereNotNull('violation_type')->count() }} Pelanggaran
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
    
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        });
    }, 5000);
    
    // Quick search on enter
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
});
</script>
@endpush