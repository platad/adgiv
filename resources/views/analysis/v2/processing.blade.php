<x-layouts.app title="Memproses Analisis V2 — {{ $analysis->title }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-fade-in">
        
        {{-- Header (Standardized with V1 & Multi-language Support) --}}
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest mb-3">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
                <span class="lang-id">Kembali ke Dashboard</span>
                <span class="lang-en">Back to Dashboard</span>
                <span class="lang-zh">返回控制面板</span>
            </a>

            <div class="flex items-center gap-3 mb-2">
                <span class="text-xs font-black text-purple-600 uppercase tracking-widest" id="status-badge-text">
                    <span class="lang-id">Sedang Menganalisis</span>
                    <span class="lang-en">Processing Analysis</span>
                    <span class="lang-zh">正在处理分析</span>
                </span>
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-700 text-[0.65rem] font-bold">
                    Multi-Task RoBERTa & Graf
                </span>
            </div>

            <h1 class="text-2xl font-black text-gray-900 truncate tracking-tight uppercase">{{ $analysis->title }}</h1>

            <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-400 font-bold uppercase tracking-widest">
                <span>
                    <span class="lang-id">Bahasa:</span>
                    <span class="lang-en">Language:</span>
                    <span class="lang-zh">语言:</span>
                    <span class="text-gray-700 font-semibold">{{ ['id' => '🇮🇩 Indonesia', 'en' => '🇬🇧 English', 'zh' => '🇨🇳 中文'][$analysis->locale] ?? $analysis->locale }}</span>
                </span>
                <span>
                    <span class="lang-id">ID Sesi:</span>
                    <span class="lang-en">Session ID:</span>
                    <span class="lang-zh">会话编号:</span>
                    <span class="text-gray-700 font-mono">{{ $analysis->slug }}</span>
                </span>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden mb-8 shadow-inner">
            <div id="progress-bar" class="bg-gradient-to-r from-purple-500 to-indigo-600 h-full rounded-full transition-all duration-700" style="width: 0%"></div>
        </div>

        {{-- Main Execution Card --}}
        <div class="bg-white border border-gray-100 rounded-[2.5rem] shadow-xl shadow-gray-100/60 p-8 md:p-12" x-data="processingApp()">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-gray-50">
                <div class="flex items-center gap-4">
                    <div id="status-icon-wrapper" class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 transition-colors">
                        {{-- Clean SVG spinner by default --}}
                        <svg id="status-icon-spinner" class="w-6 h-6 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <div id="status-icon-check" class="hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 mb-1">Status Pemrosesan AI</p>
                        <h2 class="text-lg font-black text-gray-900" id="status-text">Menyiapkan koneksi ke Cloud AI...</h2>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button type="button" @click="showLogs = !showLogs"
                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-xs font-bold text-gray-700 transition shadow-xs cursor-pointer">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                        <span x-text="showLogs ? 'Tutup Log' : 'Lihat Log Latar Belakang'"></span>
                        <span class="px-1.5 py-0.5 rounded-full bg-purple-100 text-purple-700 text-[0.65rem] font-mono" x-text="logCount"></span>
                    </button>
                    <div class="text-right">
                        <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 mb-1">Progres Keseluruhan</p>
                        <p class="text-3xl font-black text-purple-600" id="progress-text">0%</p>
                    </div>
                </div>
            </div>

            {{-- Collapsible Log Console (Rapi, Responsif & Modern) --}}
            <div x-show="showLogs" x-collapse class="mb-8 p-5 bg-gray-950 rounded-2xl border border-gray-800 text-gray-200 font-mono shadow-inner">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-800 text-xs font-bold text-gray-400">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-gray-300 uppercase tracking-wider text-[0.7rem]">Log Sinkronisasi Cloud Temaniskripsi (V2)</span>
                    </div>
                    <span class="text-[0.65rem] text-gray-500">Live WebSocket Telemetry</span>
                </div>
                <div id="v2-log-box" class="h-48 overflow-y-auto space-y-1.5 text-xs text-gray-300 pr-2 scrollbar-thin scrollbar-thumb-gray-800">
                    <div class="text-gray-500 italic">[Sistem] Menginisialisasi koneksi analitik suara...</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Status Tahapan Pipeline --}}
                <div class="space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-4">Pipeline Execution</h3>
                    
                    {{-- Step 1 --}}
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-full bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 shrink-0 transition-colors" id="step1-icon">
                            <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-gray-900" id="step1-title">Kompresi & Format Audio</p>
                            <p class="text-xs font-medium text-gray-500 mt-0.5" id="step1-desc">Format audio optimal 16kHz mono sedang disiapkan...</p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 shrink-0 transition-colors" id="step2-icon">
                            <i data-lucide="mic" class="w-4 h-4"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-gray-900" id="step2-title">WhisperX & Pemisahan Pembicara (Diarization)</p>
                            <p class="text-xs font-medium text-gray-500 mt-0.5" id="step2-desc">Menunggu giliran transkripsi percakapan...</p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 shrink-0 transition-colors" id="step3-icon">
                            <i data-lucide="network" class="w-4 h-4"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-gray-900" id="step3-title">RoBERTa Multi-Task & Pemodelan Graf Wacana</p>
                            <p class="text-xs font-medium text-gray-500 mt-0.5" id="step3-desc">Menunggu klasifikasi peran, tindak tutur, dan relasi kuasa...</p>
                        </div>
                    </div>
                </div>

                {{-- Informasi Panel Tambahan & Speaker Feedback --}}
                <div class="flex flex-col gap-4">
                    {{-- Hidden Audio Player for Speaker Snippet Previews --}}
                    <audio x-ref="processingAudioPlayer" src="{{ route('analysis.v2.audio', $analysis->slug) }}" @timeupdate="onSpeakerAudioUpdate()" @ended="stopSpeakerAudio()"></audio>

                    {{-- Speaker Detection Live Interactive Feedback Box --}}
                    <div id="speaker-detected-box" x-show="speakersLoaded && speakersList.length > 0" x-collapse
                        class="bg-purple-50/70 border border-purple-200 rounded-3xl p-6 transition-all duration-500 animate-fade-in space-y-4">
                        
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-purple-600/20">
                                    <i data-lucide="users" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <span class="px-2 py-0.5 rounded-full bg-purple-200 text-purple-800 text-[0.6rem] font-black uppercase tracking-wider">Anotasi Pembicara</span>
                                    <h4 class="text-sm font-black text-purple-950 mt-0.5" x-text="speakersList.length + ' Peran Terdeteksi'"></h4>
                                </div>
                            </div>

                            {{-- Quick Swap Button if exactly 2 speakers --}}
                            <template x-if="speakersList.length === 2">
                                <button type="button" @click="swapTwoSpeakers()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer shrink-0">
                                    <i data-lucide="arrow-left-right" class="w-3.5 h-3.5"></i>
                                    <span>Tukar Peran</span>
                                </button>
                            </template>
                        </div>

                        {{-- Multi-party Alert Notice if > 2 speakers --}}
                        <template x-if="speakersList.length > 2">
                            <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-2xl text-xs text-indigo-900 font-medium flex items-center gap-2">
                                <i data-lucide="info" class="w-4 h-4 text-indigo-600 shrink-0"></i>
                                <span><strong>Multiparty Dialogue:</strong> Putar sampel suara tiap penutur lalu beri nama peran spesifik agar analisis akurat.</span>
                            </div>
                        </template>

                        <p class="text-xs text-purple-900 leading-relaxed font-medium">
                            Dengarkan sampel suara untuk memvalidasi penutur, dan beri label nama peran (misal nama dosen/mahasiswa):
                        </p>

                        {{-- List of Speaker Cards --}}
                        <div class="space-y-3">
                            <template x-for="(spk, idx) in speakersList" :key="spk.original">
                                <div class="bg-white/90 border border-purple-100 rounded-2xl p-3.5 transition-all shadow-xs space-y-2.5">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black text-white"
                                                  :class="idx === 0 ? 'bg-purple-600' : (idx === 1 ? 'bg-blue-600' : 'bg-gray-700')"
                                                  x-text="idx + 1"></span>
                                            <span class="text-xs font-black text-gray-800 uppercase" x-text="spk.original"></span>
                                            <span class="px-1.5 py-0.2 rounded-full text-[0.6rem] font-bold bg-gray-100 text-gray-600" x-text="spk.utterance_count + ' ujaran'"></span>
                                        </div>

                                        {{-- Audio Playback Button --}}
                                        <button type="button" @click="toggleSpeakerSample(spk)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer"
                                            :class="playingSpeaker === spk.original ? 'bg-purple-600 text-white shadow-xs' : 'bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200'">
                                            <i :data-lucide="playingSpeaker === spk.original ? 'square' : 'play'" class="w-3 h-3"></i>
                                            <span x-text="playingSpeaker === spk.original ? 'Stop' : 'Putar Sampel'"></span>
                                            <span class="text-[0.6rem] opacity-75 font-mono" x-text="'[' + (spk.timestamp || 'Audio') + ']'"></span>
                                        </button>
                                    </div>

                                    {{-- Sample Quote --}}
                                    <div class="text-[0.72rem] text-gray-600 italic bg-gray-50/80 p-2 rounded-xl border border-gray-100 line-clamp-2"
                                         x-text="'“' + (spk.sample_text || '...') + '”'">
                                    </div>

                                    {{-- Editable Inputs --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-0.5">
                                        <div>
                                            <label class="block text-[0.6rem] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Nama / Label</label>
                                            <input type="text" x-model="spk.name"
                                                class="w-full text-xs font-bold rounded-lg border-gray-200 focus:border-purple-500 focus:ring-purple-500 py-1 px-2.5 bg-white"
                                                :placeholder="idx === 0 ? 'Nama Dosen' : 'Nama Mahasiswa'">
                                        </div>
                                        <div>
                                            <label class="block text-[0.6rem] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Peran</label>
                                            <select x-model="spk.role" class="w-full text-xs font-semibold rounded-lg border-gray-200 focus:border-purple-500 focus:ring-purple-500 py-1 px-2 bg-white">
                                                <option value="Dosen">Dosen / Pembimbing</option>
                                                <option value="Mahasiswa">Mahasiswa Terbimbing</option>
                                                <option value="Penguji">Dosen Penguji</option>
                                                <option value="Moderator">Moderator / Peserta</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Save / Apply Button in Processing --}}
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-[0.68rem] text-purple-700/80 font-medium" x-show="saveSuccessMsg" x-text="saveSuccessMsg"></span>
                            <button type="button" @click="saveSpeakerChanges()" :disabled="isSavingSpeakers"
                                class="ml-auto inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer disabled:opacity-50">
                                <template x-if="isSavingSpeakers">
                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                </template>
                                <span x-text="isSavingSpeakers ? 'Menyimpan...' : 'Terapkan Label Peran'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100 flex flex-col justify-center text-center">
                        <div class="w-12 h-12 mx-auto rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center mb-3">
                            <i data-lucide="shield-check" class="w-6 h-6"></i>
                        </div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-1">Analisis Multi-Task Real-Time</h4>
                        <p class="text-xs text-gray-500 font-medium leading-relaxed">
                            Sistem menggunakan saluran WebSocket langsung ke Cloud AI. Halaman ini akan secara otomatis membawa Anda ke layar hasil setelah seluruh proses analisis wacana dan graf selesai 100%.
                        </p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <x-slot name="scripts">
    <script>
        function processingApp() {
            return {
                showLogs: false,
                logCount: 0,
                speakersLoaded: false,
                speakersList: [],
                playingSpeaker: null,
                speakerAudioEndTime: null,
                isSavingSpeakers: false,
                saveSuccessMsg: '',

                init() {
                    window.v2ProcessingApp = this;
                },

                async fetchSpeakerSamples() {
                    if (this.speakersLoaded) return;
                    try {
                        const res = await fetch("{{ route('analysis.v2.speaker-samples', $analysis->slug) }}");
                        const data = await res.json();
                        if (data.status === 'success' && data.speakers && data.speakers.length > 0) {
                            this.speakersList = data.speakers;
                            this.speakersLoaded = true;
                            this.$nextTick(() => {
                                if (window.lucide) window.lucide.createIcons();
                            });
                        }
                    } catch (e) {
                        console.warn("Belum dapat mengambil sampel pembicara:", e);
                    }
                },

                toggleSpeakerSample(spk) {
                    const audio = this.$refs.processingAudioPlayer;
                    if (!audio) return;

                    if (this.playingSpeaker === spk.original) {
                        this.stopSpeakerAudio();
                        return;
                    }

                    const startSec = typeof spk.start_sec === 'number' ? spk.start_sec : 0;
                    const endSec = typeof spk.end_sec === 'number' ? spk.end_sec : (startSec + 5);

                    audio.currentTime = startSec;
                    this.speakerAudioEndTime = endSec;
                    this.playingSpeaker = spk.original;
                    audio.play().catch(e => console.warn('Audio preview play error:', e));
                },

                onSpeakerAudioUpdate() {
                    const audio = this.$refs.processingAudioPlayer;
                    if (!audio) return;
                    if (this.speakerAudioEndTime !== null && audio.currentTime >= this.speakerAudioEndTime) {
                        this.stopSpeakerAudio();
                    }
                },

                stopSpeakerAudio() {
                    const audio = this.$refs.processingAudioPlayer;
                    if (audio) {
                        audio.pause();
                    }
                    this.playingSpeaker = null;
                    this.speakerAudioEndTime = null;
                },

                swapTwoSpeakers() {
                    if (this.speakersList.length !== 2) return;
                    const spk0 = this.speakersList[0];
                    const spk1 = this.speakersList[1];

                    const tempRole = spk0.role;
                    const tempName = spk0.name;
                    spk0.role = spk1.role;
                    spk0.name = spk1.name;
                    spk1.role = tempRole;
                    spk1.name = tempName;
                },

                async saveSpeakerChanges() {
                    this.isSavingSpeakers = true;
                    this.stopSpeakerAudio();
                    this.saveSuccessMsg = '';

                    try {
                        const payload = {
                            mode: 'rename',
                            speakers: this.speakersList.map(s => ({
                                original: s.original,
                                name: (s.name || s.role || s.original).trim(),
                                role: s.role || 'Dosen'
                            }))
                        };

                        const res = await fetch("{{ route('analysis.v2.update-speakers', $analysis->slug) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await res.json();
                        if (data.status === 'success') {
                            this.saveSuccessMsg = 'Label peran berhasil disimpan!';
                            setTimeout(() => { this.saveSuccessMsg = ''; }, 3000);
                        } else {
                            alert(data.message || 'Gagal menyimpan perubahan pembicara.');
                        }
                    } catch (err) {
                        console.error('Save speaker error:', err);
                        alert('Terjadi kesalahan saat menyimpan peran.');
                    } finally {
                        this.isSavingSpeakers = false;
                    }
                }
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            const slug = "{{ $analysis->slug }}";
            const wsUrl = "wss://vps.temaniskripsi.id/api/v2/ws/" + slug;
            
            let ws;
            let retryCount = 0;
            const logBox = document.getElementById('v2-log-box');

            function addLog(message, type = 'info') {
                if (!logBox) return;
                const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                const row = document.createElement('div');
                row.className = "flex items-start gap-2 leading-relaxed";
                
                let colorClass = "text-gray-300";
                if (type === 'success') colorClass = "text-green-400 font-semibold";
                else if (type === 'warning') colorClass = "text-amber-400";
                else if (type === 'error') colorClass = "text-red-400";
                
                row.innerHTML = `<span class="text-gray-500 shrink-0 select-none">[${time}]</span><span class="${colorClass}">${message}</span>`;
                logBox.appendChild(row);
                logBox.scrollTop = logBox.scrollHeight;

                // Update Alpine logCount if available
                if (window.v2ProcessingApp) {
                    window.v2ProcessingApp.logCount++;
                }
            }

            function updateStep(stepNum, status, desc) {
                const icon = document.getElementById('step' + stepNum + '-icon');
                const title = document.getElementById('step' + stepNum + '-title');
                const descEl = document.getElementById('step' + stepNum + '-desc');
                
                if (descEl) descEl.innerText = desc;
                
                if (status === 'active') {
                    if (icon) {
                        icon.className = "w-9 h-9 rounded-full bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 shrink-0 transition-colors";
                        icon.innerHTML = '<svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
                    }
                    if (title) title.className = "text-sm font-black text-purple-700";
                } else if (status === 'done') {
                    if (icon) {
                        icon.className = "w-9 h-9 rounded-full bg-green-50 border border-green-200 flex items-center justify-center text-green-600 shrink-0 transition-colors";
                        icon.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
                    }
                    if (title) title.className = "text-sm font-bold text-gray-900";
                } else if (status === 'error') {
                    if (icon) {
                        icon.className = "w-9 h-9 rounded-full bg-red-50 border border-red-200 flex items-center justify-center text-red-600 shrink-0 transition-colors";
                        icon.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>';
                    }
                    if (title) title.className = "text-sm font-bold text-red-600";
                }
            }

            function connectWS() {
                addLog("Membuka saluran WebSocket wss://vps.temaniskripsi.id/api/v2/ws/" + slug, "info");
                ws = new WebSocket(wsUrl);

                ws.onopen = function() {
                    document.getElementById('status-text').innerText = "Terhubung dengan aman ke Cloud AI";
                    updateStep(1, 'active', 'Koneksi stabil. Menyiapkan audio...');
                    addLog("Koneksi aman ke Cloud AI berhasil dibangun.", "success");
                    ws.send(JSON.stringify({action: "start_processing"}));
                    addLog("Sinyal proses 'start_processing' dikirimkan.", "info");
                };

                ws.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        
                        if (data.progress !== undefined) {
                            document.getElementById('progress-bar').style.width = data.progress + '%';
                            document.getElementById('progress-text').innerText = data.progress + '%';
                            
                            // Map progress to steps
                            if (data.progress > 0 && data.progress < 40) {
                                updateStep(1, 'done', 'Format audio optimal siap.');
                                updateStep(2, 'active', 'Memecah audio dan membedakan pembicara...');
                            } else if (data.progress >= 40 && data.progress < 80) {
                                updateStep(1, 'done', 'Format audio optimal siap.');
                                updateStep(2, 'done', 'Transkripsi & diarization selesai.');
                                updateStep(3, 'active', 'Menganalisis relasi kuasa & membentuk graf...');
                                
                                // Fetch speaker samples for interactive audio preview
                                if (window.v2ProcessingApp) {
                                    window.v2ProcessingApp.fetchSpeakerSamples();
                                }
                                addLog("Anotasi peran & sampel pembicara siap diverifikasi.", "success");
                            } else if (data.progress >= 80) {
                                updateStep(1, 'done', 'Format audio optimal siap.');
                                updateStep(2, 'done', 'Transkripsi & diarization selesai.');
                                updateStep(3, 'done', 'Klasifikasi multi-task dan pembentukan graf selesai.');
                                
                                if (window.v2ProcessingApp) {
                                    window.v2ProcessingApp.fetchSpeakerSamples();
                                }
                            }
                        }
                        
                        if (data.message) {
                            document.getElementById('status-text').innerText = data.message;
                            addLog(data.message, data.status === 'completed' ? 'success' : 'info');
                        }

                        if (data.status === 'completed') {
                            document.getElementById('status-text').innerText = "Analisis selesai! Mengalihkan ke hasil...";
                            addLog("Seluruh pipeline RoBERTa Multi-Task selesai 100%. Menyiapkan pengalihan...", "success");
                            
                            // Swap spinner with checkmark icon without spinning
                            const iconWrapper = document.getElementById('status-icon-wrapper');
                            const spinner = document.getElementById('status-icon-spinner');
                            const check = document.getElementById('status-icon-check');
                            
                            if (iconWrapper) {
                                iconWrapper.className = "w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center shrink-0 transition-colors";
                            }
                            if (spinner) spinner.classList.add('hidden');
                            if (check) check.classList.remove('hidden');

                            const badgeText = document.getElementById('status-badge-text');
                            if (badgeText) {
                                badgeText.innerHTML = '<span class="text-green-600">Selesai</span>';
                            }
                            
                            setTimeout(() => {
                                window.location.href = "/{{ app()->getLocale() }}/analysis/v2/" + slug + "/result";
                            }, 1200);
                        }
                    } catch (e) {
                        console.error("WS Parse Error", e);
                        addLog("Gagal mem-parsing pesan WebSocket: " + e.message, "error");
                    }
                };

                ws.onclose = function(e) {
                    if (retryCount < 5) {
                        retryCount++;
                        document.getElementById('status-text').innerText = `Menghubungkan ulang... (${retryCount}/5)`;
                        updateStep(1, 'error', 'Koneksi terputus. Mencoba ulang...');
                        addLog(`Koneksi terputus. Mencoba reconnect (${retryCount}/5)...`, "warning");
                        setTimeout(connectWS, 2000);
                    } else {
                        document.getElementById('status-text').innerText = "Proses Berjalan di Latar Belakang";
                        const iconWrapper = document.getElementById('status-icon-wrapper');
                        if (iconWrapper) {
                            iconWrapper.className = "w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0";
                            iconWrapper.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"></circle><path stroke-width="2" d="M12 6v6l4 2"></path></svg>';
                        }
                        updateStep(1, 'error', 'Koneksi real-time terputus. Anda dapat me-refresh halaman ini.');
                        addLog("Koneksi real-time terputus permanen. Proses analisis tetap berjalan di background VPS.", "warning");
                    }
                };
            }

            connectWS();
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
    </x-slot>
</x-layouts.app>