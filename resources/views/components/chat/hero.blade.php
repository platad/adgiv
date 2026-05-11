{{-- resources/views/components/chat/hero.blade.php --}}
<div class="flex flex-col items-center justify-center h-full text-center max-w-xl mx-auto space-y-8 animate-fade-in">
    
    {{-- Greeting Bubble --}}
    <template x-if="messages.find(m => m.id === 'greeting')">
        <div class="bg-gray-50 border border-gray-200 rounded-2xl px-4 py-2 text-sm text-gray-600 mb-4 animate-bounce-slow shadow-sm">
            <span x-text="messages.find(m => m.id === 'greeting').content"></span>
        </div>
    </template>

    <div class="space-y-3">
        <h3 class="text-3xl font-bold text-gray-900 tracking-tight">Analisis Suara BIMA</h3>
        <p class="text-gray-500 text-lg">Pilih file audio atau rekam langsung untuk memulai perdebatan.</p>
    </div>
    
    <div class="w-full max-w-md bg-white p-8 rounded-3xl border-2 border-dashed border-gray-200 hover:border-purple-300 transition-colors shadow-sm relative group">
        <x-voice-input />
        
        <div class="mt-8 grid grid-cols-2 gap-4">
            {{-- Upload Info --}}
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-100 text-left">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mb-2">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                <div class="text-[0.65rem] font-bold text-blue-800 uppercase">Upload Audio</div>
                <div class="text-[0.6rem] text-blue-600 mt-0.5">MP3, M4A, WAV</div>
            </div>
            {{-- Record Info --}}
            <div class="p-4 rounded-2xl bg-purple-50 border border-purple-100 text-left">
                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center mb-2">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                </div>
                <div class="text-[0.65rem] font-bold text-purple-800 uppercase">Rekam Suara</div>
                <div class="text-[0.6rem] text-purple-600 mt-0.5">Gunakan Mikrofon</div>
            </div>
        </div>
    </div>
</div>
