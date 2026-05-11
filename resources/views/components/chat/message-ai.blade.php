{{-- resources/views/components/chat/message-ai.blade.php --}}
<div class="flex flex-row gap-4 w-full max-w-3xl mx-auto group">
    <div class="shrink-0">
        <div class="w-9 h-9 rounded-2xl bg-black flex items-center justify-center text-white shadow-lg border border-black ring-2 ring-white">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
    </div>
    <div class="flex flex-col gap-1.5 max-w-[85%] items-start w-full">
        {{-- Thinking Block --}}
        <template x-if="msg.metadata && msg.metadata.thinking">
            <div x-data="{ expanded: false }" class="mb-3 border border-gray-100 rounded-2xl overflow-hidden bg-gray-50/50 max-w-2xl w-full transition-all duration-300 hover:border-gray-200">
                <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2.5 text-[0.65rem] font-black text-gray-400 uppercase tracking-[0.2em] hover:bg-gray-100/50 transition-colors">
                    <span class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></div>
                        Analisis Proses Berpikir
                    </span>
                    <svg class="w-3.5 h-3.5 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div x-show="expanded" x-collapse class="px-4 pb-4 border-t border-gray-100 text-[0.8rem] text-gray-500 leading-relaxed font-medium bg-white/50"><div class="mt-3" x-text="msg.metadata.thinking"></div></div>
            </div>
        </template>
        
        {{-- Main Message Body --}}
        <div class="bg-white border border-gray-100 px-5 py-4 rounded-3xl rounded-tl-sm shadow-sm hover:shadow-md transition-shadow w-full">
            <div class="text-gray-800 text-[0.95rem] leading-relaxed break-words markdown-body prose prose-sm max-w-none font-medium tracking-tight" x-html="formatMarkdown(msg.content)"></div>
        </div>
        
        <div class="flex items-center gap-2 mt-1 ml-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <span class="text-[0.6rem] text-gray-300 font-bold uppercase tracking-[0.1em]" x-text="formatTime(msg.created_at)"></span>
        </div>
    </div>
</div>
