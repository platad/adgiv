<x-layouts.app title="Dashboard">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">
        
        {{-- Welcome Banner Section --}}
        <div class="bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 text-white rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-gray-800 relative overflow-hidden mb-10 group">
            <!-- Sleek background radial glow -->
            <div class="absolute -right-32 -top-32 w-96 h-96 bg-bima-red/[0.15] rounded-full blur-[120px] group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>
            <div class="absolute -left-32 -bottom-32 w-96 h-96 bg-purple-500/[0.08] rounded-full blur-[120px] pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-bima-red/10 text-bima-red border border-bima-red/20 text-[0.6rem] font-black uppercase tracking-widest mb-4">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        <span class="lang-id">Sistem Bimbingan Akademik Cerdas</span>
                        <span class="lang-en">Intelligent Supervision System</span>
                        <span class="lang-zh">智能学术指导系统</span>
                    </span>
                    
                    <h1 class="text-2xl md:text-4xl font-black tracking-tight uppercase mb-3 text-white leading-tight">
                        <span class="lang-id">Selamat Datang, {{ Auth::user()->name }}</span>
                        <span class="lang-en">Welcome back, {{ Auth::user()->name }}</span>
                        <span class="lang-zh">欢迎您，{{ Auth::user()->name }}</span>
                    </h1>
                    
                    <p class="text-xs md:text-sm text-gray-300 font-medium max-w-2xl leading-relaxed">
                        <span class="lang-id">Selamat datang di <b>Pusat Analisis Komunikasi Bimbingan Akademik</b>. Analisis kualitas komunikasi bimbingan, petakan intonasi percakapan, dan dapatkan anotasi pedagogis untuk meningkatkan kualitas interaksi akademik Anda secara real-time.</span>
                        <span class="lang-en">Welcome to the <b>Academic Supervision Communication Analysis Hub</b>. Analyze the quality of supervision dialogue, map conversation intonations, and extract pedagogical annotations to elevate your academic interactions.</span>
                        <span class="lang-zh">欢迎使用 <b>学术指导沟通分析控制中心</b>。实时解构您的学术沟通质量，映射会话语调流动，并生成专业的教学法注解以持续优化师生互动的学术成效。</span>
                    </p>
                </div>
                
                <div class="shrink-0 w-full lg:w-auto">
                    <a href="{{ route('analysis.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-bima-red hover:bg-bima-red-dark text-white px-8 py-5 rounded-2xl shadow-lg shadow-red-500/20 transition-all hover:scale-105 hover:shadow-xl hover:shadow-red-500/30 group cursor-pointer border border-bima-red/30">
                        <i data-lucide="mic" class="w-5.5 h-5.5 group-hover:animate-pulse"></i>
                        <span class="font-bold text-base uppercase tracking-wider">
                            <span class="lang-id">Mulai Analisa Baru</span>
                            <span class="lang-en">Start New Analysis</span>
                            <span class="lang-zh">开始新语音分析</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Metrics Grid Section --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            {{-- Total Analisa --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-bima-red/10 text-bima-red flex items-center justify-center flex-shrink-0">
                    <i data-lucide="activity" class="w-7 h-7"></i>
                </div>
                <div>
                    <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400">
                        <span class="lang-id">Total Analisa Anda</span>
                        <span class="lang-en">Your Total Analyses</span>
                        <span class="lang-zh">您的分析总计</span>
                    </p>
                    <p class="text-2xl font-black text-gray-900">{{ $history->count() }}</p>
                </div>
            </div>

            {{-- Akurasi Global --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-circle" class="w-7 h-7"></i>
                </div>
                <div>
                    <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400">
                        <span class="lang-id">Akurasi Sistem Global</span>
                        <span class="lang-en">Global System Accuracy</span>
                        <span class="lang-zh">全局系统精确度</span>
                    </p>
                    <p class="text-2xl font-black text-gray-900">{{ $accuracyRate }}%</p>
                    <p class="text-[0.6rem] text-gray-400 font-medium leading-tight mt-0.5">
                        <span class="lang-id">Dari {{ $totalFeedbacks }} feedback pakar</span>
                        <span class="lang-en">From {{ $totalFeedbacks }} expert feedbacks</span>
                        <span class="lang-zh">基于 {{ $totalFeedbacks }} 位专家的评估反馈</span>
                    </p>
                </div>
            </div>

            {{-- Durasi Bimbingan --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="clock" class="w-7 h-7"></i>
                </div>
                <div>
                    <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400">
                        <span class="lang-id">Durasi Bimbingan</span>
                        <span class="lang-en">Supervision Time</span>
                        <span class="lang-zh">辅导总时长</span>
                    </p>
                    <p class="text-2xl font-black text-gray-900">
                        <span class="lang-id">{{ $totalDurationFormatted['id'] }}</span>
                        <span class="lang-en">{{ $totalDurationFormatted['en'] }}</span>
                        <span class="lang-zh">{{ $totalDurationFormatted['zh'] }}</span>
                    </p>
                </div>
            </div>

            {{-- Pola Dominan --}}
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4 hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="brain" class="w-7 h-7"></i>
                </div>
                <div>
                    <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400">
                        <span class="lang-id">Karakter Bimbingan AI</span>
                        <span class="lang-en">Dominant Style</span>
                        <span class="lang-zh">主导学术风格</span>
                    </p>
                    <p class="text-[0.95rem] font-black text-gray-900 leading-tight mt-1">
                        @if ($topCategory)
                            @php
                                $topCatClean = trim($topCategory);
                                $topCatMap = [
                                    'Saran Bimbingan Akademik' => ['id' => 'Saran Bimbingan', 'en' => 'Supervision Advice', 'zh' => '学术建议'],
                                    'Instruksi Direktif Dosen' => ['id' => 'Instruksi Direktif', 'en' => 'Directive Instr.', 'zh' => '指令性指示'],
                                    'Saran Akademik Terarah' => ['id' => 'Saran Terarah', 'en' => 'Structured Advice', 'zh' => '定向建议'],
                                    'Bimbingan Bertahap' => ['id' => 'Bimbingan Bertahap', 'en' => 'Step-by-step', 'zh' => '渐进式指导']
                                ];
                                $trans = $topCatMap[$topCatClean] ?? ['id' => $topCatClean, 'en' => $topCatClean, 'zh' => $topCatClean];
                            @endphp
                            <span class="lang-id">{{ $trans['id'] }}</span>
                            <span class="lang-en">{{ $trans['en'] }}</span>
                            <span class="lang-zh">{{ $trans['zh'] }}</span>
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Realtime Chart Section --}}
        <div class="grid grid-cols-1 gap-8 mb-10">
            <div class="bg-white rounded-[2.5rem] p-6 md:p-8 text-gray-900 border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="absolute -top-40 -right-40 w-96 h-96 bg-bima-red/[0.03] rounded-full blur-[120px] pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-base font-black uppercase tracking-widest flex items-center gap-2">
                            <span class="lang-id">Monitor Evaluasi Kalimat (Realtime)</span>
                            <span class="lang-en">Sentence Evaluation Monitor (Real-time)</span>
                            <span class="lang-zh">句子级实时评估监控面板</span>
                        </h2>
                        <p class="text-xs text-gray-500 mt-1 font-medium">
                            <span class="lang-id">Metrik tingkat akurasi klasifikasi bimbingan Supervisory AI per kalimat percakapan secara global.</span>
                            <span class="lang-en">Global classification accuracy metric of Supervisory AI per conversational sentence.</span>
                            <span class="lang-zh">Supervisory AI 核心算法在单句会话分类中的全局系统精确度度量标准。</span>
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-2xl px-4 py-2.5">
                        <div class="text-right">
                            <span class="block text-[0.55rem] font-bold text-gray-500 uppercase tracking-widest">
                                <span class="lang-id">Rata-rata Akurasi</span>
                                <span class="lang-en">Average Accuracy</span>
                                <span class="lang-zh">平均精确度</span>
                            </span>
                            <span class="text-xl font-black text-gray-900" id="accuracyPercentageText">{{ $sentenceAccuracy }}%</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-bima-red/10 text-bima-red flex items-center justify-center">
                            <i data-lucide="trending-up" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>

                <div class="h-64 md:h-80 w-full relative">
                    <canvas id="realtimeAccuracyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Communication Distribution & Relationship Dynamics --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            <!-- Doughnut Chart: Advice Category -->
            <div class="bg-white p-6 md:p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="absolute -top-40 -left-40 w-96 h-96 bg-bima-red/[0.02] rounded-full blur-[120px] pointer-events-none"></div>
                
                <div class="relative z-10 mb-6 flex justify-between items-start gap-4">
                    <div>
                        <h3 class="text-base font-black uppercase tracking-widest flex items-center gap-2">
                            <i data-lucide="pie-chart" class="w-5 h-5 text-bima-red"></i>
                            <span class="lang-id">Distribusi Kategori Bimbingan</span>
                            <span class="lang-en">Guidance Category Distribution</span>
                            <span class="lang-zh">辅导建议类别分布</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium leading-relaxed">
                            <span class="lang-id">Persentase fokus komunikasi saran bimbingan akademik yang terdeteksi AI.</span>
                            <span class="lang-en">Percentage distribution of the advice-giving categories classified by AI.</span>
                            <span class="lang-zh">AI 自动识别并归类的学术指导话语特征类别比例分布。</span>
                        </p>
                    </div>
                </div>

                <div class="relative w-full h-64 md:h-72 flex justify-center items-center">
                    <canvas id="adviceCategoryChart"></canvas>
                </div>
            </div>

            <!-- Bar Chart: Relationship Character -->
            <div class="bg-white p-6 md:p-8 rounded-[2.5rem] border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="absolute -top-40 -right-40 w-96 h-96 bg-purple-500/[0.02] rounded-full blur-[120px] pointer-events-none"></div>
                
                <div class="relative z-10 mb-6 flex justify-between items-start gap-4">
                    <div>
                        <h3 class="text-base font-black uppercase tracking-widest flex items-center gap-2">
                            <i data-lucide="bar-chart-3" class="w-5 h-5 text-purple-600"></i>
                            <span class="lang-id">Karakter Relasi Akademik</span>
                            <span class="lang-en">Academic Relationship Dynamics</span>
                            <span class="lang-zh">师生学术关系动态</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-1 font-medium leading-relaxed">
                            <span class="lang-id">Analisis kecenderungan dinamika kekuasaan dan kemitraan dalam pembicaraan.</span>
                            <span class="lang-en">Analysis of power dynamics and collaborative structures detected in discourse.</span>
                            <span class="lang-zh">对话语篇中所表现出的权力平衡与学术合作极性倾向度量。</span>
                        </p>
                    </div>
                </div>

                <div class="relative w-full h-64 md:h-72 flex justify-center items-center">
                    <canvas id="relationDynamicsChart"></canvas>
                </div>
            </div>
        </div>

        {{-- History Table Card --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden" 
             x-data="{ 
                 items: [], 
                 currentPage: 1, 
                 lastPage: 1, 
                 total: 0, 
                 loading: false,
                 showDeleteModal: false,
                 deleteTargetId: null,
                 deleteTargetTitle: '',
                 deleting: false,
                 
                  async fetchHistory(page = 1) {
                     this.loading = true;
                     try {
                         let res = await fetch(`/{{ app()->getLocale() }}/dashboard/history-data?page=${page}`);
                         let data = await res.json();
                         this.items = data.items;
                         this.currentPage = data.current_page;
                         this.lastPage = data.last_page;
                         this.total = data.total;
                     } catch (e) {
                         console.error(e);
                     } finally {
                         this.loading = false;
                     }
                 },

                 confirmDelete(id, title) {
                     this.deleteTargetId = id;
                     this.deleteTargetTitle = title;
                     this.showDeleteModal = true;
                 },

                 async deleteAnalysis() {
                     if (this.deleting) return;
                     this.deleting = true;
                     try {
                         let res = await fetch(`/{{ app()->getLocale() }}/analysis/${this.deleteTargetId}`, {
                             method: 'DELETE',
                             headers: {
                                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                 'Content-Type': 'application/json'
                             }
                         });
                         let data = await res.json();
                         if (data.status === 'success') {
                             this.showDeleteModal = false;
                             let nextPage = this.currentPage;
                             if (this.items.length === 1 && this.currentPage > 1) {
                                 nextPage--;
                             }
                             this.fetchHistory(nextPage);
                         }
                     } catch (e) {
                         console.error(e);
                     } finally {
                         this.deleting = false;
                     }
                 }
             }"
             x-init="fetchHistory(1)">
            
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-sm font-black uppercase tracking-widest text-gray-800">
                    <span class="lang-id">Riwayat Analisa</span>
                    <span class="lang-en">Analysis History</span>
                    <span class="lang-zh">语音分析历史记录</span>
                </h2>
            </div>
            
            <!-- Skeleton Loader -->
            <div x-show="loading && items.length === 0" class="p-6 space-y-4" style="display: none;">
                <div class="h-10 bg-gray-100 rounded-xl animate-pulse w-full"></div>
                <div class="h-10 bg-gray-100 rounded-xl animate-pulse w-full"></div>
                <div class="h-10 bg-gray-100 rounded-xl animate-pulse w-full"></div>
            </div>

            <!-- Empty State -->
            <div x-show="!loading && total === 0" class="p-12 text-center" style="display: none;">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5M9 5h6"/></svg>
                </div>
                <p class="text-gray-500 font-medium">
                    <span class="lang-id">Belum ada riwayat analisa.</span>
                    <span class="lang-en">No analysis history found.</span>
                    <span class="lang-zh">暂无学术分析历史记录。</span>
                </p>
            </div>

            <!-- Table View (Desktop Only) -->
            <div x-show="total > 0" class="hidden sm:block overflow-x-auto" style="display: none;">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50 text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">
                            <th class="p-6">
                                <span class="lang-id">Judul Sesi</span>
                                <span class="lang-en">Session Title</span>
                                <span class="lang-zh">会话标题</span>
                            </th>
                            <th class="p-6">
                                <span class="lang-id">Tanggal Analisis</span>
                                <span class="lang-en">Analysis Date</span>
                                <span class="lang-zh">分析日期</span>
                            </th>
                            <th class="p-6">
                                <span class="lang-id">Status</span>
                                <span class="lang-en">Status</span>
                                <span class="lang-zh">状态</span>
                            </th>
                            <th class="p-6 text-right">
                                <span class="lang-id">Aksi</span>
                                <span class="lang-en">Actions</span>
                                <span class="lang-zh">操作</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="item in items" :key="item.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <!-- Judul -->
                                <td class="p-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-950 block text-sm" x-text="item.title"></span>
                                            <span class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider block sm:hidden" x-text="item.created_at_formatted"></span>
                                        </div>
                                    </div>
                                </td>
                                <!-- Tanggal -->
                                <td class="p-6 hidden sm:table-cell text-sm font-medium text-gray-500" x-text="item.created_at_formatted"></td>
                                <!-- Status -->
                                <td class="p-6">
                                    <span class="px-2.5 py-1 rounded-full text-[0.6rem] font-black uppercase tracking-wider inline-block border"
                                          :class="{
                                              'bg-green-50 text-green-700 border-green-100': item.status === 'completed',
                                              'bg-amber-50 text-amber-700 border-amber-100': item.status === 'pending' || item.status === 'processing',
                                              'bg-red-50 text-red-700 border-red-100': item.status === 'failed'
                                          }">
                                        <span class="lang-id" x-text="item.status === 'completed' ? 'Selesai' : (item.status === 'processing' ? 'Memproses' : (item.status === 'failed' ? 'Gagal' : 'Menunggu'))"></span>
                                        <span class="lang-en" x-text="item.status === 'completed' ? 'Completed' : (item.status === 'processing' ? 'Processing' : (item.status === 'failed' ? 'Failed' : 'Pending'))"></span>
                                        <span class="lang-zh" x-text="item.status === 'completed' ? '已完成' : (item.status === 'processing' ? '处理中' : (item.status === 'failed' ? '分析失败' : '排队中'))"></span>
                                    </span>
                                </td>
                                <!-- Aksi -->
                                <td class="p-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Lihat Hasil -->
                                        <template x-if="item.status === 'completed'">
                                            <a :href="item.result_route" class="px-3.5 py-1.5 bg-gray-950 hover:bg-gray-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                                                <span class="lang-id">Hasil</span>
                                                <span class="lang-en">Result</span>
                                                <span class="lang-zh">查看结果</span>
                                            </a>
                                        </template>
                                        <!-- Lanjutkan -->
                                        <template x-if="item.status === 'pending' || item.status === 'processing'">
                                            <a :href="item.processing_route" class="px-3.5 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                                                <span class="lang-id">Lanjutkan</span>
                                                <span class="lang-en">Resume</span>
                                                <span class="lang-zh">继续分析</span>
                                            </a>
                                        </template>
                                        
                                        <!-- Hapus Button -->
                                        <button @click="confirmDelete(item.id, item.title)" class="p-2 hover:bg-red-50 text-gray-400 hover:text-red-600 rounded-xl transition-all cursor-pointer" title="Hapus Analisis">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (Mobile Only) -->
            <div x-show="total > 0" class="block sm:hidden divide-y divide-gray-100" style="display: none;">
                <template x-for="item in items" :key="item.id">
                    <div class="p-5 flex flex-col gap-4 hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-950 block text-sm" x-text="item.title"></span>
                                    <span class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mt-0.5 block" x-text="item.created_at_formatted"></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[0.6rem] font-black uppercase tracking-wider inline-block border flex-shrink-0"
                                  :class="{
                                      'bg-green-50 text-green-700 border-green-100': item.status === 'completed',
                                      'bg-amber-50 text-amber-700 border-amber-100': item.status === 'pending' || item.status === 'processing',
                                      'bg-red-50 text-red-700 border-red-100': item.status === 'failed'
                                  }">
                                <span class="lang-id" x-text="item.status === 'completed' ? 'Selesai' : (item.status === 'processing' ? 'Memproses' : (item.status === 'failed' ? 'Gagal' : 'Menunggu'))"></span>
                                <span class="lang-en" x-text="item.status === 'completed' ? 'Completed' : (item.status === 'processing' ? 'Processing' : (item.status === 'failed' ? 'Failed' : 'Pending'))"></span>
                                <span class="lang-zh" x-text="item.status === 'completed' ? '已完成' : (item.status === 'processing' ? '处理中' : (item.status === 'failed' ? '分析失败' : '排队中'))"></span>
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between border-t border-gray-50 pt-3">
                            <button @click="confirmDelete(item.id, item.title)" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-all flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span class="lang-id">Hapus</span>
                                <span class="lang-en">Delete</span>
                                <span class="lang-zh">删除</span>
                            </button>
                            
                            <div class="flex items-center gap-2">
                                <!-- Lihat Hasil -->
                                <template x-if="item.status === 'completed'">
                                    <a :href="item.result_route" class="px-4 py-2 bg-gray-950 hover:bg-gray-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                                        <span class="lang-id">Hasil</span>
                                        <span class="lang-en">Result</span>
                                        <span class="lang-zh">查看结果</span>
                                    </a>
                                </template>
                                <!-- Lanjutkan -->
                                <template x-if="item.status === 'pending' || item.status === 'processing'">
                                    <a :href="item.processing_route" class="px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                                        <span class="lang-id">Lanjutkan</span>
                                        <span class="lang-en">Resume</span>
                                        <span class="lang-zh">继续分析</span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Dynamic Pagination Footer -->
            <div x-show="lastPage > 1" class="p-6 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-4" style="display: none;">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                    <span class="lang-id">Halaman <span x-text="currentPage"></span> dari <span x-text="lastPage"></span> (<span x-text="total"></span> Sesi)</span>
                    <span class="lang-en">Page <span x-text="currentPage"></span> of <span x-text="lastPage"></span> (<span x-text="total"></span> Sessions)</span>
                    <span class="lang-zh">第 <span x-text="currentPage"></span> 页，共 <span x-text="lastPage"></span> 页 (共 <span x-text="total"></span> 个会话)</span>
                </span>
                <div class="flex items-center gap-1.5">
                    <!-- Prev -->
                    <button @click="currentPage > 1 && fetchHistory(currentPage - 1)" 
                            :disabled="currentPage === 1"
                            class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-xs font-black uppercase tracking-wider disabled:opacity-40 disabled:hover:bg-transparent transition-all cursor-pointer">
                        <span class="lang-id">Sebelumnya</span>
                        <span class="lang-en">Previous</span>
                        <span class="lang-zh">上一页</span>
                    </button>
                    
                    <!-- Next -->
                    <button @click="currentPage < lastPage && fetchHistory(currentPage + 1)" 
                            :disabled="currentPage === lastPage"
                            class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-xs font-black uppercase tracking-wider disabled:opacity-40 disabled:hover:bg-transparent transition-all cursor-pointer">
                        <span class="lang-id">Berikutnya</span>
                        <span class="lang-en">Next</span>
                        <span class="lang-zh">下一页</span>
                    </button>
                </div>
            </div>

            {{-- Confirmation Delete Modal (Sleek Blur & Scale Transition) --}}
            <div x-show="showDeleteModal" 
                 class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none"
                 style="display: none;">
                <!-- Backdrop overlay -->
                <div x-show="showDeleteModal"
                     @click="showDeleteModal = false"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>

                <!-- Modal Dialog Card -->
                <div x-show="showDeleteModal"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="relative w-full max-w-md mx-auto my-6 z-50 bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 p-8 text-center">
                    
                    <!-- Warning Icon -->
                    <div class="w-16 h-16 bg-red-50 text-red-600 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>

                    <h3 class="text-xl font-black text-gray-950 uppercase tracking-wide mb-3">
                        <span class="lang-id">Hapus Analisis?</span>
                        <span class="lang-en">Delete Analysis?</span>
                        <span class="lang-zh">彻底删除此分析？</span>
                    </h3>
                    <p class="text-sm text-gray-500 font-medium leading-relaxed mb-8">
                        <span class="lang-id">Apakah Anda yakin ingin menghapus sesi bimbingan <span class="font-bold text-gray-950" x-text="deleteTargetTitle"></span> ini? Seluruh data transkripsi dan rekaman audio akan dihapus selamanya.</span>
                        <span class="lang-en">Are you sure you want to delete the academic session <span class="font-bold text-gray-950" x-text="deleteTargetTitle"></span>? All transcription and audio records will be permanently erased.</span>
                        <span class="lang-zh">您确定要永久删除学术辅导会话 <span class="font-bold text-gray-950" x-text="deleteTargetTitle"></span> 吗？所有转译文本及声波切片将被彻底物理抹除。</span>
                    </p>

                    <div class="flex gap-3 justify-center">
                        <button @click="showDeleteModal = false" class="px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold text-sm uppercase tracking-wider rounded-2xl transition-all cursor-pointer">
                            <span class="lang-id">Batal</span>
                            <span class="lang-en">Cancel</span>
                            <span class="lang-zh">取消</span>
                        </button>
                        <button @click="deleteAnalysis()" class="px-6 py-3 bg-red-600 hover:bg-red-500 text-white font-bold text-sm uppercase tracking-wider rounded-2xl transition-all hover:scale-105 flex items-center justify-center gap-2 cursor-pointer">
                            <span x-show="!deleting">
                                <span class="lang-id">Hapus Permanen</span>
                                <span class="lang-en">Delete Permanently</span>
                                <span class="lang-zh">永久删除</span>
                            </span>
                            <span x-show="deleting" class="flex items-center gap-1.5" style="display: none;">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="lang-id">Menghapus...</span>
                                <span class="lang-en">Deleting...</span>
                                <span class="lang-zh">正在删除...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script for Chart.js Realtime logic --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('realtimeAccuracyChart').getContext('2d');
            
            // Create gradient fill
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(204, 0, 0, 0.25)');
            gradient.addColorStop(0.5, 'rgba(204, 0, 0, 0.05)');
            gradient.addColorStop(1, 'rgba(204, 0, 0, 0.0)');

            const activeLang = '{{ app()->getLocale() }}';
            let datasetLabel = 'Tingkat Akurasi (%)';
            if (activeLang === 'en') {
                datasetLabel = 'Accuracy Level (%)';
            } else if (activeLang === 'zh') {
                datasetLabel = '系统精确度 (%)';
            }

            const config = {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: datasetLabel,
                        data: [],
                        borderColor: '#cc0000',
                        borderWidth: 3,
                        pointBackgroundColor: '#cc0000',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 0, // Remove bullet points on line
                        pointHoverRadius: 6,
                        pointHitRadius: 10,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: gradient,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(17, 24, 39, 0.95)',
                            titleColor: '#ffffff',
                            bodyColor: '#e5e7eb',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    if (activeLang === 'en') return `Accuracy: ${context.parsed.y}%`;
                                    if (activeLang === 'zh') return `精确度: ${context.parsed.y}%`;
                                    return `Akurasi: ${context.parsed.y}%`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.03)',
                                borderColor: 'transparent'
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            min: 60,
                            max: 100,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.03)',
                                borderColor: 'transparent'
                            },
                            ticks: {
                                color: '#6b7280',
                                stepSize: 10,
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                },
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            };

            const chart = new Chart(ctx, config);

            // Fetch actual database records
            async function updateChartWithRealData() {
                try {
                    let res = await fetch('{{ route('dashboard.realtime-data') }}');
                    let result = await res.json();
                    
                    if (result.labels && result.data) {
                        chart.data.labels = result.labels;
                        chart.data.datasets[0].data = result.data;
                        chart.update('none'); // smooth redraw without layout flash

                        // Dynamic percentage update in corner
                        const currentBadge = document.getElementById('accuracyPercentageText');
                        if (currentBadge) {
                            currentBadge.textContent = result.current_accuracy + '%';
                        }
                    }
                } catch (e) {
                    console.error("Gagal memuat data grafik realtime:", e);
                }
            }

            // --- NEW: Aggregated Statistics Charts (Category & Relation) ---
            const categoriesMap = {
                'Saran Bimbingan Akademik': {
                    id: 'Saran Bimbingan Akademik',
                    en: 'Academic Supervision Advice',
                    zh: '学术指导建议'
                },
                'Instruksi Direktif Dosen': {
                    id: 'Instruksi Direktif Dosen',
                    en: 'Directive Supervisor Instruction',
                    zh: '导师指令性指示'
                },
                'Saran Akademik Terarah': {
                    id: 'Saran Akademik Terarah',
                    en: 'Structured Academic Advice',
                    zh: '定向学术建议'
                },
                'Bimbingan Bertahap': {
                    id: 'Bimbingan Bertahap',
                    en: 'Step-by-Step Guidance',
                    zh: '渐进式指导'
                }
            };

            const relationsMap = {
                'Relasi Kuasa Direktif Dosen': {
                    id: 'Relasi Kuasa Direktif Dosen',
                    en: 'Directive Supervisor Power Relation',
                    zh: '导师指令性权力关系'
                },
                'Koperatif & Dialogis': {
                    id: 'Koperatif & Dialogis',
                    en: 'Collaborative & Dialogic',
                    zh: '合作与对话'
                },
                'Dialogis Kondusif': {
                    id: 'Dialogis Kondusif',
                    en: 'Conducive Dialogic',
                    zh: '建设性对话'
                },
                'Kondusif & Seimbang': {
                    id: 'Kondusif & Seimbang',
                    en: 'Conducive & Balanced',
                    zh: '建设与平衡'
                },
                'Power-maintaining (Keseimbangan Kuasa)': {
                    id: 'Keseimbangan Kuasa (Power-maintaining)',
                    en: 'Power-maintaining Balance',
                    zh: '维持权力平衡'
                }
            };

            const rawCategories = @json($categoriesCount);
            const rawRelations = @json($relationsCount);

            const categoriesLabels = Object.keys(rawCategories).map(key => {
                const cleanKey = key.trim();
                return categoriesMap[cleanKey] ? categoriesMap[cleanKey][activeLang] || cleanKey : cleanKey;
            });
            const categoriesData = Object.values(rawCategories);

            const relationsLabels = Object.keys(rawRelations).map(key => {
                const cleanKey = key.trim();
                return relationsMap[cleanKey] ? relationsMap[cleanKey][activeLang] || cleanKey : cleanKey;
            });
            const relationsData = Object.values(rawRelations);

            // 1. Doughnut Chart: Advice Categories
            const catCtx = document.getElementById('adviceCategoryChart').getContext('2d');
            new Chart(catCtx, {
                type: 'doughnut',
                data: {
                    labels: categoriesLabels,
                    datasets: [{
                        data: categoriesData,
                        backgroundColor: [
                            '#cc0000', // BIMA Red
                            'rgba(204, 0, 0, 0.7)',
                            'rgba(249, 115, 22, 0.85)', // Amber/Orange
                            'rgba(31, 41, 55, 0.85)', // Dark gray
                            'rgba(147, 51, 234, 0.85)' // Purple
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#4b5563',
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                },
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.95)',
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: true
                        }
                    },
                    cutout: '60%'
                }
            });

            // 2. Bar Chart: Relation Dynamics
            const relCtx = document.getElementById('relationDynamicsChart').getContext('2d');
            new Chart(relCtx, {
                type: 'bar',
                data: {
                    labels: relationsLabels,
                    datasets: [{
                        data: relationsData,
                        backgroundColor: 'rgba(147, 51, 234, 0.85)', // Purple
                        hoverBackgroundColor: '#9333ea',
                        borderRadius: 12,
                        borderWidth: 0,
                        barPercentage: 0.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.95)',
                            padding: 10,
                            cornerRadius: 10
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 9,
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.03)',
                                borderColor: 'transparent'
                            },
                            ticks: {
                                color: '#6b7280',
                                stepSize: 1,
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });

            // Initial load
            updateChartWithRealData();

            // Periodically refresh the data from the database every 5 seconds
            setInterval(updateChartWithRealData, 5000);
        });
    </script>
</x-layouts.app>
