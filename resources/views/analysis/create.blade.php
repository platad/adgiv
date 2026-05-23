<x-layouts.app title="Input Analisa">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 animate-fade-in" x-data="audioUploader()">
        
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">Mulai Analisa Baru</h1>
            <p class="text-gray-500 font-medium mt-2">Unggah file audio percakapan bimbingan akademik Anda (Maksimal 50MB / format MP3, WAV, M4A, WEBM, AAC, dll).</p>
        </div>

        <div class="bg-white rounded-[2.5rem] p-8 md:p-10 border border-gray-100 shadow-xl shadow-gray-200/40">
            <form @submit.prevent="handleSubmit" method="POST" enctype="multipart/form-data" class="space-y-8">
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
                    <label class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-3">File Audio (MP3, WAV, M4A, WEBM, AAC)</label>
                    
                    <div class="relative border-2 border-dashed border-gray-200 rounded-[2rem] p-10 hover:border-bima-red hover:bg-red-50/30 transition-all text-center" 
                         :class="{'border-bima-red bg-red-50/30': fileName}">
                        
                        <input type="file" name="audio" id="audio" accept="audio/*,.mp3,.wav,.m4a,.webm,.ogg,.aac" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleFileChange">
                        
                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-4" :class="{'bg-bima-red text-white': fileName}">
                                <i data-lucide="music" class="w-8 h-8" x-show="!fileName"></i>
                                <i data-lucide="check" class="w-8 h-8" x-show="fileName" style="display: none;"></i>
                            </div>
                            <h3 class="font-bold text-gray-900" x-text="fileName || 'Klik atau seret file audio ke sini'"></h3>
                            <p class="text-xs text-gray-500 mt-2 font-medium" x-show="!fileName">Maksimal ukuran file: 50MB (Format MP3, WAV, M4A, WEBM, AAC, dll)</p>
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
                
                <h2 class="text-2xl font-black uppercase tracking-tight mb-2">Frontend Parser Aktif</h2>
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
                        <span>Inisialisasi sesi bimbingan</span>
                    </div>
                    
                    <div class="flex items-center gap-3 transition-opacity" :class="{'opacity-100 text-white': activeStep >= 2, 'opacity-30 text-white/50': activeStep < 2}">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center border text-[0.6rem] transition-colors" :class="{'bg-green-500 border-green-500 text-white': activeStep > 2, 'bg-bima-red border-bima-red text-white': activeStep === 2, 'border-white/20': activeStep < 2}">
                            <span x-show="activeStep <= 2">2</span>
                            <i data-lucide="check" class="w-3 h-3" x-show="activeStep > 2" style="display: none;"></i>
                        </div>
                        <span>Slicing Berkas Audio di Browser</span>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-3 transition-opacity" :class="{'opacity-100 text-white': activeStep >= 3, 'opacity-30 text-white/50': activeStep < 3}">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center border text-[0.6rem] transition-colors" :class="{'bg-green-500 border-green-500 text-white': activeStep > 3, 'bg-bima-red border-bima-red text-white': activeStep === 3, 'border-white/20': activeStep < 3}">
                                <span x-show="activeStep <= 3">3</span>
                                <i data-lucide="check" class="w-3 h-3" x-show="activeStep > 3" style="display: none;"></i>
                            </div>
                            <span x-text="'Analisis Potongan (' + currentChunkIndex + '/' + totalChunksCount + ') GPT-Audio-1.5'"></span>
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
                                            Selesai
                                        </span>
                                        <span x-show="idx === currentChunkIndex && !uploadError" class="text-bima-red flex items-center gap-1 animate-pulse">
                                            <svg class="w-2.5 h-2.5 animate-spin border border-bima-red rounded-full border-t-transparent mr-1" fill="none" viewBox="0 0 24 24"></svg>
                                            Proses
                                        </span>
                                        <span x-show="idx > currentChunkIndex" class="text-white/30 flex items-center gap-1 font-normal">
                                            Antrean
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
                        <h4 class="text-xs font-black uppercase tracking-wider text-amber-400">Pemberitahuan Penting</h4>
                        <p class="text-[0.68rem] text-white/70 font-semibold mt-1 leading-relaxed">
                            Mohon tidak menutup browser, me-refresh halaman, atau beralih aplikasi selama pemrosesan berlangsung agar sesi pengiriman data tetap sinkron.
                        </p>
                    </div>
                </div>

                {{-- Stop / Cancel Button --}}
                <div class="mt-6 pt-6 border-t border-white/10 flex items-center justify-center">
                    <button type="button" @click="handleCancel" class="w-full flex items-center justify-center gap-2 bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-white/80 hover:text-white px-5 py-3 rounded-xl transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <rect x="5" y="5" width="14" height="14" rx="2" stroke="currentColor" stroke-width="2"></rect>
                        </svg>
                        <span class="text-xs font-black uppercase tracking-wider">Hentikan Sesi Analisa</span>
                    </button>
                </div>
                
                {{-- Error Message Box --}}
                <div x-show="uploadError" style="display: none;" class="mt-8 p-4 bg-red-500/20 border border-red-500/30 rounded-2xl text-red-300 text-xs font-bold text-center">
                    <span x-text="uploadError"></span>
                    <button type="button" @click="isUploading = false" class="block mx-auto mt-3 underline uppercase tracking-widest text-[0.65rem] hover:text-white transition-colors">Batalkan & Coba Lagi</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('audioUploader', () => ({
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
                    if (idx < this.currentChunkIndex) return 'Selesai';
                    if (idx === this.currentChunkIndex) return 'Menganalisis';
                    return 'Antrean';
                },

                handleCancel() {
                    if (confirm('Apakah Anda yakin ingin membatalkan sesi analisis ini? Sesi Anda akan dihentikan.')) {
                        this.isCancelled = true;
                        this.isUploading = false;
                        window.location.reload();
                    }
                },
                
                handleFileChange(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    
                    // Client-side validation: 50MB
                    if (file.size > 50 * 1024 * 1024) {
                        alert('Ukuran file melebihi 50MB. Silakan pilih file yang lebih kecil.');
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
                    const file = audioInput.files[0];

                    if (!titleInput.value || !file) {
                        alert('Harap isi judul sesi dan pilih berkas audio.');
                        return;
                    }

                    this.isUploading = true;
                    this.uploadError = null;
                    this.progressPercent = 5;
                    this.activeStep = 1;
                    this.progressText = 'Menghubungi server cPanel untuk menginisialisasi sesi BIMA...';

                    try {
                        // Step 1: Initialize Analysis Record
                        const initResponse = await fetch('{{ route("analysis.initialize") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ title: titleInput.value })
                        });

                        if (!initResponse.ok) {
                            throw new Error('Gagal menginisialisasi sesi di server cPanel.');
                        }

                        const initData = await initResponse.json();
                        const analysisId = initData.analysis_id;

                        this.activeStep = 2;
                        this.progressPercent = 20;
                        this.progressText = 'Membaca dan memproses berkas audio di memori browser (Client-Side)...';

                        // Step 2: Slice and encode using browser's AudioContext (Client-Side)
                        this.progressText = 'Membaca data biner audio dari browser...';
                        const arrayBuffer = await file.arrayBuffer();

                        this.progressText = 'Mendecode file audio ke PCM Audio (Client-Side)...';
                        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        const decodedBuffer = await audioCtx.decodeAudioData(arrayBuffer);

                        this.progressText = 'Membagi audio menjadi potongan-potongan presisi...';
                        const totalFrames = decodedBuffer.length;
                        const duration = decodedBuffer.duration; // duration in seconds
                        
                        // Dynamic chunk duration target: 100 seconds per chunk (e.g. 5 mins -> 3 chunks, 30 mins -> 18 chunks)
                        const targetChunkDuration = 100;
                        const numChunks = Math.max(1, Math.ceil(duration / targetChunkDuration));
                        const chunkFrames = Math.ceil(totalFrames / numChunks);

                        this.totalChunksCount = numChunks;
                        this.currentChunkIndex = 0;

                        // Helper functions for dynamic, highly compressed mono WAV encoding
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
                            const format = 1; // raw PCM
                            const bitDepth = 16;
                            const channelData = buffer.getChannelData(0); // Mono reduction
                            const bufferLength = channelData.length * 2;
                            const arrayBuffer = new ArrayBuffer(44 + bufferLength);
                            const view = new DataView(arrayBuffer);
                            
                            writeString(view, 0, 'RIFF');
                            view.setUint32(4, 36 + bufferLength, true);
                            writeString(view, 8, 'WAVE');
                            writeString(view, 12, 'fmt ');
                            view.setUint32(16, 16, true);
                            view.setUint16(20, format, true);
                            view.setUint16(22, 1, true); // Mono
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

                        // Dynamically generate the slices
                        const chunks = [];
                        for (let i = 0; i < numChunks; i++) {
                            const start = i * chunkFrames;
                            const end = Math.min(totalFrames, (i + 1) * chunkFrames);
                            chunks.push(bufferToWav(sliceAudioBuffer(audioCtx, decodedBuffer, start, end)));
                        }

                        // Step 3: Sequential parallel mapping chunk uploads
                        for (let i = 0; i < chunks.length; i++) {
                            if (this.isCancelled) {
                                break;
                            }
                            const chunkIndex = i + 1;
                            this.currentChunkIndex = chunkIndex;
                            this.activeStep = 3;
                            this.progressPercent = 20 + Math.round((i / chunks.length) * 75);
                            this.progressText = `Mengirim & menganalisis Potongan ${chunkIndex}/${numChunks} dengan GPT-Audio-1.5...`;

                            const formData = new FormData();
                            formData.append('audio_chunk', chunks[i], `chunk_${chunkIndex}.wav`);

                            const chunkResponse = await fetch(`/analysis/${analysisId}/chunk`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });

                            if (!chunkResponse.ok) {
                                const errorData = await chunkResponse.json().catch(() => ({}));
                                throw new Error(errorData.message || `Gagal menganalisis potongan audio ${chunkIndex}.`);
                            }
                        }

                        if (this.isCancelled) return;

                        // Step 4: Finished all chunk uploads!
                        this.activeStep = 4;
                        this.progressPercent = 100;
                        this.progressText = 'Selesai! Menyusun halaman hasil progresif...';

                        setTimeout(() => {
                            window.location.href = `/analysis/${analysisId}/result`;
                        }, 800);

                    } catch (err) {
                        this.uploadError = err.message || 'Gagal memproses audio. Harap periksa jaringan Anda.';
                    }
                }
            }));
        });
    </script>
</x-layouts.app>
