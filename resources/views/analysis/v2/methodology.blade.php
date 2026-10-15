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
                        <div class="text-xs font-bold text-white mb-0.5">
                            <span class="lang-id">Diarization Otomatis</span>
                            <span class="lang-en">Auto Diarization</span>
                            <span class="lang-zh">自动说话人日志</span>
                        </div>
                        <div class="text-[0.7rem] text-gray-400 leading-snug">
                            <span class="lang-id">Membedakan suara dosen dan mahasiswa tanpa input manual.</span>
                            <span class="lang-en">Distinguishes supervisor and student voices without manual input.</span>
                            <span class="lang-zh">无需人工干预即可精准分离导师与学生的语音发言。</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 transition">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center mb-2.5">
                            <i data-lucide="activity" class="w-4 h-4"></i>
                        </div>
                        <div class="text-xs font-bold text-white mb-0.5">
                            <span class="lang-id">Intonasi Nada F0</span>
                            <span class="lang-en">F0 Pitch Intonation</span>
                            <span class="lang-zh">F0 基频语调</span>
                        </div>
                        <div class="text-[0.7rem] text-gray-400 leading-snug">
                            <span class="lang-id">Membaca kontur nada naik (tanya), turun (tegas), dan netral.</span>
                            <span class="lang-en">Reads pitch contour: rising (query), falling (assertive), neutral.</span>
                            <span class="lang-zh">捕获语调轮廓：上升（疑问）、下降（决断）和中性。</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 transition">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-2.5">
                            <i data-lucide="share-2" class="w-4 h-4"></i>
                        </div>
                        <div class="text-xs font-bold text-white mb-0.5">
                            <span class="lang-id">Topologi Jaringan Graf</span>
                            <span class="lang-en">Graph Topology</span>
                            <span class="lang-zh">图拓扑网络</span>
                        </div>
                        <div class="text-[0.7rem] text-gray-400 leading-snug">
                            <span class="lang-id">Visualisasi keterhubungan aktor dan topik secara visual.</span>
                            <span class="lang-en">Visualizes actors and discourse themes connectivity interactively.</span>
                            <span class="lang-zh">交互式直观呈现发言角色与指导主题的连接网络。</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 transition">
                        <div class="w-8 h-8 rounded-xl bg-pink-500/20 text-pink-400 flex items-center justify-center mb-2.5">
                            <i data-lucide="git-merge" class="w-4 h-4"></i>
                        </div>
                        <div class="text-xs font-bold text-white mb-0.5">
                            <span class="lang-id">Bagan Alur Mermaid</span>
                            <span class="lang-en">Mermaid Flowchart</span>
                            <span class="lang-zh">Mermaid 流程图</span>
                        </div>
                        <div class="text-[0.7rem] text-gray-400 leading-snug">
                            <span class="lang-id">Rangkuman alur bimbingan kronologis dari awal hingga simpulan.</span>
                            <span class="lang-en">Chronological summary of supervision stages from start to action.</span>
                            <span class="lang-zh">按时间顺序系统梳理从初始讨论至行动决策的完整流程。</span>
                        </div>
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
                            <span class="lang-id">4 Tahap Utama</span>
                            <span class="lang-en">4 Core Stages</span>
                            <span class="lang-zh">4 大核心阶段</span>
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
                                <span class="lang-id">Gambar 1: Rangkaian 4 Tahap Pemrosesan Sekuensial dari Gelombang Audio hingga Sintesis Multimodal</span>
                                <span class="lang-en">Figure 1: Sequential 4-Stage Processing Pipeline from Audio Waveform to Multimodal Synthesis</span>
                                <span class="lang-zh">图 1: 从声学音频波形到多模态智能综合呈现的 4 阶段串联处理管线</span>
                            </span>
                        </div>
                    </div>

                    {{-- 4 Stages Description Breakdown (Bahasa Santai & Mudah Dipahami) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-purple-50/50 border border-purple-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-purple-600 text-white text-xs font-black flex items-center justify-center">1</span>
                                <h4 class="font-black text-gray-900 text-xs uppercase tracking-wider">
                                    <span class="lang-id">Pemisahan Suara Dosen & Mahasiswa</span>
                                    <span class="lang-en">Speaker Separation & Diarization</span>
                                    <span class="lang-zh">师生发言角色分离</span>
                                </h4>
                            </div>
                            <p class="text-gray-600 text-xs leading-relaxed">
                                <span class="lang-id">Rekaman suara bimbingan dibersihkan dari jeda hening, lalu AI secara otomatis membedakan giliran bicara siapa dosen pembimbing dan siapa mahasiswa yang sedang berkonsultasi.</span>
                                <span class="lang-en">Audio recording is cleaned of silence pauses, then AI diarization automatically distinguishes turns between supervisor and consulting student.</span>
                                <span class="lang-zh">自动剔除静音停顿段落，利用前沿语音日志算法精准划分导师指令与学生陈述的交替发言轮次。</span>
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white text-xs font-black flex items-center justify-center">2</span>
                                <h4 class="font-black text-gray-900 text-xs uppercase tracking-wider">
                                    <span class="lang-id">Analisis Makna & Intonasi Nada Bicara</span>
                                    <span class="lang-en">Semantic & Tone Pitch Analysis</span>
                                    <span class="lang-zh">语义解析与语调基频分析</span>
                                </h4>
                            </div>
                            <p class="text-gray-600 text-xs leading-relaxed">
                                <span class="lang-id">Kalimat yang diucapkan dibaca maknanya oleh kecerdasan buatan RoBERTa, sekaligus mendeteksi nada suara (apakah bernada tanya ramah, netral, atau penegasan instruksi langsung).</span>
                                <span class="lang-en">Spoken sentences are semantically understood by RoBERTa while simultaneously detecting acoustic pitch contours (inquiry, neutral, or directive).</span>
                                <span class="lang-zh">由 RoBERTa 模型深入理解语句内涵，同步解构声学音高基频（亲和反问、客观中立或权威指令）。</span>
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-blue-600 text-white text-xs font-black flex items-center justify-center">3</span>
                                <h4 class="font-black text-gray-900 text-xs uppercase tracking-wider">
                                    <span class="lang-id">Pemetaan Jaringan Interaksi (Graf)</span>
                                    <span class="lang-en">Interaction Network Graph Mapping</span>
                                    <span class="lang-zh">交互关系网络图拓扑建模</span>
                                </h4>
                            </div>
                            <p class="text-gray-600 text-xs leading-relaxed">
                                <span class="lang-id">Hubungan komunikasi dipetakan ke dalam jaringan titik interaktif. Semakin sering dosen memberi arahan atau mahasiswa aktif bertanya, semakin tebal garis penghubung yang terlihat.</span>
                                <span class="lang-en">Communication ties are mapped into interactive node-edge networks. Frequent guidance or proactive questions result in thicker, dynamic relation edges.</span>
                                <span class="lang-zh">将对话联系映射至节点与连线网络。指导互动越频繁或学生提问越主动，所呈现的关系边缘拓扑越密集紧密。</span>
                            </p>
                        </div>

                        <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white text-xs font-black flex items-center justify-center">4</span>
                                <h4 class="font-black text-gray-900 text-xs uppercase tracking-wider">
                                    <span class="lang-id">Sintesis Otomatis & Alur Bimbingan</span>
                                    <span class="lang-en">Automatic Synthesis & Guidance Flow</span>
                                    <span class="lang-zh">智能综合推演与流程梳理</span>
                                </h4>
                            </div>
                            <p class="text-gray-600 text-xs leading-relaxed">
                                <span class="lang-id">AI langsung membaca peta interaksi dan menghasilkan ringkasan dinamika bimbingan, rekomendasi tindak lanjut, serta bagan alur tahapan bimbingan dari awal hingga akhir.</span>
                                <span class="lang-en">AI interprets the interaction topology and generates supervision dynamics executive summary, actionable recommendations, and structured chronological flowcharts.</span>
                                <span class="lang-zh">AI 自主解构交互拓扑网络，实时生成学术指导动态概要、跟进改进建议以及全流程发展脉络图。</span>
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
                                        <span class="lang-id">Cara Kerja Model RoBERTa (Multi-Task Learning)</span>
                                        <span class="lang-en">How RoBERTa Works (Multi-Task Learning)</span>
                                        <span class="lang-zh">RoBERTa 模型运作机理 (多任务联合学习)</span>
                                    </h4>
                                    <p class="text-[0.7rem] text-purple-300">
                                        <span class="lang-id">Bagaimana AI memahami kalimat dan menghasilkan 2 analisa sekaligus</span>
                                        <span class="lang-en">How AI comprehends sentences to produce dual analyses concurrently</span>
                                        <span class="lang-zh">AI 如何通过一次语句语义解析同步推演两项关键结论</span>
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex self-start sm:self-auto px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[0.65rem] font-bold">
                                <span class="lang-id">1 Input ➔ 2 Hasil Otomatis</span>
                                <span class="lang-en">1 Input ➔ 2 Joint Outputs</span>
                                <span class="lang-zh">1 次输入 ➔ 2 项自动推演</span>
                            </span>
                        </div>
                        
                        {{-- 3 Langkah Alur Logika Visual --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Step 1 --}}
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[0.65rem] font-black uppercase tracking-widest text-purple-400">
                                            <span class="lang-id">Langkah 1: Masukan</span>
                                            <span class="lang-en">Step 1: Input</span>
                                            <span class="lang-zh">步骤 1: 文本输入</span>
                                        </span>
                                        <span class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-300 text-xs font-bold flex items-center justify-center">1</span>
                                    </div>
                                    <h5 class="text-xs font-bold text-white mb-1.5">
                                        <span class="lang-id">Kalimat Tuturan Suara</span>
                                        <span class="lang-en">Speech Utterance</span>
                                        <span class="lang-zh">语音转录语句</span>
                                    </h5>
                                    <p class="text-[0.75rem] text-gray-300 leading-relaxed mb-3">
                                        <span class="lang-id">Setiap baris kalimat percakapan dipotong menjadi kumpulan kata berurutan:</span>
                                        <span class="lang-en">Each conversational line is tokenized into a sequential word series:</span>
                                        <span class="lang-zh">每行对话语句被自动切分为连续的单词标记序列:</span>
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
                                        <span class="text-[0.65rem] font-black uppercase tracking-widest text-indigo-400">
                                            <span class="lang-id">Langkah 2: Enkoder AI</span>
                                            <span class="lang-en">Step 2: AI Encoder</span>
                                            <span class="lang-zh">步骤 2: AI 语义编码器</span>
                                        </span>
                                        <span class="w-5 h-5 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-bold flex items-center justify-center">2</span>
                                    </div>
                                    <h5 class="text-xs font-bold text-white mb-1.5">
                                        <span class="lang-id">Memahami Makna Konteks</span>
                                        <span class="lang-en">Contextual Comprehension</span>
                                        <span class="lang-zh">上下文深层理解</span>
                                    </h5>
                                    <p class="text-[0.75rem] text-gray-300 leading-relaxed mb-3">
                                        <span class="lang-id">RoBERTa mencerna arti seluruh kalimat secara bolak-balik menjadi satu inti pemahaman:</span>
                                        <span class="lang-en">RoBERTa processes bidirectional context into a consolidated semantic vector:</span>
                                        <span class="lang-zh">RoBERTa 双向解析全局语境，凝炼出代表核心语义的特征向量:</span>
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
                                        <span class="text-[0.65rem] font-black uppercase tracking-widest text-emerald-400">
                                            <span class="lang-id">Langkah 3: Output</span>
                                            <span class="lang-en">Step 3: Outputs</span>
                                            <span class="lang-zh">步骤 3: 联合输出</span>
                                        </span>
                                        <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold flex items-center justify-center">3</span>
                                    </div>
                                    <h5 class="text-xs font-bold text-white mb-1.5">
                                        <span class="lang-id">Dua Keputusan Sekaligus</span>
                                        <span class="lang-en">Dual Joint Decisions</span>
                                        <span class="lang-zh">双路并行判决</span>
                                    </h5>
                                    <p class="text-[0.75rem] text-gray-300 leading-relaxed mb-3">
                                        <span class="lang-id">Dari 1 inti makna, sistem langsung memutuskan kategori saran dan relasi kuasanya.</span>
                                        <span class="lang-en">From 1 semantic core, the model jointly determines advice category and power dynamic.</span>
                                        <span class="lang-zh">基于单一核心语义表征，同步解构建议形态与权力支配格局。</span>
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
                                <span class="lang-id">Rincian Perhitungan Komputasi (Formula Ringkas):</span>
                                <span class="lang-en">Computational Formulation Details:</span>
                                <span class="lang-zh">计算推演数学表达:</span>
                            </span>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-bold text-pink-300">
                                            <span class="lang-id">Pola Pemberian Saran:</span>
                                            <span class="lang-en">Advice-Giving Pattern:</span>
                                            <span class="lang-zh">指导建议模式:</span>
                                        </span>
                                        <span class="text-[0.65rem] text-gray-400">
                                            <span class="lang-id">Klasifikasi 5 Kelas</span>
                                            <span class="lang-en">5-Class Classification</span>
                                            <span class="lang-zh">5 个分类目标</span>
                                        </span>
                                    </div>
                                    <div class="font-mono text-xs text-white bg-black/40 p-2 rounded-lg my-1.5 text-center">
                                        Hasil_Saran = Probabilitas(Bobot_A × Inti_Makna)
                                    </div>
                                    <p class="text-[0.7rem] text-gray-300 leading-snug">
                                        <span class="lang-id">Menentukan apakah kalimat dosen berisi: <i>Arahan Eksplisit, Bimbingan Bertahap, Dukungan Keputusan, Jawaban Tegas,</i> atau <i>Petunjuk Kontekstual</i>.</span>
                                        <span class="lang-en">Identifies whether supervisor speech contains: <i>Explicit Direction, Step-by-Step, Decision Support, Assertive Response,</i> or <i>Contextual Hints</i>.</span>
                                        <span class="lang-zh">判定指导话语属性：<i>明确指令、渐进引导、决策支持、决断答复</i> 或 <i>情境提示</i>。</span>
                                    </p>
                                </div>

                                <div class="p-3 rounded-xl bg-white/5 border border-white/10">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-bold text-blue-300">
                                            <span class="lang-id">Pola Relasi Kuasa:</span>
                                            <span class="lang-en">Power Relation Dynamic:</span>
                                            <span class="lang-zh">权力关系结构:</span>
                                        </span>
                                        <span class="text-[0.65rem] text-gray-400">
                                            <span class="lang-id">Klasifikasi 3 Kelas</span>
                                            <span class="lang-en">3-Class Classification</span>
                                            <span class="lang-zh">3 个分类目标</span>
                                        </span>
                                    </div>
                                    <div class="font-mono text-xs text-white bg-black/40 p-2 rounded-lg my-1.5 text-center">
                                        Hasil_Relasi = Probabilitas(Bobot_M × Inti_Makna)
                                    </div>
                                    <p class="text-[0.7rem] text-gray-300 leading-snug">
                                        <span class="lang-id">Menentukan suasana komunikasi: <i>Power Over</i> (dominan), <i>Power Maintaining</i> (setara/kemitraan), atau <i>Power Gaining</i> (inisiatif mahasiswa).</span>
                                        <span class="lang-en">Determines communicative climate: <i>Power Over</i> (dominance), <i>Power Maintaining</i> (equality/partnership), or <i>Power Gaining</i> (student initiative).</span>
                                        <span class="lang-zh">度量沟通氛围极性：<i>权力支配 (权威导向)</i>、<i>权力维系 (平等协作)</i> 或 <i>权力争取 (学生主动)</i>。</span>
                                    </p>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[0.72rem] text-gray-300">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="check-check" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                                    <span>
                                        <span class="lang-id">Kedua tugas dilatih secara seimbang (*Joint Optimization*) sehingga akurasi saling melengkapi.</span>
                                        <span class="lang-en">Both tasks undergo balanced joint optimization, mutually reinforcing overall accuracy.</span>
                                        <span class="lang-zh">双项任务通过联合优化平衡训练，促使预测精度相互增益。</span>
                                    </span>
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
                                <span class="lang-id">5 Pola Saran Dosen (Advice Giving)</span>
                                <span class="lang-en">5 Supervisor Advice Patterns</span>
                                <span class="lang-zh">5 种导师指导建议模式</span>
                            </h4>
                            <ul class="text-xs text-gray-600 space-y-1.5 list-disc pl-4 font-medium">
                                <li>
                                    <strong class="text-gray-900">
                                        <span class="lang-id">Arahan Eksplisit:</span>
                                        <span class="lang-en">Explicit Direction:</span>
                                        <span class="lang-zh">明确指令:</span>
                                    </strong>
                                    <span class="lang-id">Perintah tegas, petunjuk langsung, dan koreksi jelas dari dosen.</span>
                                    <span class="lang-en">Firm orders, direct instructions, and clear corrections from the supervisor.</span>
                                    <span class="lang-zh">导师下达的明确命令、直接指示或针对性纠偏。</span>
                                </li>
                                <li>
                                    <strong class="text-gray-900">
                                        <span class="lang-id">Bimbingan Bertahap:</span>
                                        <span class="lang-en">Step-by-Step Guidance:</span>
                                        <span class="lang-zh">渐进式指导:</span>
                                    </strong>
                                    <span class="lang-id">Bimbingan runtut langkah demi langkah menuntun pemikiran mahasiswa.</span>
                                    <span class="lang-en">Structured step-by-step guidance scaffolding student reasoning.</span>
                                    <span class="lang-zh">循序渐进、层层递进引导学生思维发展的辅导路径。</span>
                                </li>
                                <li>
                                    <strong class="text-gray-900">
                                        <span class="lang-id">Dukungan Keputusan:</span>
                                        <span class="lang-en">Decision Support:</span>
                                        <span class="lang-zh">决策支持:</span>
                                    </strong>
                                    <span class="lang-id">Persetujuan atau penguatan atas ide yang diajukan mahasiswa.</span>
                                    <span class="lang-en">Approval or endorsement strengthening ideas proposed by students.</span>
                                    <span class="lang-zh">对学生自主提出的学术观点的积极肯定与强化支持。</span>
                                </li>
                                <li>
                                    <strong class="text-gray-900">
                                        <span class="lang-id">Jawaban Tegas:</span>
                                        <span class="lang-en">Assertive Response:</span>
                                        <span class="lang-zh">确切答复:</span>
                                    </strong>
                                    <span class="lang-id">Klarifikasi langsung menjawab keraguan atau pertanyaan teknis mahasiswa.</span>
                                    <span class="lang-en">Direct clarifications resolving students' technical uncertainties.</span>
                                    <span class="lang-zh">直接回应并澄清学生在技术或研究方法上的具体困惑。</span>
                                </li>
                                <li>
                                    <strong class="text-gray-900">
                                        <span class="lang-id">Petunjuk Kontekstual:</span>
                                        <span class="lang-en">Contextual Hints:</span>
                                        <span class="lang-zh">情境启发:</span>
                                    </strong>
                                    <span class="lang-id">Saran halus yang mengarahkan mahasiswa membaca teori atau referensi tertentu.</span>
                                    <span class="lang-en">Subtle suggestions directing students to pertinent theories and literature.</span>
                                    <span class="lang-zh">委婉引导学生拓展相关文献与理论框架的启发性建议。</span>
                                </li>
                            </ul>
                        </div>

                        <div class="border border-gray-200 rounded-2xl p-4">
                            <h4 class="text-xs font-black text-indigo-700 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                <span class="lang-id">3 Relasi Komunikasi Bimbingan (Modes)</span>
                                <span class="lang-en">3 Power Interaction Modes</span>
                                <span class="lang-zh">3 种话语权力交互形态</span>
                            </h4>
                            <ul class="text-xs text-gray-600 space-y-1.5 list-disc pl-4 font-medium">
                                <li>
                                    <strong class="text-gray-900">Power Over (Dominan):</strong>
                                    <span class="lang-id">Dosen memegang kendali penuh dan menentukan keputusan secara instruksional.</span>
                                    <span class="lang-en">Supervisor exercises full authoritative control and directs decisions.</span>
                                    <span class="lang-zh">导师主导会话并以指令性方式全盘把控指导决策。</span>
                                </li>
                                <li>
                                    <strong class="text-gray-900">Power Maintaining (Seimbang):</strong>
                                    <span class="lang-id">Komunikasi dua arah yang setara, santai, dan saling bertukar gagasan ilmiah.</span>
                                    <span class="lang-en">Equitable, collaborative bidirectional exchange of scholarly perspectives.</span>
                                    <span class="lang-zh">平等双向的学术研讨交流，展现出良好的学术伙伴关系。</span>
                                </li>
                                <li>
                                    <strong class="text-gray-900">Power Gaining (Inisiatif):</strong>
                                    <span class="lang-id">Mahasiswa menunjukkan keaktifan, kemandirian, dan berani menyampaikan ide baru.</span>
                                    <span class="lang-en">Student displays active autonomy and confidently proposes innovative thoughts.</span>
                                    <span class="lang-zh">学生展现出高度能动性，自信陈述并捍卫自主创新观点。</span>
                                </li>
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
                                <span class="text-xs font-black text-amber-900 block">
                                    <span class="lang-id">Intonasi Naik</span>
                                    <span class="lang-en">Rising Pitch</span>
                                    <span class="lang-zh">上升语调</span>
                                </span>
                                <p class="text-[0.7rem] text-amber-800 leading-snug mt-0.5">
                                    <span class="lang-id">Pertanyaan inisiatif, konfirmasi terbuka, atau klarifikasi ide mahasiswa.</span>
                                    <span class="lang-en">Proactive queries, open confirmation, or student idea clarifications.</span>
                                    <span class="lang-zh">主动发问、开放性确认或针对学术观点的启发式探讨。</span>
                                </p>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-red-50/60 border border-red-200 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-red-500 text-white flex items-center justify-center font-black text-base shrink-0">↓</div>
                            <div>
                                <span class="text-xs font-black text-red-900 block">
                                    <span class="lang-id">Intonasi Turun</span>
                                    <span class="lang-en">Falling Pitch</span>
                                    <span class="lang-zh">下降语调</span>
                                </span>
                                <p class="text-[0.7rem] text-red-800 leading-snug mt-0.5">
                                    <span class="lang-id">Penegasan otoritas, instruksi direktif mutlak, atau evaluasi korektif dosen.</span>
                                    <span class="lang-en">Authoritative assertions, directive instructions, or corrective evaluations.</span>
                                    <span class="lang-zh">权威性定论、指令性要求或导师的明确否定性审阅评价。</span>
                                </p>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-blue-50/60 border border-blue-200 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-500 text-white flex items-center justify-center font-black text-base shrink-0">➖</div>
                            <div>
                                <span class="text-xs font-black text-blue-900 block">
                                    <span class="lang-id">Intonasi Netral</span>
                                    <span class="lang-en">Neutral Pitch</span>
                                    <span class="lang-zh">平稳中性语调</span>
                                </span>
                                <p class="text-[0.7rem] text-blue-800 leading-snug mt-0.5">
                                    <span class="lang-id">Pertukaran informasi konseptual yang stabil, narasi deskriptif ilmiah.</span>
                                    <span class="lang-en">Stable conceptual data exchange and objective academic discourse.</span>
                                    <span class="lang-zh">客观平稳的概念信息阐述与学术事实描述。</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <p class="text-gray-600 text-xs font-medium leading-relaxed">
                        <span class="lang-id">Peta jaringan graf interaksi menyatukan seluruh percakapan menjadi gambar diagram yang hidup. Lingkaran (simpul) menggambarkan dosen, mahasiswa, dan kategori topik, sementara garis tebal-tipisnya menunjukkan seberapa sering pola komunikasi tersebut terjadi. Dengan begitu, dosen dan peneliti bisa langsung melihat apakah bimbingan berjalan interaktif atau satu arah.</span>
                        <span class="lang-en">The interaction network graph unifies the dialogue into a dynamic living topology. Nodes represent supervisors, students, and discourse themes, while varying edge thicknesses indicate communicative density. This allows supervisors and researchers to immediately assess whether supervision was collaborative or unilateral.</span>
                        <span class="lang-zh">交互拓扑网络将整场对话提炼为鲜活的动态图谱。圆圈（节点）代表导师、学生及核心指导范畴，线条粗细直观反应互动频次。导师与科研人员可借此一目了然地获悉指导过程究竟是平等协作还是单向灌输。</span>
                    </p>

                    {{-- Formulasi Matematis Sentralitas Graf (Bahan Bab 3 Skripsi) --}}
                    <div class="mt-6 pt-6 border-t border-gray-100 space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider">
                                <span class="lang-id">Formulasi Matematis Metrik Sentralitas Graf Berarah (Network Science)</span>
                                <span class="lang-en">Mathematical Formulation of Directed Graph Centrality Metrics</span>
                                <span class="lang-zh">有向图网络中心性度量数学建模</span>
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-200">
                                <span class="text-[0.62rem] font-black uppercase text-purple-700 block mb-1">1. Degree Centrality ($C_D$)</span>
                                <div class="font-mono text-xs bg-white p-2 rounded-xl border border-gray-200 text-purple-900 font-bold text-center mb-1.5">
                                    C_D(v) = \frac{\text{deg}_{in}(v) + \text{deg}_{out}(v)}{|V| - 1}
                                </div>
                                <p class="text-[0.68rem] text-gray-500 leading-snug">
                                    <span class="lang-id">Mengukur derajat aktivitas aktor dalam menginisiasi atau menerima respons wacana bimbingan.</span>
                                    <span class="lang-en">Measures actor activity level in initiating or receiving supervisory discourse turns.</span>
                                    <span class="lang-zh">度量发言角色在发起或接收学术指导话语轮次中的综合活跃程度。</span>
                                </p>
                            </div>

                            <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-200">
                                <span class="text-[0.62rem] font-black uppercase text-indigo-700 block mb-1">2. Betweenness Centrality ($C_B$)</span>
                                <div class="font-mono text-xs bg-white p-2 rounded-xl border border-gray-200 text-indigo-900 font-bold text-center mb-1.5">
                                    C_B(v) = \sum_{s \neq v \neq t} \frac{\sigma_{st}(v)}{\sigma_{st}} \times \frac{1}{(|V|-1)(|V|-2)}
                                </div>
                                <p class="text-[0.68rem] text-gray-500 leading-snug">
                                    <span class="lang-id">Mengidentifikasi simpul perantara krusial (<em class="font-serif">thematic bridge</em>) yang menghubungkan transisi topik bimbingan.</span>
                                    <span class="lang-en">Identifies crucial intermediate nodes (<em class="font-serif">thematic bridges</em>) linking topic transitions.</span>
                                    <span class="lang-zh">识别连接不同指导议题过渡的关键桥梁节点（介数中心性枢纽）。</span>
                                </p>
                            </div>

                            <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-200">
                                <span class="text-[0.62rem] font-black uppercase text-blue-700 block mb-1">3. Graph Density ($D$)</span>
                                <div class="font-mono text-xs bg-white p-2 rounded-xl border border-gray-200 text-blue-900 font-bold text-center mb-1.5">
                                    D = \frac{|E|}{|V|(|V| - 1)}
                                </div>
                                <p class="text-[0.68rem] text-gray-500 leading-snug">
                                    <span class="lang-id">Kerapatan interaksi yang menggambarkan intensitas keterhubungan antar entitas percakapan.</span>
                                    <span class="lang-en">Graph density representing the overall interconnectedness intensity between entities.</span>
                                    <span class="lang-zh">图网络连通密度，客观表征对话实体之间相互连接的紧密程度。</span>
                                </p>
                            </div>

                            <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-200">
                                <span class="text-[0.62rem] font-black uppercase text-emerald-700 block mb-1">4. Reciprocity Index ($R$)</span>
                                <div class="font-mono text-xs bg-white p-2 rounded-xl border border-gray-200 text-emerald-900 font-bold text-center mb-1.5">
                                    R = \frac{2 \times |E_{\leftrightarrow}|}{|E|}
                                </div>
                                <p class="text-[0.68rem] text-gray-500 leading-snug">
                                    <span class="lang-id">Derajat timbal balik Dosen ↔ Mahasiswa membuktikan secara empiris apakah interaksi bersifat kolaboratif ($R \ge 0.5$) atau direktif satu arah.</span>
                                    <span class="lang-en">Reciprocity index empirically proving whether supervision was collaborative ($R \ge 0.5$) or unidirectional.</span>
                                    <span class="lang-zh">互惠指数，科学实证师生沟通究竟属于双向协作还是单向指令主导。</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Part: Feature Highlights & Metrik Komparasi (1/3 width) --}}
            <div class="space-y-8">
                
                {{-- Quick Nav Switcher --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-[2.5rem] p-6 text-white border border-white/10 shadow-xl">
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 block mb-3">
                        <span class="lang-id">Versi Metodologi Tersedia</span>
                        <span class="lang-en">Available Methodology Versions</span>
                        <span class="lang-zh">可选方法论版本</span>
                    </span>
                    
                    <div class="space-y-2.5">
                        <a href="{{ route('methodology') }}" 
                           class="flex items-center justify-between p-3 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/5 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center">
                                    <i data-lucide="book-open" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-white block">
                                        <span class="lang-id">Metodologi V1</span>
                                        <span class="lang-en">Methodology V1</span>
                                        <span class="lang-zh">方法论 V1</span>
                                    </span>
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
                                    <span class="text-xs font-bold text-white block">
                                        <span class="lang-id">Metodologi V2 (Aktif)</span>
                                        <span class="lang-en">Methodology V2 (Active)</span>
                                        <span class="lang-zh">方法论 V2 (当前激活)</span>
                                    </span>
                                    <span class="text-[0.65rem] text-purple-200">Multi-Task RoBERTa & Graf</span>
                                </div>
                            </div>
                            <span class="text-[0.6rem] px-2 py-0.5 rounded-full bg-purple-500 text-white font-black uppercase tracking-wider">
                                <span class="lang-id">Saat ini</span>
                                <span class="lang-en">Current</span>
                                <span class="lang-zh">当前</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Fitur Unggulan V2 --}}
                <div class="bg-white rounded-[2.5rem] p-7 border border-gray-100 shadow-xl shadow-gray-200/30 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center">
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-sm font-black text-gray-900 uppercase tracking-wider">
                            <span class="lang-id">Fitur Unggulan V2</span>
                            <span class="lang-en">V2 Key Features</span>
                            <span class="lang-zh">V2 核心亮点</span>
                        </h4>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start gap-2.5">
                            <i data-lucide="zap" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">
                                    <span class="lang-id">Sintesis Otomatis (Zero-Click)</span>
                                    <span class="lang-en">Zero-Click Automated Synthesis</span>
                                    <span class="lang-zh">全自动免点击生成 (Zero-Click)</span>
                                </strong>
                                <span class="lang-id">Hasil graf interaksi dan bagan alur langsung dibuat otomatis tanpa perlu klik tombol lagi.</span>
                                <span class="lang-en">Interaction graphs and flowcharts are synthesized instantly without additional button clicks.</span>
                                <span class="lang-zh">交互拓扑网络与指导流程图即时全自动合成，无需额外手动触发。</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <i data-lucide="refresh-cw" class="w-4 h-4 text-blue-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">
                                    <span class="lang-id">Tukar Peran Pembicara</span>
                                    <span class="lang-en">Swap Speaker Roles</span>
                                    <span class="lang-zh">一键对调说话人身份</span>
                                </strong>
                                <span class="lang-id">Dapat membalik peran pembicara dalam satu klik jika deteksi peran dosen/mahasiswa tertukar.</span>
                                <span class="lang-en">Invert speaker labels with a single click if lecturer/student diarization is swapped.</span>
                                <span class="lang-zh">若角色分离判定反转，可一键快速互换导师与学生的发言标签。</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <i data-lucide="smartphone" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">
                                    <span class="lang-id">Detail Titik Graf di Layar Sentuh</span>
                                    <span class="lang-en">Mobile Touch-Friendly Nodes</span>
                                    <span class="lang-zh">移动端触控交互适配</span>
                                </strong>
                                <span class="lang-id">Titik graf dapat disentuh di HP untuk membuka kotak penjelasan detail tanpa terpotong layar.</span>
                                <span class="lang-en">Touch any graph node on smartphones to inspect details smoothly within view bounds.</span>
                                <span class="lang-zh">手机端轻触任意节点即可优雅弹出详情面板，绝无遮挡截断。</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <i data-lucide="shield-check" class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">
                                    <span class="lang-id">Pemuat Diagram Tahan Gangguan</span>
                                    <span class="lang-en">Fault-Tolerant Diagram Loader</span>
                                    <span class="lang-zh">容错自愈流程图渲染引擎</span>
                                </strong>
                                <span class="lang-id">Diagram alur tetap tampil stabil dengan jalur cadangan otomatis dan tombol <i>Coba Lagi</i>.</span>
                                <span class="lang-en">Flowcharts render reliably with automated fallback fallbacks and quick <i>Retry</i> capabilities.</span>
                                <span class="lang-zh">具备自动降级重试机制与<i>重试</i>按钮，确保图形渲染坚固稳定。</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <i data-lucide="globe" class="w-4 h-4 text-rose-500 shrink-0 mt-0.5"></i>
                            <div class="text-xs text-gray-600">
                                <strong class="text-gray-900 block">
                                    <span class="lang-id">Dukungan Multi-Bahasa</span>
                                    <span class="lang-en">Multi-Language Support</span>
                                    <span class="lang-zh">多语言无缝支持</span>
                                </strong>
                                <span class="lang-id">Tersedia dalam 3 pilihan bahasa yang mudah diganti (Indonesia, Inggris, dan Mandarin).</span>
                                <span class="lang-en">Available in 3 selectable languages (Indonesian, English, and Mandarin Chinese).</span>
                                <span class="lang-zh">提供印尼语、英语、中文三种语言随时灵活切换。</span>
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
                        <h4 class="text-base font-black uppercase tracking-tight">
                            <span class="lang-id">Coba Analisis Suara V2</span>
                            <span class="lang-en">Try Voice Analysis V2</span>
                            <span class="lang-zh">立即体验语音分析 V2</span>
                        </h4>
                        <p class="text-xs text-purple-200 mt-1">
                            <span class="lang-id">Uji coba langsung kemampuan analisis suara bimbingan pada rekaman bimbingan Anda.</span>
                            <span class="lang-en">Test the speech consultation analysis directly on your advisory recordings.</span>
                            <span class="lang-zh">立即使用真实的学术辅导录音体验多任务分析能力。</span>
                        </p>
                    </div>
                    <a href="{{ route('analysis.v2.create') }}" 
                       class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-white text-purple-900 font-black text-xs uppercase tracking-wider hover:bg-purple-50 transition shadow-lg">
                        <span>
                            <span class="lang-id">Buka Uji Coba V2</span>
                            <span class="lang-en">Launch V2 Analysis</span>
                            <span class="lang-zh">进入 V2 体验</span>
                        </span>
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
