<div class="intro-y box">
    <div class="flex items-center p-5 border-b border-slate-200/60">
        <h2 class="font-medium text-base mr-auto">
            Jadwal Audit Terbaru
            <span class="text-sm text-slate-500 font-normal ml-2">({{ $recentAudits->count() }} audit)</span>
        </h2>
        <a href="{{ route('audits.index') }}" class="btn btn-sm btn-outline-primary">
            Lihat Semua <i data-feather="arrow-right" class="w-4 h-4 ml-1"></i>
        </a>
    </div>
    <div class="p-5">
        @if($recentAudits->count() > 0)
            <div class="overflow-x-auto">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">Tanggal Audit</th>
                            <th class="whitespace-nowrap">Risiko</th>
                            <th class="whitespace-nowrap">Status</th>
                            <th class="whitespace-nowrap">Temuan</th>
                            <th class="whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAudits as $audit)
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $audit->audit_date ? \Carbon\Carbon::parse($audit->audit_date)->format('d/m/Y') : 'N/A' }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ $audit->created_at->diffForHumans() }}
                                    </div>
                                </td>
                                <td>
                                    <div class="font-medium">
                                        <a href="{{ route('risks.show', $audit->risk_id ?? '#') }}" class="text-primary">
                                            {{ $audit->risk->risk_code ?? 'Risiko #' . ($audit->risk_id ?? 'N/A') }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-slate-500 truncate max-w-xs">
                                        {{ $audit->risk->risk_description ?? 'Tidak ada deskripsi' }}
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'scheduled' => 'info',
                                            'in_progress' => 'warning',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $status = $audit->audit_status ?? 'draft';
                                    @endphp
                                    <span class="badge badge-{{ $statusColors[$status] ?? 'secondary' }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($audit->audit_findings)
                                        <div class="text-xs text-danger">
                                            <i data-feather="alert-circle" class="w-3 h-3 mr-1"></i>
                                            Ada Temuan
                                        </div>
                                    @else
                                        <div class="text-xs text-success">
                                            <i data-feather="check-circle" class="w-3 h-3 mr-1"></i>
                                            Clean
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('audits.show', $audit->id) }}" 
                                           class="btn btn-sm btn-primary" title="Detail">
                                            <i data-feather="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('audits.edit', $audit->id) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i data-feather="edit" class="w-4 h-4"></i>
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
                <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mx-auto mb-4">
                    <i data-feather="calendar" class="w-8 h-8 text-blue-400"></i>
                </div>
                <h4 class="text-lg font-medium text-slate-600 mb-2">Belum ada audit</h4>
                <p class="text-slate-500 mb-6">Anda belum memiliki jadwal audit</p>
                <a href="{{ route('audits.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="w-4 h-4 mr-2"></i> Jadwalkan Audit
                </a>
            </div>
        @endif
    </div>
</div>