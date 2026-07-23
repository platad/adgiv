<x-layouts.app title="Input Analisa">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Header --}}
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
                <span class="lang-id">Unggah file audio percakapan bimbingan akademik Anda. Semua pemrosesan dilakukan di server — tidak ada slicing di browser.</span>
                <span class="lang-en">Upload your academic supervision audio file. All processing is done on the server — no browser-side slicing.</span>
                <span class="lang-zh">上传您的学术指导录音文件，所有处理均在服务器上完成。</span>
            </p>
        </div>

        {{-- Form Card --}}
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

            {{-- Form POST biasa — tidak ada JavaScript yang ikut campur dalam submit --}}
            <form method="POST"
                  action="{{ route('analysis.initialize') }}"
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
                           class="w-full bg-gray-50 border-transparent focus:border-bima-red focus:bg-white focus:ring-0 rounded-2xl px-6 py-4 text-gray-900 font-bold placeholder-gray-300 transition-all">
                    @error('title')
                        <p class="text-red-600 text-xs mt-2 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pilihan Bahasa --}}
                <div>
                    <label for="locale" class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-3">
                        <span class="lang-id">Bahasa Percakapan</span>
                        <span class="lang-en">Spoken Language</span>
                        <span class="lang-zh">会话所用语言</span>
                    </label>
                    <select name="locale" id="locale"
                            class="w-full bg-gray-50 border-transparent focus:border-bima-red focus:bg-white focus:ring-0 rounded-2xl px-6 py-4 text-gray-900 font-bold transition-all cursor-pointer">
                        <option value="id" @selected(old('locale', 'id') === 'id')>🇮🇩 Bahasa Indonesia</option>
                        <option value="en" @selected(old('locale') === 'en')>🇬🇧 English</option>
                        <option value="zh" @selected(old('locale') === 'zh')>🇨🇳 中文 (Mandarin)</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-2 font-medium">
                        <span class="lang-id">Pilih bahasa rekaman. AI akan menyesuaikan prompt analisis sesuai bahasa yang dipilih.</span>
                        <span class="lang-en">Select the recording language. AI analysis prompts will adapt to the chosen language.</span>
                        <span class="lang-zh">选择录音中使用的语言，AI分析提示词将自动适应所选语种。</span>
                    </p>
                </div>

                {{-- Upload File Audio --}}
                <div>
                    <label class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-3">
                        <span class="lang-id">File Audio (MP3, WAV, M4A, WEBM, AAC, FLAC)</span>
                        <span class="lang-en">Audio File (MP3, WAV, M4A, WEBM, AAC, FLAC)</span>
                        <span class="lang-zh">音频文件 (MP3, WAV, M4A, WEBM, AAC, FLAC)</span>
                    </label>

                    <div class="relative border-2 border-dashed border-gray-200 rounded-[2rem] p-10 hover:border-bima-red hover:bg-red-50/30 transition-all text-center" id="drop-zone">
                        <input type="file" name="audio" id="audio"
                               accept="audio/*,.mp3,.wav,.m4a,.webm,.ogg,.aac,.flac"
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
                                <span class="lang-id">Maks. 100MB — MP3, WAV, M4A, WEBM, AAC, FLAC</span>
                                <span class="lang-en">Max 100MB — MP3, WAV, M4A, WEBM, AAC, FLAC</span>
                                <span class="lang-zh">最大100MB — 支持MP3, WAV, M4A, WEBM, AAC, FLAC</span>
                            </p>
                        </div>
                    </div>

                    @error('audio')
                        <p class="text-red-600 text-xs mt-2 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info Card --}}
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex gap-3">
                    <i data-lucide="info" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5"></i>
                    <p class="text-xs text-blue-700 font-medium leading-relaxed">
                        <span class="lang-id">Setelah upload, Anda akan diarahkan ke halaman pemrosesan khusus. Semua pemotongan audio dan analisis AI dikerjakan di server — Anda bisa refresh halaman kapan saja dan melanjutkan dari posisi terakhir jika koneksi terputus.</span>
                        <span class="lang-en">After upload, you'll be redirected to a dedicated processing page. All audio slicing and AI analysis runs on the server — you can refresh anytime and resume from the last position if the connection drops.</span>
                        <span class="lang-zh">上传后您将进入专属处理页面，所有音频切片与AI分析均在服务器完成，断线后可随时继续。</span>
                    </p>
                </div>

                {{-- Submit Button --}}
                <div class="pt-4">
                    <button type="submit" id="submit-btn"
                            class="w-full flex items-center justify-center gap-3 bg-gray-900 hover:bg-black text-white p-5 rounded-2xl shadow-lg transition-all hover:scale-[1.02] group disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100">
                        <span class="font-bold uppercase tracking-wider text-sm" id="submit-label">
                            <span class="lang-id">Unggah &amp; Mulai Proses</span>
                            <span class="lang-en">Upload &amp; Start Processing</span>
                            <span class="lang-zh">上传并启动分析</span>
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
    </div>

    <x-slot name="scripts">
    <script>
    // Update tampilan nama file yang dipilih
    function updateFileName(input) {
        const file = input.files[0];
        if (!file) return;

        const zone  = document.getElementById('drop-zone');
        const icon  = document.getElementById('upload-icon');
        const label = document.getElementById('upload-label');
        const hint  = document.getElementById('upload-hint');
        const sizeMb = (file.size / 1024 / 1024).toFixed(1);

        zone.classList.add('border-bima-red', 'bg-red-50/30');
        icon.classList.remove('bg-gray-100', 'text-gray-400');
        icon.classList.add('bg-bima-red', 'text-white');
        icon.innerHTML = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        label.textContent = file.name;
        hint.textContent = sizeMb + ' MB';
    }

    // --- Form Submit ---
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
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            e.preventDefault();
            alert('Ukuran file maksimal 10MB.');
            return;
        }

        btn.disabled = true;
        label.textContent = 'Mengunggah File...';
        icon.classList.add('hidden');
        spinner.classList.remove('hidden');
        
        // Form akan ter-submit secara natural oleh browser ke route analysis.initialize
    });
    </script>
    </x-slot>
</x-layouts.app>
