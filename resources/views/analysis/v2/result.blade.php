<x-layouts.app title="Hasil Analisis V2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">
        
        <div class="bg-gradient-to-r from-purple-900 to-indigo-900 text-white rounded-[2.5rem] p-8 shadow-xl mb-8 flex justify-between items-center relative overflow-hidden">
            <div class="relative z-10">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-[0.6rem] font-black uppercase tracking-widest mb-3">
                    Hasil Analisis Versi 2
                </span>
                <h1 class="text-2xl font-black uppercase tracking-tight">{{ $analysis->title }}</h1>
            </div>
        </div>

        {{-- Graph Network Section --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-8 relative overflow-hidden">
            <h2 class="text-lg font-black uppercase tracking-widest text-gray-900 mb-4 flex items-center gap-2">
                <i data-lucide="network" class="w-5 h-5 text-purple-600"></i> Peta Interaksi Wacana (Node & Edge)
            </h2>
            <p class="text-sm text-gray-500 font-medium mb-6">
                Visualisasi ini dihasilkan secara real-time oleh Sistem AI Pintar. Titik (Node) mewakili peran pembicara, Garis (Edge) mewakili tindak tutur dominan dan relasi antar pembicara.
            </p>
            
            <div id="graph-container" class="w-full bg-gray-50/50 rounded-2xl border border-gray-100" style="height: 500px;"></div>
        </div>

        {{-- Transkripsi Section --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-black uppercase tracking-widest text-gray-900 mb-6 flex items-center gap-2">
                <i data-lucide="message-square" class="w-5 h-5 text-indigo-600"></i> Transkripsi Detail
            </h2>

            <div class="space-y-4">
                @php
                    $transcription = $analysis->result_data['transcription'] ?? [];
                @endphp

                @forelse ($transcription as $item)
                    <div class="p-4 rounded-2xl border border-gray-100 {{ ($item['speaker'] ?? '') === 'Dosen' ? 'bg-purple-50/30' : 'bg-gray-50/50' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-black uppercase tracking-widest {{ ($item['speaker'] ?? '') === 'Dosen' ? 'text-purple-700' : 'text-gray-600' }}">
                                {{ $item['speaker'] ?? 'Unknown' }}
                            </span>
                        </div>
                        <p class="text-gray-900 font-medium leading-relaxed text-sm mb-3">
                            {{ $item['text'] ?? '' }}
                        </p>
                        
                        <div class="flex flex-wrap gap-2 mt-2">
                            @if(!empty($item['advice_giving']))
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-[0.6rem] font-bold uppercase tracking-wider">
                                    Advice: {{ str_replace('_', ' ', $item['advice_giving']) }}
                                </span>
                            @endif
                            @if(!empty($item['modes_of_interaction']))
                                <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-[0.6rem] font-bold uppercase tracking-wider">
                                    Mode: {{ str_replace('_', ' ', $item['modes_of_interaction']) }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center p-8 text-gray-500 font-medium">Belum ada data transkripsi.</div>
                @endforelse
            </div>
        </div>
    </div>

    <x-slot name="scripts">
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var chart = echarts.init(document.getElementById('graph-container'));
            
            // Mengambil struktur data graf dari backend (atau mockup jika kosong)
            var rawGraphData = @json($analysis->graph_data);
            
            // Mockup data fallback jika graph_data kosong (untuk keperluan testing UI)
            if (!rawGraphData || !rawGraphData.nodes) {
                rawGraphData = {
                    nodes: [
                        {id: '0', name: 'Dosen', symbolSize: 50, itemStyle: {color: '#9333ea'}},
                        {id: '1', name: 'Mahasiswa', symbolSize: 40, itemStyle: {color: '#6b7280'}},
                        {id: '2', name: 'Arahan Eksplisit', symbolSize: 30, itemStyle: {color: '#10b981'}},
                        {id: '3', name: 'Power Over', symbolSize: 30, itemStyle: {color: '#f59e0b'}}
                    ],
                    edges: [
                        {source: '0', target: '1', value: 'Bimbingan', lineStyle: {width: 2}},
                        {source: '0', target: '2', value: 'Advice', lineStyle: {width: 2}},
                        {source: '0', target: '3', value: 'Mode', lineStyle: {width: 2}}
                    ]
                };
            } else {
                // Formatting data dari DB agar cocok dengan struktur ECharts
                rawGraphData.nodes = rawGraphData.nodes.map(n => ({
                    id: n.id, 
                    name: n.label, 
                    symbolSize: n.group === 'speaker' ? 50 : 30,
                    itemStyle: { color: n.id === 'Dosen' ? '#9333ea' : (n.id === 'Mhs' ? '#6b7280' : '#3b82f6') }
                }));
                rawGraphData.edges = rawGraphData.edges.map(e => ({
                    source: e.from, 
                    target: e.to, 
                    value: e.label,
                    label: { show: true, formatter: e.label }
                }));
            }

            var option = {
                tooltip: {},
                animationDurationUpdate: 1500,
                animationEasingUpdate: 'quinticInOut',
                series: [{
                    type: 'graph',
                    layout: 'force',
                    roam: true, // Allow zoom & drag
                    label: { show: true, position: 'right', fontWeight: 'bold' },
                    edgeSymbol: ['circle', 'arrow'],
                    edgeSymbolSize: [4, 10],
                    edgeLabel: { fontSize: 10 },
                    data: rawGraphData.nodes,
                    links: rawGraphData.edges,
                    force: {
                        repulsion: 400,
                        edgeLength: [50, 150]
                    },
                    lineStyle: {
                        color: 'source',
                        curveness: 0.2
                    }
                }]
            };
            chart.setOption(option);
            
            // Resize chart on window resize
            window.addEventListener('resize', function() {
                chart.resize();
            });
        });
    </script>
    </x-slot>
</x-layouts.app>
