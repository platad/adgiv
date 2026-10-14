<x-layouts.app>
    <x-slot name="title">
        {{ app()->getLocale() === 'zh' ? 'Multi-Task RoBERTa V2 方法论' : (app()->getLocale() === 'en' ? 'Multi-Task RoBERTa V2 Methodology' : 'Metodologi Multi-Task RoBERTa V2') }}
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
                        Evolusi komputasional mutakhir untuk analisis wacana kritis bimbingan akademik. Mengintegrasikan pemisahan penutur (Speaker Diarization), kontur pitch intonasi ($F_0$), arsitektur Transformer Multi-Task RoBERTa untuk klasifikasi simultan <i>Advice Giving</i> dan relasi kuasa (<i>Modes of Interaction</i>), serta sintesis multimodal otomatis berbasis graf dan Mermaid Flowchart.
                    </span>
                    <span class="lang-en">
                        Cutting-edge computational evolution for academic supervision critical discourse analysis. Integrating speaker diarization, F0 pitch intonation contours, Multi-Task RoBERTa Transformer architecture for simultaneous classification of Advice Giving and power relations (Modes of Interaction), and zero-click automated multimodal synthesis based on network graphs and Mermaid Flowchart.
                    </span>
                    <span class="lang-zh">
                        学术指导批判性话语分析的前沿计算演进。整合说话人日志分离、F0 音调语调轮廓、用于同时分类建议提供（Advice Giving）与权力关系（Modes of Interaction）的 Multi-Task RoBERTa Transformer 架构，以及基于网络图和 Mermaid 流程图的零点击自动多模态合成。
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
                                    <span class="lang-id">Alur Eksekusi Sistem</span>
                                    <span class="lang-en">System Execution Flow</span>
                                    <span class="lang-zh">系统执行流程</span>
                                </span>
                                <h3 class="text-base font-black text-gray-950 uppercase tracking-wide">
                                    <span class="lang-id">Arsitektur Pipeline End-to-End V2</span>
                                    <span class="lang-en">End-to-End Pipeline Architecture V2</span>
                                    <span class="lang-zh">端到端管道架构 V2</span>
                                </h3>
                            </div>
                        </div>
                        <span class="hidden sm:inline-flex px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-xl border border-purple-200">
                            4-Stage Sequential
                        </span>
                    </div>

                    {{-- Image Container --}}
                    <div class="rounded-2xl overflow-hidden border border-gray-200 bg-gray-950/5 mb-6 group relative">
                        <img src="{{ asset('assets/images/pipeline_end_to_end_v2.png') }}" 
                             alt="Arsitektur Pipeline End-to-End Sistem Analisis Suara Bimbingan Akademik V2"
                             class="w-full h-auto object-contain transition duration-300 group-hover:scale-[1.01]" />
                        <div class="p-3 bg-gray-50 border-t border-gray-200 text-center">
                            <span class="text-[0.7rem] font-semibold text-gray-500">
                                Gambar 1: Rangkaian 4 Tahap Pemrosesan Sekuensial dari Gelombang Audio hingga Sintesis Multimodal
                            </span>
                        </div>
                    </div>

                    {{-- 4 Stages Description Breakdown --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-purple-50/50 border border-purple-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-purple-600 text-white text-xs font-black flex items-center justify-center">1</span>
                                <h4 class="font-black text-gray-900 text-xs uppercase tracking-wider">Audio Ingestion & Diarization</h4>
                            </div>
                            <p class="text-gray-600 text-xs leading-relaxed">
                                Sinyal percakapan mentah difilter melalui Voice Activity Detection (VAD) dan disegmentasi ke dalam klaster pembicara (Dosen vs Mahasiswa) menggunakan komputasi backend inference server.
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white text-xs font-black flex items-center justify-center">2</span>
                                <h4 class="font-black text-gray-900 text-xs uppercase tracking-wider">Multi-Task RoBERTa & Pitch F0</h4>
                            </div>
                            <p class="text-gray-600 text-xs leading-relaxed">
                                Transkrip tuturan diekstraksi embedding semantiknya oleh RoBERTa untuk mengklasifikasikan pola saran dan relasi kuasa secara simultan, diperkaya ekstraksi kontur nada intonasi ($F_0$).
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-blue-600 text-white text-xs font-black flex items-center justify-center">3</span>
                                <h4 class="font-black text-gray-900 text-xs uppercase tracking-wider">Konstruksi Topologi Graf</h4>
                            </div>
                            <p class="text-gray-600 text-xs leading-relaxed">
                                Memetakan keterkaitan aktor dan kategori wacana ke dalam graf berarah (Node & Edge Network) interaktif dengan bobot interaksi dan simulasi fisika daya tolak (force-directed layout).
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white text-xs font-black flex items-center justify-center">4</span>
                                <h4 class="font-black text-gray-900 text-xs uppercase tracking-wider">Sintesis Multimodal Otomatis</h4>
                            </div>
                            <p class="text-gray-600 text-xs leading-relaxed">
                                Vision AI GPT-4o menalar tangkapan visual topologi graf untuk menghasilkan interpretasi kualitatif komprehensif serta diagram alur kronologis percakapan (Mermaid Flowchart).
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
                                    <span class="lang-id">Detail Transformer & Klasifikasi Dual-Head</span>
                                    <span class="lang-en">Transformer Details & Dual-Head Classification</span>
                                    <span class="lang-zh">Transformer 细节与双头分类</span>
                                </span>
                                <h3 class="text-base font-black text-gray-950 uppercase tracking-wide">
                                    <span class="lang-id">Arsitektur Multi-Task RoBERTa V2</span>
                                    <span class="lang-en">Multi-Task RoBERTa V2 Architecture</span>
                                    <span class="lang-zh">Multi-Task RoBERTa V2 架构</span>
                                </h3>
                            </div>
                        </div>
                        <span class="hidden sm:inline-flex px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-xl border border-indigo-200">
                            Shared Representation
                        </span>
                    </div>

                    {{-- Image Container --}}
                    <div class="rounded-2xl overflow-hidden border border-gray-200 bg-gray-950/5 mb-6 group relative">
                        <img src="{{ asset('assets/images/roberta_v2_architecture.png') }}" 
                             alt="Arsitektur Model Multi-Task RoBERTa V2"
                             class="w-full h-auto object-contain transition duration-300 group-hover:scale-[1.01]" />
                        <div class="p-3 bg-gray-50 border-t border-gray-200 text-center">
                            <span class="text-[0.7rem] font-semibold text-gray-500">
                                Gambar 2: Desain Arsitektur Multi-Task Transformer RoBERTa dengan Klasifikasi Simultan dan Output Wacana
                            </span>
                        </div>
                    </div>

                    {{-- Deep Mathematical Formulation --}}
                    <div class="p-6 rounded-2xl bg-gray-900 text-white mb-6 border border-white/10 space-y-4">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <span class="text-xs font-black tracking-widest uppercase text-purple-400 flex items-center gap-2">
                                <i data-lucide="function-square" class="w-4 h-4"></i>
                                Formulasi Matematis Multi-Task Learning
                            </span>
                            <span class="text-[0.65rem] text-gray-400 font-mono">BPE + Dynamic Masking</span>
                        </div>
                        
                        <p class="text-xs text-gray-300 leading-relaxed">
                            Model mengonversi urutan token masukan $\mathbf{x} = (x_1, x_2, \dots, x_N)$ ke dalam representasi vektor tersembunyi kontekstual menggunakan RoBERTa encoder bidirectional:
                        </p>

                        <div class="p-3 bg-black/40 rounded-xl font-mono text-xs text-center text-indigo-300 overflow-x-auto">
                            $$\mathbf{h}_{[\text{CLS}]} = \text{RoBERTa}_{\boldsymbol{\theta}}(\mathbf{x})$$
                        </div>

                        <p class="text-xs text-gray-300 leading-relaxed">
                            Representasi $\mathbf{h}_{[\text{CLS}]}$ diproyeksikan ke dua kepala klasifikasi (*classification heads*) yang dilatih secara bersamaan:
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-mono">
                            <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                                <span class="text-pink-300 block font-bold mb-1">Head 1: Advice Giving</span>
                                $$\hat{y}_{\text{advice}} = \text{softmax}(\mathbf{W}_a \mathbf{h}_{[\text{CLS}]} + \mathbf{b}_a)$$
                            </div>
                            <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                                <span class="text-blue-300 block font-bold mb-1">Head 2: Modes of Interaction</span>
                                $$\hat{y}_{\text{mode}} = \text{softmax}(\mathbf{W}_m \mathbf{h}_{[\text{CLS}]} + \mathbf{b}_m)$$
                            </div>
                        </div>

                        <p class="text-xs text-gray-300 leading-relaxed">
                            Optimasi parameter jaringan menggunakan fungsi *Joint Cross-Entropy Loss* dengan bobot penyeimbang $(\alpha, \beta)$:
                        </p>

                        <div class="p-3 bg-black/40 rounded-xl font-mono text-xs text-center text-purple-300 overflow-x-auto">
                            $$\mathcal{L}_{\text{total}} = \alpha \mathcal{L}_{\text{advice}}(\hat{y}_a, y_a) + \beta \mathcal{L}_{\text{mode}}(\hat{y}_m, y_m)$$
                        </div>
                    </div>

                    {{-- Taksonomi Klasifikasi --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-gray-200 rounded-2xl p-4">
                            <h4 class="text-xs font-black text-purple-700 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                5 Pola Advice Giving
                            </h4>
                            <ul class="text-xs text-gray-600 space-y-1.5 list-disc pl-4 font-medium">
                                <li><strong class="text-gray-900">Arahan Eksplisit:</strong> Instruksi tegas, korektif, dan direktif langsung dari dosen.</li>
                                <li><strong class="text-gray-900">Bimbingan Bertahap:</strong> Panduan langkah demi langkah memandu pemikiran mahasiswa.</li>
                                <li><strong class="text-gray-900">Dukungan Keputusan:</strong> Afirmasi atau persetujuan atas gagasan mahasiswa.</li>
                                <li><strong class="text-gray-900">Jawaban Tegas:</strong> Klarifikasi langsung atas pertanyaan atau keraguan teknis.</li>
                                <li><strong class="text-gray-900">Petunjuk Kontekstual:</strong> Saran implisit yang mengarahkan literatur atau teori rujukan.</li>
                            </ul>
                        </div>

                        <div class="border border-gray-200 rounded-2xl p-4">
                            <h4 class="text-xs font-black text-indigo-700 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                3 Modes of Interaction (Relasi Kuasa)
                            </h4>
                            <ul class="text-xs text-gray-600 space-y-1.5 list-disc pl-4 font-medium">
                                <li><strong class="text-gray-900">Power Over:</strong> Dominasi instruksional di mana otoritas mutlak dipegang oleh dosen pembimbing.</li>
                                <li><strong class="text-gray-900">Power Maintaining:</strong> Relasi kolegial seimbang, saling menghormati pendapat ilmiah.</li>
                                <li><strong class="text-gray-900">Power Gaining:</strong> Inisiatif aktif dan kemandirian mahasiswa dalam mengusulkan gagasan atau data.</li>
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
                                <span class="lang-id">Akustik & Struktur Jaringan</span>
                                <span class="lang-en">Acoustics & Network Structure</span>
                                <span class="lang-zh">声学与网络结构</span>
                            </span>
                            <h3 class="text-base font-black text-gray-950 uppercase tracking-wide">
                                <span class="lang-id">Dinamika Intonasi (F0) & Pemodelan Graf Interaksi</span>
                                <span class="lang-en">Intonation Dynamics (F0) & Interaction Graph Modeling</span>
                                <span class="lang-zh">语调动态 (F0) 与交互图建模</span>
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
                        Topologi graf interaksi $G = (V, E)$ mengkristalkan relasi temporal menjadi struktur relasional berbobot. Simpul ($V$) mewakili entitas aktor dan kategori wacana, sedangkan tepi ($E$) menyimpan bobot keterhubungan interaksi. Hal ini memungkinkan AI dan peneliti membaca polarisasi kekuasaan dan pola bimbingan hanya dalam satu tampilan grafis yang intuitif.
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
                                Hasil graf dan Mermaid langsung digenerate tanpa klik manual.
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <i data-lucide="refresh-cw" class="w-4 h-4 text-blue-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">Tukar Peran Pembicara</strong>
                                Fleksibilitas membalik atribusi peran jika model diarization keliru.
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <i data-lucide="smartphone" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">Touchscreen Modal Insight</strong>
                                Simpul graf dapat disentuh di HP untuk melihat modal penjelasan detail.
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <i data-lucide="shield-check" class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">Multi-CDN Resilient Fallback</strong>
                                Pemuatan pustaka diagram tahan gangguan jaringan dengan tombol <i>Try Again</i>.
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <i data-lucide="globe" class="w-4 h-4 text-rose-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">Dukungan Multi-Bahasa</strong>
                                Navigasi dan teks bilingual/trilingual (Indonesia, English, 中文).
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
                        <p class="text-xs text-purple-200 mt-1">Uji coba langsung kehandalan Multi-Task RoBERTa pada percakapan bimbingan Anda.</p>
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
</x-layouts.app>
