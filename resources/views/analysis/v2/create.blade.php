<x-layouts.app title="Uji Coba Versi 2">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-fade-in">
        
        {{-- Header Section V2 --}}
        <div class="text-center mb-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-100 text-purple-700 border border-purple-200 text-[0.6rem] font-black uppercase tracking-widest mb-4 shadow-sm">
                <i data-lucide="flask-conical" class="w-3.5 h-3.5"></i>
                <span>Eksperimen Versi 2</span>
            </span>
            <h1 class="text-3xl font-black text-gray-950 uppercase tracking-tight mb-3">
                Uji Coba V2 (WhisperX + RoBERTa)
            </h1>
            <p class="text-sm text-gray-500 font-medium">
                Sistem ini menggunakan arsitektur real-time bypass cPanel dan pemetaan interaksi graf.
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-700 p-6 rounded-2xl mb-8 border border-red-100 text-sm font-bold flex items-start gap-3 shadow-sm">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-gray-100">
            <form action="{{ route('analysis.v2.initialize') }}" method="POST" enctype="multipart/form-data" id="analysis-form">
                @csrf
                
                {{-- Setup Analisa --}}
                <div class="space-y-6 mb-8">
                    <div>
                        <label class="block text-[0.65rem] font-black text-gray-500 uppercase tracking-widest mb-2">Judul Sesi</label>
                        <input type="text" name="title" required placeholder="Contoh: Bimbingan Bab 1-3 dengan Dosen X" 
                               class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-purple-500 focus:border-purple-500 transition-colors font-medium">
                    </div>
                    
                    <div>
                        <label class="block text-[0.65rem] font-black text-gray-500 uppercase tracking-widest mb-2">Bahasa Percakapan</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer relative">
                                <input type="radio" name="locale" value="id" class="peer sr-only" {{ app()->getLocale() == 'id' ? 'checked' : '' }}>
                                <div class="text-center p-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:text-purple-700 font-bold text-xs uppercase tracking-wider transition-all hover:bg-gray-100">Indonesia</div>
                            </label>
                            <label class="cursor-pointer relative">
                                <input type="radio" name="locale" value="en" class="peer sr-only" {{ app()->getLocale() == 'en' ? 'checked' : '' }}>
                                <div class="text-center p-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:text-purple-700 font-bold text-xs uppercase tracking-wider transition-all hover:bg-gray-100">English</div>
                            </label>
                            <label class="cursor-pointer relative">
                                <input type="radio" name="locale" value="zh" class="peer sr-only" {{ app()->getLocale() == 'zh' ? 'checked' : '' }}>
                                <div class="text-center p-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:text-purple-700 font-bold text-xs uppercase tracking-wider transition-all hover:bg-gray-100">Chinese</div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Upload Area --}}
                <div class="mb-8">
                    <label class="block text-[0.65rem] font-black text-gray-500 uppercase tracking-widest mb-2">File Rekaman (Audio/Video)</label>
                    <div class="relative group">
                        <input type="file" name="audio" id="audio" accept=".wav,.mp3,.m4a" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateFileName(this)">
                        
                        <div id="drop-zone" class="border-2 border-dashed border-gray-200 rounded-2xl p-10 text-center bg-gray-50 group-hover:bg-purple-50/50 group-hover:border-purple-300 transition-all duration-300">
                            <div id="upload-icon" class="w-16 h-16 bg-gray-100 text-gray-400 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:bg-purple-100 group-hover:text-purple-600 transition-all">
                                <i data-lucide="upload-cloud" class="w-8 h-8"></i>
                            </div>
                            <p id="upload-label" class="font-bold text-gray-900 text-sm mb-1">Klik atau seret file ke sini</p>
                            <p id="upload-hint" class="text-xs text-gray-500 font-medium">MP3, WAV, M4A (Maks 20MB)</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" id="submit-btn" class="w-full flex items-center justify-center gap-3 bg-purple-600 hover:bg-purple-700 text-white p-5 rounded-2xl shadow-lg transition-all hover:scale-[1.02] group">
                        <span class="font-bold uppercase tracking-wider text-sm" id="submit-label">Unggah & Mulai Proses V2</span>
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
    function updateFileName(input) {
        const file = input.files[0];
        if (!file) return;

        const zone  = document.getElementById('drop-zone');
        const icon  = document.getElementById('upload-icon');
        const label = document.getElementById('upload-label');
        const hint  = document.getElementById('upload-hint');
        const sizeMb = (file.size / 1024 / 1024).toFixed(1);

        zone.classList.add('border-purple-500', 'bg-purple-50/30');
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
        
        btn.disabled = true;
        label.textContent = 'Menyiapkan V2...';
        icon.classList.add('hidden');
        spinner.classList.remove('hidden');
    });
    </script>
    </x-slot>
</x-layouts.app>
