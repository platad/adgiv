<x-layouts.app title="Hasil Analisis V2 (Multi-Task RoBERTa & Graph Network)">
@php
    $transcription = $analysis->result_data['transcription'] ?? [];
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
                return ['bg' => 'bg-red-50 text-red-700 border-red-200', 'badge' => 'Arahan Eksplisit', 'color' => '#ef4444'];
            case 'bimbingan_bertahap':
                return ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'badge' => 'Bimbingan Bertahap', 'color' => '#3b82f6'];
            case 'dukungan_keputusan':
                return ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'badge' => 'Dukungan Keputusan', 'color' => '#10b981'];
            case 'jawaban_tegas':
                return ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'badge' => 'Jawaban Tegas', 'color' => '#f59e0b'];
            case 'petunjuk_kontekstual':
                return ['bg' => 'bg-purple-50 text-purple-700 border-purple-200', 'badge' => 'Petunjuk Kontekstual', 'color' => '#8b5cf6'];
            case 'pertanyaan_reflektif':
                return ['bg' => 'bg-cyan-50 text-cyan-700 border-cyan-200', 'badge' => 'Pertanyaan Reflektif', 'color' => '#06b6d4'];
            default:
                return ['bg' => 'bg-gray-50 text-gray-700 border-gray-200', 'badge' => ucwords(str_replace('_', ' ', $l)), 'color' => '#6b7280'];
        }
    };

    $getModeBadgeStyle = function($label) {
        $l = strtolower(trim($label ?? ''));
        switch ($l) {
            case 'power_over':
                return ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'badge' => 'Power Over', 'color' => '#f43f5e'];
            case 'power_maintaining':
                return ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'badge' => 'Power Maintaining', 'color' => '#6366f1'];
            case 'power_gaining':
                return ['bg' => 'bg-teal-50 text-teal-700 border-teal-200', 'badge' => 'Power Gaining', 'color' => '#14b8a6'];
            default:
                return ['bg' => 'bg-gray-50 text-gray-700 border-gray-200', 'badge' => ucwords(str_replace('_', ' ', $l)), 'color' => '#6b7280'];
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
                {{-- Tombol Tukar Peran Pembicara --}}
                <button type="button"
                    @click="showSwapModal = true"
                    class="inline-flex items-center px-4 py-2.5 rounded-2xl bg-white hover:bg-purple-50 text-purple-700 border border-purple-200 text-xs font-black uppercase tracking-wider transition-all shadow-sm active:scale-95 cursor-pointer">
                    <i data-lucide="arrow-left-right" class="w-4 h-4 mr-2 text-purple-600"></i>
                    <span>Tukar Peran Pembicara</span>
                </button>

                <button type="button"
                    @click="showRaw = !showRaw"
                    class="inline-flex items-center px-4 py-2.5 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-black uppercase tracking-wider transition-colors cursor-pointer">
                    <i data-lucide="code" class="w-4 h-4 mr-2"></i>
                    <span x-text="showRaw ? 'Tutup Raw' : 'Lihat Raw'"></span>
                </button>
            </div>
        </div>

        {{-- Raw JSON Viewer --}}
        <div x-show="showRaw" style="display: none;" class="bg-gray-900 rounded-3xl shadow-xl mb-8 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Raw JSON Output</h3>
                <span class="text-xs text-gray-500">Versi 2 RoBERTa + Graph</span>
            </div>
            <div class="p-6 overflow-x-auto max-h-96">
                <pre class="text-xs font-mono text-emerald-400"><code>{{ json_encode($analysis->result_data, JSON_PRETTY_PRINT) }}</code></pre>
            </div>
        </div>

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

        {{-- Graph Network Section (ECharts) --}}
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-6 sm:p-8 mb-8 relative overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
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
                        Visualisasi topologi wacana interaktif. Klik dan seret node untuk mengeksplorasi hubungan antar pembicara dan relasi kuasa.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 text-[0.65rem] font-bold">
                        <span class="w-2 h-2 rounded-full bg-purple-600"></span> Dosen
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 text-[0.65rem] font-bold">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span> Mahasiswa
                    </span>
                </div>
            </div>
            
            <div id="graph-container" class="w-full bg-gray-50/50 rounded-3xl border border-gray-100" style="height: 480px;"></div>
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
            
            {{-- Left Column: Paginated Transcript (2 Columns on Desktop) --}}
            <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-6 sm:p-8"
                :class="activeMobileTab === 'transkrip' ? 'block' : 'hidden lg:block'">
                
                <div class="flex items-center justify-between pb-6 border-b border-gray-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                            <i data-lucide="message-square" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-black uppercase tracking-tight text-gray-900">Transkripsi & Penanda Wacana</h2>
                            <p class="text-xs text-gray-400 font-medium">Dialog berurutan lengkap dengan atribusi peran</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-gray-400">Total {{ $totalSegments }} Tuturan</span>
                </div>

                {{-- Dialog Rows --}}
                <div class="space-y-4">
                    @forelse ($transcription as $index => $item)
                        @php
                            $speaker = $item['speaker'] ?? 'Unknown';
                            $isDosen = strcasecmp($speaker, 'Dosen') === 0 || strcasecmp($speaker, 'Supervisor') === 0;
                            $adviceStyle = !empty($item['advice_giving']) ? $getAdviceBadgeStyle($item['advice_giving']) : null;
                            $modeStyle = !empty($item['modes_of_interaction']) ? $getModeBadgeStyle($item['modes_of_interaction']) : null;
                        @endphp

                        <div id="baris-{{ $index }}"
                            class="p-5 rounded-2xl border transition-all duration-300 {{ $isDosen ? 'bg-purple-50/20 border-purple-100 hover:border-purple-200' : 'bg-gray-50/40 border-gray-100 hover:border-gray-200' }}"
                            x-show="{{ $index }} >= (currentPage - 1) * pageSize && {{ $index }} < (currentPage) * pageSize">
                            
                            <div class="flex items-center justify-between gap-4 mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider {{ $isDosen ? 'bg-purple-600 text-white shadow-sm shadow-purple-600/20' : 'bg-gray-200 text-gray-700' }}">
                                        @if($isDosen)
                                            <i data-lucide="user-check" class="w-3.5 h-3.5"></i> Dosen
                                        @else
                                            <i data-lucide="user" class="w-3.5 h-3.5"></i> Mahasiswa
                                        @endif
                                    </span>
                                    <span class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">
                                        #{{ $index + 1 }}
                                    </span>
                                </div>

                                @if(!empty($item['timestamp']))
                                    <span class="text-[0.68rem] font-semibold text-gray-400 flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3 h-3"></i> {{ $item['timestamp'] }}
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm font-medium text-gray-800 leading-relaxed font-sans mt-2 mb-3">
                                {{ $item['text'] ?? '' }}
                            </p>

                            {{-- Multi-Task Annotations --}}
                            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-gray-100/80">
                                @if($adviceStyle)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[0.65rem] font-black uppercase tracking-wider border {{ $adviceStyle['bg'] }}">
                                        <i data-lucide="target" class="w-3 h-3"></i> Advice: {{ $adviceStyle['badge'] }}
                                    </span>
                                @endif

                                @if($modeStyle)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[0.65rem] font-black uppercase tracking-wider border {{ $modeStyle['bg'] }}">
                                        <i data-lucide="shield" class="w-3 h-3"></i> Mode: {{ $modeStyle['badge'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-400 font-medium">
                            <i data-lucide="message-square-off" class="w-10 h-10 mx-auto text-gray-300 mb-2"></i>
                            Belum ada baris transkripsi tersedia.
                        </div>
                    @endforelse
                </div>

                {{-- Pagination Control --}}
                <template x-if="totalPages > 1">
                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between">
                        <button type="button" 
                            @click="currentPage = Math.max(1, currentPage - 1)" 
                            :disabled="currentPage === 1"
                            class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold uppercase tracking-widest text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:hover:bg-transparent transition-colors cursor-pointer">
                            <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i> Prev
                        </button>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">
                            Halaman <span x-text="currentPage" class="text-gray-900 font-black"></span> dari <span x-text="totalPages"></span>
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

            {{-- Right Column: Category Distribution & Insights --}}
            <div class="lg:col-span-1 space-y-6"
                :class="activeMobileTab === 'ringkasan' ? 'block' : 'hidden lg:block'">
                
                {{-- Advice Distribution Card --}}
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-6">
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
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-6">
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
                <div class="bg-gradient-to-br from-purple-900 to-indigo-900 rounded-[2.5rem] p-6 text-white shadow-xl shadow-purple-900/20">
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
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
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

    </div>

    <x-slot name="scripts">
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
    <script>
        function v2ResultApp() {
            return {
                showRaw: false,
                showSwapModal: false,
                isSwapping: false,
                activeMobileTab: 'transkrip',
                currentPage: 1,
                pageSize: 15,
                totalRows: {{ $totalSegments }},
                get totalPages() {
                    return Math.max(1, Math.ceil(this.totalRows / this.pageSize));
                },

                // Audio Player state
                isPlaying: false,
                progressPercent: 0,
                audioTimeDisplay: '00:00 / 00:00',

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
