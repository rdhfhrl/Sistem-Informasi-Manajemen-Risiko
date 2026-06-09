<div class="intro-y box mt-5">
    <div class="flex items-center p-5 border-b border-slate-200/60">
        <h2 class="font-medium text-base mr-auto">Akses Cepat</h2>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Identifikasi Risiko -->
            <a href="{{ route('risk-identifications.create') }}" 
               class="flex flex-col items-center justify-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-blue-100 mb-3">
                    <i data-feather="search" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="font-medium text-blue-800">Identifikasi Risiko</span>
                <span class="text-xs text-blue-600 mt-1">Tambah risiko baru</span>
            </a>
            
            <!-- Analisis Risiko -->
            <a href="{{ route('risk-analyses.index') }}" 
               class="flex flex-col items-center justify-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-purple-100 mb-3">
                    <i data-feather="bar-chart-2" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="font-medium text-purple-800">Analisis Risiko</span>
                <span class="text-xs text-purple-600 mt-1">Evaluasi dampak risiko</span>
            </a>
            
            <!-- Mitigasi Risiko -->
            <a href="{{ route('risk-mitigations.index') }}" 
               class="flex flex-col items-center justify-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-100 mb-3">
                    <i data-feather="shield" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="font-medium text-green-800">Mitigasi Risiko</span>
                <span class="text-xs text-green-600 mt-1">Rencana penanganan</span>
            </a>
            
            <!-- Monitoring Risiko -->
            <a href="{{ route('risk-monitorings.index') }}" 
               class="flex flex-col items-center justify-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-orange-100 mb-3">
                    <i data-feather="eye" class="w-6 h-6 text-orange-600"></i>
                </div>
                <span class="font-medium text-orange-800">Monitoring</span>
                <span class="text-xs text-orange-600 mt-1">Pantau perkembangan</span>
            </a>
            
            <!-- Laporan -->
            <a href="{{ route('reports.create') }}" 
               class="flex flex-col items-center justify-center p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-indigo-100 mb-3">
                    <i data-feather="file-text" class="w-6 h-6 text-indigo-600"></i>
                </div>
                <span class="font-medium text-indigo-800">Buat Laporan</span>
                <span class="text-xs text-indigo-600 mt-1">Laporan periodik</span>
            </a>
            
            <!-- Proyek Saya -->
            <a href="{{ route('projects.index') }}" 
               class="flex flex-col items-center justify-center p-4 bg-cyan-50 hover:bg-cyan-100 rounded-lg transition-colors">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-cyan-100 mb-3">
                    <i data-feather="briefcase" class="w-6 h-6 text-cyan-600"></i>
                </div>
                <span class="font-medium text-cyan-800">Proyek Saya</span>
                <span class="text-xs text-cyan-600 mt-1">Kelola proyek</span>
            </a>
            
            <!-- Risiko Saya -->
            <a href="{{ route('risks.index') }}" 
               class="flex flex-col items-center justify-center p-4 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-red-100 mb-3">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-red-600"></i>
                </div>
                <span class="font-medium text-red-800">Risiko Saya</span>
                <span class="text-xs text-red-600 mt-1">Semua risiko</span>
            </a>
            
            <!-- Evaluasi Risiko -->
            <a href="{{ route('risk-evaluations.index') }}" 
               class="flex flex-col items-center justify-center p-4 bg-pink-50 hover:bg-pink-100 rounded-lg transition-colors">
                <div class="w-12 h-12 flex items-center justify-center rounded-full bg-pink-100 mb-3">
                    <i data-feather="star" class="w-6 h-6 text-pink-600"></i>
                </div>
                <span class="font-medium text-pink-800">Evaluasi</span>
                <span class="text-xs text-pink-600 mt-1">Review risiko</span>
            </a>
        </div>
    </div>
</div>