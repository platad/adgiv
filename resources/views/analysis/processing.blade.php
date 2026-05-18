<x-layouts.app title="Processing Analysis">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 min-h-screen flex flex-col items-center justify-center animate-fade-in"
        x-data="processingFlow('{{ route('analysis.process', $analysis->id) }}')">

        <div
            class="bg-white p-10 md:p-16 rounded-[3rem] shadow-2xl shadow-gray-200/50 text-center w-full max-w-2xl border border-gray-100">

            {{-- Main Animation (Simplified) --}}
            <div class="relative w-24 h-24 mx-auto mb-10">
                <div
                    class="absolute inset-0 bg-bima-red text-white rounded-full flex items-center justify-center shadow-lg shadow-red-500/20 z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-10 h-10">
                        <path d="M12 4.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 1 0 0-5Z" />
                        <path d="m10.2 13.2 2.1 2.1" />
                        <path d="M13.2 10.2 19 6" />
                        <path d="M10.8 13.8 5 18" />
                        <path d="M6 10.8 2 12" />
                        <path d="M18 12.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 1 0 0-5Z" />
                        <path d="M12 19.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 1 0 0-5Z" />
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mb-4">Supervisory AI Sedang Bekerja
            </h1>
            <p class="text-gray-500 font-medium mb-8">Memproses sesi <span
                    class="font-bold text-gray-900">"{{ $analysis->title }}"</span></p>

            {{-- Step indicators --}}
            <div class="space-y-4 max-w-sm mx-auto text-left">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex flex-col transition-all duration-500"
                        :class="{ 'opacity-100 scale-100': currentStep >= index, 'opacity-40 scale-95': currentStep < index }">
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center"
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
                            <span class="text-sm font-bold uppercase tracking-wider"
                                :class="{ 'text-gray-900': currentStep >= index, 'text-gray-400': currentStep < index }"
                                x-text="step.title || step"></span>
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

            {{-- Error Message --}}
            <div x-show="error" style="display: none;"
                class="mt-8 p-4 bg-red-50 border border-red-100 rounded-2xl text-red-600 text-sm font-bold">
                <i data-lucide="alert-triangle" class="w-5 h-5 inline-block mr-2"></i>
                <span x-text="error"></span>
                <div class="mt-4">
                    <a href="{{ route('analysis.create') }}" class="underline hover:text-red-800">Kembali dan coba
                        lagi</a>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('processingFlow', (processUrl) => ({
                steps: [
                    {
                        title: 'Mengirim data ke server AI...',
                        subSteps: [
                            'Mengunggah berkas audio percakapan...',
                            'Validasi format berkas & bitrate suara...',
                            'Alokasi sumber daya Multi-Agent AI...'
                        ]
                    },
                    {
                        title: 'Multimodal AI mendengarkan intonasi...',
                        subSteps: [
                            'Mendeteksi gelombang frekuensi suara...',
                            'Mengukur kenaikan intonasi (pitch up)...',
                            'Mengukur penurunan nada tegas (pitch down)...'
                        ]
                    },
                    {
                        title: 'Mentranskripsi dan mengidentifikasi speaker...',
                        subSteps: [
                            'Transkripsi teks baris-demi-baris...',
                            'Diarisasi pembicara (Dosen vs Mahasiswa)...',
                            'Penyelarasan stempel waktu (timestamps)...'
                        ]
                    },
                    {
                        title: 'Menganotasi elemen Advice-Giving...',
                        subSteps: [
                            'Mengekstrak kalimat saran akademik...',
                            'Menganalisis tipe rekomendasi (korektif/direktif)...',
                            'Pemetaan karakter relasi (CDA)...'
                        ]
                    },
                    {
                        title: 'Menyusun hasil akhir...',
                        subSteps: [
                            'Sinkronisasi anotasi dengan teks transkripsi...',
                            'Membuat visualisasi grafik dinamika alur...',
                            'Mengompilasi interpretasi Supervisory AI...'
                        ]
                    }
                ],
                currentStep: 0,
                currentSubStep: 0,
                error: null,

                init() {
                    this.startVisualSteps();
                    this.triggerAnalysis();
                },

                startVisualSteps() {
                    // Let's create an interval to tick sub-steps and steps
                    const interval = setInterval(() => {
                        if (this.error || this.currentStep >= 5) {
                            clearInterval(interval);
                            return;
                        }

                        const activeStep = this.steps[this.currentStep];
                        if (activeStep && activeStep.subSteps && this.currentSubStep < activeStep.subSteps.length - 1) {
                            this.currentSubStep++;
                        } else {
                            if (this.currentStep < 4) {
                                this.currentStep++;
                                this.currentSubStep = 0;
                            }
                        }
                    }, 2200); // Progresses sub-step/step every 2.2s
                },

                async triggerAnalysis() {
                    try {
                        const response = await fetch(processUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok && data.status === 'success') {
                            // Instantly complete all steps to show perfect success state!
                            this.currentStep = 4;
                            this.currentSubStep = 2;
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1000);
                        } else {
                            this.error = data.message ||
                                'Terjadi kesalahan sistem saat pemrosesan.';
                            this.currentStep = -1; // Stop animation
                        }
                    } catch (err) {
                        this.error = 'Koneksi terputus. Silakan periksa jaringan Anda.';
                        this.currentStep = -1;
                    }
                }
            }));
        });
    </script>
</x-layouts.app>
