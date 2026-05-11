{{-- resources/views/components/chat/workflow-status.blade.php --}}
<div x-show="workflowBubble" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="w-full max-w-lg mx-auto">
    
    <div class="bg-white border border-gray-200 rounded-2xl shadow-xl p-3 overflow-hidden relative ring-1 ring-black/5">
        {{-- Progress Bar --}}
        <div class="absolute top-0 left-0 h-1 bg-gray-100 w-full">
            <div class="h-full bg-purple-600 transition-all duration-500" 
                 :style="'width: ' + ((currentRound / totalRounds) * 100) + '%'"></div>
        </div>

        <div class="flex items-center justify-between mb-2 mt-1">
            <div class="flex items-center gap-2">
                <div class="flex -space-x-1.5">
                    <template x-for="agent in agents" :key="agent.name">
                        <div class="w-7 h-7 rounded-full border-2 border-white flex items-center justify-center transition-all duration-300 shadow-sm"
                             :class="agent.status === 'thinking' ? 'bg-purple-600 text-white scale-110 z-10' : (agent.status === 'done' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-400')"
                             :title="agent.display">
                            <span x-html="agentIcon(agent.name)" class="scale-75"></span>
                        </div>
                    </template>
                </div>
                <div class="flex flex-col ml-1">
                    <span class="text-[0.65rem] font-black text-gray-800 uppercase tracking-wider leading-none" x-text="workflowBubble.label"></span>
                    <span class="text-[0.55rem] text-gray-400 font-bold uppercase mt-0.5" x-text="'Ronde ' + currentRound + ' dari ' + totalRounds"></span>
                </div>
            </div>
            
            {{-- Tendency Meter Compact --}}
            <div class="flex flex-col items-end gap-1 min-w-[80px]">
                <div class="flex justify-between w-full text-[0.55rem] font-black uppercase tracking-tighter">
                    <span :class="tendencyScore < 0.5 ? 'text-purple-600' : 'text-gray-300'">MHS</span>
                    <span :class="tendencyScore > 0.5 ? 'text-blue-600' : 'text-gray-300'">DSN</span>
                </div>
                <div class="h-1 w-full bg-gray-100 rounded-full overflow-hidden flex">
                    <div class="h-full bg-purple-500 transition-all duration-700" :style="'width: ' + ((1 - tendencyScore) * 100) + '%'"></div>
                    <div class="h-full bg-blue-500 transition-all duration-700" :style="'width: ' + (tendencyScore * 100) + '%'"></div>
                </div>
            </div>
        </div>
    </div>
</div>
