<x-layouts.app title="Input Analisa V2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Banner Info V2 (Development Warning) --}}
        <div class="bg-amber-50 border border-amber-200 text-amber-800 px-6 py-4 rounded-2xl mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <i data-lucide="info" class="w-6 h-6 text-amber-600 shrink-0"></i>
                <p class="font-bold text-sm leading-relaxed">
                    <span class="uppercase tracking-widest text-amber-600 mr-1">Perhatian:</span> 
                    <span class="font-medium text-amber-900">Halaman ini adalah eksperimen Versi 2 yang sedang dalam masa pengembangan. Sistem mungkin tidak selalu stabil.</span>
                </p>
            </div>
            <a href="{{ route('analysis.create') }}" class="shrink-0 bg-white border border-amber-300 text-amber-700 hover:bg-amber-100 hover:text-amber-800 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-colors">
                Gunakan Versi Stabil
            </a>
        </div>

        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                <span class="lang-id">Kembali ke Dashboard</span>
                <span class="lang-en">Back to Dashboard</span>
                <span class="lang-zh">返回控制面板</span>
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase flex items-center gap-3">
                <span class="lang-id">Mulai Analisa Baru (V2)</span>
                <span class="lang-en">Start New Analysis (V2)</span>
                <span class="lang-zh">开始新语音分析 (V2)</span>
            </h1>
            <p class="text-gray-500 font-medium mt-2">
                <span class="lang-id">Unggah file audio percakapan bimbingan akademik Anda. Versi 2 dilengkapi dengan pemetaan interaksi graf secara visual.</span>
                <span class="lang-en">Upload your academic supervision audio file. Version 2 is equipped with visual interaction graph mapping.</span>
                <span class="lang-zh">上传您的学术指导录音文件，版本 2 具有视觉互动图形映射功能。</span>
            </p>
        </div>

        {{-- Form Card (Matching old design) --}}
        <div class="bg-white rounded-[2.5rem] p-8 md:p-10 border border-gray-100 shadow-xl shadow-gray-200/40">

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
                    <ul class="text-sm text-red-600 font-bold space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('analysis.v2.initialize') }}"
                  enctype="multipart/form-data"
                  class="space-y-8"
                  id="analysis-form">
                @csrf

                {{-- Judul Sesi --}}
                <div>
                    <label for="title" class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-3">
                        <span class="lang-id">Judul Sesi Analisa</span>
                        <span class="lang-en">Analysis Session Title</span>
                        <span class="lang-zh">分析会话标题</span>
                    </label>
                    <input type="text" name="title" id="title" required
                           value="{{ old('title') }}"
                           placeholder="Contoh: Bimbingan Skripsi Bab 1 (Senin)"
                           class="w-full bg-gray-50 border-transparent focus:border-purple-600 focus:bg-white focus:ring-0 rounded-2xl px-6 py-4 text-gray-900 font-bold placeholder-gray-300 transition-all">
                    @error('title')
                        <p class="text-red-600 text-xs mt-2 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pilihan Bahasa (Dropdown) --}}
                <div>
                    <label for="locale" class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-3">
                        <span class="lang-id">Bahasa Rekaman</span>
                        <span class="lang-en">Recording Language</span>
                        <span class="lang-zh">录音语言</span>
                    </label>
                    <select name="locale" id="locale"
                            class="w-full bg-gray-50 border-transparent focus:border-purple-600 focus:bg-white focus:ring-0 rounded-2xl px-6 py-4 text-gray-900 font-bold transition-all cursor-pointer">
                        <option value="id" @selected(old('locale', 'id') === 'id')>🇮🇩 Bahasa Indonesia</option>
                        <option value="en" @selected(old('locale') === 'en')>🇬🇧 English</option>
                        <option value="zh" @selected(old('locale') === 'zh')>🇨🇳 中文 (Mandarin)</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-2 font-medium">
                        <span class="lang-id">Pilih bahasa yang digunakan dalam rekaman — bukan bahasa browser Anda.</span>
                        <span class="lang-en">Select the language spoken in the recording — not your browser language.</span>
                        <span class="lang-zh">选择录音中使用的语言，而非浏览器语言。</span>
                    </p>
                </div>

                {{-- Upload File Audio --}}
                <div>
                    <label class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-3">
                        <span class="lang-id">File Audio/Video (FLAC, MP3, MP4, MPEG, MPGA, M4A, OGG, WAV, WEBM)</span>
                        <span class="lang-en">Audio/Video File (FLAC, MP3, MP4, MPEG, MPGA, M4A, OGG, WAV, WEBM)</span>
                        <span class="lang-zh">音频/视频文件 (FLAC, MP3, MP4, MPEG, MPGA, M4A, OGG, WAV, WEBM)</span>
                    </label>

                    <div class="relative border-2 border-dashed border-gray-200 rounded-[2rem] p-10 hover:border-purple-600 hover:bg-purple-50/30 transition-all text-center" id="drop-zone">
                        <input type="file" name="audio" id="audio"
                               accept=".flac,.mp3,.mp4,.mpeg,.mpga,.m4a,.ogg,.wav,.webm,.aac"
                               required
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               onchange="updateFileName(this)">

                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-4" id="upload-icon">
                                <i data-lucide="music" class="w-8 h-8"></i>
                            </div>
                            <h3 class="font-bold text-gray-900" id="upload-label">
                                <span class="lang-id">Klik atau seret file audio ke sini</span>
                                <span class="lang-en">Click or drag audio file here</span>
                                <span class="lang-zh">点击或拖拽音频文件到这里</span>
                            </h3>
                            <p class="text-xs text-gray-500 mt-2 font-medium" id="upload-hint">
                                <span class="lang-id">Maks. 20MB — FLAC, MP3, MP4, MPEG, MPGA, M4A, OGG, WAV, WEBM</span>
                                <span class="lang-en">Max 20MB — FLAC, MP3, MP4, MPEG, MPGA, M4A, OGG, WAV, WEBM</span>
                                <span class="lang-zh">最大20MB — 支持FLAC, MP3, MP4, MPEG, MPGA, M4A, OGG, WAV, WEBM</span>
                            </p>
                        </div>
                    </div>

                    @error('audio')
                        <p class="text-red-600 text-xs mt-2 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info Card --}}
                <div class="bg-purple-50 border border-purple-100 rounded-2xl p-4 flex gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-purple-600 shrink-0 mt-0.5"></i>
                    <p class="text-xs text-purple-800 font-medium leading-relaxed">
                        <span class="lang-id">Setelah upload, Anda akan diarahkan ke halaman pemrosesan interaktif. Seluruh analisis dikerjakan oleh Kecerdasan Buatan di cloud secara real-time.</span>
                    </p>
                </div>

                {{-- Consent Checkbox --}}
                <div class="flex items-start pt-2">
                    <div class="flex items-center h-5 mt-0.5">
                        <input id="consent" name="consent" type="checkbox" required class="w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-600 cursor-pointer transition-all">
                    </div>
                    <label for="consent" class="ml-3 text-xs font-bold text-gray-600 cursor-pointer select-none">
                        <span class="lang-id">Saya menyetujui bahwa data yang dimasukkan adalah murni untuk keperluan penelitian.</span>
                        <span class="lang-en">I agree that the data entered is strictly for research purposes.</span>
                        <span class="lang-zh">我同意所输入的数据完全用于研究目的。</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <div class="pt-2">
                    <button type="submit" id="submit-btn"
                            class="w-full flex items-center justify-center gap-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white p-5 rounded-2xl shadow-lg transition-all hover:scale-[1.02] group disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100">
                        <span class="font-bold uppercase tracking-wider text-sm" id="submit-label">
                            <span class="lang-id">Unggah &amp; Mulai Analisa V2</span>
                            <span class="lang-en">Upload &amp; Start V2 Analysis</span>
                            <span class="lang-zh">上传并启动 V2 分析</span>
                        </span>
                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform" id="submit-icon"></i>
                        <svg class="w-5 h-5 animate-spin hidden" id="submit-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- Disclaimer --}}
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-400 font-medium">
                <span class="lang-id">Temaniskripsi adalah platform berbasis kecerdasan buatan dan kombinasi algoritma lainnya dan dapat membuat kesalahan.</span>
                <span class="lang-en">Temaniskripsi is a platform based on artificial intelligence and a combination of other algorithms and can make mistakes.</span>
                <span class="lang-zh">Temaniskripsi 是一个基于人工智能和其他算法组合的平台，可能会犯错。</span>
            </p>
        </div>
    </div>

    <x-slot name="scripts">
    <script>
    function updateFileName(input) {
        const file = input.files[0];
        if (!file) return;

        const zone  = document.getElementById('drop-zone');
        const icon  = document.getElementById('upload-icon');
        const label = document.getElementById('upload-label');
        const hint  = document.getElementById('upload-hint');
        const sizeMb = (file.size / 1024 / 1024).toFixed(1);

        zone.classList.add('border-purple-600', 'bg-purple-50/30');
        icon.classList.remove('bg-gray-100', 'text-gray-400');
        icon.classList.add('bg-purple-600', 'text-white');
        icon.innerHTML = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        label.textContent = file.name;
        hint.textContent = sizeMb + ' MB';
    }

    document.getElementById('analysis-form').addEventListener('submit', function (e) {
        const btn     = document.getElementById('submit-btn');
        const label   = document.getElementById('submit-label');
        const icon    = document.getElementById('submit-icon');
        const spinner = document.getElementById('submit-spinner');
        const fileInput = document.getElementById('audio');

        if (!fileInput.files[0]) {
            e.preventDefault();
            return;
        }
        
        const file = fileInput.files[0];
        const maxSize = 20 * 1024 * 1024; // 20MB
        if (file.size > maxSize) {
            e.preventDefault();
            alert('Ukuran file maksimal 20MB.');
            return;
        }

        btn.disabled = true;
        label.textContent = 'Menyiapkan Engine V2...';
        icon.classList.add('hidden');
        spinner.classList.remove('hidden');
    });
    </script>
    </x-slot>
</x-layouts.app>