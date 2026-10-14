<x-layouts.app title="Memproses Dokumen PDF">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 animate-fade-in" x-data="documentProcessingEngine()">
        
        {{-- Header Section (Consistent with create.blade.php) --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-50 text-bima-red border border-red-100 text-[0.65rem] font-black uppercase tracking-widest">
                    <i data-lucide="sparkles" class="w-3 h-3"></i>
                    OpenAI GPT-4o-mini &bull; Pipeline Analisis
                </span>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-[0.65rem] font-bold uppercase tracking-wider">
                    <i data-lucide="file-text" class="w-3 h-3"></i>
                    {{ $documentAnalysis->file_name }} &bull; {{ $documentAnalysis->formattedFileSize() }}
                </span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">
                <span class="lang-id">Memproses Analisis Dokumen</span>
                <span class="lang-en">Processing Document Analysis</span>
                <span class="lang-zh">正在处理文档智能分析</span>
            </h1>
            <p class="text-xs text-gray-500 font-medium mt-1 leading-relaxed">
                <span class="lang-id">Sistem sedang membaca naskah PDF, mengekstrak struktur teks, dan merumuskan analisis komprehensif.</span>
                <span class="lang-en">The system is parsing the PDF document, extracting text structures, and synthesizing academic insights.</span>
                <span class="lang-zh">系统正在解析 PDF 文档结构，提取正文内容并生成综合性学术分析。</span>
            </p>
        </div>

        {{-- Processing Status Card (Consistent width & padding with create.blade.php) --}}
        <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 lg:p-10 shadow-xl shadow-gray-200/40 relative overflow-hidden">
            
            {{-- Status Banner --}}
            <div class="flex flex-col items-center justify-center text-center py-4 mb-6">
                {{-- Clean Status Icon --}}
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-bima-red flex items-center justify-center mb-4 border border-red-100 shadow-sm">
                    <span x-show="!isFailed" class="flex items-center justify-center">
                        <i data-lucide="file-search" class="w-8 h-8 text-bima-red"></i>
                    </span>
                    <span x-show="isFailed" x-cloak class="flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600"></i>
                    </span>
                </div>

                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight" x-text="currentStatusTitle">
                    Mengekstrak Teks Dokumen...
                </h2>
                <p class="text-xs text-gray-500 font-medium mt-1.5 max-w-lg mx-auto leading-relaxed" x-text="currentStatusDesc">
                    Sistem sedang membaca isi halaman naskah PDF...
                </p>

                {{-- Clean Minimalist Progress Bar --}}
                <div class="w-full max-w-lg bg-gray-100 h-2 rounded-full overflow-hidden mt-6">
                    <div class="bg-bima-red h-full rounded-full transition-all duration-700 ease-out"
                         :style="'width: ' + (step === 1 ? '33%' : (step === 2 ? '66%' : (step === 3 ? '90%' : '100%')))">
                    </div>
                </div>
            </div>

            {{-- Progress Steps Card --}}
            <div class="bg-gray-50/80 border border-gray-100 rounded-2xl p-6 text-left space-y-4 max-w-2xl mx-auto mb-4">
                {{-- Step 1 --}}
                <div class="flex items-center gap-4">
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 transition-colors"
                         :class="step > 1 ? 'bg-green-600 text-white' : (step === 1 ? 'bg-bima-red text-white' : 'bg-gray-200 text-gray-500')">
                        <span x-show="step <= 1">1</span>
                        <span x-show="step > 1" x-cloak class="flex items-center justify-center">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-800" :class="step === 1 ? 'text-bima-red' : ''">Ekstraksi Halaman &amp; Teks PDF</p>
                        <p class="text-[0.68rem] text-gray-400 mt-0.5" x-show="totalWords > 0" x-text="totalWords + ' kata terdeteksi'"></p>
                    </div>
                    <span class="text-[0.65rem] font-bold uppercase tracking-wider shrink-0"
                          :class="step > 1 ? 'text-green-600' : (step === 1 ? 'text-bima-red' : 'text-gray-400')"
                          x-text="step > 1 ? 'Selesai' : (step === 1 ? 'Sedang Berjalan' : 'Menunggu')">
                    </span>
                </div>

                {{-- Step 2 --}}
                <div class="flex items-center gap-4">
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 transition-colors"
                         :class="step > 2 ? 'bg-green-600 text-white' : (step === 2 ? 'bg-bima-red text-white' : 'bg-gray-200 text-gray-500')">
                        <span x-show="step <= 2">2</span>
                        <span x-show="step > 2" x-cloak class="flex items-center justify-center">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-800" :class="step === 2 ? 'text-bima-red' : ''">Analisis Multi-Task OpenAI GPT-4o-mini</p>
                        <p class="text-[0.68rem] text-gray-400 mt-0.5">Merumuskan ringkasan eksekutif, kesimpulan, dan poin rekomendasi</p>
                    </div>
                    <span class="text-[0.65rem] font-bold uppercase tracking-wider shrink-0"
                          :class="step > 2 ? 'text-green-600' : (step === 2 ? 'text-bima-red' : 'text-gray-400')"
                          x-text="step > 2 ? 'Selesai' : (step === 2 ? 'Sedang Berjalan' : 'Menunggu')">
                    </span>
                </div>

                {{-- Step 3 --}}
                <div class="flex items-center gap-4">
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 transition-colors"
                         :class="step > 3 ? 'bg-green-600 text-white' : (step === 3 ? 'bg-bima-red text-white' : 'bg-gray-200 text-gray-500')">
                        <span x-show="step <= 3">3</span>
                        <span x-show="step > 3" x-cloak class="flex items-center justify-center">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-800" :class="step === 3 ? 'text-bima-red' : ''">Sintesis Narasi Audio (Dual-Engine TTS)</p>
                        <p class="text-[0.68rem] text-gray-400 mt-0.5">Menghasilkan audio briefing eksekutif siap dengar</p>
                    </div>
                    <span class="text-[0.65rem] font-bold uppercase tracking-wider shrink-0"
                          :class="step > 3 ? 'text-green-600' : (step === 3 ? 'text-bima-red' : 'text-gray-400')"
                          x-text="step > 3 ? 'Selesai' : (step === 3 ? 'Sedang Berjalan' : 'Menunggu')">
                    </span>
                </div>
            </div>

            {{-- Error State Actions --}}
            <div x-show="isFailed" x-cloak class="relative z-10 bg-red-50 border border-red-200 rounded-2xl p-5 mb-6 text-left max-w-2xl mx-auto">
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
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                    this.startExecution();
                },

                async startExecution() {
                    this.isFailed = false;
                    this.errorMessage = '';
                    this.step = 1;
                    this.currentStatusTitle = 'Mengekstrak Teks Dokumen...';
                    this.currentStatusDesc = 'Sistem sedang membaca isi halaman naskah PDF...';

                    // Visual step progression saat asynchronous execution berlangsung
                    const stepTimer = setTimeout(() => {
                        if (this.step < 2 && !this.isFailed) {
                            this.step = 2;
                            this.currentStatusTitle = 'Analisis Naskah Berjalan...';
                            this.currentStatusDesc = 'OpenAI GPT-4o-mini sedang menelaah substansi dokumen & merumuskan rekomendasi...';
                            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                        }
                    }, 4000);

                    const stepTimer2 = setTimeout(() => {
                        if (this.step < 3 && !this.isFailed) {
                            this.step = 3;
                            this.currentStatusTitle = 'Menyiapkan Panduan Audio...';
                            this.currentStatusDesc = 'OpenAI TTS sedang memproses narasi briefing eksekutif suara...';
                            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
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
                            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
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
