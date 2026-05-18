<x-layouts.app title="Input Analisa">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 animate-fade-in">
        
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">Mulai Analisa Baru</h1>
            <p class="text-gray-500 font-medium mt-2">Unggah file audio percakapan bimbingan akademik Anda (Maksimal 100MB / ~30 Menit).</p>
        </div>

        <div class="bg-white rounded-[2.5rem] p-8 md:p-10 border border-gray-100 shadow-xl shadow-gray-200/40">
            <form action="{{ route('analysis.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" x-data="audioUploader()">
                @csrf
                
                {{-- Title Input --}}
                <div>
                    <label for="title" class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-3">Judul Sesi Analisa</label>
                    <input type="text" name="title" id="title" required 
                           class="w-full bg-gray-50 border-transparent focus:border-bima-red focus:bg-white focus:ring-0 rounded-2xl px-6 py-4 text-gray-900 font-bold placeholder-gray-300 transition-all"
                           placeholder="Contoh: Bimbingan Skripsi Bab 1 (Senin)">
                    @error('title')
                        <p class="text-bima-red text-xs mt-2 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Audio Upload --}}
                <div>
                    <label class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-3">File Audio (MP3/WAV)</label>
                    
                    <div class="relative border-2 border-dashed border-gray-200 rounded-[2rem] p-10 hover:border-bima-red hover:bg-red-50/30 transition-all text-center" 
                         :class="{'border-bima-red bg-red-50/30': fileName}">
                        
                        <input type="file" name="audio" id="audio" accept=".mp3,.wav" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleFileChange">
                        
                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-4" :class="{'bg-bima-red text-white': fileName}">
                                <i data-lucide="music" class="w-8 h-8" x-show="!fileName"></i>
                                <i data-lucide="check" class="w-8 h-8" x-show="fileName" style="display: none;"></i>
                            </div>
                            <h3 class="font-bold text-gray-900" x-text="fileName || 'Klik atau seret file audio ke sini'"></h3>
                            <p class="text-xs text-gray-500 mt-2 font-medium" x-show="!fileName">Maksimal ukuran file: 100MB</p>
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
                        <span class="font-bold uppercase tracking-wider text-sm">Unggah & Mulai Proses</span>
                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('audioUploader', () => ({
                fileName: '',
                fileSize: '',
                
                handleFileChange(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    
                    // Client-side validation: 100MB
                    if (file.size > 100 * 1024 * 1024) {
                        alert('Ukuran file melebihi 100MB. Silakan pilih file yang lebih kecil.');
                        e.target.value = '';
                        this.fileName = '';
                        return;
                    }
                    
                    this.fileName = file.name;
                    this.fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                }
            }));
        });
    </script>
</x-layouts.app>
