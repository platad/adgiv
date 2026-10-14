<x-layouts.app title="Memproses Analisa — {{ $analysis->title }}">
    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="processingApp()" x-init="init()">

        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-2.5 h-2.5 rounded-full animate-pulse" :class="statusDotClass" id="status-dot"></div>
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest" x-text="statusLabel">Sedang Diproses</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 truncate">{{ $analysis->title }}</h1>
            <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-400 font-bold uppercase tracking-widest">
                <span>Bahasa: <span class="text-gray-700">{{ ['id' => '🇮🇩 Indonesia', 'en' => '🇬🇧 English', 'zh' => '🇨🇳 中文'][$analysis->locale] ?? $analysis->locale }}</span></span>
                <span>ID Sesi: <span class="text-gray-700 font-mono">{{ $analysis->slug }}</span></span>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden mb-8">
            <div class="bg-bima-red h-full rounded-full transition-all duration-700" :style="`width: ${globalProgress}%`"></div>
        </div>

        {{-- Status Cloud Temaniskripsi Banner --}}
        <div class="mb-8 bg-gray-50 border border-gray-200 rounded-[1.5rem] p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-bima-red/10 text-bima-red flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-[0.65rem] font-black text-gray-400 uppercase tracking-widest">Status Cloud Temaniskripsi</p>
                    <p class="text-sm font-bold text-gray-900 mt-1" x-text="vpsMessage || transcriptionStatus || 'Menunggu respon dari Cloud Temaniskripsi...'"></p>
                    <div class="flex flex-wrap gap-4 mt-3 text-xs font-medium" x-show="totalSegments > 0" style="display: none;">
                        <span class="bg-blue-50 text-blue-700 px-3 py-1.5 rounded-full font-black flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                            <span x-text="totalSegments"></span> Segmen
                        </span>
                        <span class="bg-green-50 text-green-700 px-3 py-1.5 rounded-full font-black flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" y1="2" x2="14" y2="2"/><line x1="12" y1="14" x2="15" y2="11"/><circle cx="12" cy="14" r="8"/></svg>
                            <span x-text="totalDuration"></span> detik
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Execution Card (Style Serupa Versi 2) --}}
        <div class="bg-white border border-gray-100 rounded-[2.5rem] shadow-xl shadow-gray-100/60 p-8 md:p-12 mb-8">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-gray-50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center transition-colors"
                         :class="globalStatus === 'completed' ? 'bg-green-50 text-green-600' : (globalStatus === 'failed' ? 'bg-red-50 text-red-600' : 'bg-red-50 text-bima-red')">
                        <svg x-show="globalStatus === 'completed'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg x-show="globalStatus === 'failed'" style="display:none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <svg x-show="globalStatus !== 'completed' && globalStatus !== 'failed'" class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 mb-1">Status Pemrosesan AI</p>
                        <h2 class="text-lg font-black text-gray-900" x-text="transcriptionStatus || vpsMessage || 'Menyiapkan proses...'"></h2>
                    </div>
                </div>
                <div class="flex items-center sm:text-right gap-6 justify-between">
                    <div x-show="totalSegments > 0" class="text-left sm:text-right">
                        <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 mb-1">Total Segmen</p>
                        <p class="text-sm font-black text-gray-800"><span x-text="totalSegments"></span> Segmen (<span x-text="totalDuration"></span>s)</p>
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 mb-1">Progres</p>
                        <p class="text-3xl font-black text-bima-red"><span x-text="globalProgress"></span>%</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Status Tahapan Pipeline --}}
                <div class="space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-4">Pipeline Execution</h3>
                    
                    {{-- Step 1: Kompresi & Format Audio --}}
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 border transition-colors"
                             :class="globalProgress >= 15 ? 'bg-green-50 border-green-200 text-green-600' : 'bg-red-50 border-red-200 text-bima-red'">
                            <svg x-show="globalProgress >= 15" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <svg x-show="globalProgress < 15" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-gray-900">Kompresi & Format Audio</p>
                            <p class="text-xs font-medium text-gray-500 mt-0.5" 
                               x-text="globalProgress >= 15 ? 'Format optimal MP3 16kHz mono siap' : 'Mengompresi audio untuk model Whisper...'"></p>
                        </div>
                    </div>

                    {{-- Step 2: Transkripsi & Deteksi Ucapan --}}
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 border transition-colors"
                             :class="globalProgress >= 80 ? 'bg-green-50 border-green-200 text-green-600' : (globalProgress >= 15 ? 'bg-blue-50 border-blue-200 text-blue-600' : 'bg-gray-50 border-gray-200 text-gray-400')">
                            <svg x-show="globalProgress >= 80" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <svg x-show="globalProgress >= 15 && globalProgress < 80" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <svg x-show="globalProgress < 15" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-gray-900">Transkripsi & Deteksi Ucapan</p>
                            <p class="text-xs font-medium text-gray-500 mt-0.5"
                               x-text="globalProgress >= 80 ? ('Selesai (' + totalSegments + ' segmen terdeteksi)') : (globalProgress >= 15 ? 'Mentranskripsi percakapan via Cloud...' : 'Menunggu antrean...')"></p>
                        </div>
                    </div>

                    {{-- Step 3: Sintesis AI & IndoBERT Analysis --}}
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 border transition-colors"
                             :class="globalStatus === 'completed' ? 'bg-green-50 border-green-200 text-green-600' : (globalProgress >= 80 ? 'bg-purple-50 border-purple-200 text-purple-600' : 'bg-gray-50 border-gray-200 text-gray-400')">
                            <svg x-show="globalStatus === 'completed'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <svg x-show="globalStatus !== 'completed' && globalProgress >= 80" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <svg x-show="globalProgress < 80" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-gray-900">Batch Sintesis AI & Wacana</p>
                            <p class="text-xs font-medium text-gray-500 mt-0.5"
                               x-text="globalStatus === 'completed' ? 'Analisis batch dan wacana tersimpan rapi' : (globalProgress >= 80 ? (batchProcessingText || 'Memproses Batch Sintesis AI...') : 'Menunggu tahap transkripsi...')"></p>
                        </div>
                    </div>
                </div>

                {{-- Informasi Panel Tambahan & Quick Actions --}}
                <div class="bg-gray-50 rounded-3xl p-6 md:p-8 border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-red-50 text-bima-red flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-gray-900 mb-1">Sinkronisasi Cloud Temaniskripsi Aktif</h4>
                        <p class="text-xs text-gray-500 font-medium leading-relaxed mb-6">
                            Audio distandarisasi dan diproses otomatis di Cloud Temaniskripsi. Setelah transkripsi dan sintesis AI selesai, sistem akan mengarahkan Anda langsung ke halaman laporan hasil analisis.
                        </p>
                    </div>

                    {{-- Tombol Buka Detail & Inspeksi --}}
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-200/60">
                        <button type="button" @click="showDetailModal = !showDetailModal" 
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-100 hover:text-gray-900 shadow-sm transition-all">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                            <span x-text="showDetailModal ? 'Sembunyikan Detail Log' : 'Lihat Detail Transkripsi & Log'"></span>
                        </button>
                        <span class="text-[0.65rem] text-gray-400 font-mono" x-show="realtimeTexts.length > 0">
                            <span x-text="realtimeTexts.length"></span> baris data
                        </span>
                    </div>
                </div>
            </div>

            {{-- Collapsible Section untuk Detail Transkripsi & Log Real-Time (Rapi & Tidak Mengotori Layar) --}}
            <div x-show="showDetailModal" x-collapse class="mt-8 pt-8 border-t border-gray-100 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Transkripsi Stream Box --}}
                    <div class="bg-gray-50/70 rounded-2xl p-4 border border-gray-200">
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-200/80">
                            <span class="text-xs font-black uppercase tracking-wider text-gray-600 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                Pratinjau Teks Audio
                            </span>
                            <span class="text-[0.65rem] font-mono text-gray-400" x-text="realtimeTexts.length + ' segmen'"></span>
                        </div>
                        <div class="h-64 overflow-y-auto space-y-2 pr-2 font-mono text-xs text-gray-700 leading-relaxed" id="realtime-text-box">
                            <template x-for="(text, idx) in realtimeTexts" :key="idx">
                                <div class="p-2 rounded bg-white border border-gray-100 shadow-2xs" x-html="text"></div>
                            </template>
                            <template x-if="realtimeTexts.length === 0">
                                <div class="text-center py-12 text-gray-400 italic">Belum ada segmen transkripsi yang diterima...</div>
                            </template>
                        </div>
                    </div>

                    {{-- Log Cloud Box --}}
                    <div class="bg-gray-900 rounded-2xl p-4 border border-gray-800 text-gray-300">
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-800">
                            <span class="text-xs font-black uppercase tracking-wider text-gray-400 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Log Sinkronisasi Cloud Temaniskripsi
                            </span>
                            <span class="text-[0.65rem] font-mono text-green-400">Live feed</span>
                        </div>
                        <div class="h-64 overflow-y-auto space-y-1.5 pr-2 font-mono text-[0.7rem] leading-relaxed" id="vps-log-box">
                            <template x-for="(log, idx) in logs" :key="idx">
                                <div class="flex items-start gap-2">
                                    <span class="text-gray-500 shrink-0" x-text="'[' + log.time + ']'"></span>
                                    <span :class="{'text-green-400': log.level === 'success', 'text-red-400': log.level === 'error', 'text-yellow-400': log.level === 'warning', 'text-gray-300': log.level === 'info'}" x-text="log.msg"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Timeline Batch AI History jika ada --}}
                <div x-show="batchHistory.length > 0" class="mt-6 space-y-3">
                    <h4 class="text-xs font-black uppercase tracking-wider text-gray-500">Histori Batch Sintesis AI</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="(batch, index) in batchHistory" :key="index">
                            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-2xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-800">Batch #<span x-text="index + 1"></span> Selesai</span>
                                    <span class="text-[0.65rem] font-mono bg-blue-50 text-blue-700 px-2 py-0.5 rounded">Baris <span x-text="batch.start_idx + 1"></span> - <span x-text="batch.end_idx + 1"></span></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

                {{-- Timeline Batch AI History jika ada --}}
                <div x-show="batchHistory.length > 0" class="mt-6 space-y-3">
                    <h4 class="text-xs font-black uppercase tracking-wider text-gray-500">Histori Batch Sintesis AI</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="(batch, index) in batchHistory" :key="index">
                            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-2xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-800">Batch #<span x-text="index + 1"></span> Selesai</span>
                                    <span class="text-[0.65rem] font-mono bg-blue-50 text-blue-700 px-2 py-0.5 rounded">Baris <span x-text="batch.start_idx + 1"></span> - <span x-text="batch.end_idx + 1"></span></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <x-slot name="scripts">
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('processingApp', () => ({
            slug: '{{ $analysis->slug }}',
            globalStatus: 'pending',
            globalProgress: 10,
            logs: [],
            realtimeTexts: [],
            transcriptionStatus: 'Membangun koneksi ke server VPS...',
            isProcessing: false,
            vpsMessage: '',
            totalSegments: 0,
            totalDuration: 0,
            vpsLogs: [],
            batchHistory: @json($initialBatchHistory),
            resumeStartIdx: {{ $resumeStartIdx }},
            showDetailModal: false,
            batchProcessingText: '',
            lastLoggedMessage: '',

            init() {
                if (!this.isProcessing) {
                    this.startProcessing();
                }
            },

            get statusDotClass() {
                if (this.globalStatus === 'completed') return 'bg-green-500';
                if (this.globalStatus === 'failed') return 'bg-red-600';
                return 'bg-bima-red';
            },

            get statusLabel() {
                if (this.globalStatus === 'completed') return 'Selesai';
                if (this.globalStatus === 'failed') return 'Gagal';
                return 'Sedang Diproses';
            },

            get step2Class() {
                if (this.globalProgress >= 80 || this.globalStatus === 'completed') return 'bg-green-100 text-green-600';
                if (this.globalStatus === 'failed') return 'bg-red-100 text-red-600';
                return 'bg-blue-100 text-blue-500';
            },

            get step3Class() {
                if (this.globalStatus === 'completed') return 'bg-green-100 text-green-600';
                if (this.globalStatus === 'failed') return 'bg-red-100 text-red-600';
                if (this.globalProgress >= 80) return 'bg-blue-100 text-blue-500';
                return 'bg-gray-100 text-gray-400';
            },

            appendLog(level, msg) {
                // Cegah log yang sama berturut-turut
                if (this.lastLoggedMessage === msg) return;
                this.lastLoggedMessage = msg;

                const now = new Date().toLocaleTimeString('id-ID', { hour12: false });
                this.logs = [...this.logs, { level, msg, time: now }];
                setTimeout(() => {
                    const c = document.getElementById('vps-log-box');
                    if(c) c.scrollTop = c.scrollHeight;
                }, 50);
            },

            async startProcessing() {
                this.isProcessing = true;
                this.globalStatus = 'processing';
                this.globalProgress = 10;

                this.appendLog('info', 'File berhasil diunggah dan Cloud Temaniskripsi sedang memproses di background...');

                // Polling setiap 2 detik
                const pollInterval = setInterval(async () => {
                    try {
                        const response = await fetch(`{{ route('analysis.status', $analysis->slug) }}`);
                        const data = await response.json();

                        // === UPDATE VPS MESSAGE & PROGRESS ===
                        if (data.vps_message) {
                            this.vpsMessage = data.vps_message;
                            this.transcriptionStatus = data.vps_message;
                        }

                        if (data.total_segments > 0 && this.totalSegments === 0) {
                            this.totalSegments = data.total_segments;
                            this.appendLog('success', `Cloud selesai transkripsi: ${data.total_segments} segmen ditemukan`);
                        } else if (data.total_segments > 0) {
                            this.totalSegments = data.total_segments;
                        }

                        if (data.total_duration_sec > 0) {
                            this.totalDuration = data.total_duration_sec;
                        }

                        if (data.progress !== undefined) {
                            this.globalProgress = data.progress;
                            if (data.vps_message) {
                                this.appendLog('info', `Progress: ${data.progress}% — ${data.vps_message}`);
                            }
                        }

                        // === UPDATE VPS LOGS (detail dari VPS) ===
                        if (data.vps_logs && Array.isArray(data.vps_logs) && data.vps_logs.length > 0) {
                            const newLogs = data.vps_logs.filter(log => {
                                return !this.vpsLogs.some(existing => 
                                    existing.msg === log.msg && existing.time === log.time
                                );
                            });

                            newLogs.forEach(log => {
                                this.vpsLogs.push({
                                    time: log.time || new Date().toLocaleTimeString('id-ID'),
                                    msg: log.msg || '',
                                    level: log.msg.includes('ERROR') ? 'error' : 
                                           log.msg.includes('✅') || log.msg.includes('success') || log.msg.includes('BERHASIL') ? 'success' :
                                           log.msg.includes('⚠️') ? 'warning' : 'info'
                                });
                            });
                        }

                        // === UPDATE REAL-TIME TEXT ===
                        if (data.result_data && data.result_data.transcription) {
                            const newTexts = data.result_data.transcription;

                            if (newTexts.length > this.realtimeTexts.length) {
                                this.realtimeTexts = [];
                                newTexts.forEach(seg => {
                                    this.realtimeTexts.push(`[${seg.timestamp}] <span class="text-gray-900 font-bold">${seg.text_html}</span>`);
                                });

                                setTimeout(() => {
                                    const c = document.getElementById('realtime-text-box');
                                    if(c) c.scrollTop = c.scrollHeight;
                                }, 50);
                            }
                        }

                        // === SELESAI / GAGAL ===
                        if (data.status === 'analyzing' || (data.progress >= 80 && data.status !== 'completed' && this.totalSegments > 0)) {
                            clearInterval(pollInterval);
                            this.globalStatus = 'processing';
                            this.globalProgress = 80;
                            this.appendLog('info', 'Transkripsi Cloud selesai. Memulai Analisis & Sintesis AI...');
                            this.transcriptionStatus = 'Menganalisis & Menyintesiskan dengan AI...';
                            this.batchProcessingText = 'Memulai proses batch sintesis AI...';
                            
                            this.startAiBatchProcessing();
                        } else if (data.status === 'completed' || data.is_completed) {
                            clearInterval(pollInterval);
                            this.globalStatus = 'completed';
                            this.globalProgress = 100;
                            this.appendLog('success', 'Transkripsi selesai! Mengalihkan...');
                            this.transcriptionStatus = 'Penyimpanan berhasil, mengalihkan...';

                            setTimeout(() => {
                                window.location.href = `{{ route('analysis.result', ['analysis' => $analysis->slug]) }}`;
                            }, 1500);
                        } else if (data.status === 'failed') {
                            clearInterval(pollInterval);
                            this.globalStatus = 'failed';
                            this.transcriptionStatus = 'Proses dibatalkan atau gagal di Cloud.';
                            this.appendLog('error', 'Cloud melaporkan kegagalan proses.');
                        }
                    } catch (e) {
                        console.error('Error polling status:', e);
                    }
                }, 2000);
            },

            async startAiBatchProcessing() {
                try {
                    const batchSize = 100;
                    const total = this.totalSegments;
                    let processed = this.resumeStartIdx;
                    
                    for (let startIdx = this.resumeStartIdx; startIdx < total; startIdx += batchSize) {
                        let endIdx = Math.min(startIdx + batchSize - 1, total - 1);
                        let attempt = 0;
                        const maxRetries = 3;
                        let success = false;

                        while (attempt < maxRetries && !success) {
                            try {
                                attempt++;
                                const currentBatchMsg = `Memproses Batch Sintesis AI (Baris ${startIdx + 1} - ${endIdx + 1} dari ${total})${attempt > 1 ? ` - Percobaan ${attempt}/3` : ''}...`;
                                this.transcriptionStatus = currentBatchMsg;
                                this.batchProcessingText = currentBatchMsg;
                                this.appendLog('info', `[AI Batch] Mengirim baris ${startIdx + 1} hingga ${endIdx + 1} ke AI${attempt > 1 ? ` (Percobaan ${attempt})` : ''}...`);
                                
                                const response = await fetch(`{{ route('analysis.processChunk', $analysis->slug) }}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': window.BIMA ? window.BIMA.csrfToken : document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ start_idx: startIdx, end_idx: endIdx })
                                });
                                
                                const data = await response.json();
                                if (data.status === 'error') {
                                    throw new Error(data.message || 'Gagal memproses batch.');
                                }

                                if (data.batch_history) {
                                    this.batchHistory = data.batch_history;
                                }
                                
                                success = true;
                                processed += (endIdx - startIdx + 1);
                                // Update progress from 80% to 95%
                                this.globalProgress = 80 + Math.floor((processed / total) * 15);
                                this.appendLog('success', `[AI Batch] Baris ${startIdx + 1} hingga ${endIdx + 1} berhasil dianalisis.`);

                            } catch (err) {
                                if (attempt >= maxRetries) {
                                    throw err;
                                }
                                this.appendLog('warning', `[AI Batch] Gagal (Percobaan ${attempt}/3): ${err.message}. Mengulang dalam 3 detik...`);
                                await new Promise(resolve => setTimeout(resolve, 3000));
                            }
                        }
                    }
                    
                    this.transcriptionStatus = 'Finalisasi Analisis...';
                    this.appendLog('info', 'Semua baris selesai. Menyimpan hasil akhir...');
                    
                    await fetch(`{{ route('analysis.finalizeAnalysis', $analysis->slug) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.BIMA ? window.BIMA.csrfToken : document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    this.globalStatus = 'completed';
                    this.globalProgress = 100;
                    this.appendLog('success', 'Analisis komprehensif selesai! Mengalihkan...');
                    this.transcriptionStatus = 'Penyimpanan berhasil, mengalihkan...';

                    setTimeout(() => {
                        window.location.href = `{{ route('analysis.result', ['analysis' => $analysis->slug]) }}`;
                    }, 1500);

                } catch (e) {
                    console.error('AI Batch Processing Error:', e);
                    this.globalStatus = 'failed';
                    this.transcriptionStatus = 'Gagal memproses AI Batch.';
                    this.appendLog('error', `AI Error: ${e.message}`);
                }
            }
        }));
    });
    </script>
    <script>
    document.addEventListener('alpine:initialized', () => {
        if(window.lucide) window.lucide.createIcons();
    })
    </script>
    </x-slot>
</x-layouts.app>
