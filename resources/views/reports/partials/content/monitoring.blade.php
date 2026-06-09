<div class="report-content">
    @php
        $data = $data ?? [];
        $metadata = $data['metadata'] ?? [];
        $summary = $data['summary'] ?? [];
        $mitigationEffectiveness = $data['mitigation_effectiveness'] ?? [];
        $monitoringActivities = $data['monitoring_activities'] ?? [];
        $topRisks = $data['top_risks'] ?? [];
        $trendAnalysis = $data['trend_analysis'] ?? [];
    @endphp

    <!-- Summary Section -->
    <div class="mb-8">
        <h4 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
            <i data-feather="pie-chart" class="w-5 h-5 inline mr-2 text-blue-500"></i>
            Ringkasan
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                <div class="text-sm text-gray-600 mb-2">Total Risiko</div>
                <div class="text-3xl font-bold text-blue-600">{{ $summary['total_risks'] ?? 0 }}</div>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                <div class="text-sm text-gray-600 mb-2">Skor Rata-rata</div>
                <div class="text-3xl font-bold text-green-600">{{ number_format($summary['average_risk_score'] ?? 0, 2) }}</div>
            </div>
            
            <div class="bg-orange-50 p-4 rounded-lg border border-orange-100">
                <div class="text-sm text-gray-600 mb-2">Mitigasi Aktif</div>
                <div class="text-3xl font-bold text-orange-600">{{ $mitigationEffectiveness['total_mitigations'] ?? 0 }}</div>
            </div>
            
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                <div class="text-sm text-gray-600 mb-2">Pemantauan</div>
                <div class="text-3xl font-bold text-purple-600">{{ $monitoringActivities['total_monitorings'] ?? 0 }}</div>
            </div>
        </div>
        
        <!-- Risk Distribution -->
        @if(isset($summary['risk_distribution']) && !empty($summary['risk_distribution']))
        <div class="mb-6">
            <h5 class="font-medium text-gray-700 mb-3">Distribusi Level Risiko</h5>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                @foreach($summary['risk_distribution'] as $level => $count)
                    @php
                        $color = match($level) {
                            'sangat_tinggi' => 'bg-red-100 text-red-800',
                            'tinggi' => 'bg-orange-100 text-orange-800',
                            'sedang' => 'bg-yellow-100 text-yellow-800',
                            'rendah' => 'bg-blue-100 text-blue-800',
                            'sangat_rendah' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                        
                        $label = match($level) {
                            'sangat_tinggi' => 'Sangat Tinggi',
                            'tinggi' => 'Tinggi',
                            'sedang' => 'Sedang',
                            'rendah' => 'Rendah',
                            'sangat_rendah' => 'Sangat Rendah',
                            default => $level
                        };
                    @endphp
                    
                    <div class="p-3 rounded-lg border {{ $color }}">
                        <div class="font-bold text-xl">{{ $count }}</div>
                        <div class="text-sm">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    
    <!-- Mitigation Effectiveness -->
    @if(isset($mitigationEffectiveness['completion_rate']))
    <div class="mb-8">
        <h4 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
            <i data-feather="trending-up" class="w-5 h-5 inline mr-2 text-green-500"></i>
            Efektivitas Mitigasi
        </h4>
        
        <div class="bg-gradient-to-r from-green-50 to-green-100 p-6 rounded-lg mb-4">
            <div class="text-center">
                <div class="text-5xl font-bold text-green-600 mb-2">{{ number_format($mitigationEffectiveness['completion_rate'], 1) }}%</div>
                <div class="text-gray-600">Completion Rate</div>
            </div>
        </div>
        
        @if(isset($mitigationEffectiveness['mitigations_by_status']) && !empty($mitigationEffectiveness['mitigations_by_status']))
        <div class="mb-4">
            <h5 class="font-medium text-gray-700 mb-3">Mitigasi Berdasarkan Status</h5>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($mitigationEffectiveness['mitigations_by_status'] as $status => $count)
                    <div class="p-3 bg-white rounded-lg border">
                        <div class="font-bold text-xl">{{ $count }}</div>
                        <div class="text-sm text-gray-600">{{ $status }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif
    
    <!-- Top Risks -->
    @if(isset($topRisks) && count($topRisks) > 0)
    <div class="mb-8">
        <h4 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
            <i data-feather="alert-triangle" class="w-5 h-5 inline mr-2 text-red-500"></i>
            Top 10 Risiko Tertinggi
        </h4>
        
        <div class="overflow-x-auto">
            <table class="table table-bordered w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="whitespace-nowrap">Kode Risiko</th>
                        <th class="whitespace-nowrap">Deskripsi</th>
                        <th class="whitespace-nowrap">Level</th>
                        <th class="whitespace-nowrap">Skor</th>
                        <th class="whitespace-nowrap">Organisasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topRisks as $risk)
                        @php
                            // Jika $risk adalah array, gunakan array access
                            // Jika $risk adalah object, gunakan object access
                            $riskCode = is_array($risk) ? ($risk['risk_code'] ?? 'N/A') : ($risk->risk_code ?? 'N/A');
                            $riskDescription = is_array($risk) ? ($risk['risk_description'] ?? 'N/A') : ($risk->risk_description ?? 'N/A');
                            $riskLevel = is_array($risk) ? ($risk['risk_level'] ?? 'N/A') : ($risk->risk_level ?? 'N/A');
                            $riskScore = is_array($risk) ? ($risk['risk_score'] ?? 0) : ($risk->risk_score ?? 0);
                            $organizationName = is_array($risk) ? 
                                ($risk['organization']['organization_name'] ?? ($risk['organization_name'] ?? 'N/A')) : 
                                ($risk->organization->organization_name ?? 'N/A');
                        @endphp
                        
                        <tr class="hover:bg-gray-50">
                            <td class="font-medium">{{ $riskCode }}</td>
                            <td>{{ Str::limit($riskDescription, 40) }}</td>
                            <td>
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    @if($riskLevel == 'sangat_tinggi') bg-red-100 text-red-800
                                    @elseif($riskLevel == 'tinggi') bg-orange-100 text-orange-800
                                    @elseif($riskLevel == 'sedang') bg-yellow-100 text-yellow-800
                                    @elseif($riskLevel == 'rendah') bg-blue-100 text-blue-800
                                    @elseif($riskLevel == 'sangat_rendah') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $riskLevel }}
                                </span>
                            </td>
                            <td class="font-bold">{{ number_format($riskScore, 2) }}</td>
                            <td>{{ $organizationName }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    
    <!-- Recent Monitorings -->
    @if(isset($monitoringActivities['recent_monitorings']) && count($monitoringActivities['recent_monitorings']) > 0)
    <div class="mb-8">
        <h4 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
            <i data-feather="calendar" class="w-5 h-5 inline mr-2 text-purple-500"></i>
            Aktivitas Pemantauan Terbaru
        </h4>
        
        <div class="space-y-3">
            @foreach($monitoringActivities['recent_monitorings'] as $monitoring)
                <div class="flex items-center p-3 bg-white rounded-lg border hover:bg-gray-50">
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                        <i data-feather="activity" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium">{{ $monitoring['risk_code'] }}</div>
                        <div class="text-sm text-gray-600">{{ $monitoring['result'] }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $monitoring['date'] }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold {{ $monitoring['score'] > 3.0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ number_format($monitoring['score'], 2) }}
                        </div>
                        <div class="text-xs text-gray-500">Skor</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Trend Analysis -->
    @if(isset($trendAnalysis) && !empty($trendAnalysis))
    <div>
        <h4 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
            <i data-feather="trending-up" class="w-5 h-5 inline mr-2 text-blue-500"></i>
            Analisis Tren
        </h4>
        
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-lg">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 mb-4">
                    {{ $trendAnalysis['overall_trend'] ?? 'Stabil' }}
                </div>
                <div class="text-gray-600">
                    @if(isset($trendAnalysis['key_findings']))
                        <ul class="text-left space-y-2">
                            @foreach($trendAnalysis['key_findings'] as $finding)
                                <li class="flex items-start">
                                    <i data-feather="{{ $finding['positive'] ? 'arrow-up' : 'arrow-down' }}" 
                                       class="w-4 h-4 {{ $finding['positive'] ? 'text-green-500' : 'text-red-500' }} mr-2 mt-0.5"></i>
                                    <span>{{ $finding['description'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush