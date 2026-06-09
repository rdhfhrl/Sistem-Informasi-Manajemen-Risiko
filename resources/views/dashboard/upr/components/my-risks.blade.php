<div class="intro-y box">
    <div class="flex items-center p-5 border-b border-slate-200/60">
        <h2 class="font-medium text-base mr-auto">
            Risiko Terbaru Saya
            <span class="text-sm text-slate-500 font-normal ml-2">({{ $myRecentRisks->count() }} risiko)</span>
        </h2>
        <a href="{{ route('risks.index') }}" class="btn btn-sm btn-outline-danger">
            Lihat Semua <i data-feather="arrow-right" class="w-4 h-4 ml-1"></i>
        </a>
    </div>
    <div class="p-5">
        @if($myRecentRisks->count() > 0)
            <div class="overflow-x-auto">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">Kode Risiko</th>
                            <th class="whitespace-nowrap">Deskripsi</th>
                            <th class="whitespace-nowrap">Level</th>
                            <th class="whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myRecentRisks as $risk)
                            @php
                                // Pastikan kita punya ID yang benar
                                $riskId = $risk->risk_id ?? $risk->id;
                                
                                // Pastikan proyek ada
                                $projectName = $risk->project->pro_nama ?? 'No Project';
                                
                                // Warna untuk level risiko
                                $levelColors = [
                                    'sangat_rendah' => 'success',
                                    'rendah' => 'info',
                                    'sedang' => 'warning',
                                    'tinggi' => 'danger',
                                    'sangat_tinggi' => 'danger'
                                ];
                                $level = $risk->risk_level ?? 'sedang';
                                $color = $risk->level_color ?? ($levelColors[$level] ?? 'secondary');
                            @endphp
                            
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $risk->risk_code ?? 'R-' . $riskId }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ $projectName }}
                                    </div>
                                </td>
                                <td>
                                    <div class="truncate max-w-xs" title="{{ $risk->risk_description }}">
                                        {{ \Illuminate\Support\Str::limit($risk->risk_description, 50) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $color }}">
                                        {{ ucfirst(str_replace('_', ' ', $level)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('risks.show', $riskId) }}" 
                                           class="btn btn-sm btn-primary" title="Detail">
                                            <i data-feather="eye" class="w-4 h-4"></i>
                                        </a>
                                        
                                        @if(!$risk->mitigations || $risk->mitigations->count() == 0)
                                            <a href="{{ route('risk-mitigations.create', ['riskId' => $riskId]) }}" 
                                               class="btn btn-sm btn-success" title="Buat Mitigasi">
                                                <i data-feather="shield" class="w-4 h-4"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                    <i data-feather="alert-triangle" class="w-8 h-8 text-red-400"></i>
                </div>
                <h4 class="text-lg font-medium text-slate-600 mb-2">Belum ada risiko</h4>
                <p class="text-slate-500 mb-6">Anda belum memiliki risiko yang diidentifikasi</p>
                <a href="{{ route('risk-identifications.create') }}" class="btn btn-danger">
                    <i data-feather="plus" class="w-4 h-4 mr-2"></i> Identifikasi Risiko
                </a>
            </div>
        @endif
    </div>
</div>