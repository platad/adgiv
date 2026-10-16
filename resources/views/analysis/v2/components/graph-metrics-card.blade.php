@php
    $gSummary = $graphMetrics['summary'] ?? [];
    $degreeList = array_slice($graphMetrics['degree_centrality'] ?? [], 0, 5);
    $betweennessList = array_slice($graphMetrics['betweenness_centrality'] ?? [], 0, 5);
    $densityPct = $gSummary['density_percentage'] ?? 0;
    $reciprocityPct = $gSummary['reciprocity_percentage'] ?? 0;
    $charText = $gSummary['interaction_character'] ?? 'Dialogis Seimbang';
    $dominantActor = $gSummary['dominant_actor']['name'] ?? 'Dosen';
    $topBridge = $gSummary['top_bridge']['name'] ?? 'Bimbingan Metodologi';
@endphp

<div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-5 space-y-5" x-data="{ activeMetricTab: 'overview', showFormulaModal: false }">
    {{-- Header Section --}}
    <div class="flex flex-col gap-3 pb-4 border-b border-gray-100">
        <div class="flex items-start justify-between gap-2">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="calculator" class="w-4 h-4"></i>
                </div>
                <div>
                    <span class="text-[0.6rem] font-black uppercase tracking-widest text-emerald-600 block">Network Science</span>
                    <h3 class="text-xs sm:text-sm font-black uppercase text-gray-900 leading-tight">
                        Sentralitas Graf & Topologi
                    </h3>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[0.6rem] font-bold border border-emerald-200 shrink-0">
                <i data-lucide="check-circle" class="w-3 h-3 text-emerald-600"></i>
                <span>Empiris</span>
            </span>
        </div>

        <p class="text-[0.72rem] text-gray-500 font-medium leading-relaxed">
            Kajian matematis struktur jejaring interaksi: mengukur sentralitas aktor, simpul jembatan (<em class="font-serif">betweenness bridge</em>), dan indeks timbal balik dialogis (<em class="font-serif">reciprocity</em>).
        </p>

        <div>
            <button type="button" @click="showFormulaModal = true"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold transition border border-purple-200 shadow-2xs active:scale-95 cursor-pointer">
                <i data-lucide="file-text" class="w-3.5 h-3.5 text-purple-600"></i>
                <span>Lihat Formula Matematis</span>
            </button>
        </div>
    </div>

    {{-- 4 Stat Pillar Cards (Responsive 2 Columns in Sidebar, 4 Columns on Wide Screen) --}}
    <div class="grid grid-cols-2 gap-3">
        {{-- Card 1: Reciprocity Index --}}
        <div class="bg-gray-50/90 rounded-2xl p-3 sm:p-3.5 border border-gray-200/70 relative overflow-hidden group hover:bg-white hover:border-emerald-200 hover:shadow-xs transition flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[0.6rem] font-black uppercase tracking-wider text-gray-400">Timbal Balik</span>
                    <span class="w-5 h-5 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center text-[0.65rem] font-bold">R</span>
                </div>
                <div class="text-xl sm:text-2xl font-black text-gray-900 mb-0.5">
                    {{ $reciprocityPct }}%
                </div>
                <div class="text-[0.65rem] font-bold text-emerald-700 leading-tight">
                    {{ $charText }}
                </div>
            </div>
            <div class="w-full bg-gray-200 h-1.5 rounded-full mt-2.5 overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ min(100, $reciprocityPct) }}%"></div>
            </div>
        </div>

        {{-- Card 2: Dominant Actor --}}
        <div class="bg-gray-50/90 rounded-2xl p-3 sm:p-3.5 border border-gray-200/70 relative overflow-hidden group hover:bg-white hover:border-purple-200 hover:shadow-xs transition flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[0.6rem] font-black uppercase tracking-wider text-gray-400">Aktor Sentral</span>
                    <span class="w-5 h-5 rounded-md bg-purple-100 text-purple-700 flex items-center justify-center text-[0.65rem] font-bold">C_D</span>
                </div>
                <div class="text-sm sm:text-base font-black text-purple-950 truncate mb-0.5" title="{{ $dominantActor }}">
                    {{ $dominantActor }}
                </div>
                <div class="text-[0.62rem] text-gray-500 leading-tight">
                    Skor: <strong class="text-purple-700 font-bold">{{ $gSummary['dominant_actor']['score'] ?? 0 }}</strong>
                </div>
            </div>
            <div class="mt-2.5 flex items-center gap-1 text-[0.6rem] text-gray-400 font-medium truncate">
                <i data-lucide="arrow-right-left" class="w-3 h-3 text-purple-500 shrink-0"></i>
                <span class="truncate">{{ $gSummary['dominant_actor']['total_degree'] ?? 0 }} Ujaran</span>
            </div>
        </div>

        {{-- Card 3: Top Thematic Bridge (Betweenness) --}}
        <div class="bg-gray-50/90 rounded-2xl p-3 sm:p-3.5 border border-gray-200/70 relative overflow-hidden group hover:bg-white hover:border-indigo-200 hover:shadow-xs transition flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[0.6rem] font-black uppercase tracking-wider text-gray-400">Jembatan Topik</span>
                    <span class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-700 flex items-center justify-center text-[0.65rem] font-bold">C_B</span>
                </div>
                <div class="text-sm sm:text-base font-black text-indigo-950 truncate mb-0.5" title="{{ $topBridge }}">
                    {{ $topBridge }}
                </div>
                <div class="text-[0.62rem] text-gray-500 leading-tight">
                    Skor: <strong class="text-indigo-700 font-bold">{{ $gSummary['top_bridge']['score'] ?? 0 }}</strong>
                </div>
            </div>
            <div class="mt-2.5 flex items-center gap-1 text-[0.6rem] text-gray-400 font-medium truncate">
                <i data-lucide="git-merge" class="w-3 h-3 text-indigo-500 shrink-0"></i>
                <span class="truncate">Lintas Bahasan</span>
            </div>
        </div>

        {{-- Card 4: Graph Density --}}
        <div class="bg-gray-50/90 rounded-2xl p-3 sm:p-3.5 border border-gray-200/70 relative overflow-hidden group hover:bg-white hover:border-blue-200 hover:shadow-xs transition flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[0.6rem] font-black uppercase tracking-wider text-gray-400">Kerapatan (Density)</span>
                    <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-[0.65rem] font-bold">D</span>
                </div>
                <div class="text-xl sm:text-2xl font-black text-gray-900 mb-0.5">
                    {{ $densityPct }}%
                </div>
                <div class="text-[0.62rem] text-gray-500 leading-tight truncate">
                    {{ $gSummary['unique_edges_count'] ?? 0 }} Jalur / {{ $gSummary['node_count'] ?? 0 }} Simpul
                </div>
            </div>
            <div class="w-full bg-gray-200 h-1.5 rounded-full mt-2.5 overflow-hidden">
                <div class="bg-blue-500 h-full rounded-full" style="width: {{ min(100, $densityPct) }}%"></div>
            </div>
        </div>
    </div>

    {{-- Tabs & Tabular Breakdown Table --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 overflow-hidden shadow-2xs">
        <div class="border-b border-gray-200 bg-gray-50/70 p-2 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-1.5">
            <div class="grid grid-cols-2 gap-1 w-full text-xs font-bold">
                <button type="button" @click="activeMetricTab = 'overview'"
                    class="px-2.5 py-1.5 rounded-xl transition cursor-pointer text-center truncate text-[0.65rem]"
                    :class="activeMetricTab === 'overview' ? 'bg-white text-purple-700 shadow-xs font-black' : 'text-gray-500 hover:text-gray-900'">
                    Sentralitas Derajat
                </button>
                <button type="button" @click="activeMetricTab = 'betweenness'"
                    class="px-2.5 py-1.5 rounded-xl transition cursor-pointer text-center truncate text-[0.65rem]"
                    :class="activeMetricTab === 'betweenness' ? 'bg-white text-indigo-700 shadow-xs font-black' : 'text-gray-500 hover:text-gray-900'">
                    Simpul Jembatan
                </button>
            </div>
            <div class="mt-1 text-center sm:text-right w-full">
                <span class="text-[0.62rem] text-gray-400 font-semibold italic">
                    *Dihitung dinamis dari matriks relasi topologi ujaran
                </span>
            </div>
        </div>

        {{-- Tab 1: Degree Centrality Table --}}
        <div x-show="activeMetricTab === 'overview'" class="p-4 overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-[0.65rem] font-black uppercase text-gray-400 tracking-wider">
                        <th class="pb-2.5">Entitas / Aktor Wacana</th>
                        <th class="pb-2.5 text-center">In-Degree (Masuk)</th>
                        <th class="pb-2.5 text-center">Out-Degree (Keluar)</th>
                        <th class="pb-2.5 text-center">Total Tuturan</th>
                        <th class="pb-2.5 text-right">Skor Normalisasi ($C_D$)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($degreeList as $dItem)
                        @php
                            $dName = $dItem['name'] ?? $dItem['id'];
                            $isDosenEntity = (stripos($dName, 'dosen') !== false || stripos($dName, 'supervisor') !== false);
                            $isMhsEntity = (stripos($dName, 'mahasiswa') !== false || stripos($dName, 'student') !== false);
                        @endphp
                        <tr class="hover:bg-purple-50/30 transition">
                            <td class="py-2.5 font-bold text-gray-900 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $isDosenEntity ? 'bg-purple-600' : ($isMhsEntity ? 'bg-blue-600' : 'bg-stone-400') }}"></span>
                                <span>{{ $dName }}</span>
                            </td>
                            <td class="py-2.5 text-center text-gray-600 font-medium">{{ $dItem['in_degree'] }}</td>
                            <td class="py-2.5 text-center text-gray-600 font-medium">{{ $dItem['out_degree'] }}</td>
                            <td class="py-2.5 text-center font-bold text-gray-800">{{ $dItem['total_degree'] }}</td>
                            <td class="py-2.5 text-right font-black text-purple-700 font-mono">{{ number_format($dItem['score'], 4) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-400 italic">Belum ada metrik yang dihitung.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tab 2: Betweenness Centrality Table --}}
        <div x-show="activeMetricTab === 'betweenness'" style="display: none;" class="p-4 overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 text-[0.65rem] font-black uppercase text-gray-400 tracking-wider">
                        <th class="pb-2.5">Simpul / Topik Penghubung</th>
                        <th class="pb-2.5 text-center">Skor Mentah (Lintasan Singkat)</th>
                        <th class="pb-2.5 text-right">Skor Normalisasi ($C_B$)</th>
                        <th class="pb-2.5 text-right">Peran Wacana</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($betweennessList as $bItem)
                        @php
                            $bName = $bItem['name'] ?? $bItem['id'];
                        @endphp
                        <tr class="hover:bg-indigo-50/30 transition">
                            <td class="py-2.5 font-bold text-gray-900 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                <span>{{ $bName }}</span>
                            </td>
                            <td class="py-2.5 text-center text-gray-600 font-medium">{{ $bItem['raw_score'] }}</td>
                            <td class="py-2.5 text-right font-black text-indigo-700 font-mono">{{ number_format($bItem['score'], 4) }}</td>
                            <td class="py-2.5 text-right">
                                <span class="px-2 py-0.5 rounded-md text-[0.62rem] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    Thematic Bridge
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-400 italic">Belum ada metrik betweenness yang dihitung.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Formula Matematis (Bahan Presentasi Sidang & Bab 3 Skripsi) --}}
    <div x-show="showFormulaModal"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
        style="display: none;">
        <div x-show="showFormulaModal" @click="showFormulaModal = false"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

        <div x-show="showFormulaModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-3xl max-w-2xl w-full mx-4 p-6 sm:p-8 shadow-2xl z-10 border border-gray-100 max-h-[85vh] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-200">
            
            <div class="flex items-start justify-between gap-4 mb-4 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <i data-lucide="function-square" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-700 text-[0.62rem] font-black uppercase tracking-wider">Network Science Formula</span>
                        <h3 class="text-base font-black text-gray-900 uppercase tracking-tight mt-0.5">Rumus Matematis Sentralitas Graf</h3>
                    </div>
                </div>
                <button type="button" @click="showFormulaModal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-xl transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <p class="text-xs text-gray-500 font-medium leading-relaxed mb-6">
                Formula komputasi matematis yang digunakan pada sistem untuk membuktikan relasi kuasa dan dominansi wacana bimbingan akademik secara empiris:
            </p>

            <div class="space-y-4 text-xs">
                {{-- Rumus 1: Degree Centrality --}}
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <strong class="text-gray-900 font-bold">1. Degree Centrality ($C_D$)</strong>
                        <span class="text-[0.65rem] font-mono text-purple-700 bg-purple-100 px-2 py-0.5 rounded-md">Sentralitas Derajat</span>
                    </div>
                    <div class="font-mono bg-white p-2.5 rounded-xl border border-gray-200 text-purple-900 font-bold text-center">
                        C_D(v) = (deg_in(v) + deg_out(v)) / (|V| - 1)
                    </div>
                    <p class="text-gray-600 text-[0.7rem] leading-relaxed">
                        Mengukur seberapa aktif aktor memicu atau menerima aliran interaksi dibanding total seluruh entitas dalam jaringan wacana.
                    </p>
                </div>

                {{-- Rumus 2: Betweenness Centrality --}}
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <strong class="text-gray-900 font-bold">2. Betweenness Centrality ($C_B$)</strong>
                        <span class="text-[0.65rem] font-mono text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded-md">Simpul Jembatan</span>
                    </div>
                    <div class="font-mono bg-white p-2.5 rounded-xl border border-gray-200 text-indigo-900 font-bold text-center">
                        C_B(v) = Σ (σ_st(v) / σ_st) / ((|V| - 1)(|V| - 2))
                    </div>
                    <p class="text-gray-600 text-[0.7rem] leading-relaxed">
                        Mengukur frekuensi sebuah topik atau tindakan dilewati sebagai lintasan terpendek saat wacana berpindah dari suatu argumen ke tindak lanjut berikutnya.
                    </p>
                </div>

                {{-- Rumus 3: Reciprocity Index --}}
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <strong class="text-gray-900 font-bold">3. Reciprocity Index ($R$)</strong>
                        <span class="text-[0.65rem] font-mono text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">Derajat Timbal Balik</span>
                    </div>
                    <div class="font-mono bg-white p-2.5 rounded-xl border border-gray-200 text-emerald-900 font-bold text-center">
                        R = (2 * |E_mutual|) / |E|
                    </div>
                    <p class="text-gray-600 text-[0.7rem] leading-relaxed">
                        Mengukur rasio hubungan timbal balik dua arah antara Dosen dan Mahasiswa dibanding komunikasi searah, membuktikan secara kuantitatif apakah interaksi bimbingan bersifat kolaboratif ($R \ge 0.5$) atau direktif dominan ($R < 0.3$).
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end">
                <button type="button" @click="showFormulaModal = false"
                    class="px-5 py-2.5 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-black uppercase tracking-wider transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
