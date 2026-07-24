<x-layouts.app title="Memproses Analisa — {{ $analysis->title }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="processingApp()" x-init="init()">

        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-2.5 h-2.5 rounded-full animate-pulse" :class="statusDotClass" id="status-dot"></div>
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest" x-text="statusLabel">Sedang Diproses</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 truncate">{{ $analysis->title }}</h1>
            <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-400 font-bold uppercase tracking-widest">
                <span>Model: <span class="text-gray-700">{{ $analysis->model_used ?? 'vps-faster-whisper' }}</span></span>
                <span>Bahasa: <span class="text-gray-700">{{ ['id' => '🇮🇩 Indonesia', 'en' => '🇬🇧 English', 'zh' => '🇨🇳 中文'][$analysis->locale] ?? $analysis->locale }}</span></span>
                <span>ID Sesi: <span class="text-gray-700 font-mono">{{ $analysis->slug }}</span></span>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden mb-8">
            <div class="bg-bima-red h-full rounded-full transition-all duration-700" :style="`width: ${globalProgress}%`"></div>
        </div>

        {{-- VPS Status Banner --}}
        <div class="mb-8 bg-gray-50 border border-gray-200 rounded-[1.5rem] p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-bima-red/10 text-bima-red flex items-center justify-center shrink-0">
                    <i data-lucide="cpu" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <p class="text-[0.65rem] font-black text-gray-400 uppercase tracking-widest">Status VPS</p>
                    <p class="text-sm font-bold text-gray-900 mt-1" x-text="vpsMessage || 'Menunggu respon dari VPS...'"></p>
                    <div class="flex flex-wrap gap-4 mt-3 text-xs font-medium" x-show="totalSegments > 0">
                        <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full font-black">📊 <span x-text="totalSegments"></span> Segmen</span>
                        <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full font-black">⏱️ <span x-text="totalDuration"></span> detik</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- INLINE LAYOUT GRID --}}
        <div style="display: flex; flex-wrap: wrap; gap: 2rem; width: 100%;">
            
            {{-- Kolom Kiri: Proses Utama --}}
            <div style="flex: 1 1 60%; min-width: 320px; display: flex; flex-direction: column; gap: 1.5rem;">
                
                {{-- Processing Card --}}
                <div class="bg-white border border-gray-100 rounded-[2rem] shadow-xl shadow-gray-100/60 divide-y divide-gray-50 overflow-hidden">

                    {{-- Status Audio --}}
                    <div class="p-6 flex items-start gap-4">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-green-100 text-green-600">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="font-black text-sm text-gray-900 uppercase tracking-wide">Audio Terkirim (Selesai)</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 font-medium">
                                1 File Utuh <span class="text-gray-400">Tersimpan di server</span>
                            </p>
                        </div>
                    </div>

                    {{-- Analisis AI Streaming --}}
                    <div class="p-6">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" :class="stepAnalysisClass">
                                <template x-if="globalStatus === 'completed'">
                                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                                </template>
                                <template x-if="globalStatus === 'processing'">
                                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </template>
                                <template x-if="globalStatus === 'failed'">
                                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                                </template>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <p class="font-black text-sm text-gray-900 uppercase tracking-wide">Transkripsi AI Real-Time</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1" x-text="transcriptionStatus">Menunggu respon VPS...</p>
                            </div>
                        </div>

                        {{-- Real-time Text Box --}}
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 h-64 overflow-y-auto font-mono text-xs text-gray-700 leading-relaxed" id="realtime-text-box">
                            <template x-for="(text, idx) in realtimeTexts" :key="idx">
                                <div class="mb-2" x-html="text"></div>
                            </template>
                            <template x-if="realtimeTexts.length === 0">
                                <span class="text-gray-400 italic">... transkripsi akan muncul di sini ...</span>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
            
            {{-- Kolom Kanan: Log Aktivitas --}}
            <div style="flex: 1 1 30%; min-width: 280px;">
                {{-- VPS Log Section --}}
                <div class="bg-white border border-gray-100 rounded-[1.5rem] shadow-sm shadow-gray-100/50 p-6 mb-6" x-show="vpsLogs.length > 0">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-red-50 text-bima-red flex items-center justify-center shrink-0">
                            <i data-lucide="server" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">Log VPS</h3>
                            <p class="text-[0.65rem] text-gray-400 mt-0.5 uppercase tracking-wider">Transkripsi Real-Time</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 h-72 overflow-y-auto font-mono text-[0.7rem] text-gray-700 leading-relaxed scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent" id="vps-log-box">
                        <template x-for="(log, i) in vpsLogs" :key="i">
                            <div class="mb-2 flex items-start gap-2">
                                <span class="text-gray-400 shrink-0" x-text="`[${log.time}]`"></span>
                                <span x-text="log.msg" :class="{
                                    'text-green-700 font-bold': log.level === 'success',
                                    'text-red-700 font-bold': log.level === 'error',
                                    'text-yellow-700': log.level === 'warning',
                                    'text-gray-700': log.level === 'info'
                                }"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm shadow-gray-100/50 p-6 sticky top-8" x-show="logs.length > 0">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <i data-lucide="activity" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">Aktivitas Sistem</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Riwayat proses analisis</p>
                        </div>
                    </div>
                    
                    <div class="relative space-y-5 max-h-80 overflow-y-auto pr-4 scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent" id="log-container">
                        <template x-for="(log, i) in logs" :key="i">
                            <div class="relative flex items-start gap-4 group">
                                <template x-if="i !== logs.length - 1">
                                    <div class="absolute top-6 left-[0.31rem] w-px h-full bg-gray-100 group-hover:bg-gray-200 transition-colors -z-10"></div>
                                </template>

                                <div class="relative mt-1 w-2.5 h-2.5 rounded-full ring-4 ring-white shrink-0" 
                                     :class="{
                                         'bg-red-500': log.level === 'error',
                                         'bg-yellow-400': log.level === 'warning',
                                         'bg-green-500': log.level === 'success',
                                         'bg-blue-400': log.level === 'info' || !log.level
                                     }"></div>

                                <div class="flex-1 min-w-0 pb-1">
                                    <p class="text-xs text-gray-700 leading-relaxed font-medium break-words" x-text="log.msg"></p>
                                    <p class="text-[0.65rem] text-gray-400 font-bold mt-1.5 uppercase tracking-wider flex items-center gap-1.5">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        <span x-text="log.time"></span>
                                    </p>
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

            get stepAnalysisClass() {
                if (this.globalStatus === 'completed') return 'bg-green-100 text-green-600';
                if (this.globalStatus === 'processing') return 'bg-blue-100 text-blue-500';
                return 'bg-red-100 text-red-600';
            },

            appendLog(level, msg) {
                const now = new Date().toLocaleTimeString('id-ID', { hour12: false });
                this.logs = [...this.logs, { level, msg, time: now }];
                setTimeout(() => {
                    const c = document.getElementById('log-container');
                    if(c) c.scrollTop = c.scrollHeight;
                }, 50);
            },

            async startProcessing() {
                this.isProcessing = true;
                this.globalStatus = 'processing';
                this.globalProgress = 10;

                this.appendLog('info', 'File berhasil diunggah dan VPS sedang memproses di background...');

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

                        if (data.total_segments > 0) {
                            this.totalSegments = data.total_segments;
                            this.appendLog('success', `VPS selesai transkripsi: ${data.total_segments} segmen ditemukan`);
                        }

                        if (data.total_duration_sec > 0) {
                            this.totalDuration = data.total_duration_sec;
                        }

                        if (data.progress !== undefined) {
                            this.globalProgress = data.progress;
                            this.appendLog('info', `Progress: ${data.progress}% — ${data.vps_message || 'Sedang diproses...'}`);
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

                            // Auto-scroll VPS log box
                            setTimeout(() => {
                                const vpsLogBox = document.getElementById('vps-log-box');
                                if(vpsLogBox) vpsLogBox.scrollTop = vpsLogBox.scrollHeight;
                            }, 50);
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
                        if (data.status === 'completed' || data.is_completed) {
                            clearInterval(pollInterval);
                            this.globalStatus = 'completed';
                            this.globalProgress = 100;
                            this.appendLog('success', 'Transkripsi selesai! Mengalihkan...');
                            this.transcriptionStatus = 'Penyimpanan berhasil, mengalihkan...';

                            setTimeout(() => {
                                window.location.href = `{{ route('analysis.result', $analysis->slug) }}`;
                            }, 1500);
                        } else if (data.status === 'failed') {
                            clearInterval(pollInterval);
                            this.globalStatus = 'failed';
                            this.transcriptionStatus = 'Proses dibatalkan atau gagal di VPS.';
                            this.appendLog('error', 'VPS melaporkan kegagalan proses.');
                        }
                    } catch (e) {
                        console.error('Error polling status:', e);
                    }
                }, 2000);
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
