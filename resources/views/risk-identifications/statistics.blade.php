<!-- Timeline Risiko -->
<div class="intro-y box">
    <div class="flex items-center p-5 border-b border-gray-200">
        <h2 class="font-medium text-base mr-auto">
            <i data-feather="clock" class="w-5 h-5 mr-2"></i> Timeline Risiko
        </h2>
        <button onclick="loadIdentificationStats()" 
                class="btn btn-sm btn-outline-secondary ml-2"
                title="Refresh Statistik">
            <i data-feather="refresh-cw" class="w-4 h-4"></i>
        </button>
    </div>
    <div class="p-5">
        <!-- Stats Container -->
        <div id="identification-stats" class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-medium text-sm">
                    <i data-feather="pie-chart" class="w-4 h-4 inline mr-2"></i>
                    Statistik Identifikasi
                </h3>
                <div class="text-xs text-gray-500">Live Update</div>
            </div>
            <div id="stats-content" class="text-center py-4">
                <div class="spinner-border text-primary mx-auto" role="status" style="width: 1.5rem; height: 1.5rem;"></div>
                <p class="text-gray-500 text-sm mt-2">Memuat statistik...</p>
            </div>
        </div>
        
        <div class="relative">
            <!-- Timeline Line -->
            <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>
            
            <div class="space-y-4 relative">
                <!-- Created - Identifikasi Risiko -->
                @if($risk->identification)
                    <a href="{{ route('risk-identifications.edit') }}?riskId={{ $risk->risk_id }}" 
                       class="flex items-start p-3 bg-green-50 hover:bg-green-100 rounded-lg transition cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4 z-10">
                            <i data-feather="search" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium flex items-center justify-between">
                                <span>Identifikasi Risiko</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-green-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i data-feather="edit" class="w-3 h-3 mr-1"></i> Edit
                                    </span>
                                    <button onclick="event.stopPropagation(); loadRiskTypeStats('{{ $risk->identification->loss_type }}', 'loss_type')" 
                                            class="text-xs text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity hover:underline">
                                        <i data-feather="bar-chart" class="w-3 h-3 mr-1"></i> Stat
                                    </button>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">{{ $risk->created_at->format('d F Y H:i') }}</div>
                            <div class="text-xs text-gray-500 mt-1">Risiko pertama kali diidentifikasi</div>
                            
                            <!-- Detail Identifikasi -->
                            <div class="mt-2">
                                <div class="flex flex-wrap gap-1">
                                    @if($risk->identification->loss_type)
                                        <button onclick="event.stopPropagation(); loadRiskTypeStats('{{ $risk->identification->loss_type }}', 'loss_type')"
                                                class="px-2 py-1 bg-red-100 hover:bg-red-200 text-red-800 text-xs rounded transition cursor-pointer">
                                            <i data-feather="dollar-sign" class="w-3 h-3 inline mr-1"></i>
                                            {{ $risk->identification->loss_type }}
                                        </button>
                                    @endif
                                    @if($risk->identification->violation_type)
                                        <button onclick="event.stopPropagation(); loadRiskTypeStats('{{ $risk->identification->violation_type }}', 'violation_type')"
                                                class="px-2 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs rounded transition cursor-pointer">
                                            <i data-feather="alert-octagon" class="w-3 h-3 inline mr-1"></i>
                                            {{ $risk->identification->violation_type }}
                                        </button>
                                    @endif
                                    @if($risk->identification->failure_type)
                                        <button onclick="event.stopPropagation(); loadRiskTypeStats('{{ $risk->identification->failure_type }}', 'failure_type')"
                                                class="px-2 py-1 bg-teal-100 hover:bg-teal-200 text-teal-800 text-xs rounded transition cursor-pointer">
                                            <i data-feather="x-circle" class="w-3 h-3 inline mr-1"></i>
                                            {{ $risk->identification->failure_type }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @else
                    <a href="{{ route('risk-identifications.create') }}?riskId={{ $risk->risk_id }}" 
                       class="flex items-start p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-4 z-10">
                            <i data-feather="search" class="w-5 h-5 text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium flex items-center justify-between">
                                <span>Identifikasi Risiko</span>
                                <span class="text-xs text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-feather="plus" class="w-3 h-3 mr-1"></i> Tambah
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">{{ $risk->created_at->format('d F Y H:i') }}</div>
                            <div class="text-xs text-gray-500 mt-1">Risiko pertama kali diidentifikasi</div>
                            
                            <!-- Prompt untuk identifikasi -->
                            <div class="mt-2 p-2 bg-yellow-50 border border-yellow-100 rounded">
                                <div class="text-xs text-yellow-800 flex items-center">
                                    <i data-feather="alert-circle" class="w-3 h-3 mr-1"></i>
                                    Klik untuk melakukan identifikasi risiko
                                </div>
                            </div>
                        </div>
                    </a>
                @endif
                
                <!-- First Analysis -->
                @if($risk->analyses && $risk->analyses->count() > 0)
                    @php
                        $firstAnalysis = $risk->analyses->first();
                    @endphp
                    <a href="{{ route('risk-analyses.show', ['riskId' => $risk->risk_id, 'analysisId' => $firstAnalysis->risk_analysis_id]) }}" 
                       class="flex items-start p-3 bg-orange-50 hover:bg-orange-100 rounded-lg transition cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center mr-4 z-10">
                            <i data-feather="activity" class="w-5 h-5 text-orange-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium flex items-center justify-between">
                                <span>Analisis Pertama</span>
                                <span class="text-xs text-orange-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-feather="eye" class="w-3 h-3 mr-1"></i> Lihat
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $firstAnalysis->analysis_date ? \Carbon\Carbon::parse($firstAnalysis->analysis_date)->format('d F Y') : '-' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Skor awal: 
                                <span class="font-bold 
                                    @if($firstAnalysis->risk_score >= 20) text-red-600
                                    @elseif($firstAnalysis->risk_score >= 15) text-orange-600
                                    @elseif($firstAnalysis->risk_score >= 10) text-yellow-600
                                    @else text-green-600
                                    @endif">
                                    {{ $firstAnalysis->risk_score }}
                                </span>
                            </div>
                        </div>
                    </a>
                @else
                    <a href="{{ route('risk-analyses.create', ['riskId' => $risk->risk_id]) }}" 
                       class="flex items-start p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-4 z-10">
                            <i data-feather="activity" class="w-5 h-5 text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium flex items-center justify-between">
                                <span>Analisis Pertama</span>
                                <span class="text-xs text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-feather="plus" class="w-3 h-3 mr-1"></i> Tambah
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">Belum ada analisis</div>
                            <div class="text-xs text-gray-500 mt-1">Klik untuk menambahkan analisis pertama</div>
                        </div>
                    </a>
                @endif
                
                <!-- Latest Analysis -->
                @if($risk->analyses && $risk->analyses->count() > 1)
                    @php
                        $latestAnalysis = $risk->analyses->last();
                    @endphp
                    <a href="{{ route('risk-analyses.show', ['riskId' => $risk->risk_id, 'analysisId' => $latestAnalysis->risk_analysis_id]) }}" 
                       class="flex items-start p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4 z-10">
                            <i data-feather="refresh-cw" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium flex items-center justify-between">
                                <span>Analisis Terbaru</span>
                                <span class="text-xs text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-feather="eye" class="w-3 h-3 mr-1"></i> Lihat
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ $latestAnalysis->analysis_date ? \Carbon\Carbon::parse($latestAnalysis->analysis_date)->format('d F Y') : '-' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Skor terkini: 
                                <span class="font-bold 
                                    @if($latestAnalysis->risk_score >= 20) text-red-600
                                    @elseif($latestAnalysis->risk_score >= 15) text-orange-600
                                    @elseif($latestAnalysis->risk_score >= 10) text-yellow-600
                                    @else text-green-600
                                    @endif">
                                    {{ $latestAnalysis->risk_score }}
                                </span>
                            </div>
                        </div>
                    </a>
                @elseif($risk->analyses && $risk->analyses->count() === 1)
                    <a href="{{ route('risk-analyses.create', ['riskId' => $risk->risk_id]) }}" 
                       class="flex items-start p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4 z-10">
                            <i data-feather="refresh-cw" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium flex items-center justify-between">
                                <span>Update Analisis</span>
                                <span class="text-xs text-green-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i data-feather="plus" class="w-3 h-3 mr-1"></i> Update
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">Update analisis terbaru</div>
                            <div class="text-xs text-gray-500 mt-1">Klik untuk update analisis risiko</div>
                        </div>
                    </a>
                @endif
                
                <!-- Last Updated -->
                <a href="{{ route('risks.edit', ['risk' => $risk->risk_id]) }}" 
                   class="flex items-start p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition cursor-pointer group">
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-4 z-10">
                        <i data-feather="edit" class="w-5 h-5 text-gray-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium flex items-center justify-between">
                            <span>Edit Risiko</span>
                            <span class="text-xs text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                <i data-feather="edit-2" class="w-3 h-3 mr-1"></i> Edit
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">{{ $risk->updated_at->format('d F Y H:i') }}</div>
                        <div class="text-xs text-gray-500 mt-1">
                            @php
                                $daysSinceUpdate = $risk->updated_at->diffInDays(now());
                                $updateText = $daysSinceUpdate . ' hari yang lalu';
                                
                                if ($daysSinceUpdate === 0) {
                                    $updateText = 'Hari ini';
                                } elseif ($daysSinceUpdate === 1) {
                                    $updateText = 'Kemarin';
                                }
                            @endphp
                            {{ $updateText }}
                        </div>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Quick Actions Horizontal -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('risk-analyses.create', ['riskId' => $risk->risk_id]) }}" 
                   class="px-4 py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg text-sm flex items-center transition">
                    <i data-feather="activity" class="w-4 h-4 mr-2"></i> Analisis Baru
                </a>
                
                @if($risk->identification)
                    <a href="{{ route('risk-identifications.edit') }}?riskId={{ $risk->risk_id }}" 
                       class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-sm flex items-center transition">
                        <i data-feather="edit" class="w-4 h-4 mr-2"></i> Edit Identifikasi
                    </a>
                @else
                    <a href="{{ route('risk-identifications.create') }}?riskId={{ $risk->risk_id }}" 
                       class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-sm flex items-center transition">
                        <i data-feather="search" class="w-4 h-4 mr-2"></i> Identifikasi Risiko
                    </a>
                @endif
                
                <a href="{{ route('risk-analyses.index', ['riskId' => $risk->risk_id]) }}" 
                   class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm flex items-center transition">
                    <i data-feather="list" class="w-4 h-4 mr-2"></i> Riwayat Analisis
                </a>
                
                <a href="{{ route('risk-identifications.statistics') }}" 
                   target="_blank"
                   class="px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg text-sm flex items-center transition">
                    <i data-feather="pie-chart" class="w-4 h-4 mr-2"></i> Statistik Lengkap
                </a>
                
                <a href="{{ route('risks.index') }}" 
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm flex items-center transition">
                    <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Statistik Detail -->
<div class="modal" id="stats-modal">
    <div class="modal__content modal__content--lg">
        <div class="p-5">
            <h2 class="text-lg font-medium mb-4 flex items-center">
                <i data-feather="bar-chart-2" class="w-5 h-5 mr-2"></i>
                <span id="modal-title">Statistik Identifikasi</span>
            </h2>
            <div id="modal-stats-content" class="py-4">
                <div class="text-center">
                    <div class="spinner-border text-primary mx-auto" role="status"></div>
                    <p class="text-gray-500 mt-2">Memuat statistik...</p>
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="closeStatsModal()" class="btn btn-outline-secondary w-24">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load statistik saat halaman dimuat
    loadIdentificationStats();
});

// Load statistik identifikasi
function loadIdentificationStats() {
    const statsContainer = document.getElementById('stats-content');
    
    fetch('{{ route("risk-identifications.statistics") }}')
        .then(response => response.json())
        .then(data => {
            let html = '<div class="grid grid-cols-3 gap-4">';
            
            // Total Identifikasi
            const totalCount = data.loss_types.reduce((sum, item) => sum + item.count, 0);
            html += `
                <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                    <div class="text-2xl font-bold text-blue-600">${totalCount}</div>
                    <div class="text-xs text-gray-600">Total Identifikasi</div>
                </div>
            `;
            
            // Loss Type teratas
            if (data.loss_types.length > 0) {
                const topLoss = data.loss_types[0];
                const percentage = Math.round((topLoss.count / totalCount) * 100);
                html += `
                    <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                        <div class="text-lg font-bold text-red-600">${topLoss.loss_type}</div>
                        <div class="text-xs text-gray-600">${topLoss.count} (${percentage}%)</div>
                        <div class="text-xs text-red-500 mt-1">Jenis Kerugian Terbanyak</div>
                    </div>
                `;
            }
            
            // Violation Type teratas
            if (data.violation_types.length > 0) {
                const topViolation = data.violation_types[0];
                const percentage = Math.round((topViolation.count / totalCount) * 100);
                html += `
                    <div class="text-center p-3 bg-white rounded-lg shadow-sm">
                        <div class="text-lg font-bold text-blue-600">${topViolation.violation_type}</div>
                        <div class="text-xs text-gray-600">${topViolation.count} (${percentage}%)</div>
                        <div class="text-xs text-blue-500 mt-1">Pelanggaran Terbanyak</div>
                    </div>
                `;
            }
            
            html += '</div>';
            
            // Progress bars untuk semua kategori
            html += '<div class="mt-4 space-y-3">';
            
            // Loss Types Progress
            if (data.loss_types.length > 0) {
                html += '<div><div class="text-xs text-gray-600 mb-1">Jenis Kerugian:</div>';
                data.loss_types.forEach(item => {
                    const percentage = Math.round((item.count / totalCount) * 100);
                    html += `
                        <div class="mb-1">
                            <div class="flex justify-between text-xs">
                                <span>${item.loss_type}</span>
                                <span>${item.count} (${percentage}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-red-500 h-1.5 rounded-full" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            }
            
            html += '</div>';
            
            statsContainer.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading stats:', error);
            statsContainer.innerHTML = `
                <div class="text-center py-4 text-gray-500">
                    <i data-feather="alert-circle" class="w-8 h-8 mx-auto mb-2"></i>
                    <p>Gagal memuat statistik</p>
                    <button onclick="loadIdentificationStats()" class="text-blue-600 text-sm mt-2 hover:underline">
                        Coba lagi
                    </button>
                </div>
            `;
            feather.replace();
        });
}

// Load statistik untuk tipe risiko tertentu
function loadRiskTypeStats(type, category) {
    const modal = document.getElementById('stats-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalContent = document.getElementById('modal-stats-content');
    
    // Set modal title
    let categoryName = '';
    switch(category) {
        case 'loss_type': categoryName = 'Jenis Kerugian'; break;
        case 'violation_type': categoryName = 'Jenis Pelanggaran'; break;
        case 'failure_type': categoryName = 'Jenis Kegagalan'; break;
        case 'error_type': categoryName = 'Jenis Kesalahan'; break;
    }
    modalTitle.textContent = `${categoryName}: ${type}`;
    
    // Show modal
    modal.style.display = 'flex';
    modal.classList.add('modal--show');
    
    // Load data
    fetch('{{ route("risk-identifications.statistics") }}')
        .then(response => response.json())
        .then(data => {
            let html = '<div class="space-y-4">';
            
            // Find specific type data
            let typeData = [];
            let totalCount = 0;
            
            switch(category) {
                case 'loss_type':
                    typeData = data.loss_types;
                    totalCount = data.loss_types.reduce((sum, item) => sum + item.count, 0);
                    break;
                case 'violation_type':
                    typeData = data.violation_types;
                    totalCount = data.violation_types.reduce((sum, item) => sum + item.count, 0);
                    break;
                case 'failure_type':
                    typeData = data.failure_types;
                    totalCount = data.failure_types.reduce((sum, item) => sum + item.count, 0);
                    break;
                case 'error_type':
                    typeData = data.error_types;
                    totalCount = data.error_types.reduce((sum, item) => sum + item.count, 0);
                    break;
            }
            
            // Find current type
            const currentType = typeData.find(item => item[category] === type);
            
            if (currentType) {
                const percentage = Math.round((currentType.count / totalCount) * 100);
                
                html += `
                    <div class="text-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                        <div class="text-4xl font-bold text-blue-600 mb-2">${currentType.count}</div>
                        <div class="text-lg font-medium">${type}</div>
                        <div class="text-gray-600">${percentage}% dari total ${totalCount}</div>
                    </div>
                `;
                
                // Progress bar
                html += `
                    <div class="p-4 bg-white rounded-lg shadow-sm">
                        <div class="text-sm font-medium mb-3">Distribusi ${categoryName}:</div>
                        <div class="space-y-3">
                `;
                
                typeData.forEach(item => {
                    const itemPercentage = Math.round((item.count / totalCount) * 100);
                    const isCurrent = item[category] === type;
                    const barColor = isCurrent ? 'bg-blue-500' : 'bg-gray-300';
                    
                    html += `
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="${isCurrent ? 'font-bold text-blue-600' : ''}">
                                    ${item[category]}
                                    ${isCurrent ? ' (Risiko ini)' : ''}
                                </span>
                                <span>${item.count} (${itemPercentage}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="${barColor} h-2 rounded-full" style="width: ${itemPercentage}%"></div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div></div>';
                
                // Comparison info
                html += `
                    <div class="p-4 bg-yellow-50 rounded-lg">
                        <div class="text-sm font-medium mb-2">Informasi:</div>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• ${type} merupakan ${percentage}% dari total ${categoryName}</li>
                            <li>• Ada ${currentType.count} risiko dengan ${categoryName} ini</li>
                            <li>• Dari ${totalCount} total identifikasi</li>
                        </ul>
                    </div>
                `;
            } else {
                html += `
                    <div class="text-center py-8">
                        <i data-feather="alert-circle" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                        <p class="text-gray-500">Data tidak ditemukan untuk ${type}</p>
                    </div>
                `;
            }
            
            html += '</div>';
            modalContent.innerHTML = html;
            feather.replace();
        })
        .catch(error => {
            console.error('Error loading type stats:', error);
            modalContent.innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <i data-feather="alert-octagon" class="w-12 h-12 mx-auto mb-3"></i>
                    <p>Gagal memuat data statistik</p>
                </div>
            `;
            feather.replace();
        });
}

// Close stats modal
function closeStatsModal() {
    const modal = document.getElementById('stats-modal');
    modal.style.display = 'none';
    modal.classList.remove('modal--show');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('stats-modal');
    if (e.target === modal) {
        closeStatsModal();
    }
});
</script>

<style>
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.modal--show {
    display: flex;
}

.modal__content {
    background-color: white;
    border-radius: 0.5rem;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal__content--lg {
    max-width: 600px;
}

.spinner-border {
    display: inline-block;
    width: 2rem;
    height: 2rem;
    border: 0.25em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-border .75s linear infinite;
}

@keyframes spinner-border {
    to { transform: rotate(360deg); }
}
</style>
@endpush