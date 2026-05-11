{{-- resources/views/components/chat/scripts/methods-submit.blade.php --}}
async handleSubmit(text, files = []) {
    if (!text && (!files || files.length === 0)) return;
    if (this.isAnalysing) return;
    this.messages.push({ id: Date.now(), role: 'user', content: text, metadata: { attached_files: files }, created_at: new Date() });
    this.isAnalysing = true;
    const typingId = 'typing-' + Date.now();
    this.messages.push({ id: typingId, role: 'assistant', content: '', created_at: new Date(), isTyping: true });
    try {
        const res = await this.apiFetch('/chat/analyse', { 
            method: 'POST', 
            body: JSON.stringify({ 
                session_id: this.sessionId, 
                transcription: text, 
                attached_files: files 
            }) 
        });

        const data = await res.json();
        this.messages = this.messages.filter(m => m.id !== typingId);

        if (data.success) {
            if (data.type === 'analysis_started') this.workflowBubble = { label: 'Menganalisis...' };
            if (data.message) {
                // If it's a message object, use its content
                const content = typeof data.message === 'object' ? data.message.content : data.message;
                this.startStreaming(data.message.id || Date.now(), content, data.type);
            }
        } else {
            this.isAnalysing = false;
            const errorMsg = data.message || 'Terjadi kesalahan pada server.';
            this.messages.push({ id: Date.now(), role: 'assistant', content: errorMsg, created_at: new Date() });
        }
    } catch (err) {
        this.messages = this.messages.filter(m => m.id !== typingId);
        this.isAnalysing = false;
        console.error("Submit Error:", err);
        this.messages.push({ id: Date.now(), role: 'assistant', content: 'Kesalahan koneksi atau server (Internal Server Error).', created_at: new Date() });
    }
    this.$nextTick(() => this.scrollToBottom());
},
startStreaming(msgId, fullText, type) {
    let i = 0; let msg = { id: msgId, role: 'assistant', content: '', created_at: new Date(), isTyping: true };
    this.messages.push(msg);
    let interval = setInterval(() => {
        let p = this.messages.find(m => m.id === msgId);
        if (!p) { clearInterval(interval); return; }
        if (i < fullText.length) {
            p.content += fullText.substring(i, i + 3); i += 3; this.$nextTick(() => this.scrollToBottom());
        } else {
            p.isTyping = false; if (type !== 'analysis_started') this.isAnalysing = false; clearInterval(interval);
        }
    }, 15);
},
