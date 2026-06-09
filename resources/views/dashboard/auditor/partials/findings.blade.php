<div class="intro-y box">
    <div class="flex items-center p-5 border-b border-slate-200/60">
        <h2 class="font-medium text-base mr-auto">
            Temuan Audit Terbaru
            <span class="text-sm text-slate-500 font-normal ml-2">({{ $recentAuditFindings->count() }} temuan)</span>
        </h2>
        <a href="{{ route('audits.index') }}?has_findings=true" class="btn btn-sm btn-outline-danger">
            Lihat Semua <i data-feather="arrow-right" class="w-4 h-4 ml-1"></i>
        </a>
    </div>
    <div class="p-5">
        @if($recentAuditFindings->count() > 0)
            <div class="space-y-4">
                @foreach($recentAuditFindings as $audit)
                    <div class="p-4 border border-red-100 rounded-lg bg-red-50">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium text-red-800">
                                    {{ $audit->risk->risk_code ?? 'Risiko #' . $audit->risk_id }}
                                </h4>
                                <div class="text-sm text-red-600 mt-1">
                                    <i data-feather="calendar" class="w-3 h-3 inline mr-1"></i>
                                    {{ $audit->audit_date ? \Carbon\Carbon::parse($audit->audit_date)->format('d/m/Y') : 'N/A' }}
                                </div>
                            </div>
                            <span class="badge badge-danger">Temuan</span>
                        </div>
                        
                        <div class="mt-3">
                            <h5 class="text-sm font-medium text-slate-700 mb-1">Temuan:</h5>
                            <p class="text-sm text-slate-600 line-clamp-3">
                                {{ \Illuminate\Support\Str::limit($audit->audit_findings, 120) }}
                            </p>
                        </div>
                        
                        <div class="flex justify-between items-center mt-4">
                            <div class="text-xs text-slate-500">
                                Oleh: {{ $audit->auditor }}
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('audits.show', $audit->id) }}" 
                                   class="btn btn-sm btn-danger" title="Detail Temuan">
                                    <i data-feather="alert-circle" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('audits.edit', $audit->id) }}" 
                                   class="btn btn-sm btn-warning" title="Update Follow-up">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-4">
                    <i data-feather="check-circle" class="w-8 h-8 text-green-400"></i>
                </div>
                <h4 class="text-lg font-medium text-slate-600 mb-2">Tidak ada temuan audit</h4>
                <p class="text-slate-500">Semua audit bersih dari temuan</p>
            </div>
        @endif
    </div>
</div>