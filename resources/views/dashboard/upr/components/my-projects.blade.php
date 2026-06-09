<div class="intro-y box">
    <div class="flex items-center p-5 border-b border-slate-200/60">
        <h2 class="font-medium text-base mr-auto">
            Proyek Saya
            <span class="text-sm text-slate-500 font-normal ml-2">({{ $myProjects->count() }} proyek)</span>
        </h2>
        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary">
            Lihat Semua <i data-feather="arrow-right" class="w-4 h-4 ml-1"></i>
        </a>
    </div>
    <div class="p-5">
        @if($myProjects->count() > 0)
            <div class="overflow-x-auto">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">Nama Proyek</th>
                            <th class="whitespace-nowrap">Lokasi</th>
                            <th class="whitespace-nowrap">Status</th>
                            <th class="whitespace-nowrap">Risiko</th>
                            <th class="whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myProjects as $project)
                            @php
                                // Hitung jumlah risiko
                                $risksCount = $project->risks_count ?? 0;
                                $highRisksCount = $project->risks->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])->count() ?? 0;
                                
                                // Warna status
                                $statusColors = [
                                    'Aktif' => 'bg-green-100 text-green-800',
                                    'Selesai' => 'bg-blue-100 text-blue-800',
                                    'Ditunda' => 'bg-yellow-100 text-yellow-800',
                                    'Dibatalkan' => 'bg-red-100 text-red-800',
                                    'default' => 'bg-gray-100 text-gray-800'
                                ];
                                $statusClass = $statusColors[$project->pro_status] ?? $statusColors['default'];
                            @endphp
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $project->pro_nama }}</div>
                                    <div class="text-xs text-slate-500 truncate max-w-xs">
                                        {{ $project->pro_code ?? 'No Code' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-slate-600">
                                        {{ Str::limit($project->pro_lokasi, 20) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="px-2 py-1 text-xs rounded {{ $statusClass }}">
                                        {{ $project->pro_status }}
                                    </span>
                                    @if($project->pro_status == 'Aktif' && $project->pro_tanggal_selesai < now())
                                        <div class="text-xs text-red-600 mt-1">
                                            <i data-feather="alert-circle" class="w-3 h-3"></i> Terlambat
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($risksCount > 0)
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-2">
                                                <i data-feather="alert-triangle" class="w-4 h-4 text-red-600"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ $risksCount }}</div>
                                                <div class="text-xs text-red-500">
                                                    {{ $highRisksCount }} tinggi
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('projects.show', $project->pro_id) }}" 
                                           class="btn btn-sm btn-primary" title="Detail">
                                            <i data-feather="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('risks.create', ['pro_id' => $project->pro_id]) }}" 
                                           class="btn btn-sm btn-warning" title="Tambah Risiko">
                                            <i data-feather="plus" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('risks.index', ['pro_id' => $project->pro_id]) }}" 
                                           class="btn btn-sm btn-danger" title="Daftar Risiko">
                                            <i data-feather="alert-triangle" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <i data-feather="briefcase" class="w-8 h-8 text-slate-400"></i>
                </div>
                <h4 class="text-lg font-medium text-slate-600 mb-2">Belum ada proyek</h4>
                <p class="text-slate-500 mb-6">Anda belum memiliki proyek yang ditugaskan</p>
                <a href="{{ route('projects.index') }}" class="btn btn-primary">
                    <i data-feather="plus" class="w-4 h-4 mr-2"></i> Lihat Semua Proyek
                </a>
            </div>
        @endif
    </div>
</div>