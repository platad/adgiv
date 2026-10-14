<x-layouts.app title="Hasil Analisis V2 (Multi-Task RoBERTa & Graph Network)">
@php
    $transcription = $transcription ?? ($analysis->result_data['transcription'] ?? []);
    $totalSegments = $stats['total_segments'] ?? count($transcription);
    $dosenSegments = $stats['dosen_segments'] ?? 0;
    $mhsSegments = $stats['mhs_segments'] ?? 0;
    $dosenRatio = $stats['dosen_ratio'] ?? 0;
    $mhsRatio = $stats['mhs_ratio'] ?? 0;
    $adviceCounts = $stats['advice_counts'] ?? [];
    $modeCounts = $stats['mode_counts'] ?? [];

    $getAdviceBadgeStyle = function($label) {
        $l = strtolower(trim($label ?? ''));
        switch ($l) {
            case 'arahan_eksplisit':
                return [
                    'bg' => 'bg-red-50 hover:bg-red-100 text-red-700 border-red-200',
                    'badge' => 'Arahan Eksplisit',
                    'color' => '#ef4444',
                    'icon' => '<svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>',
                    'desc' => 'Dosen memberikan instruksi langsung dan target tegas yang mengarahkan mahasiswa untuk melakukan langkah spesifik.'
                ];
            case 'bimbingan_bertahap':
                return [
                    'bg' => 'bg-blue-50 hover:bg-blue-100 text-blue-700 border-blue-200',
                    'badge' => 'Bimbingan Bertahap',
                    'color' => '#3b82f6',
                    'icon' => '<svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>',
                    'desc' => 'Dosen memandu proses penelitian langkah demi langkah secara terstruktur agar mudah dieksekusi oleh mahasiswa.'
                ];
            case 'dukungan_keputusan':
                return [
                    'bg' => 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200',
                    'badge' => 'Dukungan Keputusan',
                    'color' => '#10b981',
                    'icon' => '<svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                    'desc' => 'Dosen mengafirmasi atau menyepakati pilihan metodologi/solusi yang diajukan oleh mahasiswa.'
                ];
            case 'jawaban_tegas':
                return [
                    'bg' => 'bg-amber-50 hover:bg-amber-100 text-amber-700 border-amber-200',
                    'badge' => 'Jawaban Tegas',
                    'color' => '#f59e0b',
                    'icon' => '<svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                    'desc' => 'Dosen memberikan kepastian konseptual atau klarifikasi lugas terhadap pertanyaan krusial mahasiswa.'
                ];
            case 'petunjuk_kontekstual':
                return [
                    'bg' => 'bg-purple-50 hover:bg-purple-100 text-purple-700 border-purple-200',
                    'badge' => 'Petunjuk Kontekstual',
                    'color' => '#8b5cf6',
                    'icon' => '<svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
                    'desc' => 'Dosen memberikan acuan konteks penelitian terdahulu, studi literatur, atau batasan studi yang relevan.'
                ];
            case 'otoritas':
                return [
                    'bg' => 'bg-yellow-50 hover:bg-yellow-100 text-yellow-800 border-yellow-200',
                    'badge' => 'Otoritas Akademik',
                    'color' => '#ca8a04',
                    'icon' => '<svg class="w-3.5 h-3.5 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
                    'desc' => 'Penetapan standar ilmiah berdasarkan keahlian dan wewenang akademik dosen.'
                ];
            default:
                return [
                    'bg' => 'bg-gray-50 hover:bg-gray-100 text-gray-700 border-gray-200',
                    'badge' => ucwords(str_replace('_', ' ', $l)),
                    'color' => '#6b7280',
                    'icon' => '<svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>',
                    'desc' => 'Bimbingan dan penjelasan wacana akademik.'
                ];
        }
    };

    $getModeBadgeStyle = function($label) {
        $l = strtolower(trim($label ?? ''));
        switch ($l) {
            case 'power_over':
                return [
                    'bg' => 'bg-rose-50 hover:bg-rose-100 text-rose-700 border-rose-200',
                    'badge' => 'Power Over',
                    'color' => '#f43f5e',
                    'icon' => '<svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
                    'desc' => 'Relasi di mana Dosen mendominasi arah wacana untuk mengontrol kualitas, mengoreksi kekeliruan, atau menetapkan batasan riset.'
                ];
            case 'power_maintaining':
                return [
                    'bg' => 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border-indigo-200',
                    'badge' => 'Power Maintaining',
                    'color' => '#6366f1',
                    'icon' => '<svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M12 6v12M5 6l-2 9a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2l-2-9M15 6l-2 9a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2l-2-9"/></svg>',
                    'desc' => 'Relasi yang menjaga keseimbangan wacana secara profesional, teratur, dan saling menghormati peran akademik.'
                ];
            case 'power_gaining':
                return [
                    'bg' => 'bg-teal-50 hover:bg-teal-100 text-teal-700 border-teal-200',
                    'badge' => 'Power Gaining',
                    'color' => '#14b8a6',
                    'icon' => '<svg class="w-3.5 h-3.5 text-teal-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>',
                    'desc' => 'Mahasiswa aktif berinisiatif, mempertahankan argumen, atau mengajukan ide baru secara mandiri.'
                ];
            default:
                return [
                    'bg' => 'bg-gray-50 hover:bg-gray-100 text-gray-700 border-gray-200',
                    'badge' => ucwords(str_replace('_', ' ', $l)),
                    'color' => '#6b7280',
                    'icon' => '<svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>',
                    'desc' => 'Pola interaksi wacana bimbingan akademik.'
                ];
        }
    };
@endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 animate-fade-in"
        x-data="v2ResultApp()">

        {{-- Top Breadcrumb & Actions --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-gray-100 pb-8">
            <div>
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest mb-4">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    <span class="lang-id">Kembali ke Dashboard</span>
                    <span class="lang-en">Back to Dashboard</span>
                    <span class="lang-zh">返回控制面板</span>
                </a>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-100 text-purple-700 border border-purple-200 text-[0.65rem] font-black uppercase tracking-widest">
                        <i data-lucide="sparkles" class="w-3 h-3 text-purple-600"></i> Model Versi 2
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-200 text-[0.65rem] font-bold">
                        <i data-lucide="check-circle" class="w-3 h-3 text-green-600"></i> Selesai Diproses
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight uppercase mt-3">
                    {{ $analysis->title }}
                </h1>
                <p class="text-gray-500 font-medium mt-1 text-sm">
                    Analisis Percakapan Bimbingan Akademik: Diarization & Relasi Wacana Graf RoBERTa
                </p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                {{-- Tombol Cetak Laporan (Modal Fitur Dalam Pengembangan) --}}
                <button type="button"
                    @click="showPrintModal = true"
                    class="inline-flex items-center px-4 py-2.5 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-purple-600/20 active:scale-95 cursor-pointer">
                    <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                    <span>Cetak Laporan</span>
                </button>

                {{-- Tombol Tukar Peran Pembicara --}}
                <button type="button"
                    @click="showSwapModal = true"
                    class="inline-flex items-center px-4 py-2.5 rounded-2xl bg-white hover:bg-purple-50 text-purple-700 border border-purple-200 text-xs font-black uppercase tracking-wider transition-all shadow-sm active:scale-95 cursor-pointer">
                    <i data-lucide="arrow-left-right" class="w-4 h-4 mr-2 text-purple-600"></i>
                    <span>Tukar Peran Pembicara</span>
                </button>

                {{-- Tombol Lihat / Tutup Raw --}}
                <button type="button"
                    @click="showRaw = !showRaw"
                    class="inline-flex items-center px-4 py-2.5 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-black uppercase tracking-wider transition-colors cursor-pointer">
                    <i data-lucide="code" class="w-4 h-4 mr-2"></i>
                    <span x-text="showRaw ? 'Tutup Raw' : 'Lihat Raw'"></span>
                </button>
            </div>
        </div>

        {{-- Raw JSON Viewer (Responsif & Rapi Tanpa Overflow) --}}
        <div x-show="showRaw" x-collapse style="display: none;" class="bg-gray-950 rounded-3xl shadow-2xl mb-8 overflow-hidden border border-gray-800">
            <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h3 class="font-bold text-gray-300 uppercase tracking-widest">Raw JSON Output (Versi 2)</h3>
                </div>
                <span class="text-gray-500 font-mono">Multi-Task RoBERTa & Graph Topology</span>
            </div>
            <div class="p-6 overflow-x-auto max-h-[32rem] scrollbar-thin scrollbar-thumb-gray-800">
                <pre class="text-xs font-mono text-emerald-400 leading-relaxed"><code>{{ json_encode($analysis->result_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </div>
        </div>

        {{-- Wrapper Seluruh Konten Hasil Analisis (Disembunyikan saat showRaw aktif agar responsive seperti V1) --}}
        <div x-show="!showRaw">

        {{-- Executive Stat Cards (V1 Style) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <i data-lucide="message-square" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 block mb-0.5">Total Kalimat / Tuturan</span>
                    <span class="text-2xl font-black text-gray-900">{{ $totalSegments }} <span class="text-xs font-semibold text-gray-500">segmen</span></span>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i data-lucide="user-check" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 block mb-0.5">Porsi Dosen</span>
                    <span class="text-2xl font-black text-indigo-900">{{ $dosenSegments }} <span class="text-xs font-semibold text-indigo-500">({{ $dosenRatio }}%)</span></span>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 block mb-0.5">Porsi Mahasiswa</span>
                    <span class="text-2xl font-black text-blue-900">{{ $mhsSegments }} <span class="text-xs font-semibold text-blue-500">({{ $mhsRatio }}%)</span></span>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i data-lucide="network" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 block mb-0.5">Interaksi Wacana</span>
                    <span class="text-2xl font-black text-emerald-900">
                        {{ count($analysis->graph_data['nodes'] ?? []) }} <span class="text-xs font-semibold text-emerald-600">Nodes Aktif</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Audio Player Bar (Integrated) --}}
        @if($analysis->audio_path)
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm mb-8 flex flex-col md:flex-row items-center gap-6">
            <div class="flex items-center gap-4 w-full md:w-auto">
                <button type="button" @click="toggleAudio()"
                    class="w-12 h-12 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white flex items-center justify-center shrink-0 shadow-md shadow-purple-600/30 transition-transform active:scale-95 cursor-pointer">
                    <template x-if="!isPlaying">
                        <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </template>
                    <template x-if="isPlaying">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                    </template>
                </button>
                <div>
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 block">Pemutar Audio Rekaman</span>
                    <p class="text-xs font-bold text-gray-800" x-text="audioTimeDisplay">00:00 / 00:00</p>
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="flex-grow w-full">
                <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden cursor-pointer relative" @click="seekAudio($event)">
                    <div class="bg-purple-600 h-full rounded-full transition-all duration-100" :style="'width: ' + progressPercent + '%'"></div>
                </div>
            </div>

            <audio x-ref="audioPlayer" src="{{ route('analysis.v2.audio', $analysis->slug) }}"
                @timeupdate="onTimeUpdate()" @ended="onEnded()" @loadedmetadata="onLoadedMetadata()" preload="metadata"></audio>
        </div>
        @endif

        {{-- Conversation Dynamics Chart & Agent Analysis (Grafik Aliran Intonasi V1 Parity) --}}
        <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 mb-8 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row gap-8 items-stretch">
                <!-- Left part: The Chart (2/3 width) -->
                <div class="lg:w-2/3 flex flex-col justify-between">
                    <div>
                        <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                            <i data-lucide="activity" class="w-3.5 h-3.5 text-purple-600 animate-pulse"></i> 
                            <span>Grafik Aliran Intonasi Percakapan</span>
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-gray-950 uppercase tracking-wide mb-6">
                            Dinamika Intonasi Per Baris (Pitch Dynamics)
                        </h3>
                    </div>
                    <div class="w-full overflow-x-auto pb-4 scrollbar-thin">
                        <div class="relative min-w-[600px] w-full h-64">
                            <canvas id="dynamicsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Right part: AI Summary of Chart (1/3 width) -->
                <div class="lg:w-1/3 bg-gray-50 border border-gray-100 rounded-2xl p-6 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 text-gray-200/30 pointer-events-none">
                        <i data-lucide="sparkles" class="w-32 h-32"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center p-1.5">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <span class="block text-[0.55rem] font-black text-gray-400 uppercase tracking-widest">Supervisory AI</span>
                                <span class="text-xs font-black text-gray-800 uppercase tracking-wider">
                                    Interpretasi Aliran
                                </span>
                            </div>
                        </div>

                        @php
                            $upCount = 0;
                            $downCount = 0;
                            foreach ($transcription as $block) {
                                $type = $block['intonation_type'] ?? ($block['intonation_markers'][0]['type'] ?? 'neutral');
                                if ($type === 'up') $upCount++;
                                if ($type === 'down') $downCount++;
                            }

                            if ($downCount > $upCount) {
                                $dynamicsSummary = 'Alur percakapan didominasi oleh intonasi menurun (' . $downCount . ' kali) yang menandakan instruksi tegas, korektif, dan arahan bimbingan akademik yang pasti dari Dosen. Hal ini menunjukkan dinamika direktif di mana pembimbing mengontrol wacana untuk memastikan mutu riset mahasiswa.';
                                $dynamicsStatus = 'Direktif & Korektif';
                                $statusColor = 'text-red-700 bg-red-50 border-red-200';
                            } elseif ($upCount > $downCount) {
                                $dynamicsSummary = 'Alur percakapan didominasi oleh intonasi menaik (' . $upCount . ' kali) yang mencerminkan nada tanya, klarifikasi, atau eksplorasi produktif. Ini mencerminkan hubungan dialogis di mana bimbingan berjalan interaktif dan koperatif.';
                                $dynamicsStatus = 'Dialogis & Koperatif';
                                $statusColor = 'text-blue-700 bg-blue-50 border-blue-200';
                            } else {
                                $dynamicsSummary = 'Dinamika percakapan berjalan seimbang antara intonasi turun (penegasan) dan naik (klarifikasi/pertanyaan). Ini mencerminkan keseimbangan relasi bimbingan yang sangat kondusif, interaktif, dan berorientasi pada pemecahan masalah bersama.';
                                $dynamicsStatus = 'Kondusif & Seimbang';
                                $statusColor = 'text-green-700 bg-green-50 border-green-200';
                            }
                        @endphp

                        <p class="text-xs sm:text-sm font-medium text-gray-600 leading-relaxed font-serif mt-4">
                            {{ $dynamicsSummary }}
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-between items-center relative z-10">
                        <span class="text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">
                            Karakter Aliran
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-xl text-[0.65rem] font-black uppercase tracking-widest border {{ $statusColor }}">
                            {{ $dynamicsStatus }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Graph Network Section & Multimodal AI Analysis (Grid Desktop: Kiri Graf, Kanan Interpretasi) --}}
        <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 mb-8 relative overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center">
                            <i data-lucide="network" class="w-4 h-4"></i>
                        </div>
                        <h2 class="text-base sm:text-lg font-black uppercase tracking-tight text-gray-900">
                            Peta Interaksi & Hubungan Wacana (Node & Edge Network)
                        </h2>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-500 font-medium">
                        Visualisasi topologi wacana interaktif dipadukan dengan interpretasi mendalam kecerdasan buatan.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 text-xs font-bold shadow-2xs">
                        <i data-lucide="user-check" class="w-3.5 h-3.5 text-purple-600"></i>
                        <span>Dosen</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold shadow-2xs">
                        <i data-lucide="users" class="w-3.5 h-3.5 text-blue-600"></i>
                        <span>Mahasiswa</span>
                    </span>

                    {{-- Tombol Analisis AI Multimodal (OpenAI Vision) --}}
                    <button type="button" 
                        @click="analyzeGraphWithAI()"
                        :disabled="isInterpretingGraph"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-black uppercase tracking-wider shadow-md shadow-purple-600/20 transition-all hover:scale-105 active:scale-95 disabled:opacity-50 disabled:hover:scale-100 cursor-pointer">
                        <template x-if="!isInterpretingGraph">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="sparkles" class="w-4 h-4 text-purple-200"></i>
                                <span x-text="graphInterpretationData ? 'Analisis Ulang Graf (AI)' : 'Analisis Makna Graf (AI)'"></span>
                            </span>
                        </template>
                        <template x-if="isInterpretingGraph">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                                <span>Menganalisis Graf...</span>
                            </span>
                        </template>
                    </button>
                </div>
            </div>
            
            {{-- Grid Kontainer: Kiri Graf (lg:col-span-1 atau w-1/2), Kanan Teks Hasil Analisis --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                {{-- Kolom Kiri: Peta Graf ECharts (5 atau 6 Kolom di Desktop) --}}
                <div :class="graphInterpretationData ? 'lg:col-span-5 xl:col-span-5' : 'lg:col-span-12'" class="transition-all duration-300">
                    <div id="graph-container" class="w-full bg-gray-50/50 rounded-2xl border border-gray-100" style="height: 520px;"></div>
                </div>

                {{-- Kolom Kanan: Hasil Generate Teks Analisis AI --}}
                <div x-show="graphInterpretationData || isInterpretingGraph" 
                    :class="graphInterpretationData ? 'lg:col-span-7 xl:col-span-7' : 'lg:col-span-12'"
                    class="space-y-4" x-transition>
                    
                    {{-- Loading Skeleton saat Menganalisis --}}
                    <div x-show="isInterpretingGraph" class="bg-purple-50/40 rounded-2xl p-6 border border-purple-100 animate-pulse space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-purple-200"></div>
                            <div class="h-4 bg-purple-200 rounded-lg w-1/2"></div>
                        </div>
                        <div class="h-20 bg-white/80 rounded-xl w-full"></div>
                        <div class="h-24 bg-white/80 rounded-xl w-full"></div>
                        <div class="h-20 bg-white/80 rounded-xl w-full"></div>
                    </div>

                    {{-- Tampilan Konten Interpretasi AI --}}
                    <div x-show="!isInterpretingGraph && graphInterpretationData" class="space-y-4 max-h-[520px] overflow-y-auto pr-1 scrollbar-thin">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-purple-50/60 border border-purple-100 rounded-2xl p-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                </div>
                                <div>
                                    <h3 class="text-xs font-black uppercase tracking-widest text-purple-900 leading-tight">
                                        Interpretasi Multimodal Peta Wacana
                                    </h3>
                                    <p class="text-[0.65rem] font-medium text-purple-700">
                                        Model OpenAI Vision (Topologi Graf + Dinamika Intonasi + Hasil Analisis)
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-white text-purple-700 border border-purple-200 text-[0.6rem] font-bold">
                                    <i data-lucide="check-circle" class="w-2.5 h-2.5 text-emerald-500"></i>
                                    <span>Tersintesis</span>
                                </span>
                                <span class="text-[0.65rem] text-gray-400 font-medium" x-text="graphAnalyzedAt"></span>
                            </div>
                        </div>

                        {{-- Ringkasan Eksekutif --}}
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                            <span class="text-[0.65rem] font-black uppercase tracking-wider text-purple-700 block mb-1 flex items-center gap-1.5">
                                <i data-lucide="compass" class="w-3.5 h-3.5 text-purple-600"></i>
                                <span>Ringkasan Konfigurasi Kekuasaan & Dinamika Bimbingan</span>
                            </span>
                            <p class="text-xs sm:text-sm font-medium text-gray-800 leading-relaxed font-serif" x-text="graphInterpretationData?.ringkasan_eksekutif"></p>
                        </div>

                        {{-- Analisis Topologi --}}
                        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs">
                            <span class="text-[0.65rem] font-black uppercase tracking-wider text-indigo-700 block mb-1 flex items-center gap-1.5">
                                <i data-lucide="share-2" class="w-3.5 h-3.5 text-indigo-600"></i>
                                <span>Analisis Topologi Simpul & Tepi (Edges)</span>
                            </span>
                            <p class="text-xs font-medium text-gray-700 leading-relaxed" x-text="graphInterpretationData?.topologi_relasi"></p>
                        </div>

                        {{-- Korelasi Intonasi --}}
                        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs">
                            <span class="text-[0.65rem] font-black uppercase tracking-wider text-blue-700 block mb-1 flex items-center gap-1.5">
                                <i data-lucide="activity" class="w-3.5 h-3.5 text-blue-600"></i>
                                <span>Korelasi Dinamika Intonasi Per Baris</span>
                            </span>
                            <p class="text-xs font-medium text-gray-700 leading-relaxed" x-text="graphInterpretationData?.korelasi_intonasi"></p>
                        </div>

                        {{-- Makna Pedagogis & Rekomendasi Supervisi --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="bg-emerald-50/50 rounded-2xl p-3.5 border border-emerald-100">
                                <span class="text-[0.62rem] font-black uppercase tracking-wider text-emerald-800 block mb-1 flex items-center gap-1">
                                    <i data-lucide="book-open" class="w-3 h-3 text-emerald-600"></i>
                                    <span>Makna Pedagogis Riset</span>
                                </span>
                                <p class="text-xs font-medium text-emerald-950 leading-relaxed" x-text="graphInterpretationData?.makna_pedagogis"></p>
                            </div>

                            <div class="bg-amber-50/50 rounded-2xl p-3.5 border border-amber-100">
                                <span class="text-[0.62rem] font-black uppercase tracking-wider text-amber-800 block mb-1 flex items-center gap-1">
                                    <i data-lucide="lightbulb" class="w-3 h-3 text-amber-600"></i>
                                    <span>Rekomendasi Supervisi</span>
                                </span>
                                <p class="text-xs font-medium text-amber-950 leading-relaxed" x-text="graphInterpretationData?.rekomendasi_supervisi"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile Tab Switcher --}}
        <div class="lg:hidden flex bg-gray-100 p-1 rounded-2xl mb-6 relative z-10">
            <button type="button" @click="activeMobileTab = 'transkrip'"
                class="flex-1 py-3 text-xs font-black uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2 cursor-pointer"
                :class="activeMobileTab === 'transkrip' ? 'bg-white text-gray-950 shadow-sm' : 'text-gray-400'">
                <i data-lucide="file-text" class="w-4 h-4"></i> 
                <span>Transkrip ({{ $totalSegments }})</span>
            </button>
            <button type="button" @click="activeMobileTab = 'ringkasan'"
                class="flex-1 py-3 text-xs font-black uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2 cursor-pointer"
                :class="activeMobileTab === 'ringkasan' ? 'bg-white text-gray-950 shadow-sm' : 'text-gray-400'">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i> 
                <span>Distribusi Kategori</span>
            </button>
        </div>

        {{-- Main Two Columns Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Left Column: Paginated Transcript (Clean V1 Document View without Inner Cards) --}}
            <div class="lg:col-span-2 bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8"
                :class="activeMobileTab === 'transkrip' ? 'block' : 'hidden lg:block'">
                
                <div class="flex items-center justify-between pb-6 border-b border-gray-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                            <i data-lucide="message-square" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-black uppercase tracking-tight text-gray-900">Transkripsi & Penanda Wacana</h2>
                            <p class="text-xs text-gray-400 font-medium">Dialog berurutan lengkap dengan atribusi peran, menit rekaman, dan sintesis AI</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-400">Total {{ $totalSegments }} Tuturan</span>
                </div>

                {{-- Dialog Rows --}}
                <div class="space-y-10 md:space-y-12">
                    @forelse ($transcription as $index => $item)
                        @php
                            $speaker = $item['speaker'] ?? 'Unknown';
                            $isDosen = strcasecmp($speaker, 'Dosen') === 0 || strcasecmp($speaker, 'Supervisor') === 0;
                            $adviceStyle = !empty($item['advice_giving']) ? $getAdviceBadgeStyle($item['advice_giving']) : null;
                            $modeStyle = !empty($item['modes_of_interaction']) ? $getModeBadgeStyle($item['modes_of_interaction']) : null;
                            
                            // Generate intelligent substansi akademik based on speaker and annotations
                            if (!empty($item['agent_insight'])) {
                                $academicSubstance = $item['agent_insight'];
                            } elseif ($isDosen) {
                                if ($adviceStyle && $modeStyle) {
                                    $academicSubstance = "Dosen berperan mengarahkan wacana dengan " . strtolower($adviceStyle['badge']) . " serta menerapkan relasi " . strtolower($modeStyle['badge']) . " untuk memandu mahasiswa mencapai standar riset.";
                                } else {
                                    $academicSubstance = "Dosen memberikan arahan akademik dan umpan balik pembimbingan terhadap materi riset.";
                                }
                            } else {
                                if ($modeStyle && strtolower($item['modes_of_interaction']) === 'power_gaining') {
                                    $academicSubstance = "Mahasiswa berinisiatif mengajukan pertanyaan dan klarifikasi untuk menguji pemahaman metodologi secara aktif.";
                                } else {
                                    $academicSubstance = "Mahasiswa merespons bimbingan dan mengonfirmasi pemahaman atas arahan yang diberikan oleh Dosen.";
                                }
                            }
                        @endphp

                        <div id="baris-{{ $index }}"
                            class="flex flex-col gap-4 group rounded-2xl p-3 sm:p-4 transition-all duration-300 border border-transparent hover:bg-gray-50/50"
                            x-show="{{ $index }} >= (currentPage - 1) * pageSize && {{ $index }} < (currentPage) * pageSize">
                            
                            <div class="flex flex-col md:flex-row gap-4 md:gap-8">
                                {{-- Speaker & Timestamp Column (Identical to V1) --}}
                                <div class="md:w-36 flex-shrink-0 pt-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-black text-sm uppercase tracking-wider {{ $isDosen ? 'text-purple-700' : 'text-blue-600' }}">
                                            {{ $isDosen ? 'DOSEN' : 'MAHASISWA' }}
                                        </span>
                                    </div>
                                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        BARIS {{ $index + 1 }}
                                    </div>
                                    <div class="text-[0.7rem] font-semibold text-gray-500 mt-1 flex items-center">
                                        <i data-lucide="clock" class="w-3.5 h-3.5 mr-1 text-gray-400"></i>
                                        <span>{{ $item['timestamp'] ?? '00:00' }}</span>
                                    </div>
                                </div>

                                {{-- Text Column & Boxes --}}
                                <div class="flex-grow">
                                    <p class="text-lg md:text-xl font-medium text-gray-800 leading-relaxed tracking-wide font-serif mb-3">
                                        {!! $item['text_html'] ?? $item['text'] ?? '' !!}

                                        {{-- Interactive Clickable Badges (Pola Bimbingan & Relasi Kuasa - Full V1 Parity) --}}
                                        <span class="flex flex-col gap-2.5 w-full mt-3 select-none">
                                            
                                            {{-- Pola Bimbingan Box --}}
                                            <span class="w-full flex flex-wrap items-center gap-2 p-3 border border-dashed border-gray-300 bg-gray-50/70 rounded-2xl">
                                                <span class="w-full block text-[0.65rem] font-black uppercase tracking-wider text-gray-400 mb-0.5">Pola Bimbingan:</span>
                                                
                                                {{-- 1. Advice Giving Badge --}}
                                                @if($adviceStyle)
                                                    <button type="button"
                                                        @click="openInsight('Advice Giving: {{ $adviceStyle['badge'] }}', 'advice', '{{ addslashes($adviceStyle['desc']) }}', 'Klasifikasi tindak tutur pembimbingan akademik berdasarkan model Multi-Task RoBERTa V2.')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[0.7rem] font-black uppercase tracking-widest cursor-pointer transition-all hover:scale-105 border {{ $adviceStyle['bg'] }}"
                                                        title="Klik untuk melihat rangkuman & insight tindak tutur">
                                                        {!! $adviceStyle['icon'] !!}
                                                        <span>{{ $adviceStyle['badge'] }}</span>
                                                    </button>
                                                @endif

                                                {{-- 2. Advice Base Marker --}}
                                                @php
                                                    $isAdvice = !empty($item['is_advice']) && $item['is_advice'] !== 'false' && $item['is_advice'] !== false;
                                                    $adviceDesc = is_string($item['is_advice'] ?? null) ? $item['is_advice'] : 'Dosen menyampaikan arahan substansial bimbingan akademik.';
                                                @endphp
                                                @if($isAdvice)
                                                    <button type="button"
                                                        @click="openInsight('Pemberian Saran (Advice Giving)', 'advice', '{{ addslashes($adviceDesc) }}', 'Kalimat ini diidentifikasi sebagai saran bimbingan akademik yang menanggapi progres riset mahasiswa.')"
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-[0.68rem] font-black uppercase tracking-widest cursor-pointer transition-all hover:scale-105"
                                                        title="Klik untuk melihat detail advice">
                                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.5a6.5 6.5 0 100-13 6.5 6.5 0 000 13zM12 11.5v3M12 9h.01" />
                                                        </svg>
                                                        <span>Advice</span>
                                                    </button>
                                                @endif

                                                {{-- 3. Intonation Badge (Up / Down / Neutral) --}}
                                                @php
                                                    $intType = strtolower($item['intonation_type'] ?? ($item['intonation_markers'][0]['type'] ?? ($isDosen ? 'down' : 'up')));
                                                    $intLabel = $intType === 'up' ? 'Naik' : ($intType === 'down' ? 'Turun' : 'Netral');
                                                    $intColor = $intType === 'up' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($intType === 'down' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-gray-50 text-gray-700 border-gray-200');
                                                    $intDesc = $intType === 'down' ? 'Intonasi menurun menandakan penegasan instruksi atau arahan akademik yang pasti dari Dosen.' : ($intType === 'up' ? 'Intonasi menaik menandakan nada eksploratif, klarifikasi, atau pertanyaan bimbingan.' : 'Intonasi netral menandakan pertukaran informasi akademis umum.');
                                                @endphp
                                                <button type="button"
                                                    @click="openInsight('Intonasi Kalimat: {{ $intLabel }}', 'intonation', '{{ addslashes($intDesc) }}', 'Dinamika nada suara berkontribusi pada karakter direktif atau dialogis tuturan ini.')"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-[0.68rem] font-black uppercase tracking-widest cursor-pointer transition-all hover:scale-105 border {{ $intColor }}"
                                                    title="Klik untuk melihat detail intonasi baris">
                                                    @if($intType === 'up')
                                                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                                                    @elseif($intType === 'down')
                                                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" /></svg>
                                                    @else
                                                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" /></svg>
                                                    @endif
                                                    <span>Intonasi: {{ $intLabel }}</span>
                                                </button>
                                            </span>

                                            {{-- Relasi Kuasa Box --}}
                                            <span class="w-full flex flex-wrap items-center gap-2 p-3 border border-dashed border-purple-200 bg-purple-50/30 rounded-2xl">
                                                <span class="w-full block text-[0.65rem] font-black uppercase tracking-wider text-purple-400 mb-0.5">Relasi Kuasa:</span>

                                                {{-- 1. Modes of Interaction Badge --}}
                                                @if($modeStyle)
                                                    <button type="button"
                                                        @click="openInsight('Modes of Interaction: {{ $modeStyle['badge'] }}', 'mode', '{{ addslashes($modeStyle['desc']) }}', 'Pemodelan relasi kuasa interaksi antara Dosen dan Mahasiswa pada segmen waktu ini.')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[0.7rem] font-black uppercase tracking-widest cursor-pointer transition-all hover:scale-105 border {{ $modeStyle['bg'] }}"
                                                        title="Klik untuk melihat rangkuman & insight relasi">
                                                        {!! $modeStyle['icon'] !!}
                                                        <span>{{ $modeStyle['badge'] }}</span>
                                                    </button>
                                                @endif

                                                {{-- 2. Karakter Relasi Sintaksis/Wacana Badge --}}
                                                @php
                                                    $relText = !empty($item['relation']) ? $item['relation'] : (!empty($item['advice_relation']) ? $item['advice_relation'] : ($isDosen ? 'Relasi otoritas pedagogis membimbing topik mahasiswa.' : 'Relasi responsif mengonfirmasi pemahaman konsep.'));
                                                @endphp
                                                <button type="button"
                                                    @click="openInsight('Karakter & Relasi Kalimat', 'relation', 'AI mendeteksi hubungan antar-kalimat dan keterkaitan topik yang erat pada baris percakapan ini.', '{{ addslashes($relText) }}')"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-[0.68rem] font-black uppercase tracking-widest cursor-pointer transition-all hover:scale-105"
                                                    title="Klik untuk melihat karakter & relasi kalimat">
                                                    <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                    </svg>
                                                    <span>Relasi Wacana</span>
                                                </button>
                                            </span>

                                        </span>
                                    </p>
                                </div>
                            </div>

                            {{-- Substansi Akademik Card (Identik dengan Gambar 2) --}}
                            <div class="ml-0 md:ml-44 bg-gray-50 border-l-4 border-purple-500 rounded-r-2xl p-4 mt-2">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 text-purple-600 w-5 h-5 shrink-0 flex items-center justify-center">
                                        <i data-lucide="sparkles" class="w-4 h-4 text-purple-600"></i>
                                    </div>
                                    <div>
                                        <span class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 block mb-1">
                                            Substansi Akademik
                                        </span>
                                        <p class="text-xs sm:text-sm font-medium text-gray-700 leading-relaxed">
                                            {{ $academicSubstance }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Line Evaluation (Evaluasi Kalimat Jempol Atas / Bawah untuk Korpus Database) --}}
                            <div class="ml-0 md:ml-44 flex items-center justify-between gap-4 mt-2 text-xs"
                                x-data="{
                                    feedback: '{{ $item['user_feedback'] ?? 'none' }}',
                                    loading: false,
                                    async sendFeedback(type) {
                                        if (this.loading) return;
                                        this.loading = true;
                                        try {
                                            let res = await fetch('{{ route('analysis.line-feedback', $analysis->slug) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ index: {{ $index }}, type: type })
                                            });
                                            let data = await res.json();
                                            if (data.status === 'success') {
                                                this.feedback = data.user_feedback;
                                            }
                                        } catch (e) {
                                            console.error('Error line feedback', e);
                                        } finally {
                                            this.loading = false;
                                        }
                                    }
                                }">
                                <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-full px-3.5 py-1.5 text-gray-400">
                                    <span class="text-[0.62rem] font-black uppercase tracking-widest text-gray-500 flex items-center gap-1">
                                        <i data-lucide="check-square" class="w-3 h-3 text-purple-500"></i>
                                        <span>Evaluasi Kalimat:</span>
                                    </span>
                                    <div class="flex items-center gap-2.5">
                                        {{-- Thumbs Up --}}
                                        <button type="button" @click="sendFeedback('up')"
                                            class="hover:text-green-600 transition focus:outline-none flex items-center justify-center p-0.5 cursor-pointer"
                                            :class="feedback === 'up' ? 'text-green-600 scale-110 font-bold' : 'text-gray-400 hover:scale-105'"
                                            title="Tandai baris ini akurat / sesuai untuk korpus database">
                                            <svg class="w-4 h-4" :fill="feedback === 'up' ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3zM7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3" />
                                            </svg>
                                        </button>

                                        {{-- Thumbs Down --}}
                                        <button type="button" @click="sendFeedback('down')"
                                            class="hover:text-red-600 transition focus:outline-none flex items-center justify-center p-0.5 cursor-pointer"
                                            :class="feedback === 'down' ? 'text-red-600 scale-110 font-bold' : 'text-gray-400 hover:scale-105'"
                                            title="Tandai baris ini belum sesuai">
                                            <svg class="w-4 h-4" :fill="feedback === 'down' ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 15v4a3 3 0 003 3l4-9V2H5.72a2 2 0 00-2 1.7l-1.38 9a2 2 0 002 2.3zm7-13h3a2 2 0 012 2v7a2 2 0 01-2 2h-3" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-400 font-medium">
                            <i data-lucide="message-square-off" class="w-10 h-10 mx-auto text-gray-300 mb-2"></i>
                            Belum ada baris transkripsi tersedia.
                        </div>
                    @endforelse
                </div>

                {{-- Pagination Control (Rapi, Lengkap dengan Info Baris) --}}
                <template x-if="totalPages > 1">
                    <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <button type="button" 
                            @click="currentPage = Math.max(1, currentPage - 1)" 
                            :disabled="currentPage === 1"
                            class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold uppercase tracking-widest text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-transparent transition-colors cursor-pointer">
                            <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i> Prev
                        </button>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-widest text-center">
                            Halaman <span x-text="currentPage" class="text-gray-900 font-black"></span> dari <span x-text="totalPages"></span>
                            <span class="block md:inline-block md:ml-1 text-[0.65rem] text-gray-400 font-medium lowercase tracking-normal">(total <span x-text="totalRows" class="font-bold text-gray-600"></span> baris transkrip)</span>
                        </span>
                        <button type="button" 
                            @click="currentPage = Math.min(totalPages, currentPage + 1)" 
                            :disabled="currentPage === totalPages"
                            class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold uppercase tracking-widest text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-transparent transition-colors cursor-pointer">
                            Next <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Right Column: Category Distribution & Insights (Full V1 Parity) --}}
            <div class="lg:col-span-1 space-y-6"
                :class="activeMobileTab === 'ringkasan' ? 'block' : 'hidden lg:block'">
                
                {{-- Hasil Analisis Akhir (V1 Parity Card) --}}
                <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 text-purple-50 pointer-events-none">
                        <i data-lucide="brain-circuit" class="w-40 h-40"></i>
                    </div>

                    <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center relative z-10">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 mr-2 text-purple-600"></i>
                        <span>Hasil Analisis Akhir</span>
                    </h2>

                    <div class="space-y-3 mb-4 relative z-10">
                        {{-- Kategori Advice --}}
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex flex-col justify-center">
                            <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1">
                                Kategori Advice
                            </span>
                            <span class="font-bold text-gray-900 leading-tight block break-words text-sm">
                                {{ $summary['kategori_advice'] ?? 'Saran Bimbingan Akademik' }}
                            </span>
                        </div>

                        {{-- Karakter Relasi --}}
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex flex-col justify-center">
                            <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1">
                                Karakter Relasi
                            </span>
                            <span class="font-bold text-gray-900 leading-tight block break-words text-sm">
                                {{ $summary['karakter_relasi'] ?? 'Kondusif & Seimbang' }}
                            </span>
                        </div>

                        {{-- Intonasi Dominan --}}
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex flex-col justify-center">
                            <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1">
                                Intonasi Dominan
                            </span>
                            <span class="font-bold text-gray-900 leading-tight block break-words text-sm">
                                {{ $summary['intonasi_dominan'] ?? 'Intonasi Seimbang' }}
                            </span>
                        </div>

                        {{-- Ranah Bicara --}}
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex flex-col justify-center">
                            <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1">
                                Ranah Bicara
                            </span>
                            <span class="font-bold text-gray-900 leading-tight block break-words text-sm">
                                {{ $summary['ranah_pembicaraan'] ?? 'Bimbingan Skripsi & Pendalaman Metodologi Penelitian' }}
                            </span>
                        </div>
                    </div>

                    {{-- Umpan Balik --}}
                    <div class="bg-purple-600 rounded-2xl p-5 text-white shadow-md shadow-purple-600/20 mb-4 relative z-10">
                        <span class="block text-[0.6rem] font-black text-purple-200 uppercase tracking-widest mb-2 flex items-center">
                            <i data-lucide="target" class="w-3.5 h-3.5 mr-1.5"></i>
                            <span>Umpan Balik</span>
                        </span>
                        <p class="font-medium text-xs sm:text-sm leading-relaxed text-purple-50">
                            {{ ucfirst($summary['arah_tujuan'] ?? 'Dosen mengarahkan mahasiswa dengan fokus pada penyempurnaan landasan teori, metodologi, dan perumusan simpulan yang valid.') }}
                        </p>
                    </div>

                    {{-- Rekomendasi --}}
                    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5 text-emerald-900 relative z-10">
                        <span class="block text-[0.6rem] font-black text-emerald-600 uppercase tracking-widest mb-2 flex items-center">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5 mr-1.5"></i>
                            <span>Rekomendasi</span>
                        </span>
                        <p class="font-medium text-xs sm:text-sm leading-relaxed text-emerald-800">
                            {{ ucfirst($summary['saran_perbaikan'] ?? 'Mahasiswa disarankan menindaklanjuti catatan revisi Dosen, mengonfirmasi instrumen riset, dan menyusun target mingguan agar jadwal bimbingan berjalan optimal.') }}
                        </p>
                    </div>
                </div>

                {{-- Advice Distribution Card --}}
                <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 flex items-center gap-2">
                        <i data-lucide="pie-chart" class="w-4 h-4 text-purple-600"></i> Distribusi Tindak Tutur (Advice)
                    </h3>
                    
                    @if(count($adviceCounts) > 0)
                        <div class="space-y-3">
                            @foreach($adviceCounts as $label => $count)
                                @php
                                    $style = $getAdviceBadgeStyle($label);
                                    $pct = $totalSegments > 0 ? round(($count / $totalSegments) * 100) : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between items-center text-xs mb-1">
                                        <span class="font-bold text-gray-700">{{ $style['badge'] }}</span>
                                        <span class="font-black text-gray-900">{{ $count }} <span class="text-[0.65rem] text-gray-400 font-semibold">({{ $pct }}%)</span></span>
                                    </div>
                                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full" style="width: {{ $pct }}%; background-color: {{ $style['color'] }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">Tidak ada label tindak tutur ditemukan.</p>
                    @endif
                </div>

                {{-- Power Relation Mode Distribution Card --}}
                <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 flex items-center gap-2">
                        <i data-lucide="shield" class="w-4 h-4 text-indigo-600"></i> Relasi Kuasa (Modes of Interaction)
                    </h3>
                    
                    @if(count($modeCounts) > 0)
                        <div class="space-y-3">
                            @foreach($modeCounts as $label => $count)
                                @php
                                    $style = $getModeBadgeStyle($label);
                                    $pct = $totalSegments > 0 ? round(($count / $totalSegments) * 100) : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between items-center text-xs mb-1">
                                        <span class="font-bold text-gray-700">{{ $style['badge'] }}</span>
                                        <span class="font-black text-gray-900">{{ $count }} <span class="text-[0.65rem] text-gray-400 font-semibold">({{ $pct }}%)</span></span>
                                    </div>
                                    <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full" style="width: {{ $pct }}%; background-color: {{ $style['color'] }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">Tidak ada label relasi kuasa ditemukan.</p>
                    @endif
                </div>

                {{-- Model Architecture Info Card --}}
                <div class="bg-gradient-to-br from-purple-900 to-indigo-900 rounded-2xl md:rounded-3xl p-6 text-white shadow-xl shadow-purple-900/20">
                    <span class="px-2.5 py-1 rounded-full bg-white/10 text-[0.6rem] font-black uppercase tracking-widest border border-white/20 inline-block mb-3">
                        Arsitektur Sistem
                    </span>
                    <h4 class="text-sm font-black uppercase tracking-wide mb-2">Multi-Task RoBERTa V2</h4>
                    <p class="text-xs text-purple-200 leading-relaxed font-medium mb-4">
                        Model membaca konteks wacana multi-turn secara simultan untuk mendeteksi tindak tutur advice dan modalitas relasi kuasa secara end-to-end.
                    </p>
                    <div class="pt-3 border-t border-white/10 flex items-center justify-between text-[0.7rem] font-bold text-purple-300">
                        <span>Akurasi Multi-Task</span>
                        <span class="text-white">State-of-the-Art</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- Feedback Banner (Validasi Hasil Rekaman Terhadap Akurasi Output V2) --}}
        <div x-data="{
            submitted: {{ $analysis->feedback ? 'true' : 'false' }},
            loading: false,
            comments: '',
            async submitFeedback(status) {
                if (this.loading) return;
                this.loading = true;
                try {
                    let res = await fetch('{{ route('analysis.feedback', $analysis->slug) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ is_accurate: status, comments: this.comments })
                    });
                    let data = await res.json();
                    if (data.status === 'success') {
                        this.submitted = true;
                    }
                } catch (e) {
                    console.error('Error submitting feedback', e);
                } finally {
                    this.loading = false;
                }
            }
        }" class="mt-10">
            <div x-show="!submitted"
                class="bg-gray-900 rounded-[2.5rem] p-8 md:p-10 text-white shadow-2xl relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-900/30 via-indigo-900/20 to-transparent opacity-60"></div>
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex-grow text-center md:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400 text-[0.65rem] font-black uppercase tracking-widest mb-3">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                            <span>Validasi Model RoBERTa Multi-Task V2</span>
                        </div>
                        <h3 class="text-xl font-black uppercase tracking-tight text-white mb-2 flex items-center justify-center md:justify-start gap-2.5">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-purple-400"></i>
                            <span>Penilaian Kesesuaian Hasil Analisis</span>
                        </h3>
                        <p class="text-gray-300 text-xs sm:text-sm font-medium leading-relaxed max-w-3xl">
                            Sebagai peninjau atau pakar akademik, penilaian Anda sangat berharga bagi penyempurnaan dataset dan akurasi model Multi-Task RoBERTa. Apakah anotasi diarization, pemisahan peran pembicara, dan penandaan relasi wacana pada rekaman ini sudah sesuai dengan fakta percakapan riil?
                        </p>
                    </div>

                    <div class="flex-shrink-0 w-full md:w-auto flex items-center gap-4">
                        <div class="flex gap-3 w-full" x-show="!loading">
                            <button type="button" @click="submitFeedback(1)"
                                class="flex-1 md:flex-none px-6 py-3.5 bg-green-600 hover:bg-green-500 text-white font-black text-xs uppercase tracking-wider rounded-2xl transition-all hover:scale-105 active:scale-95 cursor-pointer flex items-center justify-center gap-2 shadow-lg shadow-green-600/20">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Sudah Sesuai</span>
                            </button>
                            <button type="button" @click="submitFeedback(0)"
                                class="flex-1 md:flex-none px-6 py-3.5 bg-red-600 hover:bg-red-500 text-white font-black text-xs uppercase tracking-wider rounded-2xl transition-all hover:scale-105 active:scale-95 cursor-pointer flex items-center justify-center gap-2 shadow-lg shadow-red-600/20">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                <span>Belum Sesuai</span>
                            </button>
                        </div>

                        <div x-show="loading" style="display: none;" class="flex items-center gap-2 text-white/70">
                            <svg class="animate-spin h-6 w-6 text-purple-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span class="text-xs font-black uppercase tracking-wider">Menyimpan Validasi...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="submitted" x-transition.opacity style="display: none;"
                class="bg-green-50 border border-green-200 rounded-2xl md:rounded-3xl p-8 text-center text-green-800 shadow-sm">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
                <p class="font-black text-sm uppercase tracking-wider">
                    <span class="lang-id">Apresiasi Kami Atas Umpan Balik Anda!</span>
                    <span class="lang-en">Our Sincere Appreciation for Your Feedback!</span>
                    <span class="lang-zh">衷心感谢您的宝贵反馈！</span>
                </p>
                <p class="text-xs mt-1 text-green-600 font-medium">
                    <span class="lang-id">Validasi Anda berhasil direkam untuk terus meningkatkan akurasi algoritma metrik Supervisory AI.</span>
                    <span class="lang-en">Your validation has been successfully recorded to continuously improve the accuracy of the Supervisory AI metrics algorithm.</span>
                    <span class="lang-zh">您的评估反馈已成功记录，这将用于持续改进 Supervisory AI 的度量算法准确性。</span>
                </p>
            </div>
        </div>

        </div> {{-- End of x-show="!showRaw" wrapper --}}

        {{-- Supervisory AI Insight Modal (Identik dengan Gambar 3) --}}
        <div x-show="showInsightModal"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
            style="display: none;">
            <div x-show="showInsightModal" @click="showInsightModal = false"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-md"></div>

            <div x-show="showInsightModal"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-lg mx-auto my-6 z-50 bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 p-8">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-gray-100 pb-5 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center">
                            <i data-lucide="sparkles" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">
                                Supervisory AI Insight
                            </span>
                            <h3 class="text-base font-black text-gray-900 uppercase tracking-wide" x-text="insightTitle"></h3>
                        </div>
                    </div>

                    <button type="button" @click="showInsightModal = false"
                        class="p-2 hover:bg-gray-100 text-gray-400 hover:text-gray-600 rounded-xl transition-all cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="space-y-5">
                    {{-- Penjelasan AI Agent --}}
                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 relative overflow-hidden">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-white border border-gray-100 text-purple-600 flex items-center justify-center shrink-0 shadow-sm">
                                <i data-lucide="brain-circuit" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                                    Penjelasan AI Agent
                                </span>
                                <p class="text-sm font-medium text-gray-700 leading-relaxed font-sans" x-text="insightExplanation"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Karakter & Relasi Kalimat --}}
                    <div class="bg-blue-50/50 border border-blue-100/80 rounded-2xl p-5 relative overflow-hidden">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-white border border-blue-100 text-blue-500 flex items-center justify-center shrink-0 shadow-sm">
                                <i data-lucide="link" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <span class="block text-[0.6rem] font-black text-blue-500 uppercase tracking-widest mb-1.5">
                                    Karakter & Relasi Kalimat (Syntactic Relation)
                                </span>
                                <p class="text-sm font-bold text-blue-900 leading-relaxed font-sans" x-text="insightRelation"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="mt-8 flex justify-end">
                    <button type="button" @click="showInsightModal = false"
                        class="px-6 py-2.5 bg-gray-950 hover:bg-gray-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        {{-- Speaker Swap Confirmation Modal --}}
        <div x-show="showSwapModal"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
            style="display: none;">
            <div x-show="showSwapModal" @click="showSwapModal = false"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

            <div x-show="showSwapModal"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white rounded-[2.5rem] max-w-md w-full mx-4 p-8 shadow-2xl z-10 border border-gray-100">
                
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4">
                    <i data-lucide="arrow-left-right" class="w-6 h-6"></i>
                </div>

                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight mb-2">Tukar Peran Pembicara?</h3>
                <p class="text-xs text-gray-500 font-medium leading-relaxed mb-6">
                    Tindakan ini akan menukar seluruh label pembicara (<strong class="text-purple-700">Dosen ↔ Mahasiswa</strong>) pada transkripsi dan memperbarui topologi Graf Wacana secara otomatis.
                </p>

                <div class="flex gap-3 justify-end">
                    <button type="button" @click="showSwapModal = false"
                        class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-bold uppercase tracking-wider text-gray-600 hover:bg-gray-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="executeSpeakerSwap()" :disabled="isSwapping"
                        class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-black uppercase tracking-wider transition shadow-md shadow-purple-600/20 flex items-center gap-2 cursor-pointer disabled:opacity-50">
                        <template x-if="isSwapping">
                            <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </template>
                        <span x-text="isSwapping ? 'Memproses...' : 'Ya, Tukar Peran'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Fitur Cetak Laporan V2 Dalam Pengembangan --}}
        <div x-show="showPrintModal"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
            style="display: none;">
            <div x-show="showPrintModal" @click="showPrintModal = false"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-md"></div>

            <div x-show="showPrintModal"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white rounded-[2.5rem] max-w-md w-full mx-4 p-8 shadow-2xl z-10 border border-gray-100 text-center">
                
                <div class="w-16 h-16 rounded-3xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-4 border border-amber-100 shadow-sm">
                    <i data-lucide="printer" class="w-8 h-8 text-amber-500"></i>
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100/70 text-amber-800 text-[0.65rem] font-black uppercase tracking-widest mb-3">
                    <i data-lucide="wrench" class="w-3.5 h-3.5"></i>
                    <span>Fitur Eksperimental V2</span>
                </div>

                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight mb-2">Cetak Laporan Dalam Pengembangan</h3>
                <p class="text-xs text-gray-500 font-medium leading-relaxed mb-6">
                    Fitur cetak laporan analitis komprehensif untuk <strong class="text-purple-700">Model Versi 2 (Multi-Task RoBERTa & Topologi Graf)</strong> saat ini sedang dalam proses pengembangan & optimasi layout visual. Untuk hasil cetak laporan formal yang stabil saat ini tersedia penuh pada <strong>Model Versi 1 (IndoBERT)</strong>.
                </p>

                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 text-left mb-6 text-xs space-y-2.5">
                    <div class="flex items-center gap-2 text-gray-700 font-bold">
                        <i data-lucide="sparkles" class="w-4 h-4 text-purple-600"></i>
                        <span>Ekspor Graf Interaktif (Node & Edges)</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700 font-bold">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                        <span>Tabulasi Matriks Relasi Kuasa Multi-Turn</span>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button type="button" @click="showPrintModal = false"
                        class="w-full py-3 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-black uppercase tracking-wider transition shadow-md cursor-pointer">
                        Mengerti & Kembali
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Komponen Penanganan Kendala / Error (Pengganti alert bawaan browser) --}}
        <div x-show="showErrorModal"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none"
            style="display: none;">
            <div x-show="showErrorModal" @click="showErrorModal = false"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-md"></div>

            <div x-show="showErrorModal"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-white rounded-2xl md:rounded-3xl max-w-md w-full mx-4 p-8 shadow-2xl z-10 border border-red-100 text-center">
                
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-4 border border-red-100 shadow-sm">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
                </div>

                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100/70 text-red-800 text-[0.65rem] font-black uppercase tracking-widest mb-3">
                    <span x-text="errorModalTitle || 'Kendala Pemrosesan Sistem'"></span>
                </div>

                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight mb-2">Terjadi Kendala</h3>
                <p class="text-xs text-gray-600 font-medium leading-relaxed mb-6" x-text="errorModalMessage"></p>

                <div class="flex justify-center">
                    <button type="button" @click="showErrorModal = false"
                        class="w-full py-3 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-black uppercase tracking-wider transition shadow-md cursor-pointer">
                        Tutup & Coba Lagi
                    </button>
                </div>
            </div>
        </div>

    </div>

    <x-slot name="scripts">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
    @php
        $chartPoints = array_map(
            function ($block, $idx) {
                $type = strtolower($block['intonation_type'] ?? ($block['intonation_markers'][0]['type'] ?? 'neutral'));
                $val = 0;
                if ($type === 'up') $val = 1;
                elseif ($type === 'down') $val = -1;

                $speaker = $block['speaker'] ?? 'Unknown';
                $isDosen = strcasecmp($speaker, 'Dosen') === 0 || strcasecmp($speaker, 'Supervisor') === 0;

                return [
                    'label' => 'Baris ' . ($idx + 1),
                    'value' => $val,
                    'speaker' => $isDosen ? 'Dosen' : 'Mahasiswa',
                    'type' => $type === 'up' ? 'Naik' : ($type === 'down' ? 'Turun' : 'Netral'),
                    'text' => mb_strimwidth(strip_tags($block['text_html'] ?? ($block['text'] ?? '')), 0, 50, '...'),
                ];
            },
            $transcription,
            array_keys($transcription)
        );
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Grafik Aliran Intonasi (Chart.js - V1 Parity)
            const canvasEl = document.getElementById('dynamicsChart');
            if (canvasEl) {
                const ctx = canvasEl.getContext('2d');
                const chartData = @json($chartPoints);
                const labels = chartData.map(d => d.label);
                const values = chartData.map(d => d.value);
                const pointColors = chartData.map(d => d.speaker === 'Dosen' ? '#9333ea' : '#2563eb');
                const pointStyles = chartData.map(d => d.speaker === 'Dosen' ? 'circle' : 'rectRot');

                const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                gradient.addColorStop(0, 'rgba(147, 51, 234, 0.2)');
                gradient.addColorStop(0.5, 'rgba(37, 99, 235, 0.08)');
                gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Dinamika Nada Intonasi',
                            data: values,
                            borderColor: '#4b5563',
                            borderWidth: 2.5,
                            tension: 0.35,
                            fill: true,
                            backgroundColor: gradient,
                            pointBackgroundColor: pointColors,
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2.5,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointStyle: pointStyles
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        onClick: function(e, activeEls) {
                            if (activeEls && activeEls.length > 0) {
                                const idx = activeEls[0].index;
                                const targetEl = document.getElementById('baris-' + idx);
                                if (targetEl) {
                                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    targetEl.classList.add('ring-2', 'ring-purple-400', 'bg-purple-50/40');
                                    setTimeout(() => targetEl.classList.remove('ring-2', 'ring-purple-400', 'bg-purple-50/40'), 2500);
                                }
                            }
                        },
                        onHover: function(e, el) {
                            const canvas = e.chart.canvas;
                            canvas.style.cursor = el.length ? 'pointer' : 'default';
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#111827',
                                titleFont: { size: 11, weight: 'bold', family: 'Inter' },
                                bodyFont: { size: 11, family: 'Inter' },
                                padding: 12,
                                borderRadius: 12,
                                callbacks: {
                                    title: function(context) {
                                        const idx = context[0].dataIndex;
                                        const d = chartData[idx];
                                        return `${d.label} - ${d.speaker}`;
                                    },
                                    label: function(context) {
                                        const idx = context.dataIndex;
                                        const d = chartData[idx];
                                        return [
                                            `Intonasi: ${d.type}`,
                                            `Teks: "${d.text}"`
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { size: 10, family: 'Inter', weight: '600' },
                                    color: '#9ca3af'
                                }
                            },
                            y: {
                                min: -1.2,
                                max: 1.2,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        if (value === 1) return 'Naik ↑';
                                        if (value === 0) return 'Netral ➖';
                                        if (value === -1) return 'Turun ↓';
                                        return '';
                                    },
                                    font: { size: 10, family: 'Inter', weight: 'bold' },
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: 'rgba(243, 244, 246, 0.8)',
                                    drawBorder: false
                                }
                            }
                        }
                    }
                });
            }
        });

        function v2ResultApp() {
            return {
                showRaw: false,
                showSwapModal: false,
                showPrintModal: false,
                isSwapping: false,
                showInsightModal: false,
                insightTitle: '',
                insightType: '',
                insightExplanation: '',
                insightRelation: '',
                activeMobileTab: 'transkrip',
                currentPage: 1,
                pageSize: 10,
                totalRows: {{ $totalSegments }},
                get totalPages() {
                    return Math.max(1, Math.ceil(this.totalRows / this.pageSize));
                },

                // Audio Player state
                isPlaying: false,
                progressPercent: 0,
                audioTimeDisplay: '00:00 / 00:00',

                // Graph Multimodal Interpretation state
                isInterpretingGraph: false,
                graphInterpretationData: @json($graphInterpretation['data'] ?? null),
                graphAnalyzedAt: '{{ isset($graphInterpretation['analyzed_at']) ? \Carbon\Carbon::parse($graphInterpretation['analyzed_at'])->translatedFormat("d M Y, H:i") : "" }}',

                // Error Modal State
                showErrorModal: false,
                errorModalTitle: '',
                errorModalMessage: '',

                showError(title, message) {
                    this.errorModalTitle = title;
                    this.errorModalMessage = message;
                    this.showErrorModal = true;
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                },

                async analyzeGraphWithAI() {
                    if (this.isInterpretingGraph) return;
                    
                    if (!window.v2GraphChart) {
                        this.showError('Visualisasi Graf', 'Grafik interaksi belum sepenuhnya dimuat di browser Anda.');
                        return;
                    }

                    // Konversi ECharts canvas ke Base64 image
                    let base64Image = '';
                    try {
                        base64Image = window.v2GraphChart.getDataURL({
                            type: 'png',
                            pixelRatio: 2,
                            backgroundColor: '#ffffff'
                        });
                    } catch (e) {
                        console.error('Gagal mengambil gambar graf:', e);
                        this.showError('Ekspor Visualisasi', 'Gagal mengekspor gambar visualisasi graf untuk dikirim ke model AI.');
                        return;
                    }

                    this.isInterpretingGraph = true;

                    try {
                        const res = await fetch("{{ route('analysis.v2.interpret-graph', $analysis->slug) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                image: base64Image
                            })
                        });

                        const data = await res.json();
                        if (data.status === 'success') {
                            this.graphInterpretationData = data.interpretation;
                            this.graphAnalyzedAt = data.analyzed_at;
                            this.$nextTick(() => {
                                if (window.lucide) window.lucide.createIcons();
                                if (window.v2GraphChart) window.v2GraphChart.resize();
                            });
                        } else {
                            this.showError('Sintesis Multimodal Gagal', data.message || 'Gagal memproses interpretasi graf.');
                        }
                    } catch (err) {
                        console.error('Network Error:', err);
                        this.showError('Koneksi Jaringan', 'Terjadi kesalahan jaringan saat menganalisis graf wacana.');
                    } finally {
                        this.isInterpretingGraph = false;
                    }
                },

                openInsight(title, type, explanation, relation) {
                    this.insightTitle = title;
                    this.insightType = type;
                    this.insightExplanation = explanation;
                    this.insightRelation = relation;
                    this.showInsightModal = true;
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                },

                toggleAudio() {
                    const audio = this.$refs.audioPlayer;
                    if (!audio) return;
                    if (audio.paused) {
                        audio.play();
                        this.isPlaying = true;
                    } else {
                        audio.pause();
                        this.isPlaying = false;
                    }
                },
                onTimeUpdate() {
                    const audio = this.$refs.audioPlayer;
                    if (!audio || !audio.duration) return;
                    this.progressPercent = (audio.currentTime / audio.duration) * 100;
                    this.audioTimeDisplay = this.formatTime(audio.currentTime) + ' / ' + this.formatTime(audio.duration);
                },
                onEnded() {
                    this.isPlaying = false;
                    this.progressPercent = 0;
                },
                onLoadedMetadata() {
                    const audio = this.$refs.audioPlayer;
                    if (!audio) return;
                    this.audioTimeDisplay = '00:00 / ' + this.formatTime(audio.duration);
                },
                seekAudio(e) {
                    const audio = this.$refs.audioPlayer;
                    if (!audio || !audio.duration) return;
                    const rect = e.currentTarget.getBoundingClientRect();
                    const clickPos = (e.clientX - rect.left) / rect.width;
                    audio.currentTime = clickPos * audio.duration;
                },
                formatTime(sec) {
                    if (isNaN(sec)) return '00:00';
                    const m = Math.floor(sec / 60);
                    const s = Math.floor(sec % 60);
                    return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
                },

                async executeSpeakerSwap() {
                    this.isSwapping = true;
                    try {
                        const res = await fetch("{{ route('analysis.v2.swap-speakers', $analysis->slug) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.status === 'success') {
                            window.location.reload();
                        } else {
                            alert(data.message || 'Gagal menukar peran pembicara.');
                            this.isSwapping = false;
                            this.showSwapModal = false;
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Terjadi kesalahan jaringan.');
                        this.isSwapping = false;
                        this.showSwapModal = false;
                    }
                }
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            var chartDom = document.getElementById('graph-container');
            if (!chartDom) return;
            var chart = echarts.init(chartDom);
            window.v2GraphChart = chart;
            
            var rawGraphData = {!! json_encode($analysis->graph_data) !!};
            
            if (!rawGraphData || !rawGraphData.nodes || rawGraphData.nodes.length === 0) {
                rawGraphData = {
                    nodes: [
                        {id: 'Dosen', name: 'Dosen', symbolSize: 55, itemStyle: {color: '#9333ea'}},
                        {id: 'Mahasiswa', name: 'Mahasiswa', symbolSize: 45, itemStyle: {color: '#3b82f6'}},
                        {id: 'Bimbingan', name: 'Bimbingan Bertahap', symbolSize: 32, itemStyle: {color: '#10b981'}},
                        {id: 'Power', name: 'Power Maintaining', symbolSize: 32, itemStyle: {color: '#6366f1'}}
                    ],
                    edges: [
                        {source: 'Dosen', target: 'Mahasiswa', value: 'Dialog', lineStyle: {width: 2.5}},
                        {source: 'Dosen', target: 'Bimbingan', value: 'Advice', lineStyle: {width: 2}},
                        {source: 'Dosen', target: 'Power', value: 'Mode', lineStyle: {width: 2}}
                    ]
                };
            } else {
                rawGraphData.nodes = rawGraphData.nodes.map(n => {
                    var isDosen = (n.id === 'Dosen' || n.label === 'Dosen');
                    var isMhs = (n.id === 'Mahasiswa' || n.id === 'Mhs' || n.label === 'Mahasiswa');
                    var color = isDosen ? '#9333ea' : (isMhs ? '#3b82f6' : '#10b981');
                    var size = (isDosen || isMhs) ? 50 : 32;
                    return {
                        id: n.id,
                        name: n.label || n.id,
                        symbolSize: size,
                        itemStyle: { color: color }
                    };
                });
                rawGraphData.edges = rawGraphData.edges.map(e => ({
                    source: e.from, 
                    target: e.to, 
                    value: e.label || '',
                    label: { show: true, formatter: e.label || '', fontSize: 9 }
                }));
            }

            var option = {
                tooltip: {
                    trigger: 'item',
                    formatter: function(params) {
                        if (params.dataType === 'node') {
                            return '<strong>Peran/Label:</strong> ' + params.name;
                        } else if (params.dataType === 'edge') {
                            return params.data.source + ' ➔ ' + params.data.target + (params.data.value ? '<br>Relasi: ' + params.data.value : '');
                        }
                    }
                },
                series: [{
                    type: 'graph',
                    layout: 'force',
                    roam: true,
                    label: { 
                        show: true, 
                        position: 'right', 
                        fontWeight: 'bold',
                        fontSize: 11
                    },
                    edgeSymbol: ['circle', 'arrow'],
                    edgeSymbolSize: [4, 10],
                    edgeLabel: { fontSize: 10 },
                    data: rawGraphData.nodes,
                    links: rawGraphData.edges,
                    force: {
                        repulsion: 450,
                        edgeLength: [60, 160]
                    },
                    lineStyle: {
                        color: 'source',
                        curveness: 0.15,
                        width: 2
                    }
                }]
            };
            chart.setOption(option);
            
            window.addEventListener('resize', function() {
                chart.resize();
            });

            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
    </x-slot>
</x-layouts.app>
