{{-- resources/views/components/chat/typing-indicator.blade.php --}}
<div class="flex flex-row gap-4 w-full max-w-3xl mx-auto animate-pulse">
    <div class="shrink-0">
        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center border border-gray-200">
            <svg class="w-4 h-4 text-gray-400 animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
    </div>
    <div class="flex flex-col gap-1.5 items-start">
        <div class="flex items-center gap-1.5 px-4 py-3 bg-gray-50 rounded-2xl border border-gray-100 shadow-sm">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-300 animate-bounce"></span>
            <span class="w-1.5 h-1.5 rounded-full bg-gray-300 animate-bounce" style="animation-delay: 0.15s"></span>
            <span class="w-1.5 h-1.5 rounded-full bg-gray-300 animate-bounce" style="animation-delay: 0.3s"></span>
        </div>
    </div>
</div>
