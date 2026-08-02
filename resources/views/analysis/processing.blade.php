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

        {{-- INLINE LAYOUT GRID --}}
        <div style="display: flex; flex-wrap: wrap; gap: 2rem; width: 100%;">
            
            {{-- Kolom Kiri: Proses Utama (Dibuat Center & Full Width) --}}
            <div style="flex: 1 1 100%; width: 100%; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">
                
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

                    {{-- Analisis AI Streaming (Step 2) --}}
                    <div class="p-6">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" :class="step2Class">
                                <svg x-show="globalProgress >= 80 || globalStatus === 'completed'" style="display: none;" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                                
                                <svg x-show="globalStatus === 'processing' && globalProgress < 80" style="display: none;" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                
                                <svg x-show="globalStatus === 'failed'" style="display: none;" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <p class="font-black text-sm text-gray-900 uppercase tracking-wide">Transkripsi AI Real-Time</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1" x-text="(globalProgress >= 80 || globalStatus === 'completed') ? 'Selesai mentranskripsi.' : (vpsMessage || 'Menunggu respon VPS...')"></p>
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

                    {{-- Sintesis AI (Step 3) --}}
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors" :class="step3Class">
                                <svg x-show="globalStatus === 'completed'" style="display: none;" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                                
                                <svg x-show="globalStatus === 'processing' && globalProgress >= 80" style="display: none;" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                
                                <svg x-show="globalStatus === 'processing' && globalProgress < 80" style="display: none;" class="w-5 h-5 text-blue-300" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>

                                <svg x-show="globalStatus === 'failed'" style="display: none;" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <p class="font-black text-sm text-gray-900 uppercase tracking-wide" :class="{'opacity-50': globalProgress < 80 && globalStatus !== 'failed'}">Analisis & Sintesis AI</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1" :class="{'opacity-50': globalProgress < 80 && globalStatus !== 'failed'}" x-text="(globalStatus === 'completed') ? 'Analisis intonasi, relasi, dan advice giving selesai.' : ((globalProgress >= 80 && globalStatus !== 'failed') ? transcriptionStatus : 'Menunggu tahap transkripsi selesai...')"></p>

                                <!-- Timeline Batch AI -->
                                <div x-show="globalProgress >= 80 || batchHistory.length > 0" style="display: none;" class="mt-6 space-y-4">
                                    <template x-for="(batch, index) in batchHistory" :key="index">
                                        <div x-data="{ expanded: false }" class="relative pl-6 border-l-2 border-gray-200 py-1">
                                            <div class="absolute w-3 h-3 bg-blue-500 rounded-full -left-[7px] top-2 shadow"></div>
                                            <div class="flex items-center justify-between gap-4">
                                                <div>
                                                    <span class="text-xs font-bold text-gray-900">Batch <span x-text="index + 1"></span> Selesai</span>
                                                    <span class="text-[0.65rem] text-gray-500 block">Baris <span x-text="batch.start_idx + 1"></span> - <span x-text="batch.end_idx + 1"></span></span>
                                                </div>
                                                <button @click="expanded = !expanded" class="text-[0.65rem] font-bold text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-1 rounded border border-blue-100 transition-colors uppercase tracking-wider flex items-center gap-1">
                                                    <span x-text="expanded ? 'Tutup Detail' : 'Lihat Detail'"></span>
                                                    <svg x-show="!expanded" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                    <svg x-show="expanded" style="display:none;" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"></polyline></svg>
                                                </button>
                                            </div>
                                            
                                            <!-- Expandable Detail -->
                                            <div x-show="expanded" x-collapse class="mt-3 space-y-3 bg-gray-900 text-gray-300 p-4 rounded-xl border border-gray-800 text-[0.65rem] overflow-hidden shadow-inner">
                                                <div>
                                                    <div class="font-black text-gray-400 uppercase tracking-widest mb-1 border-b border-gray-800 pb-1">System Prompt</div>
                                                    <pre class="whitespace-pre-wrap font-mono leading-relaxed" x-text="batch.system_prompt"></pre>
                                                </div>
                                                <div>
                                                    <div class="font-black text-blue-400 uppercase tracking-widest mb-1 border-b border-gray-800 pb-1">User Prompt (Chunks Data)</div>
                                                    <pre class="whitespace-pre-wrap font-mono leading-relaxed text-blue-200" x-text="batch.user_prompt"></pre>
                                                </div>
                                                <div>
                                                    <div class="font-black text-green-400 uppercase tracking-widest mb-1 border-b border-gray-800 pb-1">Raw Response dari AI</div>
                                                    <pre class="whitespace-pre-wrap font-mono leading-relaxed text-green-200" x-text="batch.raw_response"></pre>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
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
                        if (data.status === 'analyzing') {
                            clearInterval(pollInterval);
                            this.globalStatus = 'processing';
                            this.globalProgress = 80;
                            this.appendLog('info', 'VPS selesai. Memulai Analisis AI (Batch Processing)...');
                            this.transcriptionStatus = 'Menganalisis dengan AI...';
                            
                            this.startAiBatchProcessing();
                        } else if (data.status === 'completed' || data.is_completed) {
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
            },

            async startAiBatchProcessing() {
                try {
                    const batchSize = 10;
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
                                this.transcriptionStatus = `Memproses Analisis AI (Baris ${startIdx + 1} - ${endIdx + 1} dari ${total})${attempt > 1 ? ` - Percobaan ${attempt}/3` : ''}...`;
                                this.appendLog('info', `[AI Batch] Mengirim baris ${startIdx + 1} hingga ${endIdx + 1} ke OpenAI${attempt > 1 ? ` (Percobaan ${attempt})` : ''}...`);
                                
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
                        window.location.href = `{{ route('analysis.result', $analysis->slug) }}`;
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
