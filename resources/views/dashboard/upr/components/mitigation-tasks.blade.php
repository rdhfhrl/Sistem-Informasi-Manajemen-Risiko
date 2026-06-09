<div class="intro-y box">
    <div class="flex items-center p-5 border-b border-slate-200/60">
        <h2 class="font-medium text-base mr-auto">
            Mitigasi Kritis
            <span class="text-sm text-slate-500 font-normal ml-2">({{ $criticalMitigations->count() }} tugas)</span>
        </h2>
        <a href="{{ route('risk-mitigations.index') }}" class="btn btn-sm btn-outline-warning">
            Lihat Semua <i data-feather="arrow-right" class="w-4 h-4 ml-1"></i>
        </a>
    </div>
    <div class="p-5">
        @if($criticalMitigations->count() > 0)
            <div class="space-y-4">
                @foreach($criticalMitigations as $mitigation)
                    @php
                        $dueDate = \Carbon\Carbon::parse($mitigation->deadline);
                        $isOverdue = $dueDate->isPast();
                        $daysLeft = $dueDate->diffInDays(now(), false);
                        
                        if ($isOverdue) {
                            $badgeClass = 'badge-danger';
                            $statusText = 'Terlambat ' . abs($daysLeft) . ' hari';
                        } elseif ($daysLeft <= 3) {
                            $badgeClass = 'badge-warning';
                            $statusText = 'Segera ' . $daysLeft . ' hari';
                        } else {
                            $badgeClass = 'badge-info';
                            $statusText = $daysLeft . ' hari lagi';
                        }
                    @endphp
                    
                    <div class="p-4 border rounded-lg {{ $isOverdue ? 'bg-red-50 border-red-200' : 'bg-white border-slate-200' }}">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium">{{ $mitigation->risk->risk_code ?? 'Risiko' }}</h4>
                                <p class="text-sm text-slate-600 line-clamp-2">
                                    {{ \Illuminate\Support\Str::limit($mitigation->mitigation_plan, 80) }}
                                </p>
                            </div>
                            <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center mt-3">
                            <div class="text-xs text-slate-500">
                                <i data-feather="calendar" class="w-3 h-3 inline mr-1"></i>
                                Deadline: {{ $dueDate->format('d/m/Y') }}
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('risk-mitigations.edit', $mitigation->id) }}" 
                                   class="btn btn-sm btn-primary" title="Update">
                                    <i data-feather="edit" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('risk-mitigations.update-status', $mitigation->id) }}" 
                                      method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="selesai">
                                    <button type="submit" class="btn btn-sm btn-success" 
                                            onclick="return confirm('Tandai sebagai selesai?')"
                                            title="Selesai">
                                        <i data-feather="check-circle" class="w-4 h-4"></i>
                                    </button>
                                </form>
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
                <h4 class="text-lg font-medium text-slate-600 mb-2">Tidak ada mitigasi kritis</h4>
                <p class="text-slate-500">Semua tugas mitigasi dalam kondisi baik</p>
            </div>
        @endif
    </div>
</div>