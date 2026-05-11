{{-- resources/views/chat.blade.php --}}
<x-layouts.app :activeSessionId="$activeSession->id">
    <x-slot:title>Analisis Suara</x-slot:title>

    <x-slot:sidebar>
        <x-session-sidebar :sessions="$sessions" :activeId="$activeSession->id" />
    </x-slot:sidebar>

    <x-slot:topbar>
        <x-chat.topbar-content :activeSession="$activeSession" />
    </x-slot:topbar>

    <div class="flex flex-1 w-full h-full overflow-hidden"
         x-data="chatApp({{ $activeSession->id }}, {{ json_encode($activeSession->title) }})"
         x-on:new-session.window="createNewSession()"
         x-on:submit-transcription.window="handleSubmit($event.detail.text, $event.detail.files)">
        
        <div class="flex-1 flex flex-col min-w-0 bg-white relative">
            <div class="flex-1 overflow-y-auto p-4 md:p-8 flex flex-col gap-8" id="messages-wrap" x-ref="messagesWrap">
                
                {{-- Hero View --}}
                <template x-if="messages.filter(m => m.role === 'user').length === 0">
                    <x-chat.hero />
                </template>

                {{-- Messages View --}}
                <template x-if="messages.filter(m => m.role === 'user').length > 0">
                    <div class="flex flex-col gap-8 pb-20">
                        <template x-for="(msg, idx) in messages" :key="msg.id || idx">
                            <div class="w-full">
                                <template x-if="msg.role === 'user' && !msg.isTyping">
                                    <x-chat.message-user />
                                </template>
                                <template x-if="msg.role === 'assistant' && !msg.metadata?.decision && !msg.metadata?.agent_name && !msg.isTyping">
                                    <x-chat.message-ai />
                                </template>
                                <template x-if="msg.role === 'assistant' && msg.metadata?.decision && !msg.isTyping">
                                    <x-chat.message-decision />
                                </template>
                                <template x-if="msg.role === 'assistant' && msg.metadata?.agent_name && !msg.isTyping">
                                    <x-chat.message-argument />
                                </template>
                                <template x-if="msg.isTyping">
                                    <x-chat.typing-indicator />
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Input Bar & Workflow Status --}}
            <div class="shrink-0 w-full max-w-4xl mx-auto pb-6 px-4 md:px-8 bg-white relative" 
                 x-show="messages.filter(m => m.role === 'user').length > 0">
                {{-- Workflow Status (Floating above input) --}}
                <div class="absolute bottom-full left-0 w-full px-4 md:px-8 pb-4 pointer-events-none">
                    <div class="pointer-events-auto">
                        <x-chat.workflow-status />
                    </div>
                </div>
                
                <x-voice-input />
            </div>
        </div>

        <x-document-upload-modal :sessionId="$activeSession->id" />
    </div>

    <x-slot:scripts>
        <x-chat.scripts :activeSession="$activeSession" />
    </x-slot:scripts>
</x-layouts.app>
