@extends('layouts.master')

@section('title', 'Semua Mitigasi Risiko - SIMR')

@section('page-title', 'Semua Mitigasi Risiko')

@section('page-action')
<a href="{{ route('risks.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Daftar Risiko
</a>
<a href="{{ route('risk-mitigations.index') }}" class="btn btn-primary shadow-md mr-2">
    <i data-feather="filter" class="w-4 h-4 mr-2"></i> Mitigasi Per Risiko
</a>
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12">
        <!-- Filter Section -->
        <div class="intro-y box p-5 mb-6">
            <form method="GET" action="{{ route('risk-mitigations.all') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="form-label">Cari</label>
                    <div class="relative">
                        <input type="text" id="search" name="search" 
                               class="form-control w-full pl-10" 
                               placeholder="Cari berdasarkan rencana mitigasi, penanggung jawab..."
                               value="{{ request('search') }}">
                        <div class="absolute left-3 top-2.5">
                            <i data-feather="search" class="w-5 h-5 text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <div class="w-full md:w-48">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="belum dimulai" {{ request('status') == 'belum dimulai' ? 'selected' : '' }}>Belum Dimulai</option>
                        <option value="dalam proses" {{ request('status') == 'dalam proses' ? 'selected' : '' }}>Dalam Proses</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditunda" {{ request('status') == 'ditunda' ? 'selected' : '' }}>Ditunda</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                
                <div class="w-full md:w-48">
                    <label for="date_range" class="form-label">Rentang Deadline</label>
                    <select id="date_range" name="date_range" class="form-select">
                        <option value="">Semua Tanggal</option>
                        <option value="overdue" {{ request('date_range') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                        <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="next_month" {{ request('date_range') == 'next_month' ? 'selected' : '' }}>Bulan Depan</option>
                    </select>
                </div>
                
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn btn-primary w-24">
                        <i data-feather="filter" class="w-4 h-4 mr-2"></i> Filter
                    </button>
                    <a href="{{ route('risk-mitigations.all') }}" class="btn btn-secondary w-24">
                        <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            @php
                $totalMitigations = \App\Models\RiskMitigation::count();
                $completedCount = \App\Models\RiskMitigation::where('status', 'selesai')->count();
                $inProgressCount = \App\Models\RiskMitigation::where('status', 'dalam proses')->count();
                $overdueCount = \App\Models\RiskMitigation::where('deadline', '<', now())
                    ->whereNotIn('status', ['selesai', 'dibatalkan'])
                    ->count();
            @endphp
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-indigo-100">
                                <i data-feather="shield" class="w-6 h-6 text-indigo-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $totalMitigations }}</div>
                            <div class="text-base text-gray-600 mt-1">Total Mitigasi</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100">
                                <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $completedCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Selesai</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-yellow-100">
                                <i data-feather="activity" class="w-6 h-6 text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $inProgressCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Dalam Proses</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                <div class="report-box zoom-in h-40">
                    <div class="box p-5 h-full flex flex-col">
                        <div class="flex">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                                <i data-feather="alert-triangle" class="w-6 h-6 text-red-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="text-3xl font-bold leading-8">{{ $overdueCount }}</div>
                            <div class="text-base text-gray-600 mt-1">Terlambat</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    Daftar Semua Rencana Mitigasi
                    <span class="text-gray-500 text-sm ml-2">({{ $mitigations->total() }} data)</span>
                </h2>
                
                <!-- Export Options -->
                <div class="flex items-center space-x-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown">
                            <i data-feather="download" class="w-4 h-4 mr-2"></i> Export
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">
                                <i data-feather="file-text" class="w-4 h-4 mr-2"></i> Excel
                            </a>
                            <a class="dropdown-item" href="#">
                                <i data-feather="file" class="w-4 h-4 mr-2"></i> PDF
                            </a>
                            <a class="dropdown-item" href="#">
                                <i data-feather="printer" class="w-4 h-4 mr-2"></i> Print
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-5">
                @if($mitigations->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="table table-report -mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">RISIKO</th>
                                    <th class="whitespace-nowrap">RENCANA MITIGASI</th>
                                    <th class="whitespace-nowrap">PENANGGUNG JAWAB</th>
                                    <th class="whitespace-nowrap">DEADLINE</th>
                                    <th class="whitespace-nowrap">STATUS</th>
                                    <th class="whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mitigations as $mitigation)
                                    @php
                                        $isOverdue = \Carbon\Carbon::parse($mitigation->deadline)->lt(now()) && 
                                                    !in_array($mitigation->status, ['selesai', 'dibatalkan']);
                                        $daysRemaining = \Carbon\Carbon::parse($mitigation->deadline)->diffInDays(now(), false);
                                    @endphp
                                    <tr class="intro-x hover:bg-gray-50">
                                        <td>
                                            @if($mitigation->risk)
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                                        <i data-feather="alert-triangle" class="w-5 h-5 text-red-600"></i>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('risks.show', $mitigation->risk->risk_id) }}" 
                                                           class="font-medium hover:text-red-600">
                                                            {{ $mitigation->risk->risk_code }}
                                                        </a>
                                                        <div class="text-gray-500 text-xs mt-0.5">
                                                            {{ Str::limit($mitigation->risk->risk_description, 30) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400">Risiko tidak ditemukan</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <div class="max-w-xs">
                                                <div class="font-medium text-gray-800">
                                                    {{ Str::limit($mitigation->mitigation_plan, 40) }}
                                                </div>
                                                <div class="text-gray-500 text-xs mt-1">
                                                    {{ Str::limit($mitigation->mitigation_plan, 60) }}
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                                    <i data-feather="user" class="w-4 h-4 text-blue-600"></i>
                                                </div>
                                                <div class="font-medium">{{ $mitigation->responsible_party }}</div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full {{ $isOverdue ? 'bg-red-100' : 'bg-gray-100' }} flex items-center justify-center mr-3">
                                                    <i data-feather="calendar" class="w-5 h-5 {{ $isOverdue ? 'text-red-600' : 'text-gray-600' }}"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium {{ $isOverdue ? 'text-red-600' : 'text-gray-800' }}">
                                                        {{ $mitigation->deadline->format('d M Y') }}
                                                    </div>
                                                    <div class="text-xs {{ $isOverdue ? 'text-red-500' : 'text-gray-500' }}">
                                                        @if($isOverdue)
                                                            Terlambat {{ abs($daysRemaining) }} hari
                                                        @elseif($daysRemaining < 0)
                                                            {{ abs($daysRemaining) }} hari lagi
                                                        @else
                                                            Hari ini
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                                @if($mitigation->status == 'selesai') bg-green-100 text-green-800
                                                @elseif($mitigation->status == 'dalam proses') bg-blue-100 text-blue-800
                                                @elseif($mitigation->status == 'belum dimulai') bg-gray-100 text-gray-800
                                                @elseif($mitigation->status == 'ditunda') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                @switch($mitigation->status)
                                                    @case('belum dimulai') BD @break
                                                    @case('dalam proses') DP @break
                                                    @case('selesai') S @break
                                                    @case('ditunda') DT @break
                                                    @case('dibatalkan') DB @break
                                                @endswitch
                                            </span>
                                        </td>
                                        
                                        <td class="table-report__action w-32">
                                            <div class="flex justify-center items-center">
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-mitigations.show', [$mitigation->risk_mitigation_risk_id, $mitigation->risk_mitigation_id]) }}"
                                                   data-toggle="tooltip" title="Detail Mitigasi">
                                                    <i data-feather="eye" class="w-4 h-4"></i>
                                                </a>
                                                <a class="flex items-center mr-3" 
                                                   href="{{ route('risk-mitigations.edit', [$mitigation->risk_mitigation_risk_id, $mitigation->risk_mitigation_id]) }}"
                                                   data-toggle="tooltip" title="Edit Mitigasi">
                                                    <i data-feather="edit" class="w-4 h-4"></i>
                                                </a>
                                                <a class="flex items-center" 
                                                   href="{{ route('risk-mitigations.by-risk', $mitigation->risk_mitigation_risk_id) }}"
                                                   data-toggle="tooltip" title="Lihat Risiko">
                                                    <i data-feather="alert-triangle" class="w-4 h-4"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($mitigations->hasPages())
                    <div class="flex flex-col sm:flex-row items-center p-5 border-t border-gray-200">
                        <div class="text-sm text-gray-500">
                            Menampilkan {{ $mitigations->firstItem() }} - {{ $mitigations->lastItem() }} dari {{ $mitigations->total() }} mitigasi
                        </div>
                        <div class="w-full sm:w-auto flex items-center sm:ml-auto mt-3 sm:mt-0">
                            {{ $mitigations->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-6">
                            <i data-feather="shield" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        @if(request()->hasAny(['search', 'status', 'date_range']))
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak ditemukan</h3>
                            <p class="text-gray-500 mb-6">Tidak ada rencana mitigasi yang sesuai dengan filter yang dipilih</p>
                            <a href="{{ route('risk-mitigations.all') }}" 
                               class="btn btn-secondary mr-2">
                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Reset Filter
                            </a>
                        @else
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada rencana mitigasi</h3>
                            <p class="text-gray-500 mb-6">Rencana mitigasi akan muncul setelah dibuat pada masing-masing risiko</p>
                        @endif
                        <a href="{{ route('risks.index') }}" 
                           class="btn btn-primary mr-2">
                            <i data-feather="alert-triangle" class="w-4 h-4 mr-2"></i> Lihat Daftar Risiko
                        </a>
                        <a href="{{ route('risk-mitigations.index') }}" 
                           class="btn btn-success">
                            <i data-feather="filter" class="w-4 h-4 mr-2"></i> Mitigasi Per Risiko
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics Charts -->
        @if($mitigations->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="pie-chart" class="w-5 h-5 mr-2"></i> Statistik Mitigasi Risiko
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status Distribution -->
                    <div>
                        <h4 class="font-medium mb-3">Distribusi Status</h4>
                        <div class="space-y-2">
                            @php
                                $statusGroups = $mitigations->groupBy('status');
                                $total = $mitigations->count();
                            @endphp
                            
                            @foreach([
                                'selesai' => ['label' => 'Selesai', 'color' => 'bg-green-500'],
                                'dalam proses' => ['label' => 'Dalam Proses', 'color' => 'bg-blue-500'],
                                'belum dimulai' => ['label' => 'Belum Dimulai', 'color' => 'bg-gray-500'],
                                'ditunda' => ['label' => 'Ditunda', 'color' => 'bg-yellow-500'],
                                'dibatalkan' => ['label' => 'Dibatalkan', 'color' => 'bg-red-500']
                            ] as $status => $info)
                                @php
                                    $count = $statusGroups->get($status) ? $statusGroups->get($status)->count() : 0;
                                    $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span>{{ $info['label'] }}</span>
                                        <span>{{ $count }} ({{ round($percentage, 1) }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="{{ $info['color'] }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Monthly Mitigation Trend -->
                    <div>
                        <h4 class="font-medium mb-3">Trend Mitigasi Bulanan</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <canvas id="monthlyTrendChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Overdue Mitigations -->
                @php
                    $overdueMitigations = $mitigations->filter(function($mitigation) {
                        return \Carbon\Carbon::parse($mitigation->deadline)->lt(now()) && 
                               !in_array($mitigation->status, ['selesai', 'dibatalkan']);
                    });
                @endphp
                
                @if($overdueMitigations->count() > 0)
                <div class="mt-8">
                    <h4 class="font-medium mb-4">Mitigasi Terlambat ({{ $overdueMitigations->count() }})</h4>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-red-700">
                                        <th class="text-left py-2">Rencana Mitigasi</th>
                                        <th class="text-left py-2">Penanggung Jawab</th>
                                        <th class="text-left py-2">Deadline</th>
                                        <th class="text-left py-2">Keterlambatan</th>
                                        <th class="text-left py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overdueMitigations as $overdue)
                                        @php
                                            $daysOverdue = \Carbon\Carbon::parse($overdue->deadline)->diffInDays(now());
                                        @endphp
                                        <tr class="border-b border-red-100">
                                            <td class="py-2">
                                                <a href="{{ route('risk-mitigations.show', [$overdue->risk_mitigation_risk_id, $overdue->risk_mitigation_id]) }}" 
                                                   class="text-red-600 hover:text-red-800">
                                                    {{ Str::limit($overdue->mitigation_plan, 30) }}
                                                </a>
                                            </td>
                                            <td class="py-2">{{ $overdue->responsible_party }}</td>
                                            <td class="py-2">{{ $overdue->deadline->format('d M Y') }}</td>
                                            <td class="py-2 text-red-600 font-medium">{{ $daysOverdue }} hari</td>
                                            <td class="py-2">
                                                <span class="px-2 py-1 rounded-full text-xs 
                                                    @if($overdue->status == 'dalam proses') bg-blue-100 text-blue-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucwords($overdue->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Quick Summary -->
        @if($mitigations->count() > 0)
        <div class="intro-y box mt-6">
            <div class="flex items-center p-5 border-b border-gray-200">
                <h2 class="font-medium text-base mr-auto">
                    <i data-feather="zap" class="w-5 h-5 mr-2"></i> Ringkasan Cepat
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Most Overdue -->
                    @php
                        $mostOverdue = $mitigations->filter(function($mitigation) {
                            return \Carbon\Carbon::parse($mitigation->deadline)->lt(now()) && 
                                   !in_array($mitigation->status, ['selesai', 'dibatalkan']);
                        })->sortByDesc(function($mitigation) {
                            return \Carbon\Carbon::parse($mitigation->deadline)->diffInDays(now());
                        })->first();
                    @endphp
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                <i data-feather="alert-octagon" class="w-5 h-5 text-red-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-red-800">Mitigasi Paling Terlambat</div>
                            </div>
                        </div>
                        @if($mostOverdue)
                        <div class="text-center mb-3">
                            <div class="text-2xl font-bold text-red-600">
                                {{ \Carbon\Carbon::parse($mostOverdue->deadline)->diffInDays(now()) }} hari
                            </div>
                            <div class="text-sm text-red-700">{{ Str::limit($mostOverdue->mitigation_plan, 30) }}</div>
                        </div>
                        <div class="text-xs text-gray-600">
                            Penanggung jawab: {{ $mostOverdue->responsible_party }}
                        </div>
                        @endif
                    </div>
                    
                    <!-- Latest Mitigation -->
                    @php
                        $latestMitigation = $mitigations->sortByDesc('created_at')->first();
                    @endphp
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i data-feather="clock" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-blue-800">Mitigasi Terbaru</div>
                            </div>
                        </div>
                        @if($latestMitigation)
                        <div class="text-center mb-3">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ $latestMitigation->created_at->format('d M') }}
                            </div>
                            <div class="text-sm text-blue-700">
                                {{ $latestMitigation->risk->risk_code ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="text-xs text-gray-600">
                            Status: {{ ucwords($latestMitigation->status) }}
                        </div>
                        @endif
                    </div>
                    
                    <!-- Highest Risk Mitigation -->
                    @php
                        $highestRiskMitigation = $mitigations->filter(function($mitigation) {
                            return $mitigation->risk && $mitigation->risk->risk_score;
                        })->sortByDesc(function($mitigation) {
                            return $mitigation->risk->risk_score;
                        })->first();
                    @endphp
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center mr-3">
                                <i data-feather="alert-triangle" class="w-5 h-5 text-orange-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-orange-800">Mitigasi Risiko Tertinggi</div>
                            </div>
                        </div>
                        @if($highestRiskMitigation && $highestRiskMitigation->risk)
                        <div class="text-center mb-3">
                            <div class="text-3xl font-bold text-orange-600">
                                {{ $highestRiskMitigation->risk->risk_score }}
                            </div>
                            <div class="text-sm text-orange-700">{{ $highestRiskMitigation->risk->risk_code }}</div>
                        </div>
                        <div class="text-xs text-gray-600">
                            {{ Str::limit($highestRiskMitigation->risk->risk_description, 30) }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
    
    // Monthly Trend Chart
    @if($mitigations->count() > 0)
    const monthlyChartCtx = document.getElementById('monthlyTrendChart');
    if (monthlyChartCtx) {
        @php
            // Group mitigations by month of creation
            $monthlyData = [];
            $mitigations->each(function($mitigation) use (&$monthlyData) {
                $month = $mitigation->created_at->format('Y-m');
                if (!isset($monthlyData[$month])) {
                    $monthlyData[$month] = 0;
                }
                $monthlyData[$month]++;
            });
            
            // Prepare chart data
            $months = [];
            $counts = [];
            
            ksort($monthlyData);
            foreach($monthlyData as $month => $count) {
                $months[] = \Carbon\Carbon::parse($month)->format('M Y');
                $counts[] = $count;
            }
        @endphp
        
        new Chart(monthlyChartCtx, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Jumlah Mitigasi',
                    data: @json($counts),
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endpush