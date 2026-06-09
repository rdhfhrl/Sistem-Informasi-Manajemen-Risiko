<div class="overflow-x-auto">
    <table class="border-2 border-black border-collapse text-center w-full">
        <thead>
            <tr>
                <th rowspan="2" class="bg-slate-100 border-2 border-black p-3 align-middle w-32 font-bold text-lg">
                    Likelihood<br><span class="text-sm font-normal">(Kemungkinan)</span>
                </th>
                <th rowspan="2" class="bg-slate-100 border-2 border-black align-middle w-20 font-bold">Nilai</th>
                <th colspan="5" class="bg-slate-200 border-2 border-black font-bold text-lg">
                    Impact (Dampak)
                </th>
            </tr>
            <tr>
                <th class="border-2 border-black w-32 p-2">
                    <div class="font-bold">1</div>
                    <div class="text-sm font-normal">Insignificant</div>
                </th>
                <th class="border-2 border-black w-32 p-2">
                    <div class="font-bold">2</div>
                    <div class="text-sm font-normal">Minor</div>
                </th>
                <th class="border-2 border-black w-32 p-2">
                    <div class="font-bold">3</div>
                    <div class="text-sm font-normal">Moderate</div>
                </th>
                <th class="border-2 border-black w-32 p-2">
                    <div class="font-bold">4</div>
                    <div class="text-sm font-normal">Major</div>
                </th>
                <th class="border-2 border-black w-32 p-2">
                    <div class="font-bold">5</div>
                    <div class="text-sm font-normal">Catastrophic</div>
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $matrix = [
                    [
                        'label' => 'Certain',
                        'desc' => 'Sangat mungkin terjadi atau hampir selalu muncul',
                        'value' => 5,
                        'cells' => [
                            ['score' => 5, 'level' => 'medium', 'text' => 'Medium'],
                            ['score' => 10, 'level' => 'medium', 'text' => 'Medium'],
                            ['score' => 15, 'level' => 'high', 'text' => 'High'],
                            ['score' => 20, 'level' => 'high', 'text' => 'High'],
                            ['score' => 25, 'level' => 'high', 'text' => 'High'],
                        ]
                    ],
                    [
                        'label' => 'Likely',
                        'desc' => 'Kemungkinan tinggi atau terjadi relatif sering',
                        'value' => 4,
                        'cells' => [
                            ['score' => 4, 'level' => 'low', 'text' => 'Low'],
                            ['score' => 8, 'level' => 'medium', 'text' => 'Medium'],
                            ['score' => 12, 'level' => 'medium', 'text' => 'Medium'],
                            ['score' => 16, 'level' => 'high', 'text' => 'High'],
                            ['score' => 20, 'level' => 'high', 'text' => 'High'],
                        ]
                    ],
                    [
                        'label' => 'Possible',
                        'desc' => 'Cukup sering terjadi dalam proyek',
                        'value' => 3,
                        'cells' => [
                            ['score' => 3, 'level' => 'low', 'text' => 'Low'],
                            ['score' => 6, 'level' => 'medium', 'text' => 'Medium'],
                            ['score' => 9, 'level' => 'medium', 'text' => 'Medium'],
                            ['score' => 12, 'level' => 'medium', 'text' => 'Medium'],
                            ['score' => 15, 'level' => 'high', 'text' => 'High'],
                        ]
                    ],
                    [
                        'label' => 'Unlikely',
                        'desc' => 'Jarang terjadi atau dalam jangka waktu tertentu',
                        'value' => 2,
                        'cells' => [
                            ['score' => 2, 'level' => 'low', 'text' => 'Low'],
                            ['score' => 4, 'level' => 'low', 'text' => 'Low'],
                            ['score' => 6, 'level' => 'medium', 'text' => 'Medium'],
                            ['score' => 8, 'level' => 'medium', 'text' => 'Medium'],
                            ['score' => 10, 'level' => 'medium', 'text' => 'Medium'],
                        ]
                    ],
                    [
                        'label' => 'Rare',
                        'desc' => 'Sangat jarang terjadi atau sesekali',
                        'value' => 1,
                        'cells' => [
                            ['score' => 1, 'level' => 'low', 'text' => 'Low'],
                            ['score' => 2, 'level' => 'low', 'text' => 'Low'],
                            ['score' => 3, 'level' => 'low', 'text' => 'Low'],
                            ['score' => 4, 'level' => 'low', 'text' => 'Low'],
                            ['score' => 5, 'level' => 'medium', 'text' => 'Medium'],
                        ]
                    ],
                ];
            @endphp
            
            @foreach($matrix as $row)
            <tr>
                <td class="border-2 border-black p-3 bg-slate-50 font-medium">
                    <div class="font-bold">{{ $row['label'] }}</div>
                    <div class="text-xs text-gray-600 mt-1">{{ $row['desc'] }}</div>
                </td>
                <td class="border-2 border-black bg-slate-50 font-bold text-lg">{{ $row['value'] }}</td>
                
                @foreach($row['cells'] as $cell)
                <td class="border-2 border-black p-4 font-bold text-lg
                    @if($cell['level'] === 'low') bg-green-100 text-green-800
                    @elseif($cell['level'] === 'medium') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800 @endif">
                    <div>{{ $cell['text'] }}</div>
                    <div class="text-sm font-normal mt-1">({{ $cell['score'] }})</div>
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="mt-8 grid grid-cols-3 gap-4">
        <div class="text-center">
            <div class="w-full h-8 bg-green-100 border-2 border-green-500 rounded mb-2"></div>
            <div class="font-bold text-green-700">LOW RISK</div>
            <div class="text-sm text-gray-600">(Score: 1-5)</div>
            <div class="text-xs text-gray-500 mt-1">Dapat diterima, monitoring rutin</div>
        </div>
        <div class="text-center">
            <div class="w-full h-8 bg-yellow-100 border-2 border-yellow-500 rounded mb-2"></div>
            <div class="font-bold text-yellow-700">MEDIUM RISK</div>
            <div class="text-sm text-gray-600">(Score: 6-15)</div>
            <div class="text-xs text-gray-500 mt-1">Perlu mitigasi dan pengawasan</div>
        </div>
        <div class="text-center">
            <div class="w-full h-8 bg-red-100 border-2 border-red-500 rounded mb-2"></div>
            <div class="font-bold text-red-700">HIGH RISK</div>
            <div class="text-sm text-gray-600">(Score: 16-25)</div>
            <div class="text-xs text-gray-500 mt-1">Perlu tindakan segera dan prioritas</div>
        </div>
    </div>
    
    <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h4 class="font-bold text-blue-800 mb-2 flex items-center">
            <i data-feather="info" class="w-5 h-5 mr-2"></i> Keterangan:
        </h4>
        <p class="text-sm text-blue-700">
            <strong>Rumus: Risk Score = Likelihood × Impact</strong><br>
            • Likelihood: Kemungkinan terjadinya risiko (1-5)<br>
            • Impact: Dampak jika risiko terjadi (1-5)<br>
            • Risk Score: Hasil perkalian likelihood dan impact (1-25)<br>
            • Risk Level: Dikategorikan berdasarkan score (Low/Medium/High)
        </p>
    </div>
</div>