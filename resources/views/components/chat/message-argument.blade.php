{{-- resources/views/components/chat/message-argument.blade.php --}}
<div class="flex flex-row gap-4 w-full max-w-3xl mx-auto animate-fade-in-up">
    <div class="shrink-0">
        <div class="w-8 h-8 rounded-full bg-black flex items-center justify-center text-white border border-gray-200 shadow-sm" x-html="agentIcon(msg.metadata.agent_name.toLowerCase().replace(/ /g, '_'))">
        </div>
    </div>
    <div class="flex flex-col gap-1 max-w-[85%] items-start w-full">
        <div class="bg-white border border-gray-100 px-5 py-4 rounded-3xl rounded-tl-sm shadow-sm hover:shadow-md transition-shadow w-full">
            <div class="flex items-center justify-between mb-2">
                <div class="text-[0.65rem] font-black text-purple-600 uppercase tracking-[0.2em]" x-text="msg.metadata.agent_name"></div>
                <div class="text-[0.6rem] font-bold text-gray-300 uppercase" x-text="'Ronde ' + msg.metadata.round"></div>
            </div>
            <div class="text-[0.92rem] text-gray-700 leading-relaxed markdown-body prose prose-sm max-w-none" x-html="formatMarkdown(msg.content.split(']: ')[1] || msg.content)"></div>
        </div>
        <div class="flex items-center gap-2 mt-1 ml-2">
            <span class="text-[0.6rem] text-gray-300 font-bold uppercase tracking-widest" x-text="formatTime(msg.created_at)"></span>
            <template x-if="msg.metadata.verdict">
                <span class="px-2 py-0.5 rounded-full text-[0.55rem] font-black uppercase tracking-tighter" 
                      :class="msg.metadata.verdict === 'Dosen' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600'"
                      x-text="'Cenderung ' + msg.metadata.verdict"></span>
            </template>
        </div>
    </div>
</div>
