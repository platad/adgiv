{{-- resources/views/components/chat/message-decision.blade.php --}}
@props(['msg'])
<div class="flex flex-row gap-4 w-full max-w-3xl mx-auto">
    <div class="shrink-0">
        <div class="w-8 h-8 rounded-full bg-black flex items-center justify-center text-white">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
    </div>
    <div class="flex flex-col gap-1.5 max-w-[85%] items-start w-full">
        <div class="border-2 rounded-3xl p-5 bg-white shadow-md w-full" :class="msg.metadata.decision === 'Dosen' ? 'border-purple-200' : 'border-blue-200'">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 rounded-2xl" :class="msg.metadata.decision === 'Dosen' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600'">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 13L21 6L18 3L11 10"/><path d="M11 10L14 13"/><path d="M3 21L7 21L21 7L17 3L3 17L3 21Z"/></svg>
                </div>
                <div>
                    <div class="text-[0.65rem] uppercase tracking-widest text-gray-400 font-bold">Keputusan Akhir</div>
                    <div class="text-xl font-black text-gray-900" x-text="msg.metadata.decision"></div>
                </div>
                <div class="ml-auto text-right">
                    <div class="text-2xl font-black" :class="msg.metadata.decision === 'Dosen' ? 'text-purple-600' : 'text-blue-600'" x-text="Math.round(msg.metadata.confidence * 100) + '%'"></div>
                    <div class="text-[0.6rem] text-gray-400 font-bold uppercase">Confidence</div>
                </div>
            </div>
            <p class="text-[0.95rem] text-gray-700 leading-relaxed font-medium" x-text="msg.content"></p>
        </div>
        <span class="text-[0.6rem] text-gray-400 mt-1" x-text="formatTime(msg.created_at)"></span>
    </div>
</div>
