{{-- resources/views/components/chat/scripts/methods-load.blade.php --}}
async loadMessages() {
    try {
        const res = await this.apiFetch(`/chat/session/${this.sessionId}/messages`);
        const data = await res.json();
        if (data.success) {
            this.messages = data.messages;
            if (this.messages.length === 0) {
                this.messages.push({
                    id: 'greeting', role: 'assistant',
                    content: 'Halo! Saya BIMA. Apa yang bisa saya bantu analisis hari ini?',
                    created_at: new Date()
                });
            }
            this.$nextTick(() => this.scrollToBottom());
        }
    } catch (_) {}
},
