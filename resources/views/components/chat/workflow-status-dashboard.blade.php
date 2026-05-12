{{-- resources/views/components/chat/workflow-status-dashboard.blade.php --}}
<div class="space-y-8 relative pt-4">
    {{-- Vertical Line Connector --}}
    <div class="absolute left-[23px] top-10 bottom-10 w-0.5 bg-gray-100"></div>

    {{-- Step 1: Analisa Suara --}}
    <div class="relative flex items-start gap-4 group">
        <div class="relative z-10">
            <div class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-500 shadow-lg border-4 border-white"
                 :class="activeStep === 1 ? 'bg-bima-red text-white scale-110 ring-4 ring-red-100' : 
                         (session.raw_transcription !== '-' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-400')">
                <template x-if="activeStep === 1">
                    <div class="absolute inset-0 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
                </template>
                <i data-lucide="mic" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="flex-1 pt-1">
            <h4 class="text-xs font-black uppercase tracking-widest" 
                :class="activeStep === 1 ? 'text-bima-red' : (session.raw_transcription !== '-' ? 'text-gray-900' : 'text-gray-400')">
                Step 1: Analisa Suara
            </h4>
            <p class="text-[0.7rem] text-gray-400 mt-1 font-medium" x-text="activeStep === 1 ? 'Menyimpan transkripsi...' : (session.raw_transcription !== '-' ? 'Selesai' : 'Menunggu antrean')"></p>
        </div>
    </div>

    {{-- Step 2: Merapikan Hasil --}}
    <div class="relative flex items-start gap-4 group">
        <div class="relative z-10">
            <div class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-500 shadow-lg border-4 border-white"
                 :class="activeStep === 2 ? 'bg-bima-red text-white scale-110 ring-4 ring-red-100' : 
                         (session.refined_transcription !== '-' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-400')">
                <template x-if="activeStep === 2">
                    <div class="absolute inset-0 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
                </template>
                <i data-lucide="wand-2" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="flex-1 pt-1">
            <h4 class="text-xs font-black uppercase tracking-widest"
                :class="activeStep === 2 ? 'text-bima-red' : (session.refined_transcription !== '-' ? 'text-gray-900' : 'text-gray-400')">
                Step 2: Merapikan Hasil
            </h4>
            <p class="text-[0.7rem] text-gray-400 mt-1 font-medium" x-text="activeStep === 2 ? 'Merapikan teks...' : (session.refined_transcription !== '-' ? 'Selesai' : 'Menunggu antrean')"></p>
        </div>
    </div>

    {{-- Step 3: Pencocokan Suara --}}
    <div class="relative flex items-start gap-4 group">
        <div class="relative z-10">
            <div class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-500 shadow-lg border-4 border-white"
                 :class="activeStep === 3 ? 'bg-bima-red text-white scale-110 ring-4 ring-red-100' : 
                         (session.matched_transcription !== '-' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-400')">
                <template x-if="activeStep === 3">
                    <div class="absolute inset-0 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
                </template>
                <i data-lucide="git-compare" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="flex-1 pt-1">
            <h4 class="text-xs font-black uppercase tracking-widest"
                :class="activeStep === 3 ? 'text-bima-red' : (session.matched_transcription !== '-' ? 'text-gray-900' : 'text-gray-400')">
                Step 3: Pencocokan Suara
            </h4>
            <p class="text-[0.7rem] text-gray-400 mt-1 font-medium" x-text="activeStep === 3 ? 'Sinkronisasi teks...' : (session.matched_transcription !== '-' ? 'Selesai' : 'Menunggu antrean')"></p>
        </div>
    </div>

    {{-- Step 4: Advice Giving --}}
    <div class="relative flex items-start gap-4 group">
        <div class="relative z-10">
            <div class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-500 shadow-lg border-4 border-white"
                 :class="activeStep === 4 ? 'bg-bima-red text-white scale-110 ring-4 ring-red-100' : 
                         (session.advice_category !== '-' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-400')">
                <template x-if="activeStep === 4">
                    <div class="absolute inset-0 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
                </template>
                <i data-lucide="award" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="flex-1 pt-1">
            <h4 class="text-xs font-black uppercase tracking-widest"
                :class="activeStep === 4 ? 'text-bima-red' : (session.advice_category !== '-' ? 'text-gray-900' : 'text-gray-400')">
                Step 4: Advice Giving
            </h4>
            <p class="text-[0.7rem] text-gray-400 mt-1 font-medium" x-text="activeStep === 4 ? 'Klasifikasi advice...' : (session.advice_category !== '-' ? 'Selesai' : 'Menunggu antrean')"></p>
        </div>
    </div>

    {{-- Step 5: Karakter Relasi --}}
    <div class="relative flex items-start gap-4 group">
        <div class="relative z-10">
            <div class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-500 shadow-lg border-4 border-white"
                 :class="activeStep === 5 ? 'bg-bima-red text-white scale-110 ring-4 ring-red-100' : 
                         (session.character_category !== '-' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-400')">
                <template x-if="activeStep === 5">
                    <div class="absolute inset-0 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
                </template>
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="flex-1 pt-1">
            <h4 class="text-xs font-black uppercase tracking-widest"
                :class="activeStep === 5 ? 'text-bima-red' : (session.character_category !== '-' ? 'text-gray-900' : 'text-gray-400')">
                Step 5: Karakter Relasi
            </h4>
            <p class="text-[0.7rem] text-gray-400 mt-1 font-medium" x-text="activeStep === 5 ? 'Analisis relasi...' : (session.character_category !== '-' ? 'Selesai' : 'Menunggu antrean')"></p>
        </div>
    </div>

    {{-- Step 6: Intonasi & Insights --}}
    <div class="relative flex items-start gap-4 group">
        <div class="relative z-10">
            <div class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-500 shadow-lg border-4 border-white"
                 :class="activeStep === 6 ? 'bg-bima-red text-white scale-110 ring-4 ring-red-100' : 
                         (session.intonation_analysis !== '-' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-400')">
                <template x-if="activeStep === 6">
                    <div class="absolute inset-0 border-4 border-white/20 border-t-white rounded-full animate-spin"></div>
                </template>
                <i data-lucide="zap" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="flex-1 pt-1">
            <h4 class="text-xs font-black uppercase tracking-widest"
                :class="activeStep === 6 ? 'text-bima-red' : (session.intonation_analysis !== '-' ? 'text-gray-900' : 'text-gray-400')">
                Step 6: Intonasi & Insights
            </h4>
            <p class="text-[0.7rem] text-gray-400 mt-1 font-medium" x-text="activeStep === 6 ? 'Laporan akhir...' : (session.intonation_analysis !== '-' ? 'Selesai' : 'Menunggu antrean')"></p>
        </div>
    </div>
</div>
