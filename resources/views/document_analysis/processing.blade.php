<x-layouts.app title="Memproses Dokumen PDF">
    <div class="max-w-2xl mx-auto px-4 py-16 animate-fade-in" x-data="documentProcessingEngine()">

        <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 sm:p-12 shadow-xl shadow-gray-200/50 text-center relative overflow-hidden">
            
            {{-- Decorative Ambient Glow --}}
            <div class="absolute -top-20 -right-20 w-48 h-48 bg-red-50 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-48 h-48 bg-amber-50 rounded-full blur-3xl pointer-events-none"></div>

            {{-- Main Icon / Status Animation --}}
            <div class="relative z-10 mb-6">
                <template x-if="!isFailed">
                    <div class="w-20 h-20 rounded-3xl bg-red-50 text-bima-red mx-auto flex items-center justify-center shadow-lg shadow-red-500/10 relative">
                        <i data-lucide="sparkles" class="w-9 h-9 animate-pulse"></i>
                        <div class="absolute inset-0 rounded-3xl border-2 border-bima-red/20 border-t-bima-red animate-spin"></div>
                    </div>
                </template>
                <template x-if="isFailed">
                    <div class="w-20 h-20 rounded-3xl bg-red-100 text-red-600 mx-auto flex items-center justify-center shadow-lg shadow-red-500/20">
                        <i data-lucide="alert-triangle" class="w-9 h-9"></i>
                    </div>
                </template>
            </div>

            {{-- Title & Document Metadata --}}
            <div class="relative z-10 mb-8">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-gray-100 text-gray-600 text-[0.65rem] font-bold uppercase tracking-widest mb-3">
                    <i data-lucide="file-text" class="w-3 h-3"></i>
                    {{ $documentAnalysis->file_name }} &bull; {{ $documentAnalysis->formattedFileSize() }}
                </span>
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight" x-text="currentStatusTitle">
                    Memproses Analisis Dokumen...
                </h2>
                <p class="text-xs text-gray-400 font-medium mt-1.5 max-w-md mx-auto leading-relaxed" x-text="currentStatusDesc">
                    AI sedang mengekstrak teks, menelaah metodologi, dan merumuskan rekomendasi akademik.
                </p>
            </div>

            {{-- Progress Steps Card --}}
            <div class="relative z-10 bg-gray-50/70 border border-gray-100 rounded-2xl p-5 mb-8 text-left space-y-3.5">
                {{-- Step 1 --}}
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black shrink-0 transition-colors"
                         :class="step >= 1 ? (step > 1 ? 'bg-green-500 text-white' : 'bg-bima-red text-white') : 'bg-gray-200 text-gray-400'">
                        <template x-if="step > 1">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </template>
                        <template x-if="step <= 1">
                            <span>1</span>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-800" :class="step === 1 ? 'text-bima-red' : ''">Ekstraksi Halaman &amp; Teks PDF</p>
                        <p class="text-[0.65rem] text-gray-400" x-show="totalWords > 0" x-text="totalWords + ' kata terdeteksi'"></p>
                    </div>
                    <span x-show="step === 1" class="w-2 h-2 rounded-full bg-bima-red animate-ping"></span>
                </div>

                {{-- Step 2 --}}
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black shrink-0 transition-colors"
                         :class="step >= 2 ? (step > 2 ? 'bg-green-500 text-white' : 'bg-bima-red text-white') : 'bg-gray-200 text-gray-400'">
                        <template x-if="step > 2">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </template>
                        <template x-if="step <= 2">
                            <span>2</span>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-800" :class="step === 2 ? 'text-bima-red' : ''">Analisis Multi-Task OpenAI GPT-4o-mini</p>
                        <p class="text-[0.65rem] text-gray-400">Merumuskan ringkasan eksekutif, kesimpulan, dan poin rekomendasi</p>
                    </div>
                    <span x-show="step === 2" class="w-2 h-2 rounded-full bg-bima-red animate-ping"></span>
                </div>

                {{-- Step 3 --}}
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black shrink-0 transition-colors"
                         :class="step >= 3 ? (step > 3 ? 'bg-green-500 text-white' : 'bg-bima-red text-white') : 'bg-gray-200 text-gray-400'">
                        <template x-if="step > 3">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </template>
                        <template x-if="step <= 3">
                            <span>3</span>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-800" :class="step === 3 ? 'text-bima-red' : ''">Sintesis Narasi Audio (Dual-Engine TTS)</p>
                        <p class="text-[0.65rem] text-gray-400">Menghasilkan audio briefing eksekutif siap dengar</p>
                    </div>
                    <span x-show="step === 3" class="w-2 h-2 rounded-full bg-bima-red animate-ping"></span>
                </div>
            </div>

            {{-- Error State Actions --}}
            <div x-show="isFailed" x-cloak class="relative z-10 bg-red-50 border border-red-200 rounded-2xl p-5 mb-6 text-left">
                <div class="flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0 mt-0.5"></i>
                    <div>
                        <h4 class="text-xs font-black uppercase text-red-700">Analisis Gagal Diproses</h4>
                        <p class="text-xs text-red-600 mt-1 leading-relaxed" x-text="errorMessage || 'Terjadi kendala saat membaca berkas PDF atau menghubungi server AI.'"></p>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <button type="button" @click="startExecution()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-md">
                        Coba Lagi
                    </button>
                    <a href="{{ route('document_analysis.create', ['locale' => app()->getLocale()]) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl text-xs font-bold uppercase tracking-wider transition">
                        Unggah Dokumen Lain
                    </a>
                </div>
            </div>

        </div>

    </div>

    <script>
        function documentProcessingEngine() {
            return {
                step: 1,
                isFailed: false,
                errorMessage: '',
                totalWords: {{ $documentAnalysis->word_count ?? 0 }},
                currentStatusTitle: 'Mengekstrak Teks Dokumen...',
                currentStatusDesc: 'Sistem sedang membaca isi halaman naskah PDF...',
                slug: '{{ $documentAnalysis->slug }}',

                init() {
                    this.startExecution();
                },

                async startExecution() {
                    this.isFailed = false;
                    this.errorMessage = '';
                    this.step = 1;
                    this.currentStatusTitle = 'Mengekstrak Teks Dokumen...';
                    this.currentStatusDesc = 'Sistem sedang membaca isi halaman naskah PDF...';

                    // Simulasi visual step progression saat async call berjalan
                    const stepTimer = setTimeout(() => {
                        if (this.step < 2 && !this.isFailed) {
                            this.step = 2;
                            this.currentStatusTitle = 'Analisis Naskah Berjalan...';
                            this.currentStatusDesc = 'OpenAI GPT-4o-mini sedang menelaah substansi dokumen & merumuskan rekomendasi...';
                        }
                    }, 4000);

                    const stepTimer2 = setTimeout(() => {
                        if (this.step < 3 && !this.isFailed) {
                            this.step = 3;
                            this.currentStatusTitle = 'Menyiapkan Panduan Audio...';
                            this.currentStatusDesc = 'OpenAI TTS sedang memproses narasi briefing eksekutif suara...';
                        }
                    }, 14000);

                    try {
                        const response = await fetch(`/{{ app()->getLocale() }}/document-analysis/${this.slug}/execute`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        clearTimeout(stepTimer);
                        clearTimeout(stepTimer2);

                        if (response.ok && data.success) {
                            this.step = 4;
                            this.currentStatusTitle = 'Analisis Selesai!';
                            this.currentStatusDesc = 'Mengarahkan ke halaman hasil telaah dokumen...';
                            setTimeout(() => {
                                window.location.href = data.redirect_url;
                            }, 1000);
                        } else {
                            this.isFailed = true;
                            this.errorMessage = data.error || 'Terjadi kesalahan sistem saat memproses analisis.';
                            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                        }
                    } catch (err) {
                        clearTimeout(stepTimer);
                        clearTimeout(stepTimer2);
                        this.isFailed = true;
                        this.errorMessage = err.message || 'Gagal menghubungi server.';
                        this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                    }
                }
            }
        }
    </script>
</x-layouts.app>
