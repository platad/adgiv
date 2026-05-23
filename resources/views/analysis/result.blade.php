<x-layouts.app title="Hasil Analisa">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 animate-fade-in" x-data="{
        showRaw: false,
        showInsightModal: false,
        insightTitle: '',
        insightType: '',
        insightExplanation: '',
        insightRelation: '',
        activeMobileTab: 'transkrip',
        
        // Alpine Progressive loading states
        synthesisStatus: '{{ $analysis->status }}',
        synthesisError: null,
        currentPage: 1,
        pageSize: {{ $analysis->status === 'completed' ? '10' : '1000' }},
        totalRows: 0,
        
        async startBackgroundSynthesis() {
            if (this.synthesisStatus === 'completed') return;
            this.synthesisStatus = 'processing';
            this.synthesisError = null;
            
            try {
                const response = await fetch('{{ route("analysis.process", $analysis->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (response.ok && data.status === 'success') {
                    this.synthesisStatus = 'completed';
                    window.location.reload();
                } else {
                    this.synthesisStatus = 'failed';
                    this.synthesisError = data.message || 'Gagal menyusun halaman hasil analisa.';
                }
            } catch (err) {
                this.synthesisStatus = 'failed';
                this.synthesisError = 'Koneksi terputus. Silakan coba kembali.';
            }
        },

        init() {
            if (this.synthesisStatus !== 'completed') {
                this.startBackgroundSynthesis();
            }
        },
    
        openInsight(title, type, explanation, relation) {
            this.insightTitle = title;
            this.insightType = type;
            this.insightExplanation = explanation;
            this.insightRelation = relation || 'Tidak ada relasi khusus dengan baris lain.';
            this.showInsightModal = true;
        }
    }">

        {{-- Header --}}
        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-gray-100 pb-8">
            <div>
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest mb-4">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Dashboard
                </a>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">{{ $analysis->title }}</h1>
                <p class="text-gray-500 font-medium mt-2 text-sm">Transkripsi Multimodal & Anotasi Bimbingan</p>
            </div>

            <div class="flex items-center gap-3">
                <button @click="showRaw = !showRaw"
                    class="inline-flex items-center px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold uppercase tracking-widest transition-colors">
                    <i data-lucide="code" class="w-4 h-4 mr-2"></i> <span
                        x-text="showRaw ? 'Tutup Raw Data' : 'Lihat Raw Data'"></span>
                </button>
                
                {{-- Dynamic Status Badge --}}
                <span x-show="synthesisStatus === 'completed'"
                    class="inline-flex items-center px-4 py-2 rounded-xl bg-green-50 text-green-700 text-xs font-bold uppercase tracking-widest">
                    <i data-lucide="check-circle-2" class="w-4 h-4 mr-2"></i> Analisa Selesai
                </span>
                <span x-show="synthesisStatus === 'processing' || synthesisStatus === 'pending'" style="display: none;"
                    class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-widest animate-pulse">
                    <i data-lucide="loader-2" class="w-4.5 h-4.5 mr-2 animate-spin text-blue-500"></i> Menyusun Laporan...
                </span>
                <span x-show="synthesisStatus === 'failed'" style="display: none;"
                    class="inline-flex items-center px-4 py-2 rounded-xl bg-red-50 text-red-700 text-xs font-bold uppercase tracking-widest animate-shake">
                    <i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i> Penyusunan Gagal
                </span>
            </div>
        </div>

        {{-- Raw Data View --}}
        <div x-show="showRaw" style="display: none;" class="bg-gray-900 rounded-3xl shadow-xl mb-8 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Raw JSON Output</h3>
            </div>
            <div class="p-6 overflow-x-auto">
                <pre class="text-sm font-mono text-green-400"><code>{{ json_encode($analysis->result_data, JSON_PRETTY_PRINT) }}</code></pre>
            </div>
        </div>

        @php
            $isCompleted = $analysis->status === 'completed';
            if ($isCompleted) {
                $transcription = $analysis->result_data['transcription'] ?? [];
            } else {
                $chunks = $analysis->result_data['chunks'] ?? [];
                $transcription = [];
                foreach ($chunks as $chunkIdx => $chunk) {
                    $chunkTrans = $chunk['transcription'] ?? [];
                    foreach ($chunkTrans as $block) {
                        if (is_array($block)) {
                            $block['chunk_index'] = $chunkIdx + 1;
                            $transcription[] = $block;
                        }
                    }
                }
            }
        @endphp

        {{-- Conversation Dynamics Chart & Agent Analysis --}}
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 p-8 border border-gray-100 mb-8"
            x-show="!showRaw">
            <div class="flex flex-col lg:flex-row gap-8 items-stretch">
                <!-- Left part: The Chart (2/3 width) -->
                <div class="lg:w-2/3 flex flex-col justify-between">
                    <div>
                        <span
                            class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                            <i data-lucide="activity" class="w-3.5 h-3.5 text-bima-red animate-pulse"></i> Grafik Aliran
                            Intonasi Percakapan
                        </span>
                        <h3 class="text-lg font-black text-gray-950 uppercase tracking-wide mb-6">Dinamika Intonasi Per
                            Baris</h3>
                    </div>
                    <div class="relative w-full h-64">
                        <canvas id="dynamicsChart"></canvas>
                    </div>
                </div>

                <!-- Right part: AI Summary of Chart (1/3 width) -->
                <div
                    class="lg:w-1/3 bg-gray-50 border border-gray-100 rounded-3xl p-6 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 text-gray-200/30 pointer-events-none">
                        <i data-lucide="sparkles" class="w-32 h-32"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-3">
                            <div
                                class="w-8 h-8 rounded-lg bg-bima-red/10 text-bima-red flex items-center justify-center p-1.5">
                                <x-application-logo class="w-full h-full" />
                            </div>
                            <div>
                                <span
                                    class="block text-[0.55rem] font-black text-gray-400 uppercase tracking-widest">Supervisory
                                    AI</span>
                                <span class="text-xs font-black text-gray-800 uppercase tracking-wider">Interpretasi
                                    Aliran</span>
                            </div>
                        </div>

                        @php
                            $upCount = 0;
                            $downCount = 0;
                            foreach ($transcription as $block) {
                                $type = $block['intonation_type'] ?? ($block['advice_type'] ?? 'neutral');
                                if ($type === 'up') {
                                    $upCount++;
                                }
                                if ($type === 'down') {
                                    $downCount++;
                                }
                            }

                            if ($downCount > $upCount) {
                                $dynamicsSummary =
                                    'Alur percakapan didominasi oleh intonasi menurun (' .
                                    $downCount .
                                    ' kali) yang menandakan instruksi tegas, korektif, dan pemberian saran bimbingan yang terarah dari Dosen. Hal ini menunjukkan dinamika direktif di mana wacana dikontrol untuk mengarahkan kualitas akademis riset mahasiswa.';
                                $dynamicsStatus = 'Direktif & Korektif';
                                $statusColor = 'text-red-700 bg-red-50 border-red-200';
                            } elseif ($upCount > $downCount) {
                                $dynamicsSummary =
                                    'Alur percakapan didominasi oleh intonasi menaik (' .
                                    $upCount .
                                    ' kali) yang mencerminkan nada tanya, eksplorasi koperatif, atau memicu kebingungan produktif. Ini mencerminkan hubungan dialogis di mana bimbingan berjalan interaktif dan bersahabat.';
                                $dynamicsStatus = 'Dialogis & Koperatif';
                                $statusColor = 'text-blue-700 bg-blue-50 border-blue-200';
                            } else {
                                $dynamicsSummary =
                                    'Dinamika percakapan berjalan seimbang antara intonasi turun (korektif) dan naik (klarifikasi/tanya). Ini mencerminkan keseimbangan relasi bimbingan yang sangat kondusif, interaktif, dan berorientasi pada pemecahan masalah bersama.';
                                $dynamicsStatus = 'Kondusif & Seimbang';
                                $statusColor = 'text-green-700 bg-green-50 border-green-200';
                            }
                        @endphp

                        <p class="text-sm font-medium text-gray-600 leading-relaxed font-serif mt-4">
                            {{ $dynamicsSummary }}
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-between items-center relative z-10">
                        <span class="text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Karakter
                            Aliran</span>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-xl text-[0.65rem] font-black uppercase tracking-widest border {{ $statusColor }}">
                            {{ $dynamicsStatus }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile Tab Switcher --}}
        <div class="lg:hidden flex bg-gray-100 p-1 rounded-2xl mb-6 relative z-10" x-show="!showRaw">
            <button @click="activeMobileTab = 'transkrip'"
                class="flex-1 py-3 text-xs font-black uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2 cursor-pointer"
                :class="activeMobileTab === 'transkrip' ? 'bg-white text-gray-950 shadow-sm' : 'text-gray-400'">
                <i data-lucide="file-text" class="w-4 h-4"></i> Transkrip
            </button>
            <button @click="activeMobileTab = 'ringkasan'"
                class="flex-1 py-3 text-xs font-black uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2 cursor-pointer"
                :class="activeMobileTab === 'ringkasan' ? 'bg-white text-gray-950 shadow-sm' : 'text-gray-400'">
                <i data-lucide="brain-circuit" class="w-4 h-4"></i> Ringkasan
            </button>
        </div>

        {{-- Main Content: Two Columns --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-show="!showRaw">

            {{-- Left Column: Document View --}}
            <div
                class="lg:col-span-2 bg-white rounded-3xl shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100 transition-all duration-300"
                :class="activeMobileTab === 'transkrip' ? 'block' : 'hidden lg:block'">
                <div class="p-8 md:p-10 space-y-12" x-init="totalRows = {{ count($transcription) }}">
                    @if (is_array($transcription) && count($transcription) > 0)
                        @foreach ($transcription as $index => $block)
                            @php
                                $chunkIndex = $block['chunk_index'] ?? null;
                                $prevChunkIndex = ($index > 0) ? ($transcription[$index - 1]['chunk_index'] ?? null) : null;
                            @endphp
                            
                            {{-- Progressive mode segment header --}}
                            @if (!$isCompleted && $chunkIndex !== null && ($index === 0 || $prevChunkIndex !== $chunkIndex))
                                <div class="my-8 py-3 px-6 bg-gradient-to-r from-gray-50 to-gray-150 border border-gray-200/50 rounded-2xl flex items-center justify-between shadow-sm animate-fade-in relative overflow-hidden">
                                    <div class="absolute top-0 left-0 h-full w-1 bg-bima-red"></div>
                                    <span class="text-xs font-black uppercase tracking-wider text-gray-500 flex items-center gap-2">
                                        <i data-lucide="layers" class="w-3.5 h-3.5 text-bima-red animate-pulse"></i> Segmen Analisa {{ $chunkIndex }}
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-[0.6rem] font-black uppercase tracking-widest bg-green-50 text-green-700 border border-green-200">
                                        Selesai Diproses
                                    </span>
                                </div>
                            @endif

                            <div class="flex flex-col gap-4 group" 
                                 x-show="synthesisStatus !== 'completed' || ({{ $index }} >= (currentPage - 1) * pageSize && {{ $index }} < (currentPage) * pageSize)">
                                <div class="flex flex-col md:flex-row gap-4 md:gap-8">
                                    {{-- Speaker & Timestamp Column --}}
                                    <div class="md:w-32 flex-shrink-0 pt-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span
                                                class="font-black text-sm uppercase tracking-wider {{ strtolower($block['speaker'] ?? '') == 'dosen' ? 'text-bima-red' : 'text-blue-600' }}">
                                                {{ $block['speaker'] ?? 'Unknown' }}
                                            </span>
                                        </div>
                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Baris
                                            {{ $index + 1 }}</div>
                                        @if (!empty($block['timestamp']))
                                            <div
                                                class="text-[0.65rem] font-medium text-gray-400 mt-1 flex items-center">
                                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                                {{ $block['timestamp'] }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Text Column --}}
                                    <div class="flex-grow">
                                        <p
                                            class="text-lg md:text-xl font-medium text-gray-800 leading-relaxed tracking-wide font-serif">
                                            {!! $block['text_html'] ?? '' !!}

                                            <span class="inline-flex flex-wrap gap-2 ml-2 align-middle select-none">
                                                {{-- Advice Badge --}}
                                                @if (isset($block['is_advice']) && $block['is_advice'])
                                                    <button
                                                        @click="openInsight('Pemberian Saran (Advice Giving)', 'advice', '{{ addslashes($block['agent_insight'] ?? 'Dosen memberikan saran akademik bimbingan.') }}', '{{ addslashes($block['advice_relation'] ?? 'Kalimat ini merupakan saran bimbingan akademik yang menanggapi progres mahasiswa di baris ini.') }}')"
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-[0.65rem] font-black uppercase tracking-widest cursor-pointer transition-all hover:scale-105"
                                                        title="Klik untuk melihat detail advice & relasi kalimat">
                                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none"
                                                            stroke="currentColor" stroke-width="2.5"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 18.5a6.5 6.5 0 100-13 6.5 6.5 0 000 13zM12 11.5v3M12 9h.01" />
                                                        </svg>
                                                        Advice
                                                    </button>
                                                @endif

                                                {{-- Intonasi Badge --}}
                                                @if (!empty($block['advice_type']) || !empty($block['intonation_type']))
                                                    @php
                                                        $intType =
                                                            $block['intonation_type'] ??
                                                            ($block['advice_type'] ?? 'neutral');
                                                        $intReason =
                                                            'Tingkat intonasi baris ini adalah ' .
                                                            ($intType === 'up'
                                                                ? 'Naik / Tinggi'
                                                                : ($intType === 'down'
                                                                    ? 'Turun / Tegas'
                                                                    : 'Netral'));
                                                        if (!empty($block['intonation_markers'][0]['reason'])) {
                                                            $intReason = $block['intonation_markers'][0]['reason'];
                                                        }
                                                        $intRelation =
                                                            $block['intonation_markers'][0]['relation'] ??
                                                            'Karakter intonasi ini merefleksikan dinamika percakapan di baris ini.';
                                                    @endphp
                                                    <button
                                                        @click="openInsight('Intonasi Kalimat', '{{ $intType }}', '{{ addslashes($intReason) }}', '{{ addslashes($intRelation) }}')"
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[0.65rem] font-black uppercase tracking-widest cursor-pointer transition-all hover:scale-105 border
                                                                   {{ $intType === 'up' ? 'bg-blue-50 hover:bg-blue-100 text-blue-700 border-blue-200' : ($intType === 'down' ? 'bg-red-50 hover:bg-red-100 text-red-700 border-red-200' : 'bg-gray-50 hover:bg-gray-100 text-gray-700 border-gray-200') }}"
                                                        title="Klik untuk melihat detail intonasi baris">
                                                        @if ($intType === 'up')
                                                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none"
                                                                stroke="currentColor" stroke-width="2.5"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                            </svg>
                                                        @elseif($intType === 'down')
                                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none"
                                                                stroke="currentColor" stroke-width="2.5"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                                                            </svg>
                                                        @else
                                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none"
                                                                stroke="currentColor" stroke-width="2.5"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M5 12h14" />
                                                            </svg>
                                                        @endif
                                                        Intonasi: {{ $intType }}
                                                    </button>
                                                @endif

                                                {{-- Karakter Relasi Badge --}}
                                                @if (
                                                    !empty($block['advice_relation']) ||
                                                        !empty($block['relation']) ||
                                                        !empty($block['intonation_markers'][0]['relation']))
                                                    @php
                                                        $relExplanation =
                                                            $block['advice_relation'] ??
                                                            ($block['relation'] ??
                                                                ($block['intonation_markers'][0]['relation'] ??
                                                                    'Kalimat ini memiliki relasi logis dengan baris pembicaraan sebelumnya.'));
                                                    @endphp
                                                    <button
                                                        @click="openInsight('Karakter & Relasi Kalimat', 'relation', 'AI mendeteksi hubungan antar-kalimat yang erat pada baris ini.', '{{ addslashes($relExplanation) }}')"
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-[0.65rem] font-black uppercase tracking-widest cursor-pointer transition-all hover:scale-105"
                                                        title="Klik untuk melihat karakter & relasi kalimat">
                                                        <svg class="w-3.5 h-3.5 text-purple-500" fill="none"
                                                            stroke="currentColor" stroke-width="2.5"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                        </svg>
                                                        Relasi
                                                    </button>
                                                @endif
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                {{-- Agent Insight Banner --}}
                                @if (!empty($block['agent_insight']))
                                    <div
                                        class="ml-0 md:ml-40 bg-gray-50 border-l-4 border-gray-300 rounded-r-xl p-4 mt-2">
                                        <div class="flex items-start gap-3">
                                            <div class="mt-0.5 text-gray-400 w-5 h-5 shrink-0 flex items-center justify-center">
                                                <x-application-logo class="w-full h-full" />
                                            </div>
                                            <div>
                                                <span
                                                    class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 block mb-1">Agent
                                                    Insight</span>
                                                <p class="text-sm font-medium text-gray-600">
                                                    {{ $block['agent_insight'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Line Feedback Bar --}}
                                <div class="ml-0 md:ml-40 flex items-center justify-between gap-4 mt-2 text-xs"
                                    x-data="{
                                        feedback: '{{ $block['user_feedback'] ?? 'none' }}',
                                        loading: false,
                                        async sendFeedback(type) {
                                            if (this.loading) return;
                                            this.loading = true;
                                            try {
                                                let res = await fetch('{{ route('analysis.line-feedback', $analysis->id) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    body: JSON.stringify({ index: {{ $index }}, type: type })
                                                });
                                                let data = await res.json();
                                                if (data.status === 'success') {
                                                    this.feedback = data.user_feedback;
                                                }
                                            } catch (e) {
                                                console.error(e);
                                            } finally {
                                                this.loading = false;
                                            }
                                        }
                                    }">
                                    <div
                                        class="flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-full px-3 py-1.5 text-gray-400">
                                        <span
                                            class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Evaluasi
                                            Kalimat:</span>
                                        <div class="flex items-center gap-2">
                                            <!-- Thumbs Up -->
                                            <button @click="sendFeedback('up')"
                                                class="hover:text-green-600 transition focus:outline-none flex items-center justify-center p-0.5 cursor-pointer"
                                                :class="feedback === 'up' ? 'text-green-600' : 'text-gray-400'">
                                                <svg class="w-4 h-4"
                                                    :fill="feedback === 'up' ? 'currentColor' : 'none'"
                                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3zM7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3" />
                                                </svg>
                                            </button>

                                            <!-- Thumbs Down -->
                                            <button @click="sendFeedback('down')"
                                                class="hover:text-red-600 transition focus:outline-none flex items-center justify-center p-0.5 cursor-pointer"
                                                :class="feedback === 'down' ? 'text-red-600' : 'text-gray-400'">
                                                <svg class="w-4 h-4"
                                                    :fill="feedback === 'down' ? 'currentColor' : 'none'"
                                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M10 15v4a3 3 0 003 3l4-9V2H5.72a2 2 0 00-2 1.7l-1.38 9a2 2 0 002 2.3zm7-13h3a2 2 0 012 2v7a2 2 0 01-2 2h-3" />
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Spinner Loader -->
                                        <svg x-show="loading" class="animate-spin h-3.5 w-3.5 text-bima-red ml-1"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            style="display: none;">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            @if (!$loop->last)
                                <div class="h-px w-full bg-gray-100" x-show="synthesisStatus !== 'completed' || ({{ $index }} >= (currentPage - 1) * pageSize && {{ $index }} < (currentPage) * pageSize - 1)"></div>
                            @endif
                        @endforeach

                        <!-- Dynamic Pagination Navigation Control -->
                        <template x-if="synthesisStatus === 'completed' && Math.ceil(totalRows / pageSize) > 1">
                            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
                                <button type="button" 
                                    @click="currentPage = Math.max(1, currentPage - 1)" 
                                    :disabled="currentPage === 1"
                                    class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold uppercase tracking-widest text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:hover:bg-transparent transition-colors cursor-pointer focus:outline-none">
                                    <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i> Prev
                                </button>
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">
                                    Halaman <span x-text="currentPage" class="text-gray-900 font-extrabold"></span> dari <span x-text="Math.ceil(totalRows / pageSize)"></span>
                                </span>
                                <button type="button" 
                                    @click="currentPage = Math.min(Math.ceil(totalRows / pageSize), currentPage + 1)" 
                                    :disabled="currentPage === Math.ceil(totalRows / pageSize)"
                                    class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold uppercase tracking-widest text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:hover:bg-transparent transition-colors cursor-pointer focus:outline-none">
                                    Next <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                                </button>
                            </div>
                        </template>
                    @else
                        {{-- Beautiful Progressive Skeleton Loader when first chunk is compiling --}}
                        <div class="space-y-8 animate-pulse py-6">
                            <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-center gap-3 text-blue-700 mb-6">
                                <i data-lucide="info" class="w-4 h-4 animate-bounce"></i>
                                <span class="text-xs font-bold uppercase tracking-wider">Sedang memproses segmen pertama audio...</span>
                            </div>
                            @for ($i = 0; $i < 3; $i++)
                                <div class="flex flex-col gap-4">
                                    <div class="flex gap-8">
                                        <div class="w-32 flex-shrink-0 space-y-2">
                                            <div class="h-4 bg-gray-200 rounded-lg w-16"></div>
                                            <div class="h-3 bg-gray-100 rounded-lg w-20"></div>
                                        </div>
                                        <div class="flex-grow space-y-3">
                                            <div class="h-5 bg-gray-200 rounded-2xl w-full"></div>
                                            <div class="h-5 bg-gray-200 rounded-2xl w-5/6"></div>
                                            <div class="h-5 bg-gray-100 rounded-2xl w-3/4"></div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: Summary Dashboard --}}
            <div class="lg:col-span-1 space-y-6 transition-all duration-300"
                :class="activeMobileTab === 'ringkasan' ? 'block' : 'hidden lg:block'">
                @php $summary = $analysis->result_data['summary'] ?? []; @endphp

                <div
                    class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-6 border border-gray-100 relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 text-gray-50 opacity-50 pointer-events-none">
                        <i data-lucide="brain-circuit" class="w-48 h-48"></i>
                    </div>

                    <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 mr-2"></i> Hasil Analisis Akhir
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-4 mb-4">
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex flex-col justify-center">
                            <span
                                class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1">Kategori
                                Advice</span>
                            <span
                                class="font-bold text-gray-900 leading-tight block break-words">{{ $summary['kategori_advice'] ?? '-' }}</span>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex flex-col justify-center">
                            <span
                                class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1">Karakter
                                Relasi</span>
                            <span
                                class="font-bold text-gray-900 leading-tight block break-words">{{ $summary['karakter_relasi'] ?? '-' }}</span>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex flex-col justify-center">
                            <span
                                class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1">Intonasi</span>
                            <span
                                class="font-bold text-gray-900 leading-tight block break-words">{{ $summary['intonasi_dominan'] ?? '-' }}</span>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex flex-col justify-center">
                            <span
                                class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1">Ranah
                                Bicara</span>
                            <span
                                class="font-bold text-gray-900 leading-tight block text-sm break-words">{{ \Illuminate\Support\Str::limit($summary['ranah_pembicaraan'] ?? '-', 50) }}</span>
                        </div>
                    </div>

                    <div class="bg-bima-red rounded-2xl p-6 text-white shadow-lg shadow-red-500/20 mb-4">
                        <span
                            class="block text-[0.6rem] font-black text-red-200 uppercase tracking-widest mb-2 flex items-center">
                            <i data-lucide="target" class="w-3 h-3 mr-1"></i> Arah Tujuan
                        </span>
                        <p class="font-medium text-sm leading-relaxed">{{ $summary['arah_tujuan'] ?? '-' }}</p>
                    </div>

                    <div class="bg-green-50 border border-green-100 rounded-2xl p-6 text-green-900">
                        <span
                            class="block text-[0.6rem] font-black text-green-600 uppercase tracking-widest mb-2 flex items-center">
                            <i data-lucide="sparkles" class="w-3 h-3 mr-1"></i> Saran Perbaikan
                        </span>
                        <p class="font-medium text-sm leading-relaxed">{{ $summary['saran_perbaikan'] ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Feedback Banner --}}
        <div x-data="{
            submitted: {{ $analysis->feedback ? 'true' : 'false' }},
            loading: false,
            comments: '',
            async submitFeedback(status) {
                if (this.loading) return;
                this.loading = true;
                try {
                    let res = await fetch('{{ route('analysis.feedback', $analysis->id) }}', {
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
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            }
        }" class="mt-8">
            <div x-show="!submitted"
                class="bg-gray-900 rounded-[2.5rem] p-8 md:p-10 text-white shadow-2xl relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-r from-bima-red/20 to-transparent opacity-50"></div>
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex-grow text-center md:text-left">
                        <h3
                            class="text-lg font-black uppercase tracking-widest mb-2 flex items-center justify-center md:justify-start gap-2">
                            <x-application-logo class="w-5 h-5 text-bima-red" /> Penilaian Kesesuaian Hasil
                        </h3>
                        <p class="text-gray-300 text-sm font-medium leading-relaxed">Sebagai pakar atau peninjau akademis, penilaian Anda sangat berharga bagi penyempurnaan akurasi kami. Menurut Anda, apakah hasil anotasi intonasi dan saran bimbingan sistem ini sudah sesuai dengan fakta percakapan riil?</p>
                    </div>

                    <div class="flex-shrink-0 w-full md:w-auto flex items-center gap-4">
                        <div class="flex gap-3 w-full" x-show="!loading">
                            <button @click="submitFeedback(1)"
                                class="flex-1 md:flex-none px-6 py-3.5 bg-green-600 hover:bg-green-500 text-white font-bold text-xs uppercase tracking-wider rounded-2xl transition-all hover:scale-105 cursor-pointer flex items-center justify-center gap-1.5 shadow-lg shadow-green-600/20">
                                <i data-lucide="check-circle" class="w-4 h-4"></i> Sudah Sesuai
                            </button>
                            <button @click="submitFeedback(0)"
                                class="flex-1 md:flex-none px-6 py-3.5 bg-red-600 hover:bg-red-500 text-white font-bold text-xs uppercase tracking-wider rounded-2xl transition-all hover:scale-105 cursor-pointer flex items-center justify-center gap-1.5 shadow-lg shadow-red-600/20">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i> Belum Sesuai
                            </button>
                        </div>

                        <div x-show="loading" class="flex items-center gap-2 text-white/60" style="display: none;">
                            <svg class="animate-spin h-6 w-6 text-bima-red" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="text-xs font-black uppercase tracking-wider">Mengirim Validasi...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="submitted" x-transition.opacity style="display: none;"
                class="bg-green-50 border border-green-200 rounded-[2.5rem] p-8 text-center text-green-800 shadow-sm">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
                <p class="font-black text-sm uppercase tracking-wider">Apresiasi Kami Atas Umpan Balik Anda!</p>
                <p class="text-xs mt-1 text-green-600 font-medium">Validasi Anda berhasil direkam untuk terus meningkatkan akurasi algoritma metrik Supervisory AI.</p>
            </div>
        </div>

        {{-- Supervisory AI Insight Modal --}}
        <div x-show="showInsightModal"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none"
            style="display: none;">
            <!-- Backdrop overlay -->
            <div x-show="showInsightModal" @click="showInsightModal = false"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/55 backdrop-blur-md"></div>

            <!-- Modal Dialog Card -->
            <div x-show="showInsightModal" x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-lg mx-auto my-6 z-50 bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 p-8">

                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-5 mb-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-bima-red/10 text-bima-red flex items-center justify-center p-2.5">
                            <x-application-logo class="w-full h-full" />
                        </div>
                        <div>
                            <span
                                class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Supervisory
                                AI Insight</span>
                            <h3 class="text-base font-black text-gray-950 uppercase tracking-wide"
                                x-text="insightTitle"></h3>
                        </div>
                    </div>

                    <button @click="showInsightModal = false"
                        class="p-2 hover:bg-gray-100 text-gray-400 hover:text-gray-600 rounded-xl transition-all cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="space-y-6">
                    <!-- Penjelasan AI -->
                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 relative overflow-hidden">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-8 h-8 rounded-lg bg-white border border-gray-100 text-gray-500 flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-4 h-4 text-bima-red" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 00-2-2m2 2a2 2 0 01-2 2m2-2h-2m-6-3h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <div>
                                <span
                                    class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1.5">Penjelasan
                                    AI Agent</span>
                                <p class="text-sm font-medium text-gray-700 leading-relaxed font-sans"
                                    x-text="insightExplanation"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Karakter Relasi -->
                    <div class="bg-blue-50/50 border border-blue-100/80 rounded-2xl p-5 relative overflow-hidden">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-8 h-8 rounded-lg bg-white border border-blue-100 text-blue-500 flex items-center justify-center flex-shrink-0 shadow-sm">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                    stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                            <div>
                                <span
                                    class="block text-[0.6rem] font-black text-blue-500 uppercase tracking-widest mb-1.5">Karakter
                                    & Relasi Kalimat (Syntactic Relation)</span>
                                <p class="text-sm font-bold text-blue-900 leading-relaxed font-sans"
                                    x-text="insightRelation"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="mt-8 flex justify-end">
                    <button @click="showInsightModal = false"
                        class="px-6 py-2.5 bg-gray-950 hover:bg-gray-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Floating Compilation Status Indicator -->
        <div x-show="synthesisStatus !== 'completed'" style="display: none;"
            class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 w-[90%] max-w-2xl bg-white/95 backdrop-blur-xl border rounded-3xl p-5 shadow-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all"
            :class="synthesisStatus === 'failed' ? 'border-red-200 bg-red-50/95' : 'border-gray-200/50'">
            
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center p-2 relative"
                    :class="synthesisStatus === 'failed' ? 'bg-red-100 text-red-600' : 'bg-bima-red/10 text-bima-red'">
                    <template x-if="synthesisStatus !== 'failed'">
                        <div class="absolute inset-0 rounded-2xl bg-bima-red/20 animate-ping"></div>
                    </template>
                    <div class="w-5 h-5 flex items-center justify-center">
                        <svg class="w-full h-full" :class="synthesisStatus !== 'failed' ? 'animate-spin-slow' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"></polygon>
                            <line x1="12" y1="22" x2="12" y2="15.5"></line>
                            <line x1="12" y1="15.5" x2="22" y2="8.5"></line>
                            <line x1="12" y1="15.5" x2="2" y2="8.5"></line>
                            <polyline points="2 8.5 12 15.5 22 8.5"></polyline>
                            <polyline points="12 2 12 15.5"></polyline>
                        </svg>
                    </div>
                </div>
                <div>
                    <span class="block text-[0.6rem] font-black uppercase tracking-widest"
                        :class="synthesisStatus === 'failed' ? 'text-red-500' : 'text-gray-400'">Real-time Compiler</span>
                    <p class="text-xs font-bold uppercase tracking-wider"
                        :class="synthesisStatus === 'failed' ? 'text-red-700' : 'text-gray-800'"
                        x-text="synthesisStatus === 'failed' ? (synthesisError || 'Gagal menyusun halaman hasil analisa.') : 'Supervisory AI sedang menyusun halaman hasil analisa audio...'"></p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <template x-if="synthesisStatus === 'failed'">
                    <button type="button" @click="startBackgroundSynthesis" class="bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-xl transition-all flex items-center gap-1 cursor-pointer border border-transparent shadow-md focus:outline-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 6H16"></path>
                        </svg>
                        Coba Lagi
                    </button>
                </template>
                <template x-if="synthesisStatus !== 'failed'">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                        <span class="text-[0.65rem] font-black uppercase tracking-widest text-blue-600">Sintesis</span>
                    </div>
                </template>
            </div>
        </div>

    </div>

    <x-slot name="scripts">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @php
            $transBlocks = $analysis->result_data['transcription'] ?? [];
            $chartPoints = array_map(
                function ($block, $idx) {
                    $type = $block['intonation_type'] ?? ($block['advice_type'] ?? 'neutral');
                    $val = 0;
                    if ($type === 'up') {
                        $val = 1;
                    }
                    if ($type === 'down') {
                        $val = -1;
                    }

                    return [
                        'label' => 'Baris ' . ($idx + 1),
                        'value' => $val,
                        'speaker' => $block['speaker'] ?? 'Unknown',
                        'type' => $type === 'up' ? 'Naik' : ($type === 'down' ? 'Turun' : 'Netral'),
                        'text' => mb_strimwidth(strip_tags($block['text_html'] ?? ''), 0, 50, '...'),
                    ];
                },
                $transBlocks,
                array_keys($transBlocks),
            );
        @endphp
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const canvasEl = document.getElementById('dynamicsChart');
                if (!canvasEl) return;

                const ctx = canvasEl.getContext('2d');

                const chartData = @json($chartPoints);

                const labels = chartData.map(d => d.label);
                const values = chartData.map(d => d.value);
                const pointColors = chartData.map(d => d.speaker.toLowerCase() === 'dosen' ? '#cc0000' : '#2563eb');
                const pointStyles = chartData.map(d => d.speaker.toLowerCase() === 'dosen' ? 'circle' : 'rectRot');

                const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                gradient.addColorStop(0, 'rgba(204, 0, 0, 0.15)');
                gradient.addColorStop(0.5, 'rgba(37, 99, 235, 0.05)');
                gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Dinamika Aliran',
                            data: values,
                            borderColor: '#1f2937',
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
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#111827',
                                titleFont: {
                                    size: 11,
                                    weight: 'bold',
                                    family: 'Inter'
                                },
                                bodyFont: {
                                    size: 11,
                                    family: 'Inter'
                                },
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
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 10,
                                        family: 'Inter',
                                        weight: '600'
                                    },
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
                                    font: {
                                        size: 10,
                                        family: 'Inter',
                                        weight: 'bold'
                                    },
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
            });
        </script>
    </x-slot>
</x-layouts.app>
