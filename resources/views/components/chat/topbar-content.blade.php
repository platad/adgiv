{{-- resources/views/components/chat/topbar-content.blade.php --}}
@props(['activeSession'])
<div class="flex items-center gap-3 flex-1 min-w-0">
    <div id="topbar-session-title" class="text-sm font-bold text-gray-800 truncate" x-text="currentSessionTitle">
        {{ $activeSession->title }}
    </div>
    <template x-if="linkedDocument">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[0.65rem] font-bold bg-purple-50 text-purple-600 border border-purple-100 shrink-0 shadow-sm">
            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
            <span x-text="linkedDocument.filename"></span>
        </span>
    </template>
</div>
<div class="flex items-center gap-2 shrink-0">
    <button class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 shadow-sm transition"
            onclick="window.dispatchEvent(new CustomEvent('open-upload-modal'))" title="Upload Dataset CSV">
        <svg class="w-3.5 h-3.5 text-black" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        Upload CSV
    </button>
</div>
