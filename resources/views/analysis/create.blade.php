<x-layouts.app title="Input Analisa">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 animate-fade-in" x-data="audioUploader()">
        
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> 
                <span class="lang-id">Kembali ke Dashboard</span>
                <span class="lang-en">Back to Dashboard</span>
                <span class="lang-zh">返回控制面板</span>
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">
                <span class="lang-id">Mulai Analisa Baru</span>
                <span class="lang-en">Start New Analysis</span>
                <span class="lang-zh">开始新语音分析</span>
            </h1>
            <p class="text-gray-500 font-medium mt-2">
                <span class="lang-id">Unggah file audio percakapan bimbingan akademik Anda (Maksimal 50MB / format MP3, WAV, M4A, WEBM, AAC, dll).</span>
                <span class="lang-en">Upload your academic supervision dialogue audio file (Max 50MB / MP3, WAV, M4A, WEBM, AAC formats, etc).</span>
                <span class="lang-zh">上传您的学术指导沟通录音文件 (文件大小上限50MB / 支持 MP3, WAV, M4A, WEBM, AAC 等音频格式)。</span>
            </p>
        </div>

        <div class="bg-white rounded-[2.5rem] p-8 md:p-10 border border-gray-100 shadow-xl shadow-gray-200/40">
            <form @submit.prevent="handleSubmit" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                
                {{-- Title Input --}}
                <div>
                    <label for="title" class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-3">
                        <span class="lang-id">Judul Sesi Analisa</span>
                        <span class="lang-en">Analysis Session Title</span>
                        <span class="lang-zh">分析会话标题</span>
                    </label>
                    <input type="text" name="title" id="title" required 
                           x-bind:placeholder="activeLang === 'zh' ? '例如：毕业论文第一章指导 (周一)' : (activeLang === 'en' ? 'Example: Thesis Guidance Chapter 1 (Monday)' : 'Contoh: Bimbingan Skripsi Bab 1 (Senin)')"
                           class="w-full bg-gray-50 border-transparent focus:border-bima-red focus:bg-white focus:ring-0 rounded-2xl px-6 py-4 text-gray-900 font-bold placeholder-gray-300 transition-all"
                           placeholder="Contoh: Bimbingan Skripsi Bab 1 (Senin)">
                    @error('title')
                        <p class="text-bima-red text-xs mt-2 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Language Selector --}}
                <div>
                    <label for="analysis_locale" class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-3">
                        <span class="lang-id">Bahasa Percakapan</span>
                        <span class="lang-en">Spoken Language</span>
                        <span class="lang-zh">会话所用语言</span>
                    </label>
                    <select name="analysis_locale" id="analysis_locale" 
                            class="w-full bg-gray-50 border-transparent focus:border-bima-red focus:bg-white focus:ring-0 rounded-2xl px-6 py-4 text-gray-900 font-bold transition-all cursor-pointer">
                        <option value="id">🇮🇩 Bahasa Indonesia</option>
                        <option value="en">🇬🇧 English</option>
                        <option value="zh">🇨🇳 中文 (Mandarin)</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-2 font-medium">
                        <span class="lang-id">Pilih bahasa yang digunakan dalam rekaman audio. Sistem akan menyesuaikan prompt analisis AI sesuai bahasa.</span>
                        <span class="lang-en">Select the language spoken in the audio recording. The AI analysis prompts will adapt to the chosen language.</span>
                        <span class="lang-zh">选择录音中所使用的口头语言。AI 分析算法及提示词将自动适应所选语种以确保分析准确。</span>
                    </p>
                </div>

                {{-- Audio Upload --}}
                <div>
                    <label class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-3">
                        <span class="lang-id">File Audio (MP3, WAV, M4A, WEBM, AAC)</span>
                        <span class="lang-en">Audio File (MP3, WAV, M4A, WEBM, AAC)</span>
                        <span class="lang-zh">音频文件 (MP3, WAV, M4A, WEBM, AAC)</span>
                    </label>
                    
                    <div class="relative border-2 border-dashed border-gray-200 rounded-[2rem] p-10 hover:border-bima-red hover:bg-red-50/30 transition-all text-center" 
                         :class="{'border-bima-red bg-red-50/30': fileName}">
                        
                        <input type="file" name="audio" id="audio" accept="audio/*,.mp3,.wav,.m4a,.webm,.ogg,.aac" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleFileChange">
                        
                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-4" :class="{'bg-bima-red text-white': fileName}">
                                <i data-lucide="music" class="w-8 h-8" x-show="!fileName"></i>
                                <i data-lucide="check" class="w-8 h-8" x-show="fileName" style="display: none;"></i>
                            </div>
                            <h3 class="font-bold text-gray-900" x-text="fileName || (activeLang === 'zh' ? '点击或拖拽音频文件到这里' : (activeLang === 'en' ? 'Click or drag audio file here' : 'Klik atau seret file audio ke sini'))"></h3>
                            <p class="text-xs text-gray-500 mt-2 font-medium" x-show="!fileName">
                                <span class="lang-id">Maksimal ukuran file: 50MB (Format MP3, WAV, M4A, WEBM, AAC, dll)</span>
                                <span class="lang-en">Maximum file size: 50MB (MP3, WAV, M4A, WEBM, AAC formats, etc)</span>
                                <span class="lang-zh">文件大小上限为50MB (支持 MP3, WAV, M4A, WEBM, AAC 等音频格式)</span>
                            </p>
                            <p class="text-xs text-bima-red mt-2 font-bold" x-show="fileName" style="display: none;" x-text="fileSize"></p>
                        </div>
                    </div>
                    @error('audio')
                        <p class="text-bima-red text-xs mt-2 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div class="pt-4">
                    <button type="submit" class="w-full flex items-center justify-center gap-3 bg-gray-900 hover:bg-black text-white p-5 rounded-2xl shadow-lg transition-all hover:scale-[1.02] group">
                        <span class="font-bold uppercase tracking-wider text-sm">
                            <span class="lang-id">Unggah & Mulai Proses</span>
                            <span class="lang-en">Upload & Analyze</span>
                            <span class="lang-zh">上传并启动分析</span>
                        </span>
                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Glassmorphic Loading Overlay --}}
        <div x-show="isUploading" style="display: none;" class="fixed inset-0 bg-gray-950/80 backdrop-blur-md z-[60] flex flex-col items-center justify-center p-6 text-white text-center">
            <div class="bg-white/10 border border-white/20 p-8 md:p-10 rounded-[2.5rem] max-w-lg w-full shadow-2xl animate-scale-up backdrop-blur-xl">
                
                {{-- Dynamic Lucide Icon Container --}}
                <div class="relative w-16 h-16 mx-auto mb-6">
                    <div class="absolute inset-0 border-4 border-white/10 rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-bima-red rounded-full animate-spin border-t-transparent border-r-transparent"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i data-lucide="music" class="w-6 h-6 text-white animate-pulse"></i>
                    </div>
                </div>
                
                <h2 class="text-2xl font-black uppercase tracking-tight mb-2">
                    <span class="lang-id">Memproses Audio Anda</span>
                    <span class="lang-en">Processing Your Audio</span>
                    <span class="lang-zh">处理中</span>
                </h2>
                <p class="text-white/60 text-sm font-medium mb-6 leading-relaxed" x-text="progressText"></p>
                
                {{-- Progress Bar --}}
                <div class="w-full bg-white/10 h-3.5 rounded-full overflow-hidden mb-6 p-0.5 border border-white/5">
                    <div class="bg-gradient-to-r from-bima-red to-red-500 h-full rounded-full transition-all duration-500" :style="'width: ' + progressPercent + '%'"></div>
                </div>

                {{-- Dynamic Steps Indicators --}}
                <div class="space-y-4 text-left text-xs font-bold uppercase tracking-wider">
                    <div class="flex items-center gap-3 transition-opacity" :class="{'opacity-100 text-white': activeStep >= 1, 'opacity-30 text-white/50': activeStep < 1}">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center border text-[0.6rem] transition-colors" :class="{'bg-green-500 border-green-500 text-white': activeStep > 1, 'bg-bima-red border-bima-red text-white': activeStep === 1, 'border-white/20': activeStep < 1}">
                            <span x-show="activeStep <= 1">1</span>
                            <i data-lucide="check" class="w-3 h-3" x-show="activeStep > 1" style="display: none;"></i>
                        </div>
                        <span>
                            <span class="lang-id">Inisialisasi sesi bimbingan</span>
                            <span class="lang-en">Initializing supervision session</span>
                            <span class="lang-zh">正在初始化学术辅导会话</span>
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-3 transition-opacity" :class="{'opacity-100 text-white': activeStep >= 2, 'opacity-30 text-white/50': activeStep < 2}">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center border text-[0.6rem] transition-colors" :class="{'bg-green-500 border-green-500 text-white': activeStep > 2, 'bg-bima-red border-bima-red text-white': activeStep === 2, 'border-white/20': activeStep < 2}">
                            <span x-show="activeStep <= 2">2</span>
                            <i data-lucide="check" class="w-3 h-3" x-show="activeStep > 2" style="display: none;"></i>
                        </div>
                        <span>
                            <span class="lang-id">Slicing Berkas Audio di Browser</span>
                            <span class="lang-en">Slicing Audio File in Browser</span>
                            <span class="lang-zh">正在浏览器本地进行音频切片</span>
                        </span>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-3 transition-opacity" :class="{'opacity-100 text-white': activeStep >= 3, 'opacity-30 text-white/50': activeStep < 3}">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center border text-[0.6rem] transition-colors" :class="{'bg-green-500 border-green-500 text-white': activeStep > 3, 'bg-bima-red border-bima-red text-white': activeStep === 3, 'border-white/20': activeStep < 3}">
                                <span x-show="activeStep <= 3">3</span>
                                <i data-lucide="check" class="w-3 h-3" x-show="activeStep > 3" style="display: none;"></i>
                            </div>
                            <span x-text="(activeLang === 'zh') ? ('分析音频切片 (' + currentChunkIndex + '/' + totalChunksCount + ') GPT-4o-Audio') : ((activeLang === 'en') ? ('Analyzing Chunk (' + currentChunkIndex + '/' + totalChunksCount + ') GPT-4o-Audio') : ('Analisis Potongan (' + currentChunkIndex + '/' + totalChunksCount + ') GPT-4o-Audio'))"></span>
                        </div>

                        {{-- Chunk Status Sublist --}}
                        <div x-show="activeStep >= 3 && totalChunksCount > 0" class="pl-8 mt-2 space-y-2 border-l border-white/10 max-h-48 overflow-y-auto pr-2" style="display: none;">
                            <template x-for="idx in totalChunksCount" :key="idx">
                                <div class="flex items-center justify-between text-[0.7rem] py-1 transition-all duration-300">
                                    <div class="flex items-center gap-2">
                                        <span class="text-white/60">Potongan #<span x-text="idx"></span></span>
                                        <span class="text-[0.55rem] px-2 py-0.5 rounded-full bg-white/5 text-white/40 uppercase tracking-widest border border-white/5 font-semibold" x-text="chunkStatusLabel(idx)"></span>
                                    </div>
                                    <div class="flex items-center font-bold">
                                        <span x-show="idx < currentChunkIndex" class="text-green-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            <span class="lang-id">Selesai</span>
                                            <span class="lang-en">Done</span>
                                            <span class="lang-zh">完成</span>
                                        </span>
                                        <span x-show="idx === currentChunkIndex && !uploadError" class="text-bima-red flex items-center gap-1 animate-pulse">
                                            <svg class="w-2.5 h-2.5 animate-spin border border-bima-red rounded-full border-t-transparent mr-1" fill="none" viewBox="0 0 24 24"></svg>
                                            <span class="lang-id">Proses</span>
                                            <span class="lang-en">Processing</span>
                                            <span class="lang-zh">分析中</span>
                                        </span>
                                        <span x-show="idx > currentChunkIndex" class="text-white/30 flex items-center gap-1 font-normal">
                                            <span class="lang-id">Antrean</span>
                                            <span class="lang-en">Queued</span>
                                            <span class="lang-zh">排队中</span>
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Prominent Browser/App Alert Banner --}}
                <div class="mt-8 p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex items-start gap-3 text-left">
                    <div class="w-8 h-8 rounded-full bg-amber-500/20 flex items-center justify-center shrink-0 text-amber-400 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-amber-400">
                            <span class="lang-id">Pemberitahuan Penting</span>
                            <span class="lang-en">Important Notice</span>
                            <span class="lang-zh">核心安全提示</span>
                        </h4>
                        <p class="text-[0.68rem] text-white/70 font-semibold mt-1 leading-relaxed">
                            <span class="lang-id">Mohon tidak menutup browser, me-refresh halaman, atau beralih aplikasi selama pemrosesan berlangsung agar sesi pengiriman data tetap sinkron.</span>
                            <span class="lang-en">Please do not close the browser, refresh the page, or switch apps during processing to ensure that the data transmission session remains synchronized.</span>
                            <span class="lang-zh">在语音数据上载与深度学习推理期间，请勿关闭浏览器、刷新页面或进行多任务应用切换，以确保高吞吐量数据链路的安全与对齐。</span>
                        </p>
                    </div>
                </div>

                {{-- Stop / Cancel Button --}}
                <div class="mt-6 pt-6 border-t border-white/10 flex items-center justify-center">
                    <button type="button" @click="handleCancel" class="w-full flex items-center justify-center gap-2 bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-white/80 hover:text-white px-5 py-3 rounded-xl transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <rect x="5" y="5" width="14" height="14" rx="2" stroke="currentColor" stroke-width="2"></rect>
                        </svg>
                        <span class="text-xs font-black uppercase tracking-wider">
                            <span class="lang-id">Hentikan Sesi Analisa</span>
                            <span class="lang-en">Cancel Analysis Session</span>
                            <span class="lang-zh">中止并丢弃分析</span>
                        </span>
                    </button>
                </div>
                
                {{-- Error Message Box --}}
                <div x-show="uploadError" style="display: none;" class="mt-8 p-4 bg-red-500/20 border border-red-500/30 rounded-2xl text-red-300 text-xs font-bold text-center">
                    <span x-text="uploadError"></span>
                    <button type="button" @click="isUploading = false" class="block mx-auto mt-3 underline uppercase tracking-widest text-[0.65rem] hover:text-white transition-colors">
                        <span class="lang-id">Batalkan & Coba Lagi</span>
                        <span class="lang-en">Cancel & Try Again</span>
                        <span class="lang-zh">取消并重试</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/i18next@23.11.5/dist/umd/i18next.min.js"></script>
    <script>
        // Initialize i18next with multi-language UI strings
        const activeLang = '{{ app()->getLocale() }}';
        const i18n = i18next.createInstance();
        i18n.init({
            lng: activeLang,
            fallbackLng: 'id',
            resources: {
                id: {
                    translation: {
                        'step.init': 'Menghubungi server untuk menginisialisasi sesi...',
                        'step.slice': 'Membaca dan memproses audio di memori browser...',
                        'step.read_binary': 'Membaca data biner audio dari browser...',
                        'step.decode': 'Mendecode file audio ke PCM Audio...',
                        'step.split': 'Membagi audio menjadi potongan-potongan presisi...',
                        'step.chunk': 'Mengirim & menganalisis Potongan @{{current}}/@{{total}} ...',
                        'step.done': 'Selesai! Menyusun halaman hasil progresif...',
                        'status.done': 'Selesai',
                        'status.processing': 'Menganalisis',
                        'status.queued': 'Antrean',
                        'error.init': 'Gagal menginisialisasi sesi di server.',
                        'error.chunk': 'Gagal menganalisis potongan audio @{{index}}.',
                        'error.generic': 'Gagal memproses audio. Harap periksa jaringan Anda.',
                        'error.validation': 'Harap isi judul sesi dan pilih berkas audio.',
                        'error.file_size': 'Ukuran file melebihi 50MB. Silakan pilih file yang lebih kecil.',
                        'confirm.cancel': 'Apakah Anda yakin ingin membatalkan sesi analisis ini? Sesi Anda akan dihentikan.'
                    }
                },
                en: {
                    translation: {
                        'step.init': 'Connecting to server to initialize session...',
                        'step.slice': 'Reading and slicing audio file in browser memory...',
                        'step.read_binary': 'Reading audio binary data from browser...',
                        'step.decode': 'Decoding audio file to PCM Audio...',
                        'step.split': 'Splitting audio into precise chunks...',
                        'step.chunk': 'Uploading & analyzing Chunk @{{current}}/@{{total}} ...',
                        'step.done': 'Success! Assembling progressive results page...',
                        'status.done': 'Done',
                        'status.processing': 'Analyzing',
                        'status.queued': 'Queued',
                        'error.init': 'Failed to initialize session on the server.',
                        'error.chunk': 'Failed to analyze audio chunk @{{index}}.',
                        'error.generic': 'Failed to process audio. Please check your network connection.',
                        'error.validation': 'Please provide a session title and select an audio file.',
                        'error.file_size': 'File size exceeds 50MB. Please choose a smaller file.',
                        'confirm.cancel': 'Are you sure you want to cancel this analysis session? Your session will be terminated.'
                    }
                },
                zh: {
                    translation: {
                        'step.init': '正在连接服务器以初始化学术分析会话...',
                        'step.slice': '正在浏览器内存中进行本地音频解码与切片...',
                        'step.read_binary': '正在读取本地音频二进制数据流...',
                        'step.decode': '正在解码音频文件为无损 PCM 波形...',
                        'step.split': '正在将音频分割为高精度时段切片...',
                        'step.chunk': '正在上传并分析切片 @{{current}}/@{{total}} (使用 GPT-4o-Audio)...',
                        'step.done': '分析完成！正在构建并重定向至结果仪表板...',
                        'status.done': '分析完成',
                        'status.processing': '分析中',
                        'status.queued': '排队中',
                        'error.init': '服务器端初始化会话失败。',
                        'error.chunk': '第 @{{index}} 个音频切片分析失败。',
                        'error.generic': '音频处理失败，请检查网络连接状态。',
                        'error.validation': '请填写会话标题并选择要分析的录音文件。',
                        'error.file_size': '文件大小已超过 50MB 限制。请选择更小的文件。',
                        'confirm.cancel': '您确定要中止本次分析会话吗？当前所有进度将被丢弃。'
                    }
                }
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const localeSelect = document.getElementById('analysis_locale');
            if (localeSelect) {
                const savedLocale = localStorage.getItem('bima_analysis_locale') || 'id';
                localeSelect.value = savedLocale;
            }
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('audioUploader', () => ({
                activeLang: activeLang,
                fileName: '',
                fileSize: '',
                isUploading: false,
                uploadError: null,
                progressText: '',
                progressPercent: 0,
                activeStep: 0,
                currentChunkIndex: 0,
                totalChunksCount: 0,
                isCancelled: false,

                chunkStatusLabel(idx) {
                    if (idx < this.currentChunkIndex) return i18n.t('status.done');
                    if (idx === this.currentChunkIndex) return i18n.t('status.processing');
                    return i18n.t('status.queued');
                },

                handleCancel() {
                    if (confirm(i18n.t('confirm.cancel'))) {
                        this.isCancelled = true;
                        this.isUploading = false;
                        window.location.reload();
                    }
                },
                
                handleFileChange(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    
                    if (file.size > 50 * 1024 * 1024) {
                        alert(i18n.t('error.file_size'));
                        e.target.value = '';
                        this.fileName = '';
                        return;
                    }
                    
                    this.fileName = file.name;
                    this.fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                },

                async handleSubmit(e) {
                    const form = e.target;
                    const titleInput = form.querySelector('#title');
                    const audioInput = form.querySelector('#audio');
                    const localeSelect = form.querySelector('#analysis_locale');
                    const file = audioInput.files[0];

                    if (!titleInput.value || !file) {
                        alert(i18n.t('error.validation'));
                        return;
                    }

                    const locale = localeSelect ? localeSelect.value : 'id';
                    localStorage.setItem('bima_analysis_locale', locale);

                    this.isUploading = true;
                    this.uploadError = null;
                    this.progressPercent = 5;
                    this.activeStep = 1;
                    this.progressText = i18n.t('step.init');

                    try {
                        const initResponse = await fetch('{{ route("analysis.initialize") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ title: titleInput.value, locale: locale })
                        });

                        if (!initResponse.ok) {
                            throw new Error(i18n.t('error.init'));
                        }

                        const initData = await initResponse.json();
                        const analysisId = initData.analysis_id;

                        this.activeStep = 2;
                        this.progressPercent = 20;
                        this.progressText = i18n.t('step.slice');

                        this.progressText = i18n.t('step.read_binary');
                        const arrayBuffer = await file.arrayBuffer();

                        this.progressText = i18n.t('step.decode');
                        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        const decodedBuffer = await audioCtx.decodeAudioData(arrayBuffer);

                        this.progressText = i18n.t('step.split');
                        const totalFrames = decodedBuffer.length;
                        const duration = decodedBuffer.duration;
                        
                        const targetChunkDuration = 100;
                        const numChunks = Math.max(1, Math.ceil(duration / targetChunkDuration));
                        const chunkFrames = Math.ceil(totalFrames / numChunks);

                        this.totalChunksCount = numChunks;
                        this.currentChunkIndex = 0;

                        const writeString = (view, offset, string) => {
                            for (let i = 0; i < string.length; i++) {
                                view.setUint8(offset + i, string.charCodeAt(i));
                            }
                        };

                        const floatTo16BitPCM = (view, offset, input) => {
                            for (let i = 0; i < input.length; i++, offset += 2) {
                                let s = Math.max(-1, Math.min(1, input[i]));
                                view.setInt16(offset, s < 0 ? s * 0x8000 : s * 0x7FFF, true);
                            }
                        };

                        const bufferToWav = (buffer) => {
                            const sampleRate = buffer.sampleRate;
                            const format = 1;
                            const bitDepth = 16;
                            const channelData = buffer.getChannelData(0);
                            const bufferLength = channelData.length * 2;
                            const arrayBuffer = new ArrayBuffer(44 + bufferLength);
                            const view = new DataView(arrayBuffer);
                            
                            writeString(view, 0, 'RIFF');
                            view.setUint32(4, 36 + bufferLength, true);
                            writeString(view, 8, 'WAVE');
                            writeString(view, 12, 'fmt ');
                            view.setUint32(16, 16, true);
                            view.setUint16(20, format, true);
                            view.setUint16(22, 1, true);
                            view.setUint32(24, sampleRate, true);
                            view.setUint32(28, sampleRate * 2, true);
                            view.setUint16(32, 2, true);
                            view.setUint16(34, bitDepth, true);
                            writeString(view, 36, 'data');
                            view.setUint32(40, bufferLength, true);
                            
                            floatTo16BitPCM(view, 44, channelData);
                            return new Blob([view], { type: 'audio/wav' });
                        };

                        const sliceAudioBuffer = (ctx, buffer, start, end) => {
                            const chunkLength = end - start;
                            const newBuf = ctx.createBuffer(1, chunkLength, buffer.sampleRate);
                            const originalData = buffer.getChannelData(0);
                            const chunkData = newBuf.getChannelData(0);
                            chunkData.set(originalData.subarray(start, end));
                            return newBuf;
                        };

                        const chunks = [];
                        for (let i = 0; i < numChunks; i++) {
                            const start = i * chunkFrames;
                            const end = Math.min(totalFrames, (i + 1) * chunkFrames);
                            chunks.push(bufferToWav(sliceAudioBuffer(audioCtx, decodedBuffer, start, end)));
                        }

                        for (let i = 0; i < chunks.length; i++) {
                            if (this.isCancelled) {
                                break;
                            }
                            const chunkIndex = i + 1;
                            this.currentChunkIndex = chunkIndex;
                            this.activeStep = 3;
                            this.progressPercent = 20 + Math.round((i / chunks.length) * 75);
                            this.progressText = i18n.t('step.chunk', { current: chunkIndex, total: numChunks });

                            const formData = new FormData();
                            formData.append('audio_chunk', chunks[i], `chunk_${chunkIndex}.wav`);

                            const chunkResponse = await fetch(`/{{ app()->getLocale() }}/analysis/${analysisId}/chunk`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });

                            if (!chunkResponse.ok) {
                                const errorData = await chunkResponse.json().catch(() => ({}));
                                throw new Error(errorData.message || i18n.t('error.chunk', { index: chunkIndex }));
                            }
                        }

                        if (this.isCancelled) return;

                        this.activeStep = 4;
                        this.progressPercent = 100;
                        this.progressText = i18n.t('step.done');

                        setTimeout(() => {
                            window.location.href = `/{{ app()->getLocale() }}/analysis/${analysisId}/processing`;
                        }, 800);

                    } catch (err) {
                        this.uploadError = err.message || i18n.t('error.generic');
                    }
                }
            }));
        });
    </script>
</x-layouts.app>
