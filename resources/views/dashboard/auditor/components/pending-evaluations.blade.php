<div class="intro-y box">
    <div class="flex items-center p-5 border-b border-slate-200/60">
        <h2 class="font-medium text-base mr-auto">Evaluasi Perlu Review</h2>
        <span class="px-2 py-1 rounded-full text-xs bg-warning text-white">
            {{ count($pendingEvaluations) }} Menunggu
        </span>
    </div>
    <div class="p-5">
        <div class="space-y-4">
            @foreach($pendingEvaluations as $evaluation)
            <div class="pending-evaluation p-3 rounded-md border border-warning">
                <div class="flex items-start">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 bg-warning text-white">
                        <i data-feather="clock" class="w-4 h-4"></i>
                    </div>
                    <div class="flex-1">
                        <a href="{{ route('risks.show', $evaluation->risk_id) }}" class="font-medium">
                            {{ $evaluation->risk->risk_code ?? 'N/A' }}
                        </a>
                        <div class="text-slate-500 text-xs mt-0.5">
                            Prioritas: <span class="font-medium">{{ ucfirst($evaluation->risk_evaluation_priority) }}</span>
                        </div>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-slate-500 mr-3">
                                <i data-feather="calendar" class="w-3 h-3 mr-1"></i>
                                {{ Carbon\Carbon::parse($evaluation->evaluation_date)->format('d M Y') }}
                            </span>
                            <span class="text-xs text-slate-500">
                                <i data-feather="user" class="w-3 h-3 mr-1"></i>
                                {{ $evaluation->risk->user->name ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="mt-3">
                            <div class="text-xs font-medium mb-1">Keputusan Mitigasi:</div>
                            <p class="text-xs text-slate-600">{{ Str::limit($evaluation->mitigation_decision, 100) }}</p>
                        </div>
                        <div class="mt-3 flex space-x-2">
                            <a href="{{ route('risk-evaluations.show', ['riskId' => $evaluation->risk_id, 'evaluationId' => $evaluation->risk_evaluation_id]) }}" 
                               class="btn btn-primary btn-sm py-1 px-3 text-xs">
                                <i data-feather="eye" class="w-3 h-3 mr-1"></i> Review
                            </a>
                            <button onclick="markEvaluationComplete({{ $evaluation->risk_evaluation_id }})" 
                                    class="btn btn-success btn-sm py-1 px-3 text-xs">
                                <i data-feather="check" class="w-3 h-3 mr-1"></i> Selesai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
            @if($pendingEvaluations->isEmpty())
            <div class="text-center py-8">
                <i data-feather="check-circle" class="w-12 h-12 text-slate-300 mx-auto"></i>
                <p class="text-slate-500 mt-2">Tidak ada evaluasi yang menunggu review</p>
                <a href="{{ route('risk-evaluations.index') }}" class="btn btn-primary mt-3">
                    <i data-feather="list" class="w-4 h-4 mr-2"></i> Lihat Semua Evaluasi
                </a>
            </div>
            @endif
        </div>
    </div>
</div>