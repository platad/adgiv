<x-layouts.app>
    <x-slot name="title">
        {{ app()->getLocale() === 'zh' ? 'Multi-Task RoBERTa V2 方法论' : (app()->getLocale() === 'en' ? 'Multi-Task RoBERTa V2 Methodology' : 'Metodologi Multi-Task RoBERTa V2') }}
    </x-slot>

    {{-- KaTeX Math Renderer Assets --}}
    <x-slot name="styles">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css" integrity="sha384-n8MVd4RsNIU0tAv4ct0nTaAbDJwPJzDEaqSD1odI+WdtXRGWt2kTvGFasHpSy3SV" crossorigin="anonymous">
    </x-slot>

    <div x-data="{ showStatsModal: false, showDatasetModal: false, appLang: '{{ app()->getLocale() }}' }" class="space-y-10 pb-12">
        {{-- Header Banner --}}
        <div class="relative bg-gray-900 rounded-[2.5rem] p-8 md:p-12 text-white shadow-2xl overflow-hidden border border-white/5">
            {{-- Glowing Backdrops --}}
            <div class="absolute -right-10 -top-10 w-96 h-96 bg-purple-600/15 rounded-full blur-3xl opacity-70"></div>
            <div class="absolute -left-10 -bottom-10 w-80 h-80 bg-blue-600/15 rounded-full blur-3xl opacity-50"></div>

            <div class="relative z-10 max-w-4xl">
                <div class="flex flex-wrap items-center gap-2 mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-500/15 text-purple-400 border border-purple-500/30 text-[0.65rem] font-black uppercase tracking-widest">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                        <span class="lang-id">Metodologi Riset V2 Terpadu</span>
                        <span class="lang-en">Integrated V2 Research Methodology</span>
                        <span class="lang-zh">综合 V2 研究方法论</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-500/15 text-blue-400 border border-blue-500/30 text-[0.65rem] font-black uppercase tracking-widest">
                        <i data-lucide="cpu" class="w-3.5 h-3.5"></i>
                        Multi-Task Transformer RoBERTa
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 text-[0.65rem] font-black uppercase tracking-widest">
                        <i data-lucide="share-2" class="w-3.5 h-3.5"></i>
                        Graph Network Topology
                    </span>
                </div>

                <h1 class="text-3xl md:text-5xl font-black tracking-tight leading-tight uppercase mb-5">
                    Multi-Task RoBERTa & <br class="hidden md:inline" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-400 to-indigo-300">
                        <span class="lang-id">Topologi Graf Wacana Kritis V2</span>
                        <span class="lang-en">Critical Discourse Graph Topology V2</span>
                        <span class="lang-zh">批判性话语图拓扑 V2</span>
                    </span>
                </h1>
                
                <p class="text-gray-300 text-sm md:text-base font-medium leading-relaxed max-w-3xl">
                    <span class="lang-id">
                        Metode ilmiah terbaru untuk menganalisis rekaman bimbingan skripsi secara otomatis dan menyeluruh. Sistem ini memisahkan suara dosen dan mahasiswa, membaca tinggi-rendahnya intonasi nada bicara, mengenali pola pemberian saran dosen serta relasi komunikasi (kemitraan vs dominasi), lalu merangkum alur bimbingan dalam bentuk bagan interaktif yang mudah dipahami.
                    </span>
                    <span class="lang-en">
                        The latest scientific methodology to analyze academic thesis supervision audio recordings automatically and comprehensively. The system separates the voices of supervisors and students, tracks pitch intonation dynamics, detects advice-giving patterns and communicative power relations (partnership vs dominance), and summarizes the interaction flow into an intuitive, interactive flowchart.
                    </span>
                    <span class="lang-zh">
                        全面且自动分析学术论文指导录音的最新科学方法。该系统自动分离导师与学生的发言角色，提取语调高低变化，识别建议传达模式与权力沟通关系（平等协作或指导权威），并将指导对话全过程梳理为直观互动的流程图。
                    </span>
                </p>
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

                    {{-- Formulasi Matematis dengan KaTeX Renderer Otomatis --}}
                    <div class="p-6 rounded-2xl bg-gray-900 text-white mb-6 border border-white/10 space-y-4">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <span class="text-xs font-black tracking-widest uppercase text-purple-400 flex items-center gap-2">
                                <i data-lucide="function-square" class="w-4 h-4"></i>
                                Formulasi Matematis Multi-Task Learning
                            </span>
                            <span class="text-[0.65rem] text-gray-400 font-mono">BPE + Dynamic Masking</span>
                        </div>
                        
                        <p class="text-xs text-gray-300 leading-relaxed">
                            Model mengubah susunan kata pada kalimat percakapan <span class="math-tex inline-math font-mono text-purple-300">\mathbf{x} = (x_1, x_2, \dots, x_N)</span> menjadi pemahaman vektor semantik yang utuh melalui enkoder cerdas RoBERTa:
                        </p>

                        <div class="p-4 bg-black/40 rounded-xl font-mono text-xs text-center text-indigo-300 overflow-x-auto">
                            <div class="math-tex block-math">\mathbf{h}_{[\text{CLS}]} = \text{RoBERTa}_{\boldsymbol{\theta}}(\mathbf{x})</div>
                        </div>

                        <p class="text-xs text-gray-300 leading-relaxed">
                            Hasil pemahaman vektor <span class="math-tex inline-math font-mono text-purple-300">\mathbf{h}_{[\text{CLS}]}</span> tersebut langsung diolah bersamaan ke dua cabang klasifikasi sekaligus:
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-mono">
                            <div class="p-3.5 bg-white/5 rounded-xl border border-white/10">
                                <span class="text-pink-300 block font-bold mb-1.5 uppercase tracking-wider text-[0.68rem]">Cabang 1: Pola Saran (Advice Giving)</span>
                                <div class="math-tex block-math">\hat{y}_{\text{advice}} = \text{softmax}(\mathbf{W}_a \mathbf{h}_{[\text{CLS}]} + \mathbf{b}_a)</div>
                            </div>
                            <div class="p-3.5 bg-white/5 rounded-xl border border-white/10">
                                <span class="text-blue-300 block font-bold mb-1.5 uppercase tracking-wider text-[0.68rem]">Cabang 2: Relasi Komunikasi (Modes)</span>
                                <div class="math-tex block-math">\hat{y}_{\text{mode}} = \text{softmax}(\mathbf{W}_m \mathbf{h}_{[\text{CLS}]} + \mathbf{b}_m)</div>
                            </div>
                        </div>

                        <p class="text-xs text-gray-300 leading-relaxed">
                            Kedua cabang tersebut dipelajari dan dioptimalkan secara seimbang menggunakan perhitungan rugi terpadu (*Joint Loss*):
                        </p>

                        <div class="p-4 bg-black/40 rounded-xl font-mono text-xs text-center text-purple-300 overflow-x-auto">
                            <div class="math-tex block-math">\mathcal{L}_{\text{total}} = \alpha \mathcal{L}_{\text{advice}}(\hat{y}_a, y_a) + \beta \mathcal{L}_{\text{mode}}(\hat{y}_m, y_m)</div>
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
