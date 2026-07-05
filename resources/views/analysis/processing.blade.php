<x-layouts.app title="Memproses Analisa — {{ $analysis->title }}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="processingApp()" x-init="init()">

        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-2.5 h-2.5 rounded-full animate-pulse" :class="statusDotClass" id="status-dot"></div>
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest" x-text="statusLabel">Sedang Diproses</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 truncate">{{ $analysis->title }}</h1>
            <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-400 font-bold uppercase tracking-widest">
                <span>Model: <span class="text-gray-700">{{ $analysis->model_used ?? 'gpt-audio-1.5' }}</span></span>
                <span>Bahasa: <span class="text-gray-700">{{ ['id' => '🇮🇩 Indonesia', 'en' => '🇬🇧 English', 'zh' => '🇨🇳 中文'][$analysis->locale] ?? $analysis->locale }}</span></span>
                <span>ID Sesi: <span class="text-gray-700 font-mono">{{ $analysis->slug }}</span></span>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden mb-8">
            <div class="bg-bima-red h-full rounded-full transition-all duration-700" :style="`width: ${globalProgress}%`"></div>
        </div>

        {{-- Processing Card --}}
        <div class="bg-white border border-gray-100 rounded-[2rem] shadow-xl shadow-gray-100/60 divide-y divide-gray-50 overflow-hidden">

            {{-- STEP 1: Upload (Slicing) --}}
            <div class="p-6 flex items-start gap-4">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-green-100 text-green-600">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="font-black text-sm text-gray-900 uppercase tracking-wide">Pemotongan Audio (Selesai)</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 font-medium">
                        {{ $analysis->total_chunks }} potongan <span class="text-gray-400">via Browser (Client-side)</span>
                    </p>
                </div>
            </div>

            {{-- STEP 2: Analisis AI --}}
            <div class="p-6">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" :class="stepAnalysisClass">
                        <template x-if="globalStatus === 'completed' || globalStatus === 'synthesizing'">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                        </template>
                        <template x-if="globalStatus === 'processing' || globalStatus === 'partial_failure'">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </template>
                        <template x-if="globalStatus === 'pending'">
                            <i data-lucide="cpu" class="w-5 h-5"></i>
                        </template>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 flex-wrap">
                            <p class="font-black text-sm text-gray-900 uppercase tracking-wide">Pengiriman & Analisis AI</p>
                            <span class="text-xs bg-gray-100 text-gray-600 font-bold px-2.5 py-1 rounded-full">
                                <span x-text="processedChunks"></span>/{{ $analysis->total_chunks }} selesai
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Per-chunk rows --}}
                <div class="space-y-2">
                    <template x-for="chunk in chunks" :key="chunk.index">
                        <div class="flex items-center gap-3 p-3 rounded-xl text-xs font-bold border"
                            :class="{
                                'bg-green-50 border-green-100': chunk.status === 'done',
                                'bg-blue-50 border-blue-200': chunk.status === 'running',
                                'bg-red-50 border-red-200': chunk.status === 'failed',
                                'bg-gray-50 border-gray-100': chunk.status === 'pending'
                            }">
                            
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                                :class="{
                                    'bg-green-500 text-white': chunk.status === 'done',
                                    'bg-blue-500 text-white': chunk.status === 'running',
                                    'bg-red-500 text-white': chunk.status === 'failed',
                                    'bg-gray-200 text-gray-400': chunk.status === 'pending'
                                }">
                                <template x-if="chunk.status === 'done'"><i data-lucide="check" class="w-3.5 h-3.5"></i></template>
                                <template x-if="chunk.status === 'running'"><svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg></template>
                                <template x-if="chunk.status === 'failed'"><i data-lucide="x" class="w-3.5 h-3.5"></i></template>
                                <template x-if="chunk.status === 'pending'"><span class="text-[0.6rem]" x-text="chunk.index"></span></template>
                            </div>
                            
                            <span :class="{
                                'text-green-700': chunk.status === 'done',
                                'text-blue-700': chunk.status === 'running',
                                'text-red-700': chunk.status === 'failed',
                                'text-gray-400': chunk.status === 'pending'
                            }" x-text="`Potongan ${chunk.index}`"></span>
                            
                            <span class="text-gray-400 font-medium" x-text="getChunkStatusText(chunk.status)"></span>
                            
                            <div class="flex-1"></div>
                            
                            <template x-if="chunk.status === 'failed'">
                                <button @click="processChunk(chunk.index)" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg transition-colors text-[0.65rem] uppercase tracking-wider font-black">
                                    Coba Lagi
                                </button>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- STEP 3: Sintesis --}}
            <div class="p-6 flex items-start gap-4">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                    :class="globalStatus === 'completed' ? 'bg-green-100 text-green-600' : (globalStatus === 'synthesizing' ? 'bg-purple-100 text-purple-500' : 'bg-gray-100 text-gray-300')">
                    <template x-if="globalStatus === 'completed'"><i data-lucide="check-circle" class="w-5 h-5"></i></template>
                    <template x-if="globalStatus === 'synthesizing'"><svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg></template>
                    <template x-if="globalStatus !== 'completed' && globalStatus !== 'synthesizing'"><i data-lucide="layers" class="w-5 h-5"></i></template>
                </div>
                <div class="flex-1">
                    <p class="font-black text-sm text-gray-900 uppercase tracking-wide">Sintesis Hasil Akhir</p>
                    <p class="text-xs mt-1" :class="globalStatus === 'synthesizing' ? 'text-purple-500 font-bold animate-pulse' : 'text-gray-400'" 
                       x-text="globalStatus === 'synthesizing' ? 'Menyatukan semua hasil analisis...' : (globalStatus === 'completed' ? 'Sintesis selesai.' : 'Menunggu analisis selesai...')">
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Aktivitas Sistem (Modern Log) --}}
        <div class="mt-8 bg-white border border-gray-100 rounded-2xl shadow-sm shadow-gray-100/50 p-6" x-show="logs.length > 0">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="activity" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wide">Aktivitas Sistem</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Riwayat proses analisis secara real-time</p>
                </div>
            </div>
            
            <div class="relative space-y-5 max-h-80 overflow-y-auto pr-4 scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent" id="log-container">
                <template x-for="(log, i) in logs" :key="i">
                    <div class="relative flex items-start gap-4 group">
                        {{-- Garis konektor (hanya jika bukan log terakhir) --}}
                        <template x-if="i !== logs.length - 1">
                            <div class="absolute top-6 left-[0.31rem] w-px h-full bg-gray-100 group-hover:bg-gray-200 transition-colors -z-10"></div>
                        </template>

                        {{-- Dot status --}}
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

    <x-slot name="scripts">
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('processingApp', () => ({
            slug: '{{ $analysis->slug }}',
            totalChunks: {{ $analysis->total_chunks }},
            processedChunks: 0,
            globalStatus: 'pending', // pending, processing, partial_failure, synthesizing, completed, fatal_error
            globalProgress: 10,
            chunks: [],
            logs: [],

            init() {
                for (let i = 1; i <= this.totalChunks; i++) {
                    this.chunks.push({ index: i, status: 'pending' }); // pending, running, done, failed
                }
                this.appendLog('info', 'Sesi disiapkan. Menunggu pengiriman potongan...');
                
                // Cek status dari DB
                // Jika sudah diproses sebagian, kita harus tahu (di sini dianggap mulai dari awal untuk demo)
                this.startProcessing();
            },

            get statusDotClass() {
                if (this.globalStatus === 'completed') return 'bg-green-500';
                if (this.globalStatus === 'partial_failure' || this.globalStatus === 'fatal_error') return 'bg-red-600';
                return 'bg-bima-red';
            },

            get statusLabel() {
                if (this.globalStatus === 'completed') return 'Selesai';
                if (this.globalStatus === 'partial_failure') return 'Terdapat Kegagalan Potongan';
                if (this.globalStatus === 'fatal_error') return 'Terjadi Kegagalan Fatal';
                return 'Sedang Diproses';
            },

            get stepAnalysisClass() {
                if (this.globalStatus === 'completed' || this.globalStatus === 'synthesizing') return 'bg-green-100 text-green-600';
                if (this.globalStatus === 'processing' || this.globalStatus === 'partial_failure') return 'bg-blue-100 text-blue-500';
                return 'bg-gray-100 text-gray-300';
            },

            getChunkStatusText(status) {
                const map = { done: 'Selesai', running: 'Menganalisis...', failed: 'Gagal', pending: 'Menunggu' };
                return map[status] || status;
            },

            appendLog(level, msg) {
                const now = new Date().toLocaleTimeString('id-ID', { hour12: false });
                this.logs.push({ level, msg, time: now });
                setTimeout(() => {
                    const c = document.getElementById('log-container');
                    if(c) c.scrollTop = c.scrollHeight;
                }, 50);
            },

            async getDB() {
                return new Promise((resolve, reject) => {
                    const req = indexedDB.open('BimaAudioDB', 1);
                    req.onsuccess = () => resolve(req.result);
                    req.onerror = () => reject(req.error);
                });
            },

            async getChunkFromDB(index) {
                const db = await this.getDB();
                return new Promise((resolve, reject) => {
                    const tx = db.transaction('chunks', 'readonly');
                    const store = tx.objectStore('chunks');
                    const req = store.get([this.slug, index]);
                    req.onsuccess = () => {
                        if (req.result) resolve(req.result.blob);
                        else reject(new Error('Potongan audio tidak ditemukan di browser.'));
                    };
                    req.onerror = () => reject(req.error);
                });
            },

            async startProcessing() {
                this.globalStatus = 'processing';
                this.globalProgress = 20;

                for (let i = 1; i <= this.totalChunks; i++) {
                    const chunk = this.chunks.find(c => c.index === i);
                    if (chunk.status !== 'done') {
                        await this.processChunk(i);
                    }
                }

                if (this.processedChunks === this.totalChunks) {
                    this.finalize();
                } else {
                    this.globalStatus = 'partial_failure';
                    this.appendLog('warning', 'Beberapa potongan gagal. Silakan klik Coba Lagi pada potongan yang gagal.');
                }
            },

            async processChunk(index) {
                const chunk = this.chunks.find(c => c.index === index);
                chunk.status = 'running';
                this.appendLog('info', `Mengirim potongan ${index}...`);
                
                try {
                    const blob = await this.getChunkFromDB(index);
                    const formData = new FormData();
                    formData.append('audio', blob, `chunk_${index}.wav`);
                    formData.append('chunk_index', index);

                    const csrf = document.querySelector('meta[name="csrf-token"]').content;
                    
                    const res = await fetch(`{{ url('/en/analysis') }}/${this.slug}/chunk`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await res.json();
                    
                    if (res.ok && data.status === 'success') {
                        chunk.status = 'done';
                        this.processedChunks++;
                        this.globalProgress = 20 + Math.floor((this.processedChunks / this.totalChunks) * 70);
                        this.appendLog('success', `Potongan ${index} selesai dianalisis.`);
                        
                        if (this.processedChunks === this.totalChunks) {
                            this.finalize();
                        }
                    } else {
                        throw new Error(data.message || 'Gagal dari server.');
                    }
                } catch (e) {
                    chunk.status = 'failed';
                    this.appendLog('error', `Potongan ${index} gagal: ${e.message}`);
                }
            },

            async finalize() {
                this.globalStatus = 'synthesizing';
                this.globalProgress = 95;
                this.appendLog('info', 'Sintesis hasil akhir...');

                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]').content;
                    const res = await fetch(`{{ url('/en/analysis') }}/${this.slug}/finalize`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();
                    if (res.ok && data.status === 'success') {
                        this.globalStatus = 'completed';
                        this.globalProgress = 100;
                        this.appendLog('success', 'Analisis selesai! Mengalihkan...');
                        setTimeout(() => window.location.href = data.redirect, 1200);
                    } else {
                        throw new Error(data.message || 'Gagal sintesis.');
                    }
                } catch (e) {
                    this.globalStatus = 'fatal_error';
                    this.appendLog('error', `Gagal sintesis: ${e.message}`);
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
