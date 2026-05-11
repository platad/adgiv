{{-- resources/views/components/chat/scripts/markdown.blade.php --}}
<script>
window.formatMarkdown = function(text) {
    if (!text) return '';
    let html = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    html = html.replace(/^&gt;\s?(.*)$/gm, '<blockquote class="border-l-4 border-gray-300 pl-4 py-1 text-gray-600 italic my-2">$1</blockquote>');
    html = html.replace(/```(\w*)\n?([\s\S]*?)(?:```|$)/g, function(match, lang, code) {
        const safeCode = encodeURIComponent(code);
        return `<div class="bg-[#fcfcfc] border border-gray-200 text-gray-800 rounded-xl my-2 overflow-hidden shadow-sm">
            <div class="bg-gray-100/80 px-4 py-2 text-[0.7rem] font-bold text-gray-500 uppercase tracking-wider font-mono border-b border-gray-200 flex items-center justify-between">
                <span>${lang || 'code'}</span>
                <button onclick="window.copyCodeFromElement(this)" data-code="${safeCode}" class="flex items-center gap-1 hover:text-gray-900 transition focus:outline-none">
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                    <span class="copy-text">Salin</span>
                </button>
            </div>
            <div class="p-4 overflow-x-auto text-[0.85rem] font-mono leading-relaxed"><pre><code>${code}</code></pre></div>
        </div>`;
    });
    html = html.replace(/`([^`]+)`/g, '<code class="bg-gray-100 text-pink-600 px-1.5 py-0.5 rounded-md text-[0.85rem] border border-gray-200 font-mono">$1</code>');
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="font-semibold text-gray-900">$1</strong>');
    const parts = html.split(/(<div class="bg-\[\#fcfcfc\][\s\S]*?<\/div>)/g);
    for (let i = 0; i < parts.length; i++) {
        if (!parts[i].startsWith('<div')) parts[i] = parts[i].replace(/\n/g, '<br>');
    }
    return parts.join('');
};
</script>
