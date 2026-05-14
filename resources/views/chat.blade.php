<x-layouts.app :activeSessionId="$activeSession->id">
    <x-slot:title>BIMA AI Dashboard Analisis</x-slot:title>

    <div class="flex-1 flex flex-col w-full bg-gray-50/30 overflow-hidden"
         x-data="chatApp(@js($activeSession->id), @js($activeSession->title))"
         x-on:submit-transcription.window="handleSubmit($event.detail.text, $event.detail.files)"
         x-on:start-multi-step-analysis.window="handleMultiStepAnalysis($event.detail.text)">
        
        {{-- ── Top: Input Section ── --}}
        <div class="shrink-0 bg-white border-b border-gray-100 p-4 lg:p-8 shadow-sm z-10">
            <div class="w-full max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-bima-red rounded-2xl flex items-center justify-center text-white shadow-lg shrink-0">
                        <i data-lucide="mic-2" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h2 class="text-xl lg:text-2xl font-black text-gray-900 tracking-tight">Mulai Analisis Bimbingan</h2>
                        <p class="text-xs lg:text-sm text-gray-500 font-medium italic">Rekam atau unggah audio untuk memulai workflow BIMA AI</p>
                    </div>
                </div>
                
                <div class="bg-white rounded-[1.5rem] lg:rounded-[2rem] shadow-2xl shadow-red-100/50 border border-red-50 p-2">
                    <x-voice-input />
                </div>
            </div>
        </div>

        {{-- ── Bottom: Dashboard Grids ── --}}
        <div class="flex-1 p-4 lg:p-8 overflow-y-auto">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-start">
                
                {{-- Left Grid: Process Flow (1/3) --}}
                <div class="lg:col-span-1 flex flex-col gap-6">
                    <div class="bg-white rounded-[2rem] lg:rounded-[2.5rem] p-6 lg:p-8 border border-gray-100 shadow-sm">
                        <h3 class="text-[0.6rem] lg:text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6 lg:mb-8 flex items-center gap-2">
                            <i data-lucide="activity" class="w-4 h-4"></i> Flow Process
                        </h3>
                        
                        <x-chat.workflow-status-dashboard />
                    </div>
                </div>

                {{-- Right Grid: Analysis Results (2/3) --}}
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <div class="bg-white rounded-[2rem] lg:rounded-[2.5rem] p-6 lg:p-8 border border-gray-100 shadow-sm relative overflow-hidden min-h-[400px] lg:min-h-[500px]">
                        <div class="absolute top-0 right-0 p-6 lg:p-10 opacity-[0.03] pointer-events-none">
                            <i data-lucide="brain-circuit" class="w-48 h-48 lg:w-64 lg:h-64 text-bima-red"></i>
                        </div>
                        
                        <h3 class="text-[0.6rem] lg:text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6 lg:mb-8 flex items-center gap-2">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Hasil Analisis Akhir
                        </h3>

                        <div class="space-y-6">
                            {{-- Placeholder if not analysing yet and no data --}}
                            <template x-if="!isAnalysing && session.advice_category === '-' && messages.filter(m => m.role === 'assistant').length === 0">
                                <div class="flex flex-col items-center justify-center py-12 lg:py-20 text-center space-y-4">
                                    <div class="w-16 h-16 lg:w-20 lg:h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300">
                                        <i data-lucide="file-search" class="w-8 h-8 lg:w-10 lg:h-10"></i>
                                    </div>
                                    <p class="text-gray-400 font-medium tracking-tight">Belum ada data untuk ditampilkan.<br>Silakan masukkan audio bimbingan.</p>
                                </div>
                            </template>

                            {{-- Final Results Display --}}
                            <div x-show="session.advice_category !== '-' || messages.find(m => m.role === 'assistant' && !m.isTyping)" x-transition>
                                <x-chat.analysis-results-dashboard />
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Transcription Modal --}}
        <div @open-transcription-modal.window="transcriptionModalOpen = true"
             class="relative z-[100]">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" style="display:none;" x-show="transcriptionModalOpen" x-transition.opacity @click="transcriptionModalOpen = false"></div>
            <div class="fixed inset-0 flex items-center justify-center p-6 pointer-events-none" style="display:none;" x-show="transcriptionModalOpen">
                <div class="bg-white rounded-[2.5rem] w-full max-w-4xl max-h-[80vh] overflow-hidden flex flex-col shadow-2xl pointer-events-auto border border-gray-100" x-show="transcriptionModalOpen" x-transition>
                    <div class="p-8 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-xl font-black text-gray-900">Hasil Transkripsi & Perbaikan</h3>
                        <button @click="transcriptionModalOpen = false" class="w-10 h-10 rounded-xl hover:bg-gray-50 transition flex items-center justify-center text-gray-400">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto p-8 space-y-8">
                        <div>
                            <h4 class="text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-3">1. Teks Asli (Raw)</h4>
                            <div class="p-6 bg-gray-50 rounded-2xl text-sm text-gray-600 leading-relaxed italic" x-text="session.raw_transcription"></div>
                        </div>
                        <div>
                            <h4 class="text-[0.65rem] font-black text-bima-red uppercase tracking-widest mb-3">2. Teks Rapih (Refined)</h4>
                            <div class="p-6 bg-red-50/50 rounded-2xl text-sm text-gray-900 leading-relaxed font-medium" x-text="session.refined_transcription"></div>
                        </div>
                        <div>
                            <h4 class="text-[0.65rem] font-black text-green-600 uppercase tracking-widest mb-3">3. Teks Final (Matched)</h4>
                            <div class="p-6 bg-green-50/50 rounded-2xl text-sm text-green-900 leading-relaxed font-bold" x-text="session.matched_transcription"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <x-slot:scripts>
        <x-chat.scripts :activeSession="$activeSession" />
    </x-slot:scripts>
</x-layouts.app>
