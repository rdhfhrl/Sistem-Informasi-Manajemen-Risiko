<div class="intro-y box">
    <div class="flex items-center p-5 border-b border-slate-200/60">
        <h2 class="font-medium text-base mr-auto">
            Pemberitahuan & Update
            <span class="text-sm text-slate-500 font-normal ml-2">({{ count($updates ?? []) }} update)</span>
        </h2>
        <button onclick="markAllAsRead()" class="btn btn-sm btn-outline-secondary">
            <i data-feather="check-circle" class="w-4 h-4 mr-1"></i> Tandai Semua
        </button>
    </div>
    <div class="p-5">
        @if(count($updates ?? []) > 0)
            <div class="space-y-4">
                @foreach($updates as $update)
                    @php
                        $typeColors = [
                            'danger' => 'bg-red-50 border-red-200',
                            'warning' => 'bg-yellow-50 border-yellow-200',
                            'info' => 'bg-blue-50 border-blue-200',
                            'primary' => 'bg-indigo-50 border-indigo-200',
                            'success' => 'bg-green-50 border-green-200'
                        ];
                        $iconColors = [
                            'danger' => 'text-red-600',
                            'warning' => 'text-yellow-600',
                            'info' => 'text-blue-600',
                            'primary' => 'text-indigo-600',
                            'success' => 'text-green-600'
                        ];
                    @endphp
                    
                    <div class="p-4 border rounded-lg {{ $typeColors[$update['type']] ?? 'bg-slate-50 border-slate-200' }}">
                        <div class="flex items-start">
                            <div class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-full {{ $iconColors[$update['type']] ?? 'text-slate-600' }} bg-white mr-3">
                                <i data-feather="{{ $update['icon'] ?? 'info' }}" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium mb-1">{{ $update['title'] ?? 'Update' }}</h4>
                                <p class="text-sm text-slate-600 mb-2">{{ $update['message'] ?? '' }}</p>
                                <div class="text-xs text-slate-500">
                                    <i data-feather="clock" class="w-3 h-3 inline mr-1"></i>
                                    {{ now()->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <button onclick="markAsRead(this)" class="text-slate-400 hover:text-slate-600 ml-2">
                                <i data-feather="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <i data-feather="bell-off" class="w-8 h-8 text-slate-400"></i>
                </div>
                <h4 class="text-lg font-medium text-slate-600 mb-2">Tidak ada pemberitahuan</h4>
                <p class="text-slate-500">Semua tugas sudah ditangani</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function markAsRead(button) {
    const card = button.closest('.border');
    card.style.opacity = '0.5';
    setTimeout(() => {
        card.style.display = 'none';
    }, 300);
}

function markAllAsRead() {
    if (confirm('Tandai semua pemberitahuan sebagai sudah dibaca?')) {
        const cards = document.querySelectorAll('.border');
        cards.forEach(card => {
            card.style.opacity = '0.5';
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        });
        
        // Kirim request ke server jika perlu
        // fetch('/notifications/mark-all-read', { method: 'POST' });
    }
}
</script>
@endpush