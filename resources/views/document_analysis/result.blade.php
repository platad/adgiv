<x-layouts.app :title="$documentAnalysis->title . ' — Hasil Analisis PDF'">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 animate-fade-in" x-data="documentResultApp()">

        {{-- Top Navigation & Action Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}" 
                       class="text-[0.65rem] font-bold text-gray-400 hover:text-bima-red uppercase tracking-wider flex items-center gap-1 transition">
                        <i data-lucide="arrow-left" class="w-3 h-3"></i>
                        Kembali ke Dashboard
                    </a>
                    <span class="text-gray-300">&bull;</span>
                    <span class="text-[0.65rem] font-bold text-gray-400 uppercase">
                        {{ $documentAnalysis->created_at->format('d M Y, H:i') }}
                    </span>
                </div>
                <h1 class="text-2xl lg:text-3xl font-black text-gray-900 tracking-tight uppercase">
                    {{ $documentAnalysis->title }}
                </h1>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('document_analysis.history', ['locale' => app()->getLocale()]) }}" class="px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 hover:border-gray-300 text-gray-700 text-xs font-bold uppercase tracking-wider transition flex items-center gap-1.5 shadow-sm" title="Lihat Riwayat Analisis Dokumen">
                    <i data-lucide="history" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Riwayat</span>
                </a>
                <a href="{{ route('document_analysis.create', ['locale' => app()->getLocale()]) }}" 
                   class="px-4 py-2.5 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider transition flex items-center gap-2 shadow-md">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Analisis Baru</span>
                </a>
                <a href="{{ route('document_analysis.pdf', ['documentAnalysis' => $documentAnalysis->slug, 'locale' => app()->getLocale()]) }}" 
                   target="_blank"
                   class="px-3.5 py-2.5 rounded-xl bg-white border border-gray-200 hover:border-gray-300 text-gray-700 text-xs font-bold uppercase tracking-wider transition flex items-center gap-1.5 shadow-sm"
                   title="Buka PDF di Tab Baru">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Buka PDF</span>
                </a>
            </div>
        </div>

        {{-- Mobile View Switcher Pill --}}
        <div class="lg:hidden flex bg-gray-200/60 p-1 rounded-2xl mb-4 max-w-sm mx-auto">
            <button type="button" 
                    @click="activeMobileTab = 'analysis'"
                    class="flex-1 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition"
                    :class="activeMobileTab === 'analysis' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'">
                Hasil Analisis
            </button>
            <button type="button" 
                    @click="activeMobileTab = 'pdf'"
                    class="flex-1 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition"
                    :class="activeMobileTab === 'pdf' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'">
                Pratinjau PDF
            </button>
        </div>

        {{-- Split Grid Layout (Desktop Side-by-Side, Mobile Tabbed) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- LEFT PANE: Interactive PDF Preview --}}
            <div class="lg:col-span-5 xl:col-span-5 flex flex-col"
                 :class="activeMobileTab === 'pdf' ? 'block' : 'hidden lg:flex'">
                
                <div class="bg-white rounded-3xl border border-gray-200/80 shadow-md p-4 sticky top-6 overflow-hidden flex flex-col h-[82vh]">
                    {{-- PDF Pane Header --}}
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 shrink-0">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-red-50 text-bima-red flex items-center justify-center shrink-0">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-xs font-black uppercase text-gray-800 truncate max-w-[200px]" title="{{ $documentAnalysis->file_name }}">
                                    {{ $documentAnalysis->file_name }}
                                </h3>
                                <p class="text-[0.62rem] text-gray-400 font-bold uppercase mt-0.5">
                                    {{ $documentAnalysis->total_pages ?? '?' }} Halaman &bull; {{ number_format($documentAnalysis->word_count ?? 0) }} Kata
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('document_analysis.pdf', ['documentAnalysis' => $documentAnalysis->slug, 'locale' => app()->getLocale()]) }}" 
                           download="{{ $documentAnalysis->file_name }}"
                           class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-500 hover:text-gray-900 flex items-center justify-center transition"
                           title="Unduh Berkas Asli">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>

                    {{-- PDF Iframe Embed --}}
                    <div class="flex-1 w-full bg-gray-100 rounded-2xl overflow-hidden relative">
                        <iframe src="{{ route('document_analysis.pdf', ['documentAnalysis' => $documentAnalysis->slug, 'locale' => app()->getLocale()]) }}#toolbar=1&navpanes=0" 
                                class="w-full h-full border-none rounded-2xl"
                                title="Pratinjau Dokumen PDF">
                        </iframe>
                    </div>
                </div>
            </div>

            {{-- RIGHT PANE: Structured Analysis Results & Audio Player --}}
            <div class="lg:col-span-7 xl:col-span-7 flex flex-col gap-6"
                 :class="activeMobileTab === 'analysis' ? 'block' : 'hidden lg:flex'">

                {{-- 1. Modern Audio Briefing Player Bar --}}
                <div class="bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 text-white rounded-3xl p-5 shadow-xl border border-gray-800 relative overflow-hidden">
                    {{-- Radial glow --}}
                    <div class="absolute -right-20 -top-20 w-48 h-48 bg-bima-red/20 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            {{-- Play/Pause Button (Deterministic Lucide SVG Render) --}}
                            <button type="button" 
                                    @click="toggleAudio()"
                                    class="w-12 h-12 rounded-2xl bg-bima-red hover:bg-red-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-red-600/30 transition-transform active:scale-95 cursor-pointer"
                                    :title="isPlaying ? 'Jeda Audio' : 'Putar Audio Narasi'">
                                <span x-show="!isPlaying" class="flex items-center justify-center">
                                    <i data-lucide="play" class="w-5 h-5 fill-current ml-0.5"></i>
                                </span>
                                <span x-show="isPlaying" x-cloak class="flex items-center justify-center">
                                    <i data-lucide="pause" class="w-5 h-5 fill-current"></i>
                                </span>
                            </button>

                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[0.62rem] font-black uppercase tracking-widest text-red-400">Audio Briefing Eksekutif</span>
                                    <span x-show="isPlaying" x-cloak class="flex items-center gap-0.5">
                                        <span class="w-1 h-3 bg-red-500 rounded-full animate-bounce"></span>
                                        <span class="w-1 h-4 bg-red-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                                        <span class="w-1 h-2 bg-red-500 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                                    </span>
                                </div>
                                <h4 class="text-sm font-black text-white uppercase tracking-tight mt-0.5">
                                    Ulasan Suara AI &bull; BIMA Supervisory
                                </h4>
                                <p class="text-[0.68rem] text-gray-400 mt-0.5" x-text="audioTimeDisplay"></p>
                            </div>
                        </div>

                        {{-- Player Controls --}}
                        <div class="flex items-center gap-3 self-end sm:self-center">
                            {{-- Speed Pill --}}
                            <button type="button" 
                                    @click="cycleSpeed()"
                                    class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-[0.65rem] font-bold text-white uppercase tracking-wider transition cursor-pointer"
                                    title="Atur Kecepatan Putar">
                                <span x-text="playbackRate + 'x'"></span>
                            </button>

                            {{-- Web Speech fallback reading --}}
                            <button type="button" 
                                    @click="speakExecutiveSummary()"
                                    class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white transition cursor-pointer"
                                    title="Baca Ulang Ringkasan (Web Speech API)">
                                <i data-lucide="volume-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Scrubber Progress Bar --}}
                    <div class="relative z-10 mt-4">
                        <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden cursor-pointer" 
                             @click="seekAudio($event)">
                            <div class="bg-bima-red h-full rounded-full transition-all duration-100" 
                                 :style="'width: ' + progressPercent + '%'"></div>
                        </div>
                    </div>

                    {{-- Audio Element (Streaming via Dedicated Controller Endpoint) --}}
                    <audio x-ref="audioPlayer" 
                           src="{{ route('document_analysis.audio', ['documentAnalysis' => $documentAnalysis->slug, 'locale' => app()->getLocale()]) }}" 
                           @timeupdate="onTimeUpdate()" 
                           @ended="onEnded()" 
                           @loadedmetadata="onLoadedMetadata()"
                           x-on:error="onAudioError($event)"
                           preload="metadata">
                    </audio>
                </div>

                {{-- 2. Executive Summary Card --}}
                <div class="bg-white rounded-3xl border border-gray-100 p-6 lg:p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-red-50 text-bima-red flex items-center justify-center shrink-0">
                                <i data-lucide="file-text" class="w-4.5 h-4.5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black uppercase text-gray-900 tracking-tight">Ringkasan Eksekutif Materi</h3>
                                <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Sintesis Intisari Dokumen</p>
                            </div>
                        </div>

                        <button type="button" 
                                @click="speakText({{ json_encode($documentAnalysis->executive_summary) }})"
                                class="px-3 py-1.5 rounded-xl bg-gray-50 hover:bg-red-50 hover:text-bima-red text-gray-600 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                                title="Dengarkan Bagian Ini">
                            <i data-lucide="volume-2" class="w-3.5 h-3.5"></i>
                            <span class="text-[0.65rem] uppercase">Dengarkan</span>
                        </button>
                    </div>

                    <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed font-medium text-sm space-y-3">
                        {!! nl2br(e($documentAnalysis->executive_summary)) !!}
                    </div>
                </div>

                {{-- 3. Key Conclusions Card --}}
                <div class="bg-white rounded-3xl border border-gray-100 p-6 lg:p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                <i data-lucide="check-circle-2" class="w-4.5 h-4.5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black uppercase text-gray-900 tracking-tight">Kesimpulan Pokok</h3>
                                <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Poin Kunci Temuan Ilmiah</p>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-[0.65rem] font-black uppercase">
                            {{ count($documentAnalysis->key_conclusions ?? []) }} Poin
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse($documentAnalysis->key_conclusions ?? [] as $idx => $conclusion)
                            <div class="bg-gray-50/70 border border-gray-100 rounded-2xl p-4.5 transition hover:bg-gray-50 hover:border-amber-200">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <span class="w-6 h-6 rounded-lg bg-amber-500 text-white font-black text-xs flex items-center justify-center shrink-0 mt-0.5">
                                            {{ $idx + 1 }}
                                        </span>
                                        <div>
                                            <h4 class="text-xs font-black uppercase text-gray-900 tracking-tight">
                                                {{ $conclusion['title'] ?? 'Kesimpulan ' . ($idx + 1) }}
                                            </h4>
                                            <p class="text-xs text-gray-600 font-medium mt-1 leading-relaxed">
                                                {{ $conclusion['detail'] ?? '' }}
                                            </p>
                                        </div>
                                    </div>

                                    <button type="button" 
                                            @click="speakText({{ json_encode(($conclusion['title'] ?? '') . '. ' . ($conclusion['detail'] ?? '')) }})"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition shrink-0 cursor-pointer"
                                            title="Dengarkan kesimpulan ini">
                                        <i data-lucide="volume-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 italic">Tidak ada kesimpulan khusus yang diekstraksi.</p>
                        @endforelse
                    </div>
                </div>

                {{-- 4. Actionable Recommendations Card --}}
                <div class="bg-white rounded-3xl border border-gray-100 p-6 lg:p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <i data-lucide="target" class="w-4.5 h-4.5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black uppercase text-gray-900 tracking-tight">Rekomendasi Terarah &amp; Perbaikan</h3>
                                <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">Pedoman Tindak Lanjut Akademik</p>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-[0.65rem] font-black uppercase">
                            {{ count($documentAnalysis->recommendations ?? []) }} Rekomendasi
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse($documentAnalysis->recommendations ?? [] as $index => $rec)
                            @php
                                $urgency = strtolower($rec['urgency'] ?? 'sedang');
                                $badgeClass = match($urgency) {
                                    'tinggi' => 'bg-red-50 text-red-700 border-red-200',
                                    'sedang' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-blue-50 text-blue-700 border-blue-200',
                                };
                            @endphp
                            <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition space-y-3">
                                {{-- Header Rekomendasi --}}
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-[0.62rem] font-bold uppercase tracking-wider">
                                            {{ $rec['category'] ?? 'Akademik' }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md border text-[0.62rem] font-black uppercase tracking-wider {{ $badgeClass }}">
                                            Urgensi: {{ $rec['urgency'] ?? 'Sedang' }}
                                        </span>
                                    </div>

                                    <button type="button" 
                                            @click="speakText({{ json_encode('Temuan: ' . ($rec['finding'] ?? '') . '. Rekomendasi Aksi: ' . ($rec['action'] ?? '')) }})"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition shrink-0 cursor-pointer"
                                            title="Dengarkan butir rekomendasi ini">
                                        <i data-lucide="volume-2" class="w-4 h-4"></i>
                                    </button>
                                </div>

                                {{-- Temuan / Finding --}}
                                <div>
                                    <span class="text-[0.62rem] font-bold uppercase tracking-widest text-gray-400 block mb-0.5">Temuan / Celah:</span>
                                    <p class="text-xs font-semibold text-gray-800 leading-relaxed">
                                        {{ $rec['finding'] ?? '-' }}
                                    </p>
                                </div>

                                {{-- Action / Solusi Konkret --}}
                                <div class="bg-blue-50/50 border border-blue-100/60 rounded-xl p-3.5">
                                    <span class="text-[0.62rem] font-black uppercase tracking-widest text-blue-700 flex items-center gap-1 mb-1">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        Rekomendasi Aksi:
                                    </span>
                                    <p class="text-xs text-blue-900 font-medium leading-relaxed">
                                        {{ $rec['action'] ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 italic">Tidak ada butir rekomendasi.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        function documentResultApp() {
            return {
                activeMobileTab: 'analysis', // 'analysis' or 'pdf'
                isPlaying: false,
                playbackRate: 1.0,
                progressPercent: 0,
                audioTimeDisplay: '00:00 / 00:00',
                audioUrl: '{{ route("document_analysis.audio", ["documentAnalysis" => $documentAnalysis->slug, "locale" => app()->getLocale()]) }}',

                init() {
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });

                    // Pre-warm Web Speech API jika tersedia
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.onvoiceschanged = () => {
                            window.speechSynthesis.getVoices();
                        };
                    }
                },

                toggleAudio() {
                    const player = this.$refs.audioPlayer;
                    if (player) {
                        if (this.isPlaying) {
                            player.pause();
                            this.isPlaying = false;
                        } else {
                            player.playbackRate = this.playbackRate;
                            const playPromise = player.play();
                            if (playPromise !== undefined) {
                                playPromise.then(() => {
                                    this.isPlaying = true;
                                }).catch(err => {
                                    console.warn('Audio streaming playback error, fallback to Web Speech:', err);
                                    this.isPlaying = false;
                                    this.speakExecutiveSummary();
                                });
                            }
                        }
                    } else {
                        this.speakExecutiveSummary();
                    }
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                },

                onAudioError(event) {
                    console.warn('Audio stream error event:', event);
                },

                cycleSpeed() {
                    const speeds = [1.0, 1.25, 1.5, 2.0];
                    const nextIndex = (speeds.indexOf(this.playbackRate) + 1) % speeds.length;
                    this.playbackRate = speeds[nextIndex];
                    if (this.$refs.audioPlayer) {
                        this.$refs.audioPlayer.playbackRate = this.playbackRate;
                    }
                },

                onTimeUpdate() {
                    const player = this.$refs.audioPlayer;
                    if (!player || !player.duration || isNaN(player.duration)) return;
                    this.progressPercent = (player.currentTime / player.duration) * 100;
                    this.audioTimeDisplay = this.formatTime(player.currentTime) + ' / ' + this.formatTime(player.duration);
                },

                onEnded() {
                    this.isPlaying = false;
                    this.progressPercent = 0;
                    this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                },

                onLoadedMetadata() {
                    const player = this.$refs.audioPlayer;
                    if (player && player.duration && !isNaN(player.duration)) {
                        this.audioTimeDisplay = '00:00 / ' + this.formatTime(player.duration);
                    }
                },

                seekAudio(e) {
                    const player = this.$refs.audioPlayer;
                    if (!player || !player.duration || isNaN(player.duration)) return;
                    const rect = e.currentTarget.getBoundingClientRect();
                    const clickX = e.clientX - rect.left;
                    const percent = Math.max(0, Math.min(1, clickX / rect.width));
                    player.currentTime = percent * player.duration;
                },

                formatTime(seconds) {
                    if (isNaN(seconds) || seconds < 0) return '00:00';
                    const mins = Math.floor(seconds / 60);
                    const secs = Math.floor(seconds % 60);
                    return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                },

                speakText(text) {
                    if (!('speechSynthesis' in window)) {
                        alert('Browser Anda tidak mendukung Web Speech API.');
                        return;
                    }

                    // Hentikan player audio MP3 jika sedang aktif
                    const player = this.$refs.audioPlayer;
                    if (player && !player.paused) {
                        player.pause();
                        this.isPlaying = false;
                    }

                    window.speechSynthesis.cancel();

                    setTimeout(() => {
                        const utterance = new SpeechSynthesisUtterance(text);
                        const targetLang = '{{ $documentAnalysis->locale === "en" ? "en-US" : ($documentAnalysis->locale === "zh" ? "zh-CN" : "id-ID") }}';
                        utterance.lang = targetLang;
                        utterance.rate = this.playbackRate;

                        const voices = window.speechSynthesis.getVoices();
                        if (voices && voices.length > 0) {
                            const langPrefix = targetLang.substring(0, 2).toLowerCase();
                            const matchingVoice = voices.find(v => v.lang.toLowerCase().replace('_', '-').startsWith(langPrefix));
                            if (matchingVoice) {
                                utterance.voice = matchingVoice;
                            }
                        }

                        utterance.onerror = (e) => {
                            console.warn('Web Speech error:', e);
                        };

                        window.speechSynthesis.speak(utterance);
                    }, 50);
                },

                speakExecutiveSummary() {
                    const summary = {{ json_encode($documentAnalysis->executive_summary) }};
                    this.speakText(summary);
                }
            }
        }
    </script>
</x-layouts.app>
