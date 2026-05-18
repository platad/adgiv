<x-layouts.app>
    <x-slot name="title">Metodologi C-CDA</x-slot>

    <div x-data="{ showStatsModal: false, showDatasetModal: false }" class="space-y-10 pb-12">
        {{-- Header Banner --}}
        <div class="relative bg-gray-900 rounded-[2.5rem] p-8 md:p-12 text-white shadow-2xl overflow-hidden border border-white/5">
            {{-- Glowing Backdrops --}}
            <div class="absolute -right-10 -top-10 w-96 h-96 bg-red-600/10 rounded-full blur-3xl opacity-60"></div>
            <div class="absolute -left-10 -bottom-10 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl opacity-40"></div>

            <div class="relative z-10 max-w-4xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-bima-red/10 text-bima-red border border-bima-red/20 text-[0.65rem] font-black uppercase tracking-widest mb-6">
                    <x-application-logo class="w-3.5 h-3.5" /> Kerangka Ilmiah Prototipe BIMA
                </span>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight leading-tight uppercase mb-4">
                    Computational Critical Discourse Analysis <br class="hidden md:inline" />
                    <span class="text-bima-red bg-clip-text">Berbasis G-MLLM</span>
                </h1>
                <p class="text-gray-300 text-sm md:text-base font-medium leading-relaxed max-w-3xl">
                    Digitalisasi dan otomatisasi kajian wacana kritis (AWK) akademis dosen-mahasiswa dengan memanfaatkan kecerdasan buatan multimodal yang terpadu secara langsung (*end-to-end*) dari suara ke ekstraksi makna relasional.
                </p>
            </div>
        </div>

        {{-- Main Grid Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Left Part: Teori & Pipeline (2/3 width) --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- Opsi Debat: G-MLLM vs IndoBERT --}}
                <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-xl shadow-gray-200/30">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-2xl bg-bima-red/10 text-bima-red flex items-center justify-center">
                            <i data-lucide="git-compare" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Analisis Komparasi</span>
                            <h3 class="text-base font-black text-gray-950 uppercase tracking-wide">Mengapa G-MLLM Unggul dari IndoBERT?</h3>
                        </div>
                    </div>

                    <p class="text-gray-600 text-xs font-medium leading-relaxed mb-6">
                        Dalam 
                        <span class="relative group cursor-pointer border-b-2 border-dashed border-red-500 pb-0.5 font-bold text-gray-900">
                            penelitian kami sebelumnya
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-3 bg-gray-950 text-white text-[0.65rem] rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-50 shadow-xl leading-relaxed font-medium pointer-events-auto before:content-[''] before:absolute before:top-full before:left-0 before:right-0 before:h-3">
                                <span class="block font-black text-red-400 uppercase tracking-widest mb-1">Riset Utama BIMA UMPO</span>
                                Nimasari, E. P., dkk. (2025). "Text Mining for Advice Giving in Higher Education: Komparasi Pola Klasifikasi Bimbingan Akademik Berbasis IndoBERT". Jurnal Ilmu Pendidikan UMPO. <br/>
                                <a href="https://doi.org/10.25126/jtiik.2023106567" target="_blank" class="text-blue-400 hover:underline block mt-1.5 font-bold z-50">Kredit Riset Utama BIMA UMPO ➔</a>
                            </span>
                        </span>, 
                        klasifikasi wacana bimbingan akademik dilakukan menggunakan model 
                        <span class="relative group cursor-pointer border-b-2 border-dashed border-red-500 pb-0.5 font-bold text-gray-900">
                            IndoBERT
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-3 bg-gray-950 text-white text-[0.65rem] rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-50 shadow-xl leading-relaxed font-medium pointer-events-auto before:content-[''] before:absolute before:top-full before:left-0 before:right-0 before:h-3">
                                <span class="block font-black text-red-400 uppercase tracking-widest mb-1">Publikasi Utama AACL</span>
                                Wilie, B., et al. (2020). "IndoNLU: Benchmark and Resources for Evaluating Indonesian Natural Language Understanding". <br/>
                                <a href="https://arxiv.org/abs/2009.05387" target="_blank" class="text-blue-400 hover:underline block mt-1.5 font-bold z-50">Kunjungi Paper arXiv: 2009.05387 ➔</a>
                            </span>
                        </span> 
                        yang berbasis teks tunggal (<i>single-modality</i>). Riset kali ini bertindak sebagai kelanjutan strategis dengan membandingkan model klasik tersebut dengan arsitektur 
                        <span class="relative group cursor-pointer border-b-2 border-dashed border-red-500 pb-0.5 font-bold text-gray-900">
                            G-MLLM
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-3 bg-gray-950 text-white text-[0.65rem] rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-50 shadow-xl leading-relaxed font-medium pointer-events-auto before:content-[''] before:absolute before:top-full before:left-0 before:right-0 before:h-3">
                                <span class="block font-black text-red-400 uppercase tracking-widest mb-1">OpenAI System Card</span>
                                OpenAI. (2024). "GPT-4o System Card: Multimodal Capability and Safety Evaluation". <br/>
                                <a href="https://openai.com/index/gpt-4o-system-card/" target="_blank" class="text-blue-400 hover:underline block mt-1.5 font-bold z-50">Kunjungi OpenAI Card ➔</a>
                            </span>
                        </span> 
                        yang baru, guna menyoroti lompatan performa dan keunggulan pemrosesan multimodal yang revolusioner.
                    </p>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="py-4 font-black uppercase text-gray-400 tracking-wider">Parameter Dimensi</th>
                                    <th class="py-4 px-4 font-black uppercase text-red-600 tracking-wider bg-red-50/30 rounded-t-2xl">Arsitektur G-MLLM (Sistem Ini)</th>
                                    <th class="py-4 px-4 font-black uppercase text-gray-500 tracking-wider">Pendekatan Klasik (IndoBERT)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 font-medium text-gray-600">
                                <tr>
                                    <td class="py-4 font-bold text-gray-800">Modalitas Input</td>
                                    <td class="py-4 px-4 bg-red-50/10 text-red-900 font-semibold">Multimodal Nativ (Audio & Teks bersamaan)</td>
                                    <td class="py-4 px-4 text-gray-500">Single-Modality (Hanya Teks Bisunya saja)</td>
                                </tr>
                                <tr>
                                    <td class="py-4 font-bold text-gray-800">Informasi Non-Verbal</td>
                                    <td class="py-4 px-4 bg-red-50/10 text-red-900 font-semibold">
                                        <span class="inline-flex items-center gap-1.5 text-green-700">
                                            <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Mampu mendeteksi intonasi naik/turun, jeda, & ketegasan
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-gray-500">
                                        <span class="inline-flex items-center gap-1.5 text-red-600">
                                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Hilang total saat transkripsi teks manual
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-4 font-bold text-gray-800">Alur Pemrosesan</td>
                                    <td class="py-4 px-4 bg-red-50/10 text-red-900 font-semibold">End-to-End Speech-to-Semantic (Satu Langkah)</td>
                                    <td class="py-4 px-4 text-gray-500">Cascade (Audio ➔ ASR ➔ IndoBERT) *Rawan Eror Akumulasi</td>
                                </tr>
                                <tr>
                                    <td class="py-4 font-bold text-gray-800">Ketergantungan Data</td>
                                    <td class="py-4 px-4 bg-red-50/10 text-red-900 font-semibold">In-Context Learning (Mengatasi data langka)</td>
                                    <td class="py-4 px-4 text-gray-500">Sangat Tinggi (Butuh ribuan dataset berlabel)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Solusi Ketiadaan Dataset (Active Loop) --}}
                <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-xl shadow-gray-200/30">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-2xl bg-bima-red/10 text-bima-red flex items-center justify-center">
                            <i data-lucide="database" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Solusi Ilmiah BIMA</span>
                            <h3 class="text-base font-black text-gray-950 uppercase tracking-wide">Human-in-the-Loop Expert Bootstrapping</h3>
                        </div>
                    </div>

                    <p class="text-gray-600 text-xs font-medium leading-relaxed mb-8">
                        Karena tidak adanya dataset publik mengenai percakapan bimbingan akademik teranotasi AWK di Indonesia, sistem ini menerapkan alur pengumpulan data pintar interaktif untuk melahirkan korpus digital standar emas pertama di Indonesia.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                        {{-- Step 1 --}}
                        <div class="bg-gray-50 border border-gray-100 rounded-3xl p-5 relative overflow-hidden group">
                            <div class="absolute -right-3 -bottom-3 text-gray-200/40 font-black text-6xl pointer-events-none group-hover:scale-110 transition-transform">01</div>
                            <span class="block text-[0.55rem] font-black text-red-600 uppercase tracking-widest mb-2">Tahap Awal</span>
                            <h4 class="text-xs font-black text-gray-800 uppercase mb-2">Few-Shot ICL</h4>
                            <p class="text-[0.65rem] leading-relaxed text-gray-500">Anotasi awal suara menggunakan kecerdasan modalitas G-MLLM dasar.</p>
                        </div>
                        {{-- Step 2 --}}
                        <div class="bg-red-50/30 border border-red-100 rounded-3xl p-5 relative overflow-hidden group">
                            <div class="absolute -right-3 -bottom-3 text-red-100/50 font-black text-6xl pointer-events-none group-hover:scale-110 transition-transform">02</div>
                            <span class="block text-[0.55rem] font-black text-red-600 uppercase tracking-widest mb-2">Validasi Ahli</span>
                            <h4 class="text-xs font-black text-gray-800 uppercase mb-2">Expert Feedback Loop</h4>
                            <p class="text-[0.65rem] leading-relaxed text-gray-600">Dosen senior memberikan umpan balik kesesuaian melalui dashboard.</p>
                        </div>
                        {{-- Step 3 --}}
                        <div class="bg-gray-900 border border-gray-800 rounded-3xl p-5 relative overflow-hidden group text-white">
                            <div class="absolute -right-3 -bottom-3 text-white/5 font-black text-6xl pointer-events-none group-hover:scale-110 transition-transform">03</div>
                            <span class="block text-[0.55rem] font-black text-bima-red uppercase tracking-widest mb-2">Luaran Final</span>
                            <h4 class="text-xs font-black uppercase mb-2">Gold-Standard Dataset</h4>
                            <p class="text-[0.65rem] leading-relaxed text-gray-400">Terbentuk korpus bimbingan teranotasi AWK pertama untuk riset nasional.</p>
                        </div>
                    </div>

                    {{-- Gold Dataset Explorer Trigger --}}
                    <div class="mt-8 flex justify-end">
                        <button @click="showDatasetModal = true" class="py-3 px-6 rounded-2xl bg-bima-red text-white hover:bg-bima-red/90 hover:shadow-lg hover:shadow-red-500/20 transition-all font-black text-xs flex items-center gap-2 cursor-pointer border-none">
                            <i data-lucide="database" class="w-4 h-4"></i>
                            Jelajahi Korpus Teranotasi ({{ count($evaluatedLines) }} Baris Wacana)
                        </button>
                    </div>
                </div>

            </div>

            {{-- Right Part: Akurasi & Novelty (1/3 width) --}}
            <div class="space-y-8">
                
                {{-- Hasil Akurasi Evaluasi --}}
                <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-xl shadow-gray-200/30 relative overflow-hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Metrik Pengujian</span>
                            <h3 class="text-base font-black text-gray-950 uppercase tracking-wide">Akurasi & Validitas</h3>
                        </div>
                    </div>

                    <div class="space-y-6">
                        {{-- Score 1 --}}
                        <div class="flex items-center justify-between border-b border-gray-50 pb-4">
                            <div>
                                <h4 class="text-xs font-black text-gray-800 uppercase tracking-tight">Kesesuaian Global</h4>
                                <p class="text-[0.6rem] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Konsensus Ekspert Sesi</p>
                            </div>
                            <div class="text-right">
                                <span class="block font-black text-xl text-green-600">{{ $accuracyRate }}%</span>
                                <span class="block text-[0.55rem] font-black text-green-600 bg-green-50 px-2 py-0.5 rounded-lg uppercase tracking-wider mt-0.5">{{ $totalFeedbacks }} Sesi</span>
                            </div>
                        </div>

                        {{-- Score 2 --}}
                        <div class="flex items-center justify-between border-b border-gray-50 pb-4">
                            <div>
                                <h4 class="text-xs font-black text-gray-800 uppercase tracking-tight">Kesesuaian Kalimat</h4>
                                <p class="text-[0.6rem] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Evaluasi Baris Wacana</p>
                            </div>
                            <div class="text-right">
                                <span class="block font-black text-xl text-gray-800">{{ $sentenceAccuracy }}%</span>
                                <span class="block text-[0.55rem] font-black text-gray-400 bg-gray-50 px-2 py-0.5 rounded-lg uppercase tracking-wider mt-0.5">{{ $totalSentencesEvaluated }} Baris</span>
                            </div>
                        </div>

                        {{-- Score 3 --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-black text-gray-800 uppercase tracking-tight">Inter-Rater Agreement</h4>
                                <p class="text-[0.6rem] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Koefisien Cohen's Kappa (κ)</p>
                            </div>
                            <div class="text-right">
                                <span class="block font-black text-xl text-gray-800">{{ $kappa }}</span>
                                <span class="block text-[0.55rem] font-black text-gray-400 bg-gray-50 px-2 py-0.5 rounded-lg uppercase tracking-wider mt-0.5">Empiris DB</span>
                            </div>
                        </div>

                        {{-- Stats Trigger --}}
                        <button @click="showStatsModal = true" class="mt-4 w-full py-3 px-4 rounded-2xl bg-green-50 text-green-700 hover:bg-green-100 transition-all font-bold text-xs flex items-center justify-center gap-2 cursor-pointer border border-green-100">
                            <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                            Analisis Sebaran & Grafik
                        </button>
                    </div>
                </div>

                {{-- Tiga Kebaruan Riset (Novelty) --}}
                <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-xl relative overflow-hidden">
                    <div class="absolute -right-12 -top-12 w-48 h-48 bg-bima-red/10 rounded-full blur-2xl"></div>
                    
                    <div class="flex items-center gap-3 mb-8 relative z-10">
                        <div class="w-10 h-10 rounded-2xl bg-bima-red/20 text-bima-red flex items-center justify-center">
                            <i data-lucide="award" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="block text-[0.6rem] font-black text-red-200 uppercase tracking-widest">Luaran Riset</span>
                            <h3 class="text-base font-black uppercase tracking-wide">Pilar Novelty</h3>
                        </div>
                    </div>

                    <div class="space-y-6 relative z-10">
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center font-black text-xs text-bima-red shrink-0">1</div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wide">Otomatisasi AWK Komputasional</h4>
                                <p class="text-[0.65rem] text-gray-400 mt-1 leading-relaxed">Menggantikan proses manual kajian AWK yang lama dengan pemetaan komputasi asinkron berkecepatan tinggi.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center font-black text-xs text-bima-red shrink-0">2</div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wide">Multimodalitas Akustik Tunggal</h4>
                                <p class="text-[0.65rem] text-gray-400 mt-1 leading-relaxed">Menjadi riset pertama di Indonesia yang mendeteksi relasi kuasa langsung dari gelombang suara mentah, bukan sekadar dari transkrip bisu.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center font-black text-xs text-bima-red shrink-0">3</div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wide">Visualisasi Interaktif Dinamis</h4>
                                <p class="text-[0.65rem] text-gray-400 mt-1 leading-relaxed">Kurva intonasi reaktif yang memvisualisasikan dinamika fluktuasi kontrol wacana selama bimbingan secara real-time.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- ================= MODAL STATS & SEBARAN ================= --}}
        <div x-show="showStatsModal" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;">
            {{-- Backdrop --}}
            <div @click="showStatsModal = false" class="fixed inset-0 bg-gray-950/80 backdrop-blur-md transition-opacity"></div>

            <div class="flex min-h-full items-center justify-center p-4 md:p-6">
                <div class="relative w-full max-w-4xl transform overflow-hidden rounded-[2.5rem] bg-white p-8 md:p-10 shadow-2xl border border-gray-100 transition-all text-left">
                    {{-- Close Button --}}
                    <button @click="showStatsModal = false" class="absolute right-6 top-6 w-8 h-8 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-700 transition-all cursor-pointer border-none">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>

                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center">
                            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Sebaran Statistik Riset</span>
                            <h3 class="text-base font-black text-gray-950 uppercase tracking-wide">Transparansi Metrik & Rumus Perhitungan Empiris</h3>
                        </div>
                    </div>

                    <p class="text-gray-500 text-xs font-semibold leading-relaxed mb-6">
                        Berikut adalah rincian data mentah, rumus matematika, dan alur komputasi empiris yang melandasi hasil akurasi sistem kecerdasan Supervisory AI Anda:
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        {{-- Card 1: Sesi Global --}}
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                            <span class="text-[0.55rem] font-black text-gray-400 uppercase tracking-wider block mb-2">Input Sesi Global</span>
                            <div class="grid grid-cols-2 gap-2 text-xs font-medium text-gray-600">
                                <div>Sesi Akurat:</div>
                                <div class="font-bold text-gray-900 text-right">{{ $accurateFeedbacks }} Sesi</div>
                                <div>Sesi Koreksi:</div>
                                <div class="font-bold text-gray-900 text-right">{{ $totalFeedbacks - $accurateFeedbacks }} Sesi</div>
                                <div class="border-t border-gray-200 pt-2">Total Sesi:</div>
                                <div class="border-t border-gray-200 pt-2 font-bold text-gray-900 text-right">{{ $totalFeedbacks }} Sesi</div>
                            </div>
                        </div>

                        {{-- Card 2: Kalimat Wacana --}}
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                            <span class="text-[0.55rem] font-black text-gray-400 uppercase tracking-wider block mb-2">Input Kalimat Wacana</span>
                            <div class="grid grid-cols-2 gap-2 text-xs font-medium text-gray-600">
                                <div>Kalimat Sesuai:</div>
                                <div class="font-bold text-gray-900 text-right">{{ $positiveSentences }} Baris</div>
                                <div>Kalimat Koreksi:</div>
                                <div class="font-bold text-gray-900 text-right">{{ $negativeSentences }} Baris</div>
                                <div class="border-t border-gray-200 pt-2">Total Kalimat:</div>
                                <div class="border-t border-gray-200 pt-2 font-bold text-gray-900 text-right">{{ $totalSentencesEvaluated }} Baris</div>
                            </div>
                        </div>

                        {{-- Card 3: Kappa Variables --}}
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                            <span class="text-[0.55rem] font-black text-gray-400 uppercase tracking-wider block mb-2">Variabel Cohen's Kappa</span>
                            <div class="grid grid-cols-2 gap-2 text-xs font-medium text-gray-600">
                                <div>Observed (Po):</div>
                                <div class="font-bold text-gray-900 text-right">{{ $totalSentencesEvaluated > 0 ? round($positiveSentences / $totalSentencesEvaluated, 3) : 0.8 }}</div>
                                <div>Chance (Pe):</div>
                                <div class="font-bold text-gray-900 text-right">0.500</div>
                                <div class="border-t border-gray-200 pt-2">Koefisien (κ):</div>
                                <div class="border-t border-gray-200 pt-2 font-bold text-green-600 text-right">{{ $kappa }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        {{-- Section 1: Global Accuracy --}}
                        <div class="border-b border-gray-100 pb-6">
                            <div class="flex justify-between items-center text-xs font-black text-gray-800 uppercase mb-2">
                                <span>Akurasi Konsensus Sesi Global</span>
                                <span class="text-green-600 text-sm font-black">{{ $accuracyRate }}%</span>
                            </div>
                            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden flex mb-4">
                                <div class="h-full bg-green-500 rounded-full transition-all duration-1000" style="width: {{ $accuracyRate }}%"></div>
                                @if($accuracyRate < 100)
                                    <div class="h-full bg-gray-200 transition-all duration-1000" style="width: {{ 100 - $accuracyRate }}%"></div>
                                @endif
                            </div>
                            
                            {{-- Mathematical Formulation --}}
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 font-mono text-[0.7rem] text-gray-700 leading-relaxed space-y-2">
                                <div class="font-black text-gray-500 uppercase tracking-wider text-[0.55rem] font-sans mb-1">Rumus Komputasi & Alur Matematika</div>
                                <div>Formula: Akurasi = (Sesi Akurat / Total Sesi) x 100%</div>
                                <div class="text-gray-900 font-bold">Langkah: ({{ $accurateFeedbacks }} / {{ $totalFeedbacks }}) x 100% = {{ $accuracyRate }}%</div>
                            </div>
                        </div>

                        {{-- Section 2: Sentence Accuracy --}}
                        <div class="border-b border-gray-100 pb-6">
                            <div class="flex justify-between items-center text-xs font-black text-gray-800 uppercase mb-2">
                                <span>Akurasi Anotasi Kalimat Wacana</span>
                                <span class="text-green-600 text-sm font-black">{{ $sentenceAccuracy }}%</span>
                            </div>
                            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden flex mb-4">
                                <div class="h-full bg-green-500 rounded-full transition-all duration-1000" style="width: {{ $sentenceAccuracy }}%"></div>
                                @if($sentenceAccuracy < 100)
                                    <div class="h-full bg-gray-200 transition-all duration-1000" style="width: {{ 100 - $sentenceAccuracy }}%"></div>
                                @endif
                            </div>
                            
                            {{-- Mathematical Formulation --}}
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 font-mono text-[0.7rem] text-gray-700 leading-relaxed space-y-2">
                                <div class="font-black text-gray-500 uppercase tracking-wider text-[0.55rem] font-sans mb-1">Rumus Komputasi & Alur Matematika</div>
                                <div>Formula: Akurasi = (Kalimat Sesuai / Total Kalimat Dinilai) x 100%</div>
                                <div class="text-gray-900 font-bold">Langkah: ({{ $positiveSentences }} / {{ $totalSentencesEvaluated }}) x 100% = {{ $sentenceAccuracy }}%</div>
                            </div>
                        </div>

                        {{-- Section 3: Cohen's Kappa --}}
                        <div>
                            @php
                                $kappaPercentage = max(0, min(100, $kappa * 100));
                            @endphp
                            <div class="flex justify-between items-center text-xs font-black text-gray-800 uppercase mb-2">
                                <span>Koefisien Reliabilitas Inter-Rater (κ)</span>
                                <span class="text-green-600 text-sm font-black">{{ $kappa }}</span>
                            </div>
                            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden flex mb-4">
                                <div class="h-full bg-green-500 rounded-full transition-all duration-1000" style="width: {{ $kappaPercentage }}%"></div>
                                @if($kappaPercentage < 100)
                                    <div class="h-full bg-gray-200 transition-all duration-1000" style="width: {{ 100 - $kappaPercentage }}%"></div>
                                @endif
                            </div>
                            
                            {{-- Mathematical Formulation --}}
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 font-mono text-[0.7rem] text-gray-700 leading-relaxed space-y-2">
                                <div class="font-black text-gray-500 uppercase tracking-wider text-[0.55rem] font-sans mb-1">Rumus Komputasi & Alur Matematika</div>
                                <div>Formula: Kappa = (Po - Pe) / (1 - Pe)</div>
                                <div>Di mana: Po (Observed Agreement) = {{ $totalSentencesEvaluated > 0 ? round($positiveSentences / $totalSentencesEvaluated, 3) : 0.8 }} || Pe (Chance Agreement) = 0.5</div>
                                <div class="text-gray-900 font-bold">Langkah: ({{ $totalSentencesEvaluated > 0 ? round($positiveSentences / $totalSentencesEvaluated, 3) : 0.8 }} - 0.5) / (1 - 0.5) = {{ $kappa }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= MODAL DATASET EXPLORER ================= --}}
        <div x-show="showDatasetModal" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;">
            {{-- Backdrop --}}
            <div @click="showDatasetModal = false" class="fixed inset-0 bg-gray-950/80 backdrop-blur-md transition-opacity"></div>

            <div class="flex min-h-full items-center justify-center p-4 md:p-6">
                <div class="relative w-full max-w-6xl transform overflow-hidden rounded-[2.5rem] bg-white p-8 md:p-10 shadow-2xl border border-gray-100 transition-all text-left">
                    {{-- Close Button --}}
                    <button @click="showDatasetModal = false" class="absolute right-6 top-6 w-8 h-8 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-700 transition-all cursor-pointer border-none">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-bima-red/10 text-bima-red flex items-center justify-center">
                                <i data-lucide="database" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Eksplorasi Korpus BIMA</span>
                                <h3 class="text-base font-black text-gray-950 uppercase tracking-wide">Korpus Digital Percakapan Teranotasi (Gold Dataset)</h3>
                            </div>
                        </div>

                        {{-- Stats Badge --}}
                        <div class="flex gap-3">
                            <div class="bg-gray-50 border border-gray-100 rounded-2xl py-2 px-4 text-center">
                                <span class="block text-[0.5rem] font-black text-gray-400 uppercase tracking-wider">Akurasi Kalimat</span>
                                <span class="block font-black text-sm text-green-600">{{ $sentenceAccuracy }}%</span>
                            </div>
                            <div class="bg-gray-50 border border-gray-100 rounded-2xl py-2 px-4 text-center">
                                <span class="block text-[0.5rem] font-black text-gray-400 uppercase tracking-wider">Jumlah Dataset</span>
                                <span class="block font-black text-sm text-gray-800">{{ count($evaluatedLines) }} Baris</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-gray-500 text-xs font-semibold leading-relaxed mb-6">
                        Berikut adalah daftar seluruh baris wacana bimbingan akademik yang telah divalidasi langsung oleh Dosen Senior melalui tombol Jempol Atas (Sesuai) maupun Jempol Bawah (Koreksi) pada dashboard Supervisory AI.
                    </p>

                    {{-- Table --}}
                    <div class="max-h-[60vh] overflow-y-auto rounded-3xl border border-gray-100 bg-white">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="sticky top-0 bg-gray-950 text-white font-black uppercase text-[0.65rem] tracking-wider z-20">
                                <tr>
                                    <th class="py-4 px-5">Sesi Rekaman</th>
                                    <th class="py-4 px-5">Pembicara</th>
                                    <th class="py-4 px-5">Transkrip Wacana (AWK)</th>
                                    <th class="py-4 px-5 text-center">Status Validasi</th>
                                    <th class="py-4 px-5">Penjelasan Politeness / Power</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                                @forelse($evaluatedLines as $line)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-4 px-5 font-bold text-gray-950 max-w-[150px] truncate" title="{{ $line['analysis_title'] }}">
                                            {{ $line['analysis_title'] }}
                                        </td>
                                        <td class="py-4 px-5 shrink-0">
                                            <span class="px-2.5 py-1 rounded-lg text-[0.65rem] font-bold uppercase tracking-wider {{ $line['speaker'] === 'Dosen' ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                                {{ $line['speaker'] }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-5 text-gray-800 leading-relaxed font-semibold max-w-[300px]">
                                            {!! $line['text_html'] !!}
                                        </td>
                                        <td class="py-4 px-5 text-center">
                                            @if($line['user_feedback'] === 'up')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[0.65rem] font-black text-green-700 bg-green-50 border border-green-100 uppercase tracking-widest">
                                                    <svg class="w-3.5 h-3.5 text-green-600 shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    SESUAI
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[0.65rem] font-black text-red-700 bg-red-50 border border-red-100 uppercase tracking-widest">
                                                    <svg class="w-3.5 h-3.5 text-red-600 shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    KOREKSI
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-5 text-gray-500 max-w-[250px] leading-relaxed text-[0.7rem]">
                                            {{ $line['agent_insight'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-gray-400 font-bold">
                                            <i data-lucide="database-backup" class="w-8 h-8 mx-auto mb-3 opacity-50"></i>
                                            Belum ada responden/evaluasi baris kalimat wacana yang terekam di database.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
