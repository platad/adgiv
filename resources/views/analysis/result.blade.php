@php
    $isCompleted = $analysis->status === 'completed';
    $totalChunks = $analysis->result_data['total_chunks'] ?? count($analysis->result_data['chunks'] ?? []);
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

    $translationsMap = [
        // Kategori Advice
        'Saran Bimbingan Akademik' => [
            'id' => 'Saran Bimbingan Akademik',
            'en' => 'Academic Supervision Advice',
            'zh' => '学术指导建议'
        ],
        'Instruksi Direktif Dosen' => [
            'id' => 'Instruksi Direktif Dosen',
            'en' => 'Directive Supervisor Instruction',
            'zh' => '导师指令性指示'
        ],
        'Saran Akademik Terarah' => [
            'id' => 'Saran Akademik Terarah',
            'en' => 'Structured Academic Advice',
            'zh' => '定向学术建议'
        ],

        // Karakter Relasi
        'Relasi Kuasa Direktif Dosen' => [
            'id' => 'Relasi Kuasa Direktif Dosen',
            'en' => 'Directive Supervisor Power Relation',
            'zh' => '导师指令性权力关系'
        ],
        'Koperatif & Dialogis' => [
            'id' => 'Koperatif & Dialogis',
            'en' => 'Collaborative & Dialogic',
            'zh' => '合作与对话'
        ],
        'Dialogis Kondusif' => [
            'id' => 'Dialogis Kondusif',
            'en' => 'Conducive Dialogic',
            'zh' => '建设性对话'
        ],
        'Kondusif & Seimbang' => [
            'id' => 'Kondusif & Seimbang',
            'en' => 'Conducive & Balanced',
            'zh' => '建设与平衡'
        ],

        // Intonasi
        'Intonasi Turun Dominan' => [
            'id' => 'Intonasi Turun Dominan',
            'en' => 'Dominant Falling Intonation',
            'zh' => '主导递降语调'
        ],
        'Intonasi Naik Dominan' => [
            'id' => 'Intonasi Naik Dominan',
            'en' => 'Dominant Rising Intonation',
            'zh' => '主导递升语调'
        ],
        'Intonasi Seimbang' => [
            'id' => 'Intonasi Seimbang',
            'en' => 'Balanced Intonation',
            'zh' => '平衡语调'
        ],
        'Netral' => [
            'id' => 'Netral',
            'en' => 'Neutral',
            'zh' => '中性'
        ],
        'Turun' => [
            'id' => 'Turun',
            'en' => 'Falling',
            'zh' => '递降'
        ],
        'Naik' => [
            'id' => 'Naik',
            'en' => 'Rising',
            'zh' => '递升'
        ]
    ];

    $translateValue = function($val) use ($translationsMap) {
        $trimmed = trim($val ?? '');
        if ($trimmed === '' || $trimmed === '-') {
            return ['id' => '-', 'en' => '-', 'zh' => '-'];
        }
        if (isset($translationsMap[$trimmed])) {
            return $translationsMap[$trimmed];
        }
        foreach ($translationsMap as $k => $trans) {
            if (stripos($trimmed, $k) !== false) {
                return $trans;
            }
        }
        return ['id' => $val, 'en' => $val, 'zh' => $val];
    };
@endphp
<x-slot name="styles">
    <style>
        @media print {
            /* Hide non-printable elements */
            aside, nav, header, footer, button, .no-print, 
            [class*="mobile-header"], [class*="mobile-nav"],
            .lg\:hidden, .fixed, .absolute, .pointer-events-none,
            #insightModal, [x-show="showRaw"] {
                display: none !important;
            }
            
            /* Reset body background for printing */
            body {
                background: white !important;
                color: black !important;
            }
            
            /* Remove left sidebar padding completely */
            .lg\:pl-52, .lg\:pr-8, .lg\:py-10 {
                padding-left: 0 !important;
                padding-right: 0 !important;
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                margin: 0 !important;
            }
            
            .flex-1.flex.flex-col.w-full {
                padding: 0 !important;
                margin: 0 !important;
            }
            
            main {
                overflow: visible !important;
            }
            
            /* Force all paginated dialogue rows to print together */
            [id^="baris-"] {
                display: flex !important;
                page-break-inside: avoid !important;
                opacity: 1 !important;
                visibility: visible !important;
                border-bottom: 1px solid #f3f4f6 !important;
                background-color: transparent !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            
            /* Force layout columns to block layout on paper */
            .lg\:col-span-2, .lg\:col-span-1, .lg\:w-2/3, .lg\:w-1/3 {
                width: 100% !important;
                float: none !important;
            }
            
            .grid {
                display: block !important;
            }
            
            .flex {
                display: flex !important;
            }
            
            /* Clean container border box rules for printing */
            .bg-white {
                box-shadow: none !important;
                border: 1px solid #e5e7eb !important;
                page-break-inside: avoid !important;
                margin-bottom: 1.5rem !important;
                border-radius: 1rem !important;
                padding: 1.5rem !important;
            }
            
            /* Hide pagination navigation footer */
            .mt-8.pt-6.border-t.border-gray-100 {
                display: none !important;
            }
        }
    </style>
</x-slot>

<x-layouts.app :title="app()->getLocale() === 'zh' ? '分析结果' : (app()->getLocale() === 'en' ? 'Analysis Result' : 'Hasil Analisis')">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 animate-fade-in" 
        x-data="{
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
            appLang: '{{ app()->getLocale() }}',
        
            openInsight(title, type, explanation, relation) {
                this.insightTitle = title;
                this.insightType = type;
                this.insightExplanation = explanation;
                this.insightRelation = relation || (this.appLang === 'zh' ? '此行暂无特定关联关系。' : (this.appLang === 'en' ? 'No specific relation with other lines.' : 'Tidak ada relasi khusus dengan baris lain.'));
                this.showInsightModal = true;
            }
        }"
        @go-to-baris.window="
            const idx = $event.detail.index;
            const targetPage = Math.ceil((idx + 1) / pageSize);
            currentPage = targetPage;
            activeMobileTab = 'transkrip';
            setTimeout(() => {
                const el = document.getElementById('baris-' + idx);
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    el.classList.add('bg-amber-50', 'ring-4', 'ring-amber-200/50', 'scale-[1.01]', 'border-amber-200');
                    setTimeout(() => {
                        el.classList.remove('bg-amber-50', 'ring-4', 'ring-amber-200/50', 'scale-[1.01]', 'border-amber-200');
                    }, 2000);
                }
            }, 150);
        "
    >
        

        {{-- Header --}}
        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-gray-100 pb-8">
            <div>
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest mb-4">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> 
                    <span class="lang-id">Dashboard</span>
                    <span class="lang-en">Dashboard</span>
                    <span class="lang-zh">控制面板</span>
                </a>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">{{ $analysis->title }}</h1>
                <p class="text-gray-500 font-medium mt-2 text-sm">
                    <span class="lang-id">Transkripsi Multimodal & Anotasi Bimbingan</span>
                    <span class="lang-en">Multimodal Transcription & Supervision Annotation</span>
                    <span class="lang-zh">多模态转录与指导注解</span>
                </p>
            </div>

            <div class="flex flex-wrap md:flex-nowrap items-center gap-3 shrink-0">
                <a x-show="synthesisStatus === 'completed'" href="{{ route('analysis.print', $analysis->id) }}" target="_blank"
                    class="inline-flex items-center px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-[0.7rem] font-black uppercase tracking-widest transition-colors shadow-sm shadow-purple-500/10 cursor-pointer whitespace-nowrap">
                    <i data-lucide="printer" class="w-3.5 h-3.5 mr-2"></i> 
                    <span class="lang-id">Cetak Laporan</span>
                    <span class="lang-en">Print Report</span>
                    <span class="lang-zh">打印报告</span>
                </a>

                <button @click="showRaw = !showRaw"
                    class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 text-[0.7rem] font-black uppercase tracking-widest transition-colors whitespace-nowrap cursor-pointer">
                    <i data-lucide="code" class="w-3.5 h-3.5 mr-2"></i> <span
                        x-text="showRaw ? (appLang === 'zh' ? '关闭原始数据' : (appLang === 'en' ? 'Close Raw' : 'Tutup Raw')) : (appLang === 'zh' ? '查看原始数据' : (appLang === 'en' ? 'View Raw' : 'Lihat Raw'))"></span>
                </button>
                
                {{-- Dynamic Status Badge --}}
                <span x-show="synthesisStatus === 'completed'" class="inline-flex flex-wrap md:flex-nowrap items-center gap-2 shrink-0">
                    <span class="inline-flex items-center px-4 py-2.5 rounded-xl bg-purple-50 text-purple-700 text-[0.7rem] font-black uppercase tracking-widest border border-purple-100 shadow-sm whitespace-nowrap">
                        <i data-lucide="music-4" class="w-3.5 h-3.5 mr-1.5 text-purple-500"></i> {{ $totalChunks > 0 ? $totalChunks : '8' }} Chunks
                    </span>
                    <span class="inline-flex items-center px-4 py-2.5 rounded-xl bg-green-50 text-green-700 text-[0.7rem] font-black uppercase tracking-widest border border-green-100 shadow-sm whitespace-nowrap">
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5 mr-1.5 text-green-500"></i> 
                        <span class="lang-id">Selesai</span>
                        <span class="lang-en">Completed</span>
                        <span class="lang-zh">已完成</span>
                    </span>
                </span>
                <span x-show="synthesisStatus === 'processing' || synthesisStatus === 'pending'" style="display: none;"
                    class="inline-flex items-center px-4 py-2.5 rounded-xl bg-blue-50 text-blue-700 text-[0.7rem] font-black uppercase tracking-widest animate-pulse whitespace-nowrap">
                    <i data-lucide="loader-2" class="w-3.5 h-3.5 mr-2 animate-spin text-blue-500"></i> 
                    <span class="lang-id">Menyusun...</span>
                    <span class="lang-en">Compiling...</span>
                    <span class="lang-zh">编译中...</span>
                </span>
                <span x-show="synthesisStatus === 'failed'" style="display: none;"
                    class="inline-flex items-center px-4 py-2.5 rounded-xl bg-red-50 text-red-700 text-[0.7rem] font-black uppercase tracking-widest animate-shake whitespace-nowrap">
                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5 mr-2"></i> 
                    <span class="lang-id">Gagal</span>
                    <span class="lang-en">Failed</span>
                    <span class="lang-zh">失败</span>
                </span>
            </div>
        </div>

        {{-- Raw Data View --}}
        <div x-show="showRaw" style="display: none;" class="bg-gray-900 rounded-3xl shadow-xl mb-8 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                    <span class="lang-id">Raw JSON Output</span>
                    <span class="lang-en">Raw JSON Output</span>
                    <span class="lang-zh">原始 JSON 输出</span>
                </h3>
            </div>
            <div class="p-6 overflow-x-auto">
                <pre class="text-sm font-mono text-green-400"><code>{{ json_encode($analysis->result_data, JSON_PRETTY_PRINT) }}</code></pre>
            </div>
        </div>


        {{-- Conversation Dynamics Chart & Agent Analysis --}}
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 p-8 border border-gray-100 mb-8"
            x-show="!showRaw">
            <div class="flex flex-col lg:flex-row gap-8 items-stretch">
                <!-- Left part: The Chart (2/3 width) -->
                <div class="lg:w-2/3 flex flex-col justify-between">
                    <div>
                        <span
                            class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5">
                            <i data-lucide="activity" class="w-3.5 h-3.5 text-bima-red animate-pulse"></i> 
                            <span class="lang-id">Grafik Aliran Intonasi Percakapan</span>
                            <span class="lang-en">Conversation Intonation Flow Chart</span>
                            <span class="lang-zh">对话语调流动图</span>
                        </span>
                        <h3 class="text-lg font-black text-gray-950 uppercase tracking-wide mb-6">
                            <span class="lang-id">Dinamika Intonasi Per Baris</span>
                            <span class="lang-en">Line-by-Line Intonation Dynamics</span>
                            <span class="lang-zh">逐行语调动态</span>
                        </h3>
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
                                <span class="text-xs font-black text-gray-800 uppercase tracking-wider">
                                    <span class="lang-id">Interpretasi Aliran</span>
                                    <span class="lang-en">Flow Interpretation</span>
                                    <span class="lang-zh">音流解构</span>
                                </span>
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
                                $dynamicsSummaryId = 'Alur percakapan didominasi oleh intonasi menurun (' . $downCount . ' kali) yang menandakan instruksi tegas, korektif, dan pemberian saran bimbingan yang terarah dari Dosen. Hal ini menunjukkan dinamika direktif di mana wacana dikontrol untuk mengarahkan kualitas akademis riset mahasiswa.';
                                $dynamicsSummaryEn = 'The flow of conversation is dominated by falling intonation (' . $downCount . ' times), signaling assertive, corrective guidance, and structured advice from the supervisor. This indicates a directive dynamic where the discourse is steered to direct the academic quality of the student\'s research.';
                                $dynamicsSummaryZh = '对话流以递降语调为主（达 ' . $downCount . ' 次），这体现了导师坚定、纠偏的指导特征与针对性学术建议。这表明一种指令性动态，其中话语权得到有效引导，以确保学生研究的学术质量。';

                                $dynamicsStatusId = 'Direktif & Korektif';
                                $dynamicsStatusEn = 'Directive & Corrective';
                                $dynamicsStatusZh = '指令与矫正';

                                $statusColor = 'text-red-700 bg-red-50 border-red-200';
                            } elseif ($upCount > $downCount) {
                                $dynamicsSummaryId = 'Alur percakapan didominasi oleh intonasi menaik (' . $upCount . ' kali) yang mencerminkan nada tanya, eksplorasi koperatif, atau memicu kebingungan produktif. Ini mencerminkan hubungan dialogis di mana bimbingan berjalan interaktif dan bersahabat.';
                                $dynamicsSummaryEn = 'The flow of conversation is dominated by rising intonation (' . $upCount . ' times), reflecting interrogative tone, collaborative exploration, or productive confusion. This indicates a dialogic dynamic where the supervision is highly interactive and supportive.';
                                $dynamicsSummaryZh = '对话流 by 递升语调为主（达 ' . $upCount . ' 次），反映出询问语气、合作探究或启发性疑惑。这体现了一种对话性动态，指导过程呈现高度互动且亲和友好。';

                                $dynamicsStatusId = 'Dialogis & Koperatif';
                                $dynamicsStatusEn = 'Dialogic & Collaborative';
                                $dynamicsStatusZh = '对话与合作';

                                $statusColor = 'text-blue-700 bg-blue-50 border-blue-200';
                            } else {
                                $dynamicsSummaryId = 'Dinamika percakapan berjalan seimbang antara intonasi turun (korektif) dan naik (klarifikasi/tanya). Ini mencerminkan keseimbangan relasi bimbingan yang sangat kondusif, interaktif, dan berorientasi pada pemecahan masalah bersama.';
                                $dynamicsSummaryEn = 'The conversation dynamics are balanced between falling (corrective) and rising (clarification/inquiry) intonations. This reflects a highly conducive, interactive, and problem-solving oriented supervision relationship.';
                                $dynamicsSummaryZh = '对话动态在递降语调（纠偏）与递升语调（澄清/询问）之间保持良好平衡。这体现了一种极具建设性、互动性且以共同解决问题为导向的指导关系。';

                                $dynamicsStatusId = 'Kondusif & Seimbang';
                                $dynamicsStatusEn = 'Conducive & Balanced';
                                $dynamicsStatusZh = '建设与平衡';

                                $statusColor = 'text-green-700 bg-green-50 border-green-200';
                            }
                        @endphp

                        <p class="text-sm font-medium text-gray-600 leading-relaxed font-serif mt-4">
                            <span class="lang-id">{{ $dynamicsSummaryId }}</span>
                            <span class="lang-en">{{ $dynamicsSummaryEn }}</span>
                            <span class="lang-zh">{{ $dynamicsSummaryZh }}</span>
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-between items-center relative z-10">
                        <span class="text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">
                            <span class="lang-id">Karakter Aliran</span>
                            <span class="lang-en">Flow Character</span>
                            <span class="lang-zh">语流特征</span>
                        </span>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-xl text-[0.65rem] font-black uppercase tracking-widest border {{ $statusColor }}">
                            <span class="lang-id">{{ $dynamicsStatusId }}</span>
                            <span class="lang-en">{{ $dynamicsStatusEn }}</span>
                            <span class="lang-zh">{{ $dynamicsStatusZh }}</span>
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
                <i data-lucide="file-text" class="w-4 h-4"></i> 
                <span class="lang-id">Transkrip</span>
                <span class="lang-en">Transcript</span>
                <span class="lang-zh">转录文本</span>
            </button>
            <button @click="activeMobileTab = 'ringkasan'"
                class="flex-1 py-3 text-xs font-black uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2 cursor-pointer"
                :class="activeMobileTab === 'ringkasan' ? 'bg-white text-gray-950 shadow-sm' : 'text-gray-400'">
                <i data-lucide="brain-circuit" class="w-4 h-4"></i> 
                <span class="lang-id">Ringkasan</span>
                <span class="lang-en">Summary</span>
                <span class="lang-zh">摘要信息</span>
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
                                $speakerText = $block['speaker'] ?? 'Unknown';
                                $speakerLower = strtolower($speakerText);
                                $speakerColor = $speakerLower === 'dosen' ? 'text-bima-red' : ($speakerLower === 'mahasiswa' ? 'text-blue-600' : 'text-gray-600');
                            @endphp
                            
                            {{-- Progressive mode segment header --}}
                            @if (!$isCompleted && $chunkIndex !== null && ($index === 0 || $prevChunkIndex !== $chunkIndex))
                                <div class="my-8 py-3 px-6 bg-gradient-to-r from-gray-50 to-gray-150 border border-gray-200/50 rounded-2xl flex items-center justify-between shadow-sm animate-fade-in relative overflow-hidden">
                                    <div class="absolute top-0 left-0 h-full w-1 bg-bima-red"></div>
                                    <span class="text-xs font-black uppercase tracking-wider text-gray-500 flex items-center gap-2">
                                        <i data-lucide="layers" class="w-3.5 h-3.5 text-bima-red animate-pulse"></i> 
                                        <span class="lang-id">Segmen Analisis {{ $chunkIndex }}</span>
                                        <span class="lang-en">Analysis Segment {{ $chunkIndex }}</span>
                                        <span class="lang-zh">分析分段 {{ $chunkIndex }}</span>
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-xl text-[0.6rem] font-black uppercase tracking-widest bg-green-50 text-green-700 border border-green-200">
                                        <span class="lang-id">Selesai Diproses</span>
                                        <span class="lang-en">Processed</span>
                                        <span class="lang-zh">处理完毕</span>
                                    </span>
                                </div>
                            @endif

                            <div id="baris-{{ $index }}"
                                 class="flex flex-col gap-4 group rounded-3xl p-4 transition-all duration-700 border border-transparent" 
                                 x-show="synthesisStatus !== 'completed' || ({{ $index }} >= (currentPage - 1) * pageSize && {{ $index }} < (currentPage) * pageSize)">
                                <div class="flex flex-col md:flex-row gap-4 md:gap-8">
                                    {{-- Speaker & Timestamp Column --}}
                                    <div class="md:w-32 flex-shrink-0 pt-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-black text-sm uppercase tracking-wider {{ $speakerColor }}">
                                                @if($speakerLower === 'dosen')
                                                    <span class="lang-id">Dosen</span>
                                                    <span class="lang-en">Supervisor</span>
                                                    <span class="lang-zh">导师</span>
                                                @elseif($speakerLower === 'mahasiswa')
                                                    <span class="lang-id">Mahasiswa</span>
                                                    <span class="lang-en">Student</span>
                                                    <span class="lang-zh">学生</span>
                                                @else
                                                    {{ $speakerText }}
                                                @endif
                                            </span>
                                        </div>
                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                            <span class="lang-id">Baris {{ $index + 1 }}</span>
                                            <span class="lang-en">Line {{ $index + 1 }}</span>
                                            <span class="lang-zh">第 {{ $index + 1 }} 行</span>
                                        </div>
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
                                                        @click="
                                                            const t = appLang === 'zh' ? '提出建议 (Advice Giving)' : (appLang === 'en' ? 'Advice Giving' : 'Pemberian Saran (Advice Giving)');
                                                            const exp = '{{ addslashes($block['agent_insight'] ?? 'Dosen memberikan saran akademik bimbingan.') }}';
                                                            const rel = '{{ addslashes($block['advice_relation'] ?? 'Kalimat ini merupakan saran bimbingan akademik yang menanggapi progres mahasiswa di baris ini.') }}';
                                                            openInsight(t, 'advice', exp, rel);
                                                        "
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-[0.65rem] font-black uppercase tracking-widest cursor-pointer transition-all hover:scale-105"
                                                        title="{{ app()->getLocale() === 'zh' ? '点击查看建议与话语关联详情' : (app()->getLocale() === 'en' ? 'Click to view advice and sentence relation details' : 'Klik untuk melihat detail advice & relasi kalimat') }}">
                                                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none"
                                                            stroke="currentColor" stroke-width="2.5"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 18.5a6.5 6.5 0 100-13 6.5 6.5 0 000 13zM12 11.5v3M12 9h.01" />
                                                        </svg>
                                                        <span class="lang-id">Advice</span>
                                                        <span class="lang-en">Advice</span>
                                                        <span class="lang-zh">建议</span>
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
                                                        @click="
                                                            const t = appLang === 'zh' ? '句子语调' : (appLang === 'en' ? 'Sentence Intonation' : 'Intonasi Kalimat');
                                                            const exp = '{{ addslashes($intReason) }}';
                                                            const rel = '{{ addslashes($intRelation) }}';
                                                            openInsight(t, '{{ $intType }}', exp, rel);
                                                        "
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[0.65rem] font-black uppercase tracking-widest cursor-pointer transition-all hover:scale-105 border
                                                                    {{ $intType === 'up' ? 'bg-blue-50 hover:bg-blue-100 text-blue-700 border-blue-200' : ($intType === 'down' ? 'bg-red-50 hover:bg-red-100 text-red-700 border-red-200' : 'bg-gray-50 hover:bg-gray-100 text-gray-700 border-gray-200') }}"
                                                        title="{{ app()->getLocale() === 'zh' ? '点击查看行语调详情' : (app()->getLocale() === 'en' ? 'Click to view line intonation details' : 'Klik untuk melihat detail intonasi baris') }}">
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
                                                        <span class="lang-id">Intonasi: {{ $intType === 'up' ? 'Naik' : ($intType === 'down' ? 'Turun' : 'Netral') }}</span>
                                                        <span class="lang-en">Intonation: {{ $intType === 'up' ? 'Up' : ($intType === 'down' ? 'Down' : 'Neutral') }}</span>
                                                        <span class="lang-zh">语调: {{ $intType === 'up' ? '升调' : ($intType === 'down' ? '降调' : '平调') }}</span>
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
                                                        @click="
                                                            const t = appLang === 'zh' ? '句子特征与关联' : (appLang === 'en' ? 'Sentence Character & Relation' : 'Karakter & Relasi Kalimat');
                                                            const exp = appLang === 'zh' ? '人工智能在此行检测到紧密的话语关联。' : (appLang === 'en' ? 'AI detected a close discourse relation on this line.' : 'AI mendeteksi hubungan antar-kalimat yang erat pada baris ini.');
                                                            const rel = '{{ addslashes($relExplanation) }}';
                                                            openInsight(t, 'relation', exp, rel);
                                                        "
                                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-[0.65rem] font-black uppercase tracking-widest cursor-pointer transition-all hover:scale-105"
                                                        title="{{ app()->getLocale() === 'zh' ? '点击查看特征与话语关联详情' : (app()->getLocale() === 'en' ? 'Click to view character and sentence relation details' : 'Klik untuk melihat karakter & relasi kalimat') }}">
                                                        <svg class="w-3.5 h-3.5 text-purple-500" fill="none"
                                                            stroke="currentColor" stroke-width="2.5"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                        </svg>
                                                        <span class="lang-id">Relasi</span>
                                                        <span class="lang-en">Relation</span>
                                                        <span class="lang-zh">关联</span>
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
                                                    class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 block mb-1">
                                                    <span class="lang-id">Agent Insight</span>
                                                    <span class="lang-en">Agent Insight</span>
                                                    <span class="lang-zh">智能体见解</span>
                                                </span>
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
                                        <span class="text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">
                                            <span class="lang-id">Evaluasi Kalimat:</span>
                                            <span class="lang-en">Line Evaluation:</span>
                                            <span class="lang-zh">单行评估:</span>
                                        </span>
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
                                    <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i>
                                    <span class="lang-id">Prev</span>
                                    <span class="lang-en">Prev</span>
                                    <span class="lang-zh">上一页</span>
                                </button>
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest text-center">
                                    <span class="lang-id">
                                        Halaman <span x-text="currentPage" class="text-gray-900 font-black"></span> dari <span x-text="Math.ceil(totalRows / pageSize)"></span>
                                        <span class="block md:inline-block md:ml-1 text-[0.65rem] text-gray-400 font-medium lowercase tracking-normal">(total <span x-text="totalRows" class="font-bold text-gray-600"></span> baris transkrip)</span>
                                    </span>
                                    <span class="lang-en">
                                        Page <span x-text="currentPage" class="text-gray-900 font-black"></span> of <span x-text="Math.ceil(totalRows / pageSize)"></span>
                                        <span class="block md:inline-block md:ml-1 text-[0.65rem] text-gray-400 font-medium lowercase tracking-normal">(total <span x-text="totalRows" class="font-bold text-gray-600"></span> transcript lines)</span>
                                    </span>
                                    <span class="lang-zh">
                                        第 <span x-text="currentPage" class="text-gray-900 font-black"></span> 页，共 <span x-text="Math.ceil(totalRows / pageSize)"></span> 页
                                        <span class="block md:inline-block md:ml-1 text-[0.65rem] text-gray-400 font-medium lowercase tracking-normal">(共 <span x-text="totalRows" class="font-bold text-gray-600"></span> 行转录文本)</span>
                                    </span>
                                </span>
                                <button type="button" 
                                    @click="currentPage = Math.min(Math.ceil(totalRows / pageSize), currentPage + 1)" 
                                    :disabled="currentPage === Math.ceil(totalRows / pageSize)"
                                    class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold uppercase tracking-widest text-gray-600 hover:bg-gray-50 disabled:opacity-50 disabled:hover:bg-transparent transition-colors cursor-pointer focus:outline-none">
                                    <span class="lang-id">Next</span>
                                    <span class="lang-en">Next</span>
                                    <span class="lang-zh">下一页</span>
                                    <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
                                </button>
                            </div>
                        </template>
                    @else
                        {{-- Beautiful Progressive Skeleton Loader when first chunk is compiling --}}
                        <div class="space-y-8 animate-pulse py-6">
                            <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-center gap-3 text-blue-700 mb-6">
                                <i data-lucide="info" class="w-4 h-4 animate-bounce"></i>
                                <span class="text-xs font-bold uppercase tracking-wider">
                                    <span class="lang-id">Sedang memproses segmen pertama audio...</span>
                                    <span class="lang-en">Processing the first audio segment...</span>
                                    <span class="lang-zh">正在处理第一段音频分段...</span>
                                </span>
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
                        <i data-lucide="layout-dashboard" class="w-4 h-4 mr-2"></i> 
                        <span class="lang-id">Hasil Analisis Akhir</span>
                        <span class="lang-en">Final Analysis Summary</span>
                        <span class="lang-zh">最终分析总结</span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-4 mb-4">
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex flex-col justify-center">
                            <span
                                class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1">
                                <span class="lang-id">Kategori Advice</span>
                                <span class="lang-en">Advice Category</span>
                                <span class="lang-zh">建议类别</span>
                            </span>
                            @php $transAdvice = $translateValue($summary['kategori_advice'] ?? '-'); @endphp
                            <span class="font-bold text-gray-900 leading-tight block break-words text-sm">
                                <span class="lang-id">{{ $transAdvice['id'] }}</span>
                                <span class="lang-en">{{ $transAdvice['en'] }}</span>
                                <span class="lang-zh">{{ $transAdvice['zh'] }}</span>
                            </span>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex flex-col justify-center">
                            <span
                                class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1">
                                <span class="lang-id">Karakter Relasi</span>
                                <span class="lang-en">Relation Character</span>
                                <span class="lang-zh">话语关联特征</span>
                            </span>
                            @php $transRelation = $translateValue($summary['karakter_relasi'] ?? '-'); @endphp
                            <span class="font-bold text-gray-900 leading-tight block break-words text-sm">
                                <span class="lang-id">{{ $transRelation['id'] }}</span>
                                <span class="lang-en">{{ $transRelation['en'] }}</span>
                                <span class="lang-zh">{{ $transRelation['zh'] }}</span>
                            </span>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex flex-col justify-center">
                            <span
                                class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1">
                                <span class="lang-id">Intonasi Dominan</span>
                                <span class="lang-en">Dominant Intonation</span>
                                <span class="lang-zh">主导语调</span>
                            </span>
                            @php $transInt = $translateValue($summary['intonasi_dominan'] ?? '-'); @endphp
                            <span class="font-bold text-gray-900 leading-tight block break-words text-sm">
                                <span class="lang-id">{{ $transInt['id'] }}</span>
                                <span class="lang-en">{{ $transInt['en'] }}</span>
                                <span class="lang-zh">{{ $transInt['zh'] }}</span>
                            </span>
                        </div>
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 flex flex-col justify-center">
                            <span
                                class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1">
                                <span class="lang-id">Ranah Bicara</span>
                                <span class="lang-en">Discourse Domain</span>
                                <span class="lang-zh">话语权领域</span>
                            </span>
                            <span
                                class="font-bold text-gray-900 leading-tight block text-sm break-words">{{ \Illuminate\Support\Str::limit($summary['ranah_pembicaraan'] ?? '-', 50) }}</span>
                        </div>
                    </div>

                    <div class="bg-bima-red rounded-2xl p-6 text-white shadow-lg shadow-red-500/20 mb-4">
                        <span
                            class="block text-[0.6rem] font-black text-red-200 uppercase tracking-widest mb-2 flex items-center">
                            <i data-lucide="target" class="w-3 h-3 mr-1"></i> 
                            <span class="lang-id">Arah Tujuan</span>
                            <span class="lang-en">Goal / Direction</span>
                            <span class="lang-zh">方向目标</span>
                        </span>
                        <p class="font-medium text-sm leading-relaxed">{{ $summary['arah_tujuan'] ?? '-' }}</p>
                    </div>

                    <div class="bg-green-50 border border-green-100 rounded-2xl p-6 text-green-900">
                        <span
                            class="block text-[0.6rem] font-black text-green-600 uppercase tracking-widest mb-2 flex items-center">
                            <i data-lucide="sparkles" class="w-3 h-3 mr-1"></i> 
                            <span class="lang-id">Saran Perbaikan</span>
                            <span class="lang-en">Improvement Recommendation</span>
                            <span class="lang-zh">整改建议</span>
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
                            <x-application-logo class="w-5 h-5 text-bima-red" /> 
                            <span class="lang-id">Penilaian Kesesuaian Hasil</span>
                            <span class="lang-en">Result Accuracy Evaluation</span>
                            <span class="lang-zh">结果准确性评估</span>
                        </h3>
                        <p class="text-gray-300 text-sm font-medium leading-relaxed">
                            <span class="lang-id">Sebagai pakar atau peninjau akademis, penilaian Anda sangat berharga bagi penyempurnaan akurasi kami. Menurut Anda, apakah hasil anotasi intonasi dan saran bimbingan sistem ini sudah sesuai dengan fakta percakapan riil?</span>
                            <span class="lang-en">As an academic expert or reviewer, your assessment is highly valuable for refining our accuracy. Do you think the intonation annotation and supervision advice of this system match the real conversation facts?</span>
                            <span class="lang-zh">作为学术专家或审阅者，您的评估对于提升我们的准确度至关重要。您认为本系统的语调标注与指导建议是否符合真实的对话事实？</span>
                        </p>
                    </div>

                    <div class="flex-shrink-0 w-full md:w-auto flex items-center gap-4">
                        <div class="flex gap-3 w-full" x-show="!loading">
                            <button @click="submitFeedback(1)"
                                class="flex-1 md:flex-none px-6 py-3.5 bg-green-600 hover:bg-green-500 text-white font-bold text-xs uppercase tracking-wider rounded-2xl transition-all hover:scale-105 cursor-pointer flex items-center justify-center gap-1.5 shadow-lg shadow-green-600/20">
                                <i data-lucide="check-circle" class="w-4 h-4"></i> 
                                <span class="lang-id">Sudah Sesuai</span>
                                <span class="lang-en">Accurate</span>
                                <span class="lang-zh">符合事实</span>
                            </button>
                            <button @click="submitFeedback(0)"
                                class="flex-1 md:flex-none px-6 py-3.5 bg-red-600 hover:bg-red-500 text-white font-bold text-xs uppercase tracking-wider rounded-2xl transition-all hover:scale-105 cursor-pointer flex items-center justify-center gap-1.5 shadow-lg shadow-red-600/20">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i> 
                                <span class="lang-id">Belum Sesuai</span>
                                <span class="lang-en">Inaccurate</span>
                                <span class="lang-zh">有待商榷</span>
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
                            <span class="text-xs font-black uppercase tracking-wider">
                                <span class="lang-id">Mengirim Validasi...</span>
                                <span class="lang-en">Submitting Feedback...</span>
                                <span class="lang-zh">提交反馈中...</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="submitted" x-transition.opacity style="display: none;"
                class="bg-green-50 border border-green-200 rounded-[2.5rem] p-8 text-center text-green-800 shadow-sm">
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
                            <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">
                                <span class="lang-id">Supervisory AI Insight</span>
                                <span class="lang-en">Supervisory AI Insight</span>
                                <span class="lang-zh">Supervisory AI 智能洞察</span>
                            </span>
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
                                <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest mb-1.5">
                                    <span class="lang-id">Penjelasan AI Agent</span>
                                    <span class="lang-en">AI Agent Explanation</span>
                                    <span class="lang-zh">AI 智能体解构</span>
                                </span>
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
                                <span class="block text-[0.6rem] font-black text-blue-500 uppercase tracking-widest mb-1.5">
                                    <span class="lang-id">Karakter & Relasi Kalimat (Syntactic Relation)</span>
                                    <span class="lang-en">Sentence Character & Relation (Syntactic Relation)</span>
                                    <span class="lang-zh">句子特征与关联 (Syntactic Relation)</span>
                                </span>
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
                        <span class="lang-id">Tutup</span>
                        <span class="lang-en">Close</span>
                        <span class="lang-zh">关闭</span>
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
                        :class="synthesisStatus === 'failed' ? 'text-red-500' : 'text-gray-400'">
                        <span class="lang-id">Real-time Compiler</span>
                        <span class="lang-en">Real-time Compiler</span>
                        <span class="lang-zh">实时编译器</span>
                    </span>
                    <p class="text-xs font-bold uppercase tracking-wider"
                        :class="synthesisStatus === 'failed' ? 'text-red-700' : 'text-gray-800'"
                        x-text="synthesisStatus === 'failed' ? (synthesisError || (appLang === 'zh' ? '编译分析结果失败。' : (appLang === 'en' ? 'Failed to compile analysis results.' : 'Gagal menyusun halaman hasil analisa.'))) : (appLang === 'zh' ? 'Supervisory AI 正在构建您的多模态分析结果...' : (appLang === 'en' ? 'Supervisory AI is compiling the audio analysis results...' : 'Supervisory AI sedang menyusun halaman hasil analisa audio...'))"></p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <template x-if="synthesisStatus === 'failed'">
                    <button type="button" @click="startBackgroundSynthesis" class="bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-xl transition-all flex items-center gap-1 cursor-pointer border border-transparent shadow-md focus:outline-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 6H16"></path>
                        </svg>
                        <span class="lang-id">Coba Lagi</span>
                        <span class="lang-en">Retry</span>
                        <span class="lang-zh">重试</span>
                    </button>
                </template>
                <template x-if="synthesisStatus !== 'failed'">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                        <span class="text-[0.65rem] font-black uppercase tracking-widest text-blue-600">
                            <span class="lang-id">Sintesis</span>
                            <span class="lang-en">Synthesis</span>
                            <span class="lang-zh">合成中</span>
                        </span>
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

                    $speakerText = $block['speaker'] ?? 'Unknown';
                    $speakerLower = strtolower($speakerText);
                    $speakerId = $speakerLower === 'dosen' ? 'Dosen' : ($speakerLower === 'mahasiswa' ? 'Mahasiswa' : $speakerText);
                    $speakerEn = $speakerLower === 'dosen' ? 'Supervisor' : ($speakerLower === 'mahasiswa' ? 'Student' : $speakerText);
                    $speakerZh = $speakerLower === 'dosen' ? '导师' : ($speakerLower === 'mahasiswa' ? '学生' : $speakerText);

                    return [
                        'labelId' => 'Baris ' . ($idx + 1),
                        'labelEn' => 'Line ' . ($idx + 1),
                        'labelZh' => '第 ' . ($idx + 1) . ' 行',
                        'value' => $val,
                        'speakerId' => $speakerId,
                        'speakerEn' => $speakerEn,
                        'speakerZh' => $speakerZh,
                        'typeId' => $type === 'up' ? 'Naik' : ($type === 'down' ? 'Turun' : 'Netral'),
                        'typeEn' => $type === 'up' ? 'Up' : ($type === 'down' ? 'Down' : 'Neutral'),
                        'typeZh' => $type === 'up' ? '升调' : ($type === 'down' ? '降调' : '平调'),
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

                const labels = chartData.map(d => {
                    const activeL = document.documentElement.lang;
                    return activeL === 'zh' ? d.labelZh : (activeL === 'en' ? d.labelEn : d.labelId);
                });
                const values = chartData.map(d => d.value);
                const pointColors = chartData.map(d => d.speakerId.toLowerCase() === 'dosen' ? '#cc0000' : '#2563eb');
                const pointStyles = chartData.map(d => d.speakerId.toLowerCase() === 'dosen' ? 'circle' : 'rectRot');

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
                        onClick: function(e, activeEls) {
                            if (activeEls && activeEls.length > 0) {
                                const idx = activeEls[0].index;
                                window.dispatchEvent(new CustomEvent('go-to-baris', { detail: { index: idx } }));
                            }
                        },
                        onHover: function(e, el) {
                            const canvas = e.chart.canvas;
                            canvas.style.cursor = el.length ? 'pointer' : 'default';
                        },
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
                                        const activeL = document.documentElement.lang;
                                        const label = activeL === 'zh' ? d.labelZh : (activeL === 'en' ? d.labelEn : d.labelId);
                                        const speaker = activeL === 'zh' ? d.speakerZh : (activeL === 'en' ? d.speakerEn : d.speakerId);
                                        return `${label} - ${speaker}`;
                                    },
                                    label: function(context) {
                                        const idx = context.dataIndex;
                                        const d = chartData[idx];
                                        const activeL = document.documentElement.lang;
                                        const type = activeL === 'zh' ? d.typeZh : (activeL === 'en' ? d.typeEn : d.typeId);
                                        const labelText = activeL === 'zh' ? '语调: ' : (activeL === 'en' ? 'Intonation: ' : 'Intonasi: ');
                                        const textLabel = activeL === 'zh' ? '文本: ' : (activeL === 'en' ? 'Text: ' : 'Teks: ');
                                        return [
                                            `${labelText}${type}`,
                                            `${textLabel}"${d.text}"`
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
                                        const activeL = document.documentElement.lang;
                                        if (value === 1) {
                                            return activeL === 'zh' ? '升调 ↑' : (activeL === 'en' ? 'Up ↑' : 'Naik ↑');
                                        }
                                        if (value === 0) {
                                            return activeL === 'zh' ? '平调 ➖' : (activeL === 'en' ? 'Neutral ➖' : 'Netral ➖');
                                        }
                                        if (value === -1) {
                                            return activeL === 'zh' ? '降调 ↓' : (activeL === 'en' ? 'Down ↓' : 'Turun ↓');
                                        }
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
