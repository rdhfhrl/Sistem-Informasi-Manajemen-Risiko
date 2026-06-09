<div class="intro-y box mt-5">
    <div class="flex items-center p-5 border-b border-slate-200/60">
        <h2 class="font-medium text-base mr-auto">Antrian Evaluasi Cepat</h2>
        <button onclick="refreshQueue()" class="btn btn-sm btn-outline-secondary">
            <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i> Refresh
        </button>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Quick Action: Evaluate High Priority -->
            <a href="{{ route('risk-evaluations.index') }}?priority=tinggi" 
               class="flex flex-col items-center justify-center p-4 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100 mb-3">
                    <i data-feather="alert-octagon" class="w-6 h-6 text-red-600"></i>
                </div>
                <span class="font-medium text-red-800">Prioritas Tinggi</span>
                <span class="text-xs text-red-600 mt-1">
                    {{ $pendingEvaluations->where('risk_evaluation_priority', 'tinggi')->count() }} evaluasi
                </span>
            </a>
            
            <!-- Quick Action: Review Findings -->
            <a href="{{ route('audits.index') }}?has_findings=true" 
               class="flex flex-col items-center justify-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-orange-100 mb-3">
                    <i data-feather="alert-circle" class="w-6 h-6 text-orange-600"></i>
                </div>
                <span class="font-medium text-orange-800">Review Temuan</span>
                <span class="text-xs text-orange-600 mt-1">
                    {{ $recentAuditFindings->count() }} temuan
                </span>
            </a>
            
            <!-- Quick Action: Schedule Audits -->
            <a href="{{ route('audits.create') }}" 
               class="flex flex-col items-center justify-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100 mb-3">
                    <i data-feather="calendar" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="font-medium text-blue-800">Jadwalkan Audit</span>
                <span class="text-xs text-blue-600 mt-1">Audit baru</span>
            </a>
            
            <!-- Quick Action: Generate Compliance Report -->
            <a href="{{ route('reports.create') }}?type=compliance" 
               class="flex flex-col items-center justify-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100 mb-3">
                    <i data-feather="file-text" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="font-medium text-green-800">Laporan Kepatuhan</span>
                <span class="text-xs text-green-600 mt-1">Generate report</span>
            </a>
        </div>
        
        <div class="mt-6 p-4 bg-slate-50 rounded-lg">
            <h4 class="font-medium text-slate-700 mb-2 flex items-center">
                <i data-feather="info" class="w-4 h-4 mr-2 text-slate-500"></i>
                Panduan Cepat
            </h4>
            <ul class="text-sm text-slate-600 space-y-1">
                <li class="flex items-start">
                    <i data-feather="check-circle" class="w-3 h-3 text-green-500 mr-2 mt-0.5"></i>
                    <span>Review evaluasi prioritas tinggi dalam 24 jam</span>
                </li>
                <li class="flex items-start">
                    <i data-feather="check-circle" class="w-3 h-3 text-green-500 mr-2 mt-0.5"></i>
                    <span>Follow-up temuan audit maksimal 7 hari</span>
                </li>
                <li class="flex items-start">
                    <i data-feather="check-circle" class="w-3 h-3 text-green-500 mr-2 mt-0.5"></i>
                    <span>Buat laporan kepatuhan bulanan sebelum tanggal 5</span>
                </li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
function refreshQueue() {
    location.reload();
}
</script>
@endpush