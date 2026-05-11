{{-- resources/views/components/chat/message-user.blade.php --}}
<div class="flex flex-row-reverse gap-4 w-full max-w-3xl mx-auto group">
    <div class="shrink-0">
        <div class="w-9 h-9 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-[0.8rem] font-black text-gray-600 shadow-sm border border-white">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
    </div>
    <div class="flex flex-col items-end gap-1.5 max-w-[85%]">
        {{-- Attached Files (Audios) --}}
        <template x-if="msg.metadata && msg.metadata.attached_files">
            <div class="flex flex-wrap gap-2 justify-end mb-1">
                <template x-for="f in msg.metadata.attached_files" :key="f.name">
                    <div class="flex flex-col bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden max-w-[280px] hover:shadow-md transition">
                        <div class="flex items-center gap-2 px-3 py-2 bg-gray-50/50 border-b border-gray-100">
                            <div class="p-1.5 bg-purple-100 text-purple-600 rounded-lg">
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 18v3c0 .6.4 1 1 1h4v-3h3v-3h2l1.4-1.4a6.5 6.5 0 1 0-4-4Z"/><circle cx="16.5" cy="7.5" r=".5"/></svg>
                            </div>
                            <span class="text-[0.7rem] font-black text-gray-700 truncate" x-text="f.name"></span>
                        </div>
                        <template x-if="f.transcription">
                            <div class="px-3 py-2.5 text-[0.8rem] text-gray-500 italic bg-white leading-relaxed font-medium" x-text="'&quot;' + f.transcription + '&quot;'"></div>
                        </template>
                    </div>
                </template>
            </div>
        </template>
        
        {{-- Text Content --}}
        <template x-if="msg.content">
            <div class="bg-[#1a1a1a] text-white px-5 py-3 rounded-3xl rounded-tr-sm text-[0.95rem] leading-relaxed break-words shadow-lg shadow-black/5 font-medium tracking-tight">
                <p x-text="msg.content"></p>
            </div>
        </template>
        
        <div class="flex items-center gap-2 mt-1 mr-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <span class="text-[0.6rem] text-gray-300 font-bold uppercase tracking-[0.1em]" x-text="formatTime(msg.created_at)"></span>
            <svg class="w-3 h-3 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
    </div>
</div>
