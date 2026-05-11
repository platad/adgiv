{{-- resources/views/components/chat/scripts.blade.php --}}
@props(['activeSession'])
<x-chat.scripts.markdown />
<script>
window._bimaAgentIcons = {
    kosakata: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>',
    otoritas: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
    gaya_bahasa: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
    judge: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
};

function chatApp(sessionId, sessionTitle) {
    return {
        sessionId, currentSessionTitle: sessionTitle, isAnalysing: false,
        messages: [], workflowBubble: null, linkedDocument: null, tendencyScore: 0.5,
        currentRound: 1, totalRounds: 1,
        agents: [
            { name: 'kosakata', display: 'Kosakata', status: 'idle' },
            { name: 'otoritas', display: 'Otoritas', status: 'idle' },
            { name: 'gaya_bahasa', display: 'Gaya Bahasa', status: 'idle' },
            { name: 'judge', display: 'Judge', status: 'idle' }
        ],
        init() {
            this.linkedDocument = @json($activeSession->document);
            this.loadMessages();
            this.subscribeToReverb();
        },
        @include('components.chat.scripts.methods-load')
        @include('components.chat.scripts.methods-submit')
        @include('components.chat.scripts.methods-reverb')
        @include('components.chat.scripts.methods-utils')
    };
}
</script>
