<x-layouts.app>
    <x-slot name="title">
        {{ app()->getLocale() === 'zh' ? '智能语音分析与交互图谱方法' : (app()->getLocale() === 'en' ? 'Intelligent Voice & Interaction Mapping' : 'Metodologi Analisis Suara & Peta Hubungan') }}
    </x-slot>

    {{-- KaTeX Math Renderer Assets --}}
    <x-slot name="styles">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css" integrity="sha384-n8MVd4RsNIU0tAv4ct0nTaAbDJwPJzDEaqSD1odI+WdtXRGWt2kTvGFasHpSy3SV" crossorigin="anonymous">
    </x-slot>

    <div x-data="{ showStatsModal: false, showDatasetModal: false, appLang: '{{ app()->getLocale() }}' }" class="space-y-10 pb-12">
        {{-- Header Banner (Balanced 2-Column Layout) --}}
        <div class="relative bg-gray-900 rounded-[2.5rem] p-8 md:p-12 text-white shadow-2xl overflow-hidden border border-white/5">
            {{-- Glowing Backdrops --}}
            <div class="absolute -right-10 -top-10 w-96 h-96 bg-purple-600/15 rounded-full blur-3xl opacity-70"></div>
            <div class="absolute -left-10 -bottom-10 w-80 h-80 bg-blue-600/15 rounded-full blur-3xl opacity-50"></div>

            <div class="relative z-10 grid grid-cols-1 xl:grid-cols-12 gap-8 items-center">
                {{-- Left Text Area (7 cols on xl) --}}
                <div class="xl:col-span-7 space-y-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-500/15 text-purple-400 border border-purple-500/30 text-[0.65rem] font-black uppercase tracking-widest">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                            <span class="lang-id">Metodologi Analisis Cerdas</span>
                            <span class="lang-en">Smart Analysis Methodology</span>
                            <span class="lang-zh">智能分析方法论</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-500/15 text-blue-400 border border-blue-500/30 text-[0.65rem] font-black uppercase tracking-widest">
                            <i data-lucide="cpu" class="w-3.5 h-3.5"></i>
                            Multi-Task RoBERTa
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 text-[0.65rem] font-black uppercase tracking-widest">
                            <i data-lucide="share-2" class="w-3.5 h-3.5"></i>
                            Peta Jaringan Graf
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight uppercase">
                        <span class="lang-id">
                            Analisis Suara Cerdas & <br class="hidden sm:inline" />
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-400 to-indigo-300">
                                Peta Hubungan Bimbingan Akademik
                            </span>
                        </span>
                        <span class="lang-en">
                            Intelligent Voice Analysis & <br class="hidden sm:inline" />
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-400 to-indigo-300">
                                Academic Supervision Interaction Mapping
                            </span>
                        </span>
                        <span class="lang-zh">
                            智能语音分析与 <br class="hidden sm:inline" />
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-400 to-indigo-300">
                                学术指导交互关系图谱
                            </span>
                        </span>
                    </h1>
                    
                    <p class="text-gray-300 text-sm md:text-base font-medium leading-relaxed">
                        <span class="lang-id">
                            Metode ilmiah terbaru untuk menganalisis rekaman bimbingan akademik secara otomatis dan menyeluruh. Sistem ini memisahkan suara dosen dan mahasiswa, membaca tinggi-rendahnya intonasi nada bicara, mengenali pola pemberian saran dosen serta relasi komunikasi (kemitraan vs dominasi), lalu merangkum alur bimbingan dalam bentuk bagan interaktif yang mudah dipahami.
                        </span>
                        <span class="lang-en">
                            The latest scientific methodology to analyze academic thesis supervision audio recordings automatically and comprehensively. The system separates the voices of supervisors and students, tracks pitch intonation dynamics, detects advice-giving patterns and communicative power relations (partnership vs dominance), and summarizes the interaction flow into an intuitive, interactive flowchart.
                        </span>
                        <span class="lang-zh">
                            全面且自动分析学术论文指导录音的最新科学方法。该系统自动分离导师与学生的发言角色，提取语调高低变化，识别建议传达模式与权力沟通关系（平等协作或指导权威），并将指导对话全过程梳理为直观互动的流程图。
                        </span>
                    </p>
                </div>

                {{-- Right Highlight Cards (5 cols on xl - Fills the empty space elegantly) --}}
                <div class="xl:col-span-5 grid grid-cols-2 gap-3.5">
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 transition">
                        <div class="w-8 h-8 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center mb-2.5">
                            <i data-lucide="mic" class="w-4 h-4"></i>
                        </div>
                        <div class="text-xs font-bold text-white mb-0.5">Diarization Otomatis</div>
                        <div class="text-[0.7rem] text-gray-400 leading-snug">Membedakan suara dosen dan mahasiswa tanpa input manual.</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 transition">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center mb-2.5">
                            <i data-lucide="activity" class="w-4 h-4"></i>
                        </div>
                        <div class="text-xs font-bold text-white mb-0.5">Intonasi Nada F0</div>
                        <div class="text-[0.7rem] text-gray-400 leading-snug">Membaca kontur nada naik (tanya), turun (tegas), dan netral.</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 transition">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-2.5">
                            <i data-lucide="share-2" class="w-4 h-4"></i>
                        </div>
                        <div class="text-xs font-bold text-white mb-0.5">Topologi Jaringan Graf</div>
                        <div class="text-[0.7rem] text-gray-400 leading-snug">Visualisasi keterhubungan aktor dan topik secara visual.</div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 transition">
                        <div class="w-8 h-8 rounded-xl bg-pink-500/20 text-pink-400 flex items-center justify-center mb-2.5">
                            <i data-lucide="git-merge" class="w-4 h-4"></i>
                        </div>
                        <div class="text-xs font-bold text-white mb-0.5">Bagan Alur Mermaid</div>
                        <div class="text-[0.7rem] text-gray-400 leading-snug">Rangkuman alur bimbingan kronologis dari awal hingga simpulan.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Grid Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Left Part: Arsitektur, Pipeline & Penjelasan Teori (2/3 width) --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- Diagram 1: Pipeline End-to-End Image Showcase --}}
                <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-xl shadow-gray-200/30">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-purple-500/10 text-purple-600 flex items-center justify-center">
                                <i data-lucide="git-branch" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">
                                    <span class="lang-id">Alur Kerja Sistem</span>
                                    <span class="lang-en">System Workflow</span>
                                    <span class="lang-zh">系统运行流程</span>
                                </span>
                                <h3 class="text-base font-black text-gray-950 uppercase tracking-wide">
                                    <span class="lang-id">Alur Lengkap Pemrosesan Suara V2 (End-to-End)</span>
                                    <span class="lang-en">End-to-End Voice Processing Pipeline V2</span>
                                    <span class="lang-zh">V2 语音端到端处理全流程</span>
                                </h3>
                            </div>
                        </div>
                        <span class="hidden sm:inline-flex px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-xl border border-purple-200">
                            4 Tahap Utama
                        </span>
                    </div>

                    {{-- Image Container (with multiple fallback sources for cPanel resilience) --}}
                    <div class="rounded-2xl overflow-hidden border border-gray-200 bg-gray-950/5 mb-6 group relative">
                        <img src="{{ asset('assets/img/pipeline_end_to_end_v2.png') }}" 
                             onerror="this.onerror=null; this.src='{{ asset('assets/images/pipeline_end_to_end_v2.png') }}';"
                             alt="Arsitektur Pipeline End-to-End Sistem Analisis Suara Bimbingan Akademik V2"
                             class="w-full h-auto object-contain transition duration-300 group-hover:scale-[1.01]" />
                        <div class="p-3 bg-gray-50 border-t border-gray-200 text-center">
                            <span class="text-[0.7rem] font-semibold text-gray-500">
                                Gambar 1: Rangkaian 4 Tahap Pemrosesan Sekuensial dari Gelombang Audio hingga Sintesis Multimodal
                            </span>
                        </div>
                    </div>

                    {{-- 4 Stages Description Breakdown (Bahasa Santai & Mudah Dipahami) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-purple-50/50 border border-purple-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-purple-600 text-white text-xs font-black flex items-center justify-center">1</span>
                                <h4 class="font-black text-gray-900 text-xs uppercase tracking-wider">Pemisahan Suara Dosen & Mahasiswa</h4>
                            </div>
                            <p class="text-gray-600 text-xs leading-relaxed">
                                Rekaman suara bimbingan dibersihkan dari jeda hening, lalu AI secara otomatis membedakan giliran bicara siapa dosen pembimbing dan siapa mahasiswa yang sedang berkonsultasi.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white text-xs font-black flex items-center justify-center">2</span>
                                <h4 class="font-black text-gray-900 text-xs uppercase tracking-wider">Analisis Makna & Intonasi Nada Bicara</h4>
                            </div>
                            <p class="text-gray-600 text-xs leading-relaxed">
                                Kalimat yang diucapkan dibaca maknanya oleh kecerdasan buatan RoBERTa, sekaligus mendeteksi nada suara (apakah bernada tanya ramah, netral, atau penegasan instruksi langsung).
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-blue-600 text-white text-xs font-black flex items-center justify-center">3</span>
                                <h4 class="font-black text-gray-900 text-xs uppercase tracking-wider">Pemetaan Jaringan Interaksi (Graf)</h4>
                            </div>
                            <p class="text-gray-600 text-xs leading-relaxed">
                                Hubungan komunikasi dipetakan ke dalam jaringan titik interaktif. Semakin sering dosen memberi arahan atau mahasiswa aktif bertanya, semakin tebal garis penghubung yang terlihat.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white text-xs font-black flex items-center justify-center">4</span>
                                <h4 class="font-black text-gray-900 text-xs uppercase tracking-wider">Sintesis Otomatis & Alur Bimbingan</h4>
                            </div>
                            <p class="text-gray-600 text-xs leading-relaxed">
                                AI langsung membaca peta interaksi dan menghasilkan ringkasan dinamika bimbingan, rekomendasi tindak lanjut, serta bagan alur tahapan bimbingan dari awal hingga akhir.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Diagram 2: RoBERTa V2 Deep Architecture Image Showcase --}}
                <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-xl shadow-gray-200/30">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
                                <i data-lucide="layers" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">
                                    <span class="lang-id">Struktur Model AI Cerdas</span>
                                    <span class="lang-en">Intelligent AI Model Structure</span>
                                    <span class="lang-zh">智能 AI 模型架构</span>
                                </span>
                                <h3 class="text-base font-black text-gray-950 uppercase tracking-wide">
                                    <span class="lang-id">Arsitektur Multi-Task RoBERTa V2</span>
                                    <span class="lang-en">Multi-Task RoBERTa V2 Architecture</span>
                                    <span class="lang-zh">Multi-Task RoBERTa V2 架构</span>
                                </h3>
                            </div>
                        </div>
                        <span class="hidden sm:inline-flex px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-xl border border-indigo-200">
                            Dual-Task Processing
                        </span>
                    </div>

                    {{-- Image Container (with fallback) --}}
                    <div class="rounded-2xl overflow-hidden border border-gray-200 bg-gray-950/5 mb-6 group relative">
                        <img src="{{ asset('assets/img/roberta_v2_architecture.png') }}" 
                             onerror="this.onerror=null; this.src='{{ asset('assets/images/roberta_v2_architecture.png') }}';"
                             alt="Arsitektur Model Multi-Task RoBERTa V2"
                             class="w-full h-auto object-contain transition duration-300 group-hover:scale-[1.01]" />
                        <div class="p-3 bg-gray-50 border-t border-gray-200 text-center">
                            <span class="text-[0.7rem] font-semibold text-gray-500">
                                Gambar 2: Desain Arsitektur Multi-Task Transformer RoBERTa dengan Klasifikasi Simultan dan Output Wacana
                            </span>
                        </div>
                    </div>

                    {{-- Cara Kerja Kecerdasan Buatan (Visual & Mudah Dipahami) --}}
                    <div class="p-6 md:p-8 rounded-3xl bg-gradient-to-br from-gray-900 via-gray-900 to-indigo-950 text-white mb-6 border border-white/10 space-y-6 shadow-2xl">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-white/10 pb-4 gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center">
                                    <i data-lucide="brain" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black tracking-wide uppercase text-white">
                                        Cara Kerja Model RoBERTa (Multi-Task Learning)
                                    </h4>
                                    <p class="text-[0.7rem] text-purple-300">Bagaimana AI memahami kalimat dan menghasilkan 2 analisa sekaligus</p>
                                </div>
                            </div>
                            <span class="inline-flex self-start sm:self-auto px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[0.65rem] font-bold">
                                1 Input ➔ 2 Hasil Otomatis
                            </span>
                        </div>
                        
                        {{-- 3 Langkah Alur Logika Visual --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Step 1 --}}
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[0.65rem] font-black uppercase tracking-widest text-purple-400">Langkah 1: Masukan</span>
                                        <span class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-300 text-xs font-bold flex items-center justify-center">1</span>
                                    </div>
                                    <h5 class="text-xs font-bold text-white mb-1.5">Kalimat Tuturan Suara</h5>
                                    <p class="text-[0.75rem] text-gray-300 leading-relaxed mb-3">
                                        Setiap baris kalimat percakapan dipotong menjadi kumpulan kata berurutan:
                                    </p>
                                </div>
                                <div class="p-2.5 rounded-xl bg-black/40 border border-white/10 text-center font-mono text-xs text-indigo-300">
                                    Kalimat = (kata₁, kata₂, ..., kataₙ)
                                </div>
                            </div>

                            {{-- Step 2 --}}
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[0.65rem] font-black uppercase tracking-widest text-indigo-400">Langkah 2: Enkoder AI</span>
                                        <span class="w-5 h-5 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold flex items-center justify-center">2</span>
                                    </div>
                                    <h5 class="text-xs font-bold text-white mb-1.5">Memahami Makna Konteks</h5>
                                    <p class="text-[0.75rem] text-gray-300 leading-relaxed mb-3">
                                        RoBERTa mencerna arti seluruh kalimat secara bolak-balik menjadi satu inti pemahaman:
                                    </p>
                                </div>
                                <div class="p-2.5 rounded-xl bg-black/40 border border-white/10 text-center font-mono text-xs text-purple-300 font-bold">
                                    Inti_Makna = RoBERTa(Kalimat)
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[0.65rem] font-black uppercase tracking-widest text-emerald-400">Langkah 3: Output</span>
                                        <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold flex items-center justify-center">3</span>
                                    </div>
                                    <h5 class="text-xs font-bold text-white mb-1.5">Dua Keputusan Sekaligus</h5>
                                    <p class="text-[0.75rem] text-gray-300 leading-relaxed mb-3">
                                        Dari 1 inti makna, sistem langsung memutuskan kategori saran dan relasi kuasanya.
                                    </p>
                                </div>
                                <div class="p-2.5 rounded-xl bg-black/40 border border-white/10 text-center font-mono text-[0.68rem] text-emerald-300 font-bold">
                                    Pola Saran + Relasi Kuasa
                                </div>
                            </div>
                        </div>

                        {{-- Kotak Rincian Formula Sederhana --}}
                        <div class="p-4 rounded-2xl bg-black/30 border border-white/10 space-y-3">
                            <span class="text-[0.7rem] font-black uppercase tracking-wider text-purple-300 block">
                                Rincian Perhitungan Komputasi (Formula Ringkas):
                            </span>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-bold text-pink-300">Pola Pemberian Saran:</span>
                                        <span class="text-[0.65rem] text-gray-400">Klasifikasi 5 Kelas</span>
                                    </div>
                                    <div class="font-mono text-xs text-white bg-black/40 p-2 rounded-lg my-1.5 text-center">
                                        Hasil_Saran = Probabilitas(Bobot_A × Inti_Makna)
                                    </div>
                                    <p class="text-[0.7rem] text-gray-300 leading-snug">
                                        Menentukan apakah kalimat dosen berisi: <i>Arahan Eksplisit, Bimbingan Bertahap, Dukungan Keputusan, Jawaban Tegas,</i> atau <i>Petunjuk Kontekstual</i>.
                                    </p>
                                </div>

                                <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-bold text-blue-300">Pola Relasi Kuasa:</span>
                                        <span class="text-[0.65rem] text-gray-400">Klasifikasi 3 Kelas</span>
                                    </div>
                                    <div class="font-mono text-xs text-white bg-black/40 p-2 rounded-lg my-1.5 text-center">
                                        Hasil_Relasi = Probabilitas(Bobot_M × Inti_Makna)
                                    </div>
                                    <p class="text-[0.7rem] text-gray-300 leading-snug">
                                        Menentukan suasana komunikasi: <i>Power Over</i> (dominan), <i>Power Maintaining</i> (setara/kemitraan), atau <i>Power Gaining</i> (inisiatif mahasiswa).
                                    </p>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[0.72rem] text-gray-300">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="check-check" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                                    <span>Kedua tugas dilatih secara seimbang (*Joint Optimization*) sehingga akurasi saling melengkapi.</span>
                                </div>
                                <span class="font-mono text-[0.68rem] text-purple-300 self-end sm:self-auto bg-purple-500/10 px-2 py-0.5 rounded border border-purple-500/20">
                                    Total_Error = Error_Saran + Error_Relasi
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Taksonomi Klasifikasi Sederhana & Jelas --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-gray-200 rounded-2xl p-4">
                            <h4 class="text-xs font-black text-purple-700 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                5 Pola Saran Dosen (Advice Giving)
                            </h4>
                            <ul class="text-xs text-gray-600 space-y-1.5 list-disc pl-4 font-medium">
                                <li><strong class="text-gray-900">Arahan Eksplisit:</strong> Perintah tegas, petunjuk langsung, dan koreksi jelas dari dosen.</li>
                                <li><strong class="text-gray-900">Bimbingan Bertahap:</strong> Bimbingan runtut langkah demi langkah menuntun pemikiran mahasiswa.</li>
                                <li><strong class="text-gray-900">Dukungan Keputusan:</strong> Persetujuan atau penguatan atas ide yang diajukan mahasiswa.</li>
                                <li><strong class="text-gray-900">Jawaban Tegas:</strong> Klarifikasi langsung menjawab keraguan atau pertanyaan teknis mahasiswa.</li>
                                <li><strong class="text-gray-900">Petunjuk Kontekstual:</strong> Saran halus yang mengarahkan mahasiswa membaca teori atau referensi tertentu.</li>
                            </ul>
                        </div>

                        <div class="border border-gray-200 rounded-2xl p-4">
                            <h4 class="text-xs font-black text-indigo-700 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                3 Relasi Komunikasi Bimbingan (Modes)
                            </h4>
                            <ul class="text-xs text-gray-600 space-y-1.5 list-disc pl-4 font-medium">
                                <li><strong class="text-gray-900">Power Over (Dominan):</strong> Dosen memegang kendali penuh dan menentukan keputusan secara instruksional.</li>
                                <li><strong class="text-gray-900">Power Maintaining (Seimbang):</strong> Komunikasi dua arah yang setara, santai, dan saling bertukar gagasan ilmiah.</li>
                                <li><strong class="text-gray-900">Power Gaining (Inisiatif):</strong> Mahasiswa menunjukkan keaktifan, kemandirian, dan berani menyampaikan ide baru.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Dinamika Intonasi & Topologi Graf --}}
                <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-xl shadow-gray-200/30">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                            <i data-lucide="activity" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">
                                <span class="lang-id">Nada Suara & Visualisasi Hubungan</span>
                                <span class="lang-en">Voice Tone & Relationship Visualization</span>
                                <span class="lang-zh">语音语调与交互关系可视化</span>
                            </span>
                            <h3 class="text-base font-black text-gray-950 uppercase tracking-wide">
                                <span class="lang-id">Dinamika Intonasi Bicara & Peta Hubungan Interaksi</span>
                                <span class="lang-en">Speech Intonation Dynamics & Interaction Network</span>
                                <span class="lang-zh">语音语调动态与交互网络图</span>
                            </h3>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
                        <div class="p-3.5 rounded-2xl bg-amber-50/60 border border-amber-200 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black text-base shrink-0">↑</div>
                            <div>
                                <span class="text-xs font-black text-amber-900 block">Intonasi Naik</span>
                                <p class="text-[0.7rem] text-amber-800 leading-snug mt-0.5">Pertanyaan inisiatif, konfirmasi terbuka, atau klarifikasi ide mahasiswa.</p>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-red-50/60 border border-red-200 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-red-500 text-white flex items-center justify-center font-black text-base shrink-0">↓</div>
                            <div>
                                <span class="text-xs font-black text-red-900 block">Intonasi Turun</span>
                                <p class="text-[0.7rem] text-red-800 leading-snug mt-0.5">Penegasan otoritas, instruksi direktif mutlak, atau evaluasi korektif dosen.</p>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-blue-50/60 border border-blue-200 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-500 text-white flex items-center justify-center font-black text-base shrink-0">➖</div>
                            <div>
                                <span class="text-xs font-black text-blue-900 block">Intonasi Netral</span>
                                <p class="text-[0.7rem] text-blue-800 leading-snug mt-0.5">Pertukaran informasi konseptual yang stabil, narasi deskriptif ilmiah.</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-gray-600 text-xs font-medium leading-relaxed">
                        Peta jaringan graf interaksi menyatukan seluruh percakapan menjadi gambar diagram yang hidup. Lingkaran (simpul) menggambarkan dosen, mahasiswa, dan kategori topik, sementara garis tebal-tipisnya menunjukkan seberapa sering pola komunikasi tersebut terjadi. Dengan begitu, dosen dan peneliti bisa langsung melihat apakah bimbingan berjalan interaktif atau satu arah.
                    </p>
                </div>

            </div>

            {{-- Right Part: Feature Highlights & Metrik Komparasi (1/3 width) --}}
            <div class="space-y-8">
                
                {{-- Quick Nav Switcher --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-[2.5rem] p-6 text-white border border-white/10 shadow-xl">
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 block mb-3">Versi Metodologi Tersedia</span>
                    
                    <div class="space-y-2.5">
                        <a href="{{ route('methodology') }}" 
                           class="flex items-center justify-between p-3 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/5 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center">
                                    <i data-lucide="book-open" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-white block">Metodologi V1</span>
                                    <span class="text-[0.65rem] text-white/50">IndoBERT & G-MLLM Hybrid</span>
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-white/40 group-hover:translate-x-1 transition-transform"></i>
                        </a>

                        <div class="flex items-center justify-between p-3 rounded-2xl bg-purple-600/30 border border-purple-500 text-white">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-purple-500 text-white flex items-center justify-center">
                                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-white block">Metodologi V2 (Aktif)</span>
                                    <span class="text-[0.65rem] text-purple-200">Multi-Task RoBERTa & Graf</span>
                                </div>
                            </div>
                            <span class="text-[0.6rem] px-2 py-0.5 rounded-full bg-purple-500 text-white font-black uppercase tracking-wider">Saat ini</span>
                        </div>
                    </div>
                </div>

                {{-- Fitur Unggulan V2 --}}
                <div class="bg-white rounded-[2.5rem] p-7 border border-gray-100 shadow-xl shadow-gray-200/30 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center">
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-sm font-black text-gray-900 uppercase tracking-wider">Fitur Unggulan V2</h4>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start gap-2.5">
                            <i data-lucide="zap" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">Sintesis Otomatis (Zero-Click)</strong>
                                Hasil graf interaksi dan bagan alur langsung dibuat otomatis tanpa perlu klik tombol lagi.
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <i data-lucide="refresh-cw" class="w-4 h-4 text-blue-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">Tukar Peran Pembicara</strong>
                                Dapat membalik peran pembicara dalam satu klik jika deteksi peran dosen/mahasiswa tertukar.
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <i data-lucide="smartphone" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">Detail Titik Graf di Layar Sentuh</strong>
                                Titik graf dapat disentuh di HP untuk membuka kotak penjelasan detail tanpa terpotong layar.
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <i data-lucide="shield-check" class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">Pemuat Diagram Tahan Gangguan</strong>
                                Diagram alur tetap tampil stabil dengan jalur cadangan otomatis dan tombol <i>Coba Lagi</i>.
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <i data-lucide="globe" class="w-4 h-4 text-rose-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">Dukungan Multi-Bahasa</strong>
                                Tersedia dalam 3 pilihan bahasa yang mudah diganti (Indonesia, Inggris, dan Mandarin).
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Coba Uji Model V2 --}}
                <div class="bg-gradient-to-br from-purple-700 via-indigo-700 to-purple-900 rounded-[2.5rem] p-7 text-white shadow-xl shadow-purple-900/20 text-center space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 text-white flex items-center justify-center mx-auto">
                        <i data-lucide="flask-conical" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-black uppercase tracking-tight">Coba Analisis Suara V2</h4>
                        <p class="text-xs text-purple-200 mt-1">Uji coba langsung kemampuan analisis suara bimbingan pada rekaman bimbingan Anda.</p>
                    </div>
                    <a href="{{ route('analysis.v2.create') }}" 
                       class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-white text-purple-900 font-black text-xs uppercase tracking-wider hover:bg-purple-50 transition shadow-lg">
                        <span>Buka Uji Coba V2</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>

            </div>

        </div>

    </div>

    {{-- KaTeX Math Auto-Render Script --}}
    <x-slot name="scripts">
        <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js" integrity="sha384-XjKyOOlGwcjNTAIQHIpgOno0Hl1YQqzUOEleOLALmuqehneUG+vnGctmUb0ZY0l8" crossorigin="anonymous"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js" integrity="sha384-+VBxd3r6XgURycqtZ117nYw44OOcIax56Z4dCRWbxyPt0Koah1uHoK0o4+/RRE05" crossorigin="anonymous"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const renderKaTeX = () => {
                    if (window.renderMathInElement) {
                        renderMathInElement(document.body, {
                            delimiters: [
                                {left: '$$', right: '$$', display: true},
                                {left: '$', right: '$', display: false},
                                {left: '\\(', right: '\\)', display: false},
                                {left: '\\[', right: '\\]', display: true}
                            ],
                            throwOnError: false
                        });
                    } else {
                        setTimeout(renderKaTeX, 100);
                    }
                };
                renderKaTeX();
            });
        </script>
    </x-slot>
</x-layouts.app>
