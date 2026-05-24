<x-layouts.app title="Processing Analysis">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 min-h-screen flex flex-col items-center justify-center animate-fade-in"
        x-data="processingFlow('{{ route('analysis.process', $analysis->id) }}')">

        <div
            class="bg-white p-10 md:p-16 rounded-[3rem] shadow-2xl shadow-gray-200/50 text-center w-full max-w-2xl border border-gray-100 animate-scale-up">

            {{-- Dynamic Icon Container --}}
            <div class="relative w-24 h-24 mx-auto mb-10">
                <div class="absolute inset-0 bg-bima-red text-white rounded-full flex items-center justify-center shadow-lg shadow-red-500/20 z-10 p-5">
                    <x-application-logo class="w-full h-full animate-pulse text-white" />
                </div>
                <div class="absolute inset-0 border-4 border-dashed border-bima-red/20 rounded-full animate-spin"></div>
            </div>

            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mb-4">
                <span class="lang-id">Supervisory AI Sedang Bekerja</span>
                <span class="lang-en">Supervisory AI is Working</span>
                <span class="lang-zh">Supervisory AI 正在深度处理中</span>
            </h1>
            <p class="text-gray-500 font-medium mb-8">
                <span class="lang-id">Memproses sesi <span class="font-bold text-gray-900">"{{ $analysis->title }}"</span></span>
                <span class="lang-en">Processing session <span class="font-bold text-gray-900">"{{ $analysis->title }}"</span></span>
                <span class="lang-zh">正在计算分析会话：<span class="font-bold text-gray-900">"{{ $analysis->title }}"</span></span>
            </p>

            {{-- Step Indicators --}}
            <div class="space-y-4 max-w-md mx-auto text-left">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex flex-col transition-all duration-500"
                        :class="{ 'opacity-100 scale-100': currentStep >= index, 'opacity-40 scale-95': currentStep < index }">
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center transition-colors duration-300"
                                :class="{
                                    'bg-green-100 text-green-600': currentStep > index,
                                    'bg-bima-red text-white animate-pulse': currentStep === index,
                                    'bg-gray-100 text-gray-400': currentStep < index
                                }">
                                {{-- Check Icon (Completed) --}}
                                <svg x-show="currentStep > index" class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>

                                {{-- Spinner Icon (Active) --}}
                                <svg x-show="currentStep === index" class="w-4 h-4 animate-spin" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>

                                <span class="text-xs font-bold" x-show="currentStep < index" x-text="index + 1"></span>
                            </div>
                            <span class="text-sm font-bold uppercase tracking-wider transition-colors duration-300"
                                :class="{ 'text-gray-900': currentStep >= index, 'text-gray-400': currentStep < index }"
                                x-text="step.title"></span>
                        </div>

                        {{-- Sub steps --}}
                        <div x-show="currentStep >= index" 
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mt-3 space-y-2 ml-10 border-l border-gray-100 pl-4 py-1">
                            <template x-for="(subStep, subIndex) in step.subSteps" :key="subIndex">
                                <div class="flex items-center gap-2.5 text-xs font-semibold transition-all duration-300"
                                     :class="{
                                         'text-gray-400': currentStep > index || (currentStep === index && currentSubStep > subIndex),
                                         'text-bima-red animate-pulse scale-[1.02] origin-left': currentStep === index && currentSubStep === subIndex,
                                         'text-gray-300 opacity-60': currentStep === index && currentSubStep < subIndex
                                     }">
                                    
                                    {{-- Sub-step Status Indicator --}}
                                    <div class="flex-shrink-0 flex items-center justify-center w-4 h-4">
                                        <!-- Completed Sub-step: Green Check -->
                                        <svg x-show="currentStep > index || (currentStep === index && currentSubStep > subIndex)" class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        
                                        <!-- Active Sub-step: Spinner -->
                                        <svg x-show="currentStep === index && currentSubStep === subIndex" class="w-3.5 h-3.5 text-bima-red animate-spin" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        
                                        <!-- Future Sub-step: Small Dot -->
                                        <div x-show="currentStep === index && currentSubStep < subIndex" class="w-1.5 h-1.5 rounded-full bg-gray-300 mx-1"></div>
                                    </div>
                                    
                                    <span x-text="subStep" class="leading-relaxed"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Error Message Box --}}
            <div x-show="error" style="display: none;"
                class="mt-8 p-6 bg-red-50 border border-red-100 rounded-3xl text-red-600 text-sm font-semibold animate-shake text-center">
                <div class="flex items-center justify-center gap-2 mb-3">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="text-base font-black uppercase tracking-tight text-gray-900">
                        <span class="lang-id">Gagal Memproses Hasil</span>
                        <span class="lang-en">Failed to Process Results</span>
                        <span class="lang-zh">计算融合诊断结果失败</span>
                    </span>
                </div>
                <p class="text-gray-600 mb-6 leading-relaxed" x-text="error"></p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    {{-- Retry Synthesis Button --}}
                    <button type="button" @click="handleRetry" class="w-full sm:w-auto bg-bima-red hover:bg-red-700 text-white font-bold px-6 py-3 rounded-xl transition-all shadow-md shadow-red-500/10 cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="lang-id">Ulangi Proses Sintesis</span>
                        <span class="lang-en">Retry Synthesis Process</span>
                        <span class="lang-zh">重试融合计算</span>
                    </button>
                    
                    {{-- Go Back / Upload New File --}}
                    <a href="{{ route('analysis.create') }}" class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-6 py-3 rounded-xl transition-all text-center">
                        <span class="lang-id">Upload Ulang File</span>
                        <span class="lang-en">Re-upload File</span>
                        <span class="lang-zh">重新上传文件</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script>
        const activeLang = '{{ app()->getLocale() }}';
        document.addEventListener('alpine:init', () => {
            Alpine.data('processingFlow', (processUrl) => ({
                steps: (activeLang === 'zh') ? [
                    {
                        title: '整合分析切片',
                        subSteps: [
                            '正在读取所有已分析的音频分段数据...',
                            '正在按时间线对齐时间戳记录...'
                        ]
                    },
                    {
                        title: 'GPT-4o 智能体融合计算',
                        subSteps: [
                            '正在初始化 GPT-4o 深度融合智能体...',
                            '正在对重叠区域进行文本去重处理...',
                            '正在优化和润色学术话语语义转译...'
                        ]
                    },
                    {
                        title: '同步 C-CDA 语义参数',
                        subSteps: [
                            '正在构建 Advice-Giving 建议特征图谱...',
                            '正在测算学术话语支配性权力指数 (Power Relation)...',
                            '正在分类学术建议策略维度 (指令性/启发性)...'
                        ]
                    },
                    {
                        title: '计算系统性能度量',
                        subSteps: [
                            '正在测算本会话的主观声学分类置信度...',
                            '正在计算系统专家标定 Cohen\'s Kappa 一致性系数...'
                        ]
                    },
                    {
                        title: '构建可视化仪表板',
                        subSteps: [
                            '正在归纳整理 Supervisory AI 核心诊断报告...',
                            '正在初始化交互式多模态分析图表...'
                        ]
                    }
                ] : ((activeLang === 'en') ? [
                    {
                        title: 'Compiling Analysis Chunks',
                        subSteps: [
                            'Reading analyzed audio segment records...',
                            'Aligning conversational timestamps chronologically...'
                        ]
                    },
                    {
                        title: 'GPT-4o Agent Merging Results',
                        subSteps: [
                            'Initializing GPT-4o synthesis agent...',
                            'Performing sequence text deduplication in overlap regions...',
                            'Polishing and structuring academic discourse transcription...'
                        ]
                    },
                    {
                        title: 'Synchronizing C-CDA Elements',
                        subSteps: [
                            'Structuring advice-giving classification map...',
                            'Calculating conversational power dynamics index...',
                            'Extracting academic advice types (Directive/Corrective)...'
                        ]
                    },
                    {
                        title: 'Evaluating Academic Metrics',
                        subSteps: [
                            'Evaluating precision rates and speech confidence levels...',
                            'Calculating experts annotations Cohen\'s Kappa agreement rate...'
                        ]
                    },
                    {
                        title: 'Assembling Results Dashboard',
                        subSteps: [
                            'Compiling Supervisory AI diagnostic summaries...',
                            'Preparing dynamic multi-modal data visualizations...'
                        ]
                    }
                ] : [
                    {
                        title: 'Mengompilasi Potongan Analisis',
                        subSteps: [
                            'Membaca data dari segmen audio teranalisis...',
                            'Menyelaraskan stempel waktu (timestamps) secara kronologis...'
                        ]
                    },
                    {
                        title: 'Agen AI GPT-4o Menggabungkan Hasil',
                        subSteps: [
                            'Menginisialisasi Agen Sintesis GPT-4o...',
                            'Menghapus tumpang-tindih teks (deduplication) pada wilayah overlap...',
                            'Penyuntingan tata bahasa wacana bimbingan akademik...'
                        ]
                    },
                    {
                        title: 'Sinkronisasi Elemen C-CDA',
                        subSteps: [
                            'Menyusun peta klasifikasi Advice-Giving...',
                            'Menghitung indeks dinamika kuasa (Power Relation)...',
                            'Mengekstrak tipe saran bimbingan (Direktif/Korektif)...'
                        ]
                    },
                    {
                        title: 'Evaluasi Metrik Akademik',
                        subSteps: [
                            'Menghitung tingkat akurasi model vokal secara presisi...',
                            'Melakukan kalkulasi nilai kesesuaian Cohen\'s Kappa...'
                        ]
                    },
                    {
                        title: 'Menyusun Dashboard Hasil',
                        subSteps: [
                            'Mengompilasi interpretasi akhir Supervisory AI...',
                            'Mempersiapkan visualisasi grafik interaktif...'
                        ]
                    }
                ]),
                currentStep: 0,
                currentSubStep: 0,
                error: null,
                isCompleted: false,
                visualInterval: null,

                init() {
                    this.startVisualSteps();
                    this.triggerAnalysis();
                },

                startVisualSteps() {
                    if (this.visualInterval) clearInterval(this.visualInterval);
                    this.visualInterval = setInterval(() => {
                        if (this.error || this.isCompleted) {
                            clearInterval(this.visualInterval);
                            return;
                        }

                        const activeStep = this.steps[this.currentStep];
                        if (activeStep && activeStep.subSteps && this.currentSubStep < activeStep.subSteps.length - 1) {
                            this.currentSubStep++;
                        } else {
                            if (this.currentStep < 4) {
                                this.currentStep++;
                                this.currentSubStep = 0;
                            } else {
                                clearInterval(this.visualInterval);
                            }
                        }
                    }, 1800); // Progress smoothly every 1.8s
                },

                handleRetry() {
                    this.error = null;
                    this.currentStep = 0;
                    this.currentSubStep = 0;
                    this.isCompleted = false;
                    this.startVisualSteps();
                    this.triggerAnalysis();
                },

                async triggerAnalysis() {
                    try {
                        const response = await fetch(processUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok && data.status === 'success') {
                            this.isCompleted = true;
                            // Jump instantly to completed state
                            this.currentStep = 4;
                            this.currentSubStep = 1;
                            
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 800);
                        } else {
                            this.error = data.message || (activeLang === 'zh' ? '智能体融合数据时服务器发生错误。' : (activeLang === 'en' ? 'Server error occurred during AI agent synthesis.' : 'Terjadi kesalahan sistem saat penggabungan oleh Agen AI.'));
                            this.currentStep = -1;
                        }
                    } catch (err) {
                        this.error = (activeLang === 'zh' ? '网络连接已断开，请检查您的服务器链路状态。' : (activeLang === 'en' ? 'Network connection lost. Please check your server status.' : 'Koneksi terputus. Silakan periksa jaringan server Anda.'));
                        this.currentStep = -1;
                    }
                }
            }));
        });
    </script>
</x-layouts.app>
